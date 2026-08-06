<?php

use Livewire\Component;
use App\Models\AssetType;
use App\Models\Assets;
use Livewire\WithPagination;

new class extends Component
{

    use WithPagination;
    public $asset_id;
    public $asset_code = '';

    public $tag_number;
    public $user_location;
    public $pm_schedule_date;
    public $actual_pm_date;
    public $remarks;
    public $sortBy = 'actual_pm_date';
    public $sortDirection = 'asc';


    public function render()
    {
        $assetTypes = AssetType::all();

        $assets = Assets::select('assets.*')
                ->join('asset_types', 'assets.asset_id', '=', 'asset_types.id')
                ->orderBy(
                    $this->sortBy === 'asset_type'
                        ? 'asset_types.asset_type'
                        : 'assets.' . $this->sortBy,
                    $this->sortDirection
                )
                ->paginate(10);

        return view('components.assets.⚡index', [
            'assetTypes' => $assetTypes,
            'assets' => $assets,
        ]);
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

    public function delete($id)
    {
        // Delete or show confirmation
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
};
?>

<div>
    <flux:heading size="xl">Manage Assets</flux:heading>
    <flux:text class="mt-1 mb-2">Manage IT assets</flux:text>
    
    <flux:separator variant="subtle" />

        <flux:modal.trigger name="add-asset">
            <flux:button icon="plus-circle" class="mt-2 cursor-pointer" variant="primary">
                Add Asset
            </flux:button>
        </flux:modal.trigger>


        <!------- Table section ------->

        <flux:table :paginate="$assets" class="mt-3">
            <flux:table.columns>
                <flux:table.column>
                    IT Equipment   
                </flux:table.column>
                <flux:table.column>
                    Tag Number   
                </flux:table.column>
                <flux:table.column>
                    User/Location   
                </flux:table.column>
                <flux:table.column >
                    PM Schedule Date   
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'actual_pm_date'" :direction="$sortDirection" wire:click="sort('actual_pm_date')">
                    Actual PM Date   
                </flux:table.column>
                <flux:table.column>
                    Person in Charge   
                </flux:table.column>
                <flux:table.column>
                    Status/Remarks   
                </flux:table.column>
                <flux:table.column>
                       
                </flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($assets as $asset)
                    <flux:table.row>
                        <flux:table.cell>
                            {{ $asset->assetType?->asset_type }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $asset->tag_number }} 
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $asset->user_location }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $asset->pm_schedule_date }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $asset->actual_pm_date?->format('M d, Y') ?? '-' }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $asset->person_in_charge }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $asset->remarks ?? '-' }}
                        </flux:table.cell>
                        <flux:table.cell class="py-0">

                            <flux:dropdown position="bottom" align="end">

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
                                        wire:click="view({{ $asset->id }})"
                                        class="cursor-pointer"
                                    >
                                        View
                                    </flux:menu.item>

                                    <flux:menu.separator />

                                    <flux:menu.item
                                        icon="trash"
                                        variant="danger"
                                        wire:click="delete({{ $asset->id }})"
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
                        <flux:table.cell colspan="8" class="text-center">
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
                                type="number"
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

                    {{-- <div>
                        <flux:input
                            wire:model="asset_tag"
                            label="Asset Tag"
                            placeholder="Enter asset tag"
                        />
                    </div> --}}

                    <div>
                        {{-- <flux:input
                            wire:model="PM Schedule Date"
                            label="Purchase Date"
                            type="date"
                        /> --}}
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

                    {{-- <div>
                        <flux:input
                            wire:model="location"
                            label="Location"
                            placeholder="Enter location"
                        />
                    </div> --}}

                    {{-- <div>
                        <flux:input
                            wire:model="assigned_to"
                            label="Assigned To"
                            placeholder="Enter assigned user"
                        />
                    </div>--}}

                    <div class="lg:col-span-2">
                        <flux:textarea
                            wire:model="remarks"
                            label="Remarks"
                            placeholder="Additional information..."
                        />
                    </div> 

                </div>
                

                    <div class="flex justify-end gap-2">
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
</div>