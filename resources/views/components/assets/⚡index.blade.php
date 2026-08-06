<?php

use Livewire\Component;
use App\Models\AssetType;
use Livewire\WithPagination;

new class extends Component
{

    use WithPagination;
    public $asset_id;
    public $asset_code = '';



    public function render()
    {
        $assets = AssetType::all();

        return view('components.assets.⚡index', [
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
        // Validate the input data
        $validatedData = $this->validate([
            'asset_id' => 'required|exists:asset_types,id',
            'asset_code' => 'required|string',
            'tag_number' => 'nullable|unique:|string',
            'user' => 'nullable|string',
            'Actual PM' => 'nullable|date',
            'model' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        // // Create a new asset record in the database
        // AssetType::create($validatedData);

        // // Reset the form fields
        // $this->reset(['asset_id', 'asset_code', 'tag_number', 'user', 'Actual PM', 'model', 'remarks']);

        // // Optionally, you can emit an event or show a success message
        // session()->flash('success', 'Asset added successfully!');
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


    <!---- MODAL ---->
    <flux:modal name="add-asset" class="w-full max-w-5xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Add Asset</flux:heading>
                <flux:text class="mt-2">
                    Enter asset details.
                </flux:text>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">

                <div>
                    <flux:select
                        wire:model.live="asset_id"
                        label="Asset Type"
                        placeholder="Choose equipment..."
                    >
                        @foreach ($assets as $asset)
                            <flux:select.option value="{{ $asset->id }}">
                                {{ $asset->asset_type }}
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
                        wire:model="user"
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
                        type="date"
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

                <flux:button type="submit" variant="primary">
                    Save Asset
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>