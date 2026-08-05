<?php

use Livewire\Component;
use Flux\Flux;
use App\Models\Equipment;
use Livewire\WithPagination;

new class extends Component {

    use WithPagination;

    public $equipment_name;
    public $deleteId;

    public function render()
    {
        $equipments = Equipment::paginate(8);

        return view('components.equipments.⚡index', [
            'equipments' => $equipments,
        ]);
    }

    public function save()
    {
        // Validate the input
        $this->validate([
            'equipment_name' => 'required|string|max:255',
        ]);

        Equipment::create([
            'equipment_name' => $this->equipment_name,
        ]);

        Flux::toast(
            heading: 'Equipment added', 
            text: 'The new equipment has been added.', 
            variant: 'success'
            );

        $this->reset();

        flux::modal('create-equipment')->close();
    }

    public function delete()
    {
        Equipment::findOrFail($this->deleteId)->delete();

        $this->deleteId = null;

        Flux::modal('delete-equipment')->close();

        Flux::toast(
            heading: 'Success',
            text: 'Equipment deleted successfully.',
            variant: 'success',
        );
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;

        Flux::modal('delete-equipment')->show();
    }
};
?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Asset Types')" :subheading="__('Create and manage categories used for IT assets.')">

        <div class="space-y-6">

            {{-- Add Asset Type --}}
            <flux:card class="p-6">
                <div class="flex items-end gap-4">
                    <form wire:submit="save" class="flex flex-col md:flex-row items-end gap-4 w-full">

                        <div class="flex-1 w-full">
                            <flux:input
                                wire:model.defer="equipment_name"
                                label="Equipment Name"
                                placeholder="e.g. Desktop Computer"
                            />
                        </div>

                        <flux:button
                            type="submit"
                            variant="primary"
                            icon="plus"
                            class="cursor-pointer"
                        >
                            Add Equipment
                        </flux:button>

                    </form>
                </div>
            </flux:card>

            {{-- Existing Asset Types --}}
            <flux:card>

                <div class="flex items-center justify-between px-6 py-4 border-b">

                    <div>
                        <flux:heading size="lg">
                            Existing Equipments
                        </flux:heading>

                        <flux:text class="mt-1">
                            Manage the equipments available throughout the system.
                        </flux:text>
                    </div>

                    <flux:input icon="magnifying-glass" placeholder="Search..." class="max-w-xs" />

                </div>

                <div class="overflow-x-auto">

                <table :paginate="$equipments" class="w-full">

                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>

                            <th class="px-6 py-3 text-sm font-medium text-center">
                                Equipment Name
                            </th>

                            <th class="px-6 py-3 text-sm font-medium text-center w-40">
                                Actions
                            </th>

                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">

                        @foreach ($equipments as $equipment)
                            <tr>

                                <td class="px-7 py-5 text-center">
                                    {{ $equipment->equipment_name }}
                                </td>

                                <td class="px-7 py-4">
                                    <div class="flex justify-center gap-2">

                                        <flux:button
                                            size="sm"
                                            class="cursor-pointer"
                                            variant="ghost"
                                            icon="pencil-square">
                                            Edit
                                        </flux:button>

                                        <flux:button
                                            wire:key="delete-{{ $equipment->id }}"
                                            size="sm"
                                            variant="danger"
                                            icon="trash"
                                            wire:click="confirmDelete({{ $equipment->id }})"
                                            class="cursor-pointer"
                                        >
                                            Delete
                                        </flux:button>

                                    </div>
                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>

                    <!-- $orders = Order::paginate(5) -->
                    <flux:pagination class="cursor-pointer" :paginator="$equipments" />

                </div>

            </flux:card>

        </div>

    </x-settings.layout>

    <!---- Confirmation Modal when deleting ---->
    <flux:modal name="delete-equipment" class="max-w-md">
    <div class="space-y-6">

        <div>
            <flux:heading size="lg">
                Delete Equipment
            </flux:heading>

            <flux:text class="mt-2">
                Are you sure you want to delete this equipment? This action
                cannot be undone.
            </flux:text>
        </div>

        <div class="flex justify-end gap-2">
            <flux:button
                variant="ghost"
                x-on:click="$flux.modal('delete-equipment').close()"
                class="cursor-pointer"
            >
                Cancel
            </flux:button>

            <flux:button
                variant="danger"
                icon="trash"
                wire:click="delete"
                class="cursor-pointer"
            >
                Delete
            </flux:button>
        </div>

    </div>
    </flux:modal>

</section>
