<?php

use Livewire\Component;
use App\Models\AssetType;
use Livewire\WithPagination;

new class extends Component
{

    use WithPagination;
    public $asset_id;



    public function render()
    {
        $assets = AssetType::all();


        return view('components.assets.⚡index', [
            'assets' => $assets,
        ]);
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
        <flux:modal name="add-asset" class="md:w-96">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Add Asset</flux:heading>
                    <flux:text class="mt-2">Enter asset details.</flux:text>
                </div>
                
                <flux:select wire:model="asset_id" placeholder="Choose equipment...">
                    
                    @foreach ($assets as $asset)
                        <flux:select.option value="{{ $asset->id }}">{{ $asset->asset_type }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input label="Date of birth" type="date" />
                <div class="flex">
                    <flux:spacer />
                    <flux:button type="submit" variant="primary">Save changes</flux:button>
                </div>
            </div>
        </flux:modal>
</div>