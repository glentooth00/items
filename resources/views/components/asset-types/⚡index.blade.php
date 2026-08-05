<?php

use Livewire\Component;
use Flux\Flux;
use App\Models\AssetType;
use Livewire\WithPagination;

new class extends Component {

    use WithPagination;

    public $asset_code;
    public $asset_type;
    public $deleteId;
    public $asset_route;

    public function render()
    {
        $assetTypes = AssetType::paginate(8);

        return view('components.asset-types.⚡index', [
            'assetTypes' => $assetTypes,
        ]);
    }

    public function save()
    {
        // Validate the input
        $this->validate([
            'asset_type' => 'required|string|max:255',
            'asset_code' => 'nullable|string|max:255',
            'asset_route' => 'nullable|string|max:255',
        ]);

        AssetType::create([
            'asset_type' => $this->asset_type,
            'asset_code' => $this->asset_code,
            'asset_route' => $this->asset_route,
        ]);

        Flux::toast(heading: 'Asset type added', text: 'The new asset type has been added.', variant: 'success');

        $this->reset();

        flux::modal('create-asset-type')->close();
    }

    public function delete()
    {
        AssetType::findOrFail($this->deleteId)->delete();

        $this->deleteId = null;

        Flux::modal('delete-asset-type')->close();

        Flux::toast(
            heading: 'Success',
            text: 'Asset type deleted successfully.',
            variant: 'success'
        );
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;

        Flux::modal('delete-asset-type')->show();
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
                                wire:model.defer="asset_type"
                                label="Asset Type"
                                placeholder="e.g. Desktop Computer"
                            />
                        </div>

                        <div class="flex-1 w-full">
                            <flux:input
                                wire:model.defer="asset_code"
                                label="Asset Code"
                                placeholder="Enter asset code"
                            />
                        </div>

                        <div class="flex-1 w-full">
                            <flux:input
                                wire:model.defer="asset_route"
                                label="Asset Route"
                                placeholder="Enter asset route"
                            />
                        </div>

                        <flux:button
                            type="submit"
                            variant="primary"
                            icon="plus"
                            class="cursor-pointer"
                        >
                            Add Type
                        </flux:button>

                    </form>
                </div>
            </flux:card>

            {{-- Existing Asset Types --}}
            <flux:card>

                <div class="flex items-center justify-between px-6 py-4 border-b">

                    <div>
                        <flux:heading size="lg">
                            Existing Asset Types
                        </flux:heading>

                        <flux:text class="mt-1">
                            Manage the categories available throughout the system.
                        </flux:text>
                    </div>

                    <flux:input icon="magnifying-glass" placeholder="Search..." class="max-w-xs" />

                </div>

                <div class="overflow-x-auto">

                <table :paginate="$assetTypes" class="w-full">

                    <thead class="bg-zinc-50 dark:bg-zinc-800">

                        <tr>

                            <th class="px-6 py-3 text-sm font-medium text-center">
                                Asset Type
                            </th>

                            <th class="px-6 py-3 text-sm font-medium text-center w-40">
                                Asset Code
                            </th>

                            <th class="px-6 py-3 text-sm font-medium text-center w-40">
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">

                        @foreach ($assetTypes as $type)
                            <tr wire:key="asset-type-{{ $type->id }}">

                                <td class="px-7 py-5 text-center">
                                    {{ $type->asset_type }}
                                </td>

                                <td class="px-7 py-5 text-center">
                                    {{ $type->asset_code }}
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
                                            size="sm"
                                            class="cursor-pointer"
                                            variant="danger"
                                            icon="trash"
                                            wire:click="confirmDelete({{ $type->id }})">
                                            Delete
                                        </flux:button>

                                    </div>
                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>

                    <!-- $orders = Order::paginate(5) -->
                    <flux:pagination class="cursor-pointer" :paginator="$assetTypes" />

                </div>

            </flux:card>

        </div>

    </x-settings.layout>

    <!---- Confirmation Modal when deleting ---->
    <flux:modal name="delete-asset-type" class="max-w-md">
    <div class="space-y-6">

        <div>
            <flux:heading size="lg">
                Delete Asset Type
            </flux:heading>

            <flux:text class="mt-2">
                Are you sure you want to delete this asset type? This action
                cannot be undone.
            </flux:text>
        </div>

        <div class="flex justify-end gap-2">
            <flux:button
                variant="ghost"
                x-on:click="$flux.modal('delete-asset-type').close()"
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
