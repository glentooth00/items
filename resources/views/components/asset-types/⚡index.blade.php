<?php

use Livewire\Component;
use Flux\Flux;
use App\Models\AssetType;

new class extends Component
{
    public $name = '';

    // Sample data
    public $assetTypes = [
        ['id' => 1, 'name' => 'System Unit'],
        ['id' => 2, 'name' => 'Laptop'],
        ['id' => 3, 'name' => 'Printer'],
        ['id' => 4, 'name' => 'UPS'],
    ];

    public $asset_type;

    public function save()
    {
        // Validate the input
        $this->validate([
            'asset_type' => 'required|string|max:255',
        ]);

        AssetType::create([
            'asset_type' => $this->asset_type
        ]);

        Flux::toast(
        heading: 'Asset type added',
        text: 'The new asset type has been added.',
        variant: 'success');

        $this->reset();

        flux::modal('create-asset-type')->close();
    }
};
?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout
        :heading="__('Asset Types')"
        :subheading="__('Create and manage categories used for IT assets.')">

        <div class="space-y-6">

            {{-- Add Asset Type --}}
            <flux:card class="p-6">
                <div class="flex items-end gap-4">
                <form wire:submit="save">
                        <div class="flex-1">
                            <flux:input
                                wire:model.defer="asset_type"
                                label="Asset Type"
                                placeholder="e.g. Desktop Computer"
                            />
                        </div>
                        <flux:button
                            type="submit"
                            variant="primary"
                            icon="plus"
                            class="mt-6 cursor-pointer">
                            Add Type
                        </flux:button>

                    </div>
                </form>
                    
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
                    
                    <flux:input
                        icon="magnifying-glass"
                        placeholder="Search..."
                        class="max-w-xs"
                    />

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-zinc-50 dark:bg-zinc-800">

                            <tr class="text-left">

                                <th class="px-6 py-3 text-sm font-medium">
                                    Asset Type
                                </th>

                                <th class="px-6 py-3 text-right text-sm font-medium w-40">
                                    Actions
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">

                            @foreach ($assetTypes as $type)

                                <tr>

                                    <td class="px-6 py-4">
                                        {{ $type['name'] }}
                                    </td>

                                    <td class="px-6 py-4">

                                        <div class="flex justify-end gap-2">

                                            <flux:button
                                                size="sm"
                                                variant="ghost"
                                                icon="pencil-square">
                                                Edit
                                            </flux:button>

                                            <flux:button
                                                size="sm"
                                                variant="danger"
                                                icon="trash">
                                                Delete
                                            </flux:button>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </flux:card>

        </div>

    </x-settings.layout>

</section>