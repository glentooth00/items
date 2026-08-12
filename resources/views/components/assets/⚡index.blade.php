<?php

use Livewire\Component;
use App\Models\AssetType;
use App\Models\Assets;
use Livewire\WithPagination;
use Flux\Flux;

new class extends Component
{

    use WithPagination;
    public $asset_id;
    public $asset_code = '';
    public $deleteId = null;
    public $tag_number;
    public $user_location;
    public $pm_schedule_date;
    public $actual_pm_date;
    public $remarks;
    public $sortBy = 'actual_pm_date';
    public $sortDirection = 'asc';
    public $status;
    public $filterAssetType = '';
    public $searchTag = '';
    public array $selectedAssets = [];
    public bool $selectAll = false;

    public function render()
    {
        $assetTypes = AssetType::orderBy('asset_type')->get();

        $assets = $this->assetsQuery()->paginate(10);

        return view('components.assets.⚡index', [
            'assetTypes' => $assetTypes,
            'assets' => $assets,
        ]);
    }

    protected function assetsQuery()
{
    return Assets::with('assetType')
        ->select('assets.*')
        ->join('asset_types', 'assets.asset_id', '=', 'asset_types.id')

        // Filter by IT Equipment
        ->when($this->filterAssetType, function ($query) {
            $query->where(
                'assets.asset_id',
                $this->filterAssetType
            );
        })

        // Search by Tag Number
        ->when($this->searchTag, function ($query) {
            $query->where(
                'assets.tag_number',
                'like',
                '%' . $this->searchTag . '%'
            );
        })

        // Sorting
        ->orderBy(
            $this->sortBy === 'asset_type'
                ? 'asset_types.asset_type'
                : 'assets.' . $this->sortBy,
            $this->sortDirection
        );
    }

    public function updatedAssetId($asset_id)
    {

        $asset_code = AssetType::find($asset_id);
        
        if ($asset_code) {
            $this->asset_code = $asset_code->asset_code;
        } else {
            $this->asset_code = '';
        }

    }

    public function saveAsset()
    {
        // Combine first
        $tagNumber = $this->asset_code . '-' . $this->tag_number;

        // Validate
        $this->validate([
            'asset_id'          => 'required|exists:asset_types,id',
            'tag_number'        => 'required|string',
            'user_location'     => 'nullable|string',
            'pm_schedule_date'  => 'nullable',
            'actual_pm_date'    => 'nullable|date',
            'remarks'           => 'nullable|string',
            'status'            => 'required|string|in:Pending,In Progress,Done,For Repair,Defective,Disposed,Damaged',
        ]);


        // Check the final tag number for uniqueness
        if (Assets::where('tag_number', $tagNumber)->exists()) {
            $this->addError('tag_number', 'This asset tag already exists.');
            return;
        }

        Assets::create([
            'asset_id'         => $this->asset_id,
            'tag_number'       => $tagNumber,
            'user_location'    => $this->user_location,
            'pm_schedule_date' => $this->pm_schedule_date,
            'actual_pm_date'   => $this->actual_pm_date,
            'remarks'          => $this->remarks,
            'status'           => $this->status,
        ]);

        Flux::toast(
            heading: 'Asset added',
            text: 'The new asset was successfully saved.',
            variant: 'success'
        );

        $this->reset();

        Flux::modal('add-asset')->close();
    }

    public function edit($id)
    {
        // Load asset and open edit modal
    }

    public function viewAsset($id)
    {
        // Open view modal
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;

        Flux::modal('delete-asset')->show();
    }

    public function delete()
    {
        $asset = Assets::findOrFail($this->deleteId);

        if (! $asset) {

            Flux::toast(
                heading: 'Not Found',
                text: 'Asset no longer exists.',
                variant: 'danger'
            );

            return;
        }

        $asset->delete();

        Flux::modal('delete-asset')->close();

        Flux::toast(
            heading: 'Deleted',
            text: 'Asset deleted successfully.',
            variant: 'success'
        );

        $this->deleteId = null;

        $this->resetPage();
    }

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedFilterAssetType()
    {
        $this->resetPage();
    }

    public function updatedSearchTag()
    {
        $this->resetPage();
    }

    public function updatedSelectedAssets()
    {
        // Get IDs from the current page
        $currentPageIds = $this->assetsQuery()
            ->paginate(10)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->toArray();

        // Check if every asset on the current page is selected
        $this->selectAll =
            count($currentPageIds) > 0 &&
            empty(array_diff(
                $currentPageIds,
                $this->selectedAssets
            ));
    }

    public function updatedSelectAll($value)
    {
        if ($value) {

            // Get IDs from the CURRENT PAGE
            $this->selectedAssets = $this->assetsQuery()
                ->paginate(10)
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();

        } else {

            $this->selectedAssets = [];
        }
    }

    public function deselectAll()
    {
        $this->selectedAssets = [];
        $this->selectAll = false;
    }

};
?>

<div>
    <flux:heading size="xl">Manage Assets</flux:heading>
    <flux:text class="mt-1 mb-2">Manage IT assets</flux:text>
    
    <flux:separator variant="subtle" />

<div class="flex items-end justify-between gap-3 mt-3">

    <flux:modal.trigger name="add-asset">
        <flux:button
            icon="plus-circle"
            class="cursor-pointer"
            variant="primary"
            style="margin-top:1.6em"
        >
            Add Asset
        </flux:button>
    </flux:modal.trigger>

    <div class="flex items-end gap-3">
        <div class="w-64">
            <flux:select
                wire:model.live="filterAssetType"
                label="Filter by IT Equipment"
            >
                <flux:select.option value="">
                    All IT Equipment
                </flux:select.option>

                @foreach ($assetTypes as $assetType)
                    <flux:select.option value="{{ $assetType->id }}">
                        {{ $assetType->asset_type }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <div class="w-64">
            <flux:input
                wire:model.live.debounce.300ms="searchTag"
                label="Search Tag Number"
                placeholder="e.g. PC-001"
                icon="magnifying-glass"
            />
        </div>
    </div>

</div>

        <!------- Table section ------->
<flux:table :paginate="$assets" class="mt-3">

    <flux:table.columns>

        {{-- Select All --}}
        <flux:table.column class="w-10">
            <flux:checkbox wire:model.live="selectAll" />
        </flux:table.column>

        <flux:table.column>
            IT Equipment
        </flux:table.column>

        <flux:table.column>
            Tag Number
        </flux:table.column>

        <flux:table.column>
            User/Location
        </flux:table.column>

        <flux:table.column>
            PM Schedule Date
        </flux:table.column>

        <flux:table.column
            sortable
            :sorted="$sortBy === 'actual_pm_date'"
            :direction="$sortDirection"
            wire:click="sort('actual_pm_date')"
        >
            Actual PM Date
        </flux:table.column>

        <flux:table.column>
            Person in Charge
        </flux:table.column>

        <flux:table.column>
            Status
        </flux:table.column>

        <flux:table.column></flux:table.column>

    </flux:table.columns>


    <flux:table.rows>

        @forelse ($assets as $asset)

            <flux:table.row>

                {{-- Individual Checkbox --}}
                <flux:table.cell class="w-10">
                    <flux:checkbox
                        wire:model.live="selectedAssets"
                        value="{{ $asset->id }}"
                    />
                </flux:table.cell>


                {{-- IT Equipment --}}
                <flux:table.cell>
                    @php
                        $color = match ($asset->assetType?->asset_type) {
                            'WiFi Router'          => 'blue',
                            'UPS'                  => 'amber',
                            'System Unit'          => 'zinc',
                            'Server'               => 'red',
                            'Router'               => 'sky',
                            'Printer (Ink Tank)'   => 'green',
                            'Printer (Dot Matrix)' => 'orange',
                            'Network Switch'      => 'indigo',
                            'Laser Printer'        => 'violet',
                            'Laptop'               => 'cyan',
                            'External Drive'       => 'emerald',
                            'CCTV NVR'             => 'purple',
                            'CCTV Camera'          => 'yellow',
                            'Access Point'         => 'teal',
                            default                => 'pink',
                        };
                    @endphp

                    <flux:badge variant="solid" :color="$color">
                        {{ $asset->assetType?->asset_type }}
                    </flux:badge>
                </flux:table.cell>


                {{-- Tag Number --}}
                <flux:table.cell>
                    {{ $asset->tag_number }}
                </flux:table.cell>


                {{-- User / Location --}}
                <flux:table.cell>
                    {{ $asset->user_location }}
                </flux:table.cell>


                {{-- PM Schedule Date --}}
                <flux:table.cell>
                    {{ $asset->pm_schedule_date }}
                </flux:table.cell>


                {{-- Actual PM Date --}}
                <flux:table.cell>
                    {{ $asset->actual_pm_date?->format('M d, Y') ?? '-' }}
                </flux:table.cell>


                {{-- Person in Charge --}}
                <flux:table.cell>
                    {{ $asset->person_in_charge ?? 'R.J. Dequilla/ G.D. Alpasan/ P.G. Cabrillos' }}
                </flux:table.cell>


                {{-- Status --}}
                <flux:table.cell>

                    @php
                        $status = trim($asset->status ?? '');

                        $color = match (strtolower($status)) {
                            'done'        => 'emerald',
                            'pending'     => 'amber',
                            'in progress' => 'blue',
                            'for repair'  => 'orange',
                            'defective'   => 'red',
                            'disposed'    => 'zinc',
                            'damaged'     => 'red',
                            default       => 'purple',
                        };
                    @endphp

                    @if ($status)

                        <flux:badge
                            variant="solid"
                            :color="$color"
                        >
                            {{ $status }}
                        </flux:badge>

                    @else

                        <span class="text-zinc-500">
                            -
                        </span>

                    @endif

                </flux:table.cell>


                {{-- Actions --}}
                <flux:table.cell class="py-0">

                    <flux:dropdown
                        position="bottom"
                        align="end"
                    >

                        <flux:button
                            variant="ghost"
                            size="sm"
                            icon="ellipsis-horizontal"
                            class="cursor-pointer"
                        />

                        <flux:menu>

                            <flux:menu.item
                                icon="pencil-square"
                                wire:click="edit({{ $asset->id }})"
                                class="cursor-pointer"
                            >
                                Edit
                            </flux:menu.item>


                            <flux:menu.item
                                icon="eye"
                                href="{{ route('assets.view', $asset->id) }}"
                                class="cursor-pointer"
                            >
                                View
                            </flux:menu.item>


                            <flux:menu.separator />


                            <flux:menu.item
                                icon="trash"
                                variant="danger"
                                wire:click="confirmDelete({{ $asset->id }})"
                                class="cursor-pointer"
                            >
                                Delete
                            </flux:menu.item>

                        </flux:menu>

                    </flux:dropdown>

                </flux:table.cell>

            </flux:table.row>

        @empty

            <flux:table.row>

                <flux:table.cell
                    colspan="9"
                    class="text-center"
                >
                    No assets found.
                </flux:table.cell>

            </flux:table.row>

        @endforelse

    </flux:table.rows>

</flux:table>

        <!-- $$assets = Assets::paginate(10) -->
        {{-- <flux:pagination class="cursor-pointer" :paginator="$assets" /> --}}


    <!---- MODAL ---->
    <flux:modal name="add-asset" class="w-full max-w-5xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Add Asset</flux:heading>
                <flux:text class="mt-2">
                    Enter asset details.
                </flux:text>
            </div>

                <form wire:submit="saveAsset">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">

                    <div>
                        <flux:select
                            wire:model.live="asset_id"
                            label="Asset Type"
                            placeholder="Choose equipment..."
                        >
                            @foreach ($assetTypes as $assetType)
                                <flux:select.option value="{{ $assetType->id }}">
                                    {{ $assetType->asset_type }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-1">
                            <flux:input
                                wire:model="asset_code"
                                label="Tag Number"
                                readonly
                            />
                        </div>

                        <div class="flex-1">
                            <flux:input
                                class="mt-4"
                                wire:model="tag_number"
                                label=""
                            />
                        </div>
                    </div>

                    <div>
                        <flux:input
                            wire:model="user_location"
                            label="User / Location"
                            placeholder="Enter name of user"
                        />
                        </div>

                    <div>
                    </div>

                    <div>
                        <flux:input
                            wire:model="pm_schedule_date"
                            label="PM Schedule Date"
                        />
                    </div>

                    <div>
                        <flux:input
                            wire:model="actual_pm_date"
                            label="Actual PM Date"
                            type="date"
                        />
                    </div>


                    <div>
                    <flux:select
                        wire:model.live="status"
                        label="Status"
                        
                    >
                        <flux:select.option value="" hidden>
                            Select Status...
                        </flux:select.option>
                        <flux:select.option value="Pending">
                            Pending
                        </flux:select.option>

                        <flux:select.option value="In Progress">
                            In Progress
                        </flux:select.option>

                        <flux:select.option value="Done">
                            Done
                        </flux:select.option>

                        <flux:select.option value="For Repair">
                            For Repair
                        </flux:select.option>

                        <flux:select.option value="Defective">
                            Defective
                        </flux:select.option>

                        <flux:select.option value="Disposed">
                            Disposed
                        </flux:select.option>
                        <flux:select.option value="Damaged">
                            Damaged
                        </flux:select.option>
                    </flux:select>
                    </div>

                    <div class="lg:col-span-2">
                        <flux:textarea
                            wire:model="remarks"
                            label="Remarks"
                            placeholder="Additional information..."
                        />
                    </div> 

                </div>
                

                    <div class="flex justify-end gap-2 mt-2">
                        <flux:modal.close>
                            <flux:button variant="ghost">
                                Cancel
                            </flux:button>
                        </flux:modal.close>

                        <flux:button type="submit" class="cursor-pointer" variant="primary">
                            Save Asset
                        </flux:button>
                    </div>
                </form>
        </div>
    </flux:modal>


    <flux:modal name="delete-asset" class="md:w-96">
    <div class="space-y-6">

        <div>
            <flux:heading size="lg">
                Delete Asset
            </flux:heading>

            <flux:text class="mt-2">
                Are you sure you want to delete this asset?
                This action cannot be undone.
            </flux:text>
        </div>

        <div class="flex justify-end gap-2">

            <flux:modal.close>
                <flux:button variant="ghost">
                    Cancel
                </flux:button>
            </flux:modal.close>

            <flux:button
                variant="danger"
                wire:click="delete"
                class="cursor-pointer"
            >
                Delete
            </flux:button>

        </div>

    </div>
</flux:modal>
</div>