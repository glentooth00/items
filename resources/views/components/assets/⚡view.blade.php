<?php

use Livewire\Component;
use App\Models\Assets;

new class extends Component
{

    public $asset;

    public function mount($id)
    {
        $this->asset = Assets::with('assetType')
            ->findOrFail($id);
    }
};
?>
<div>
    <flux:heading size="xl">
        {{ $asset->assetType?->asset_type }}
    </flux:heading>

    <flux:text class="mt-1">
        {{ $asset->tag_number }}
    </flux:text>

    <flux:separator variant="subtle" class="my-4" />

    {{-- Asset Information --}}
    <flux:heading size="lg" class="mb-5 mt-3">
        Asset Information
    </flux:heading>

<div class="flex flex-row gap-3 w-full mb-5">

    <div class="flex-1 min-w-0">
        <flux:input
            label="IT Equipment"
            value="{{ $asset->assetType?->asset_type }}"
            readonly
        />
    </div>

    <div class="flex-1 min-w-0">
        <flux:input
            label="Tag Number"
            value="{{ $asset->tag_number }}"
            readonly
        />
    </div>

    <div class="flex-1 min-w-0">
        <flux:input
            label="User / Location"
            value="{{ $asset->user_location ?? '-' }}"
            readonly
        />
    </div>

    <div class="flex-1 min-w-0">
        <flux:input
            label="Status"
            value="{{ $asset->status ?? '-' }}"
            readonly
        />
    </div>

    <div class="flex-1 min-w-0">
        <flux:input
            label="PM Schedule"
            value="{{ $asset->pm_schedule_date ?? '-' }}"
            readonly
        />
    </div>

    <div class="flex-1 min-w-0">
        <flux:input
            label="Actual PM Date"
            value="{{ $asset->actual_pm_date?->format('M d, Y') ?? '-' }}"
            readonly
        />
    </div>

</div>

<flux:separator />

    {{-- Preventive Maintenance History --}}
    <div class="mt-10">

        <div class="flex items-center justify-between mb-3">

            <div>
                <flux:heading size="lg">
                    Preventive Maintenance History
                </flux:heading>

                <flux:text class="mt-1">
                    Maintenance records for this asset.
                </flux:text>
            </div>

            <flux:button
                icon="plus-circle"
                variant="primary"
                class="cursor-pointer"
            >
                Schedule PM
            </flux:button>

        </div>

        <flux:table>

            <flux:table.columns>

                <flux:table.column>
                    Year
                </flux:table.column>

                <flux:table.column>
                    PM Schedule
                </flux:table.column>

                <flux:table.column>
                    Actual PM Date
                </flux:table.column>

                <flux:table.column>
                    Status
                </flux:table.column>

                <flux:table.column>
                    Remarks
                </flux:table.column>

            </flux:table.columns>

            <flux:table.rows>

                <flux:table.row>

                    <flux:table.cell>
                        {{ $asset->pm_schedule_date
                            ? \Carbon\Carbon::parse($asset->pm_schedule_date)->format('Y')
                            : '-' }}
                    </flux:table.cell>

                    <flux:table.cell>
                        {{ $asset->pm_schedule_date ?? '-' }}
                    </flux:table.cell>

                    <flux:table.cell>
                        {{ $asset->actual_pm_date?->format('M d, Y') ?? '-' }}
                    </flux:table.cell>

                    <flux:table.cell>

                        @php
                            $status = trim($asset->status ?? '');

                            $color = match (strtolower($status)) {
                                'done' => 'emerald',
                                'pending' => 'amber',
                                'in progress' => 'blue',
                                'for repair' => 'orange',
                                'defective',
                                'damaged' => 'red',
                                'disposed' => 'zinc',
                                default => 'purple',
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
                            <span class="text-zinc-500">-</span>
                        @endif

                    </flux:table.cell>

                    <flux:table.cell>
                        {{ $asset->remarks ?? '-' }}
                    </flux:table.cell>

                </flux:table.row>

            </flux:table.rows>

        </flux:table>

    </div>

</div>