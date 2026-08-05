<?php

use Livewire\Component;
use App\Models\Equipment;

new class extends Component
{
    public function render()
    {
        $equipments = Equipment::all();
        return view('components.inventory.⚡index',[
            'equipments' => $equipments,
        ]);
    }
};
?>


<div>
    <flux:heading size="xl">Manage Inventory</flux:heading>
    <flux:text class="mt-1 mb-2">Manage asset inventory</flux:text>
    
    <flux:separator variant="subtle" />

    <flux:modal.trigger name="add-inventory">
            <flux:button icon="plus-circle" class="mt-2 cursor-pointer" variant="primary">
                Add Inventory
            </flux:button>
    </flux:modal.trigger>




    <!----  ADD INVENTORY MODAL ---->
<flux:modal name="add-inventory" class="md:w-5xl">
    <form wire:submit="saveInventory">

        <div class="space-y-6">

            <div>
                <flux:heading size="lg">Add Inventory</flux:heading>
                <flux:text class="mt-2">
                    Enter the inventory details below.
                </flux:text>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">

                <!-- Left Column -->
                <div class="flex-1 space-y-4">

                    <flux:select wire:model="equipment_id" label="Equipment" placeholder="Choose equipment...">
                        @foreach ($equipments as $equipment)
                            <flux:select.option value="{{ $equipment->id }}">
                                {{ $equipment->equipment_name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input
                        wire:model="quality_control_date"
                        label="Date of Quality Control Checking"
                        type="date"
                    />

                    <flux:input
                        wire:model="name"
                        label="Name"
                    />

                    <flux:input
                        wire:model="location"
                        label="Location"
                    />

                    <flux:select
                        wire:model="status"
                        label="Status"
                        placeholder="Select status"
                    >
                        <flux:select.option value="Working">Working</flux:select.option>
                        <flux:select.option value="For Repair">For Repair</flux:select.option>
                        <flux:select.option value="Condemned">Condemned</flux:select.option>
                        <flux:select.option value="Disposed">Disposed</flux:select.option>
                    </flux:select>

                </div>

                <!-- Right Column -->
                <div class="flex-1 space-y-4">

                    <flux:input
                        wire:model="tag_number"
                        label="Tag Number"
                    />

                    <flux:input
                        wire:model="brand_model"
                        label="Brand / Model"
                    />

                    <flux:input
                        wire:model="serial_number"
                        label="Serial Number"
                    />

                    <flux:input
                        wire:model="price"
                        label="Price"
                        type="number"
                        step="0.01"
                    />

                    <flux:input
                        wire:model="manufacturer"
                        label="Manufacturer"
                    />

                </div>

            </div>

            <div class="flex justify-end gap-2">
                <flux:button
                    type="button"
                    variant="ghost"
                    x-on:click="$flux.modal('add-inventory').close()"
                >
                    Cancel
                </flux:button>

                <flux:button
                    type="submit"
                    variant="primary"
                >
                    Save Inventory
                </flux:button>
            </div>

        </div>

    </form>
</flux:modal>
</div>