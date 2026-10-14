<?php

use Livewire\Component;
use App\Models\Assets;
use App\Models\AreaOffices;
use App\Models\MaintenanceSchedule;
use Illuminate\Support\Facades\DB;

new class extends Component
{
    public $area_office_id = '';
    public $from = '';
    public $to = '';
    public $remarks = '';
    public $schedules= [];
    public $selectedAssets = [];


    public function render(){

        // MaintenanceSchedule
        $offices = AreaOffices::get();

        $schedules = MaintenanceSchedule::get();

        return view('components.schedule.⚡index',[
            'offices' => $offices,
            'schedules' => $schedules 
        ]);

    }

};
?>

<div class="w-full">

    <div class="space-y-6">

        {{-- Header / Create Button --}}
        <div class="flex items-center justify-between">

            <div>
                <flux:heading size="lg">
                    Maintenance Schedules
                </flux:heading>

                <flux:text class="mt-1">
                    Manage scheduled maintenance trips by area office.
                </flux:text>
            </div>

            <flux:button
                variant="primary"
                icon="plus"
                x-on:click="$flux.modal('create-maintenance-schedule').show()"
                class="cursor-pointer"
            >
                Create Schedule
            </flux:button>

        </div>

        {{-- Filters --}}
        <flux:card class="p-4">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <flux:select
                    wire:model.live="filterAreaOffice"
                    label="Area Office"
                >
                    <flux:select.option value="">
                        All Offices
                    </flux:select.option>

                    @foreach ($offices as $office)
                        <flux:select.option value="{{ $office->id }}">
                            {{ $office->area_office }}
                        </flux:select.option>
                    @endforeach

                </flux:select>


                <flux:select
                    wire:model.live="filterStatus"
                    label="Status"
                >
                    <flux:select.option value="">
                        All Status
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

                    <flux:select.option value="Cancelled">
                        Cancelled
                    </flux:select.option>

                </flux:select>


                <flux:input
                    type="date"
                    wire:model.live="filterFrom"
                    label="From Date"
                />


                <flux:input
                    wire:model.live="search"
                    icon="magnifying-glass"
                    label="Search"
                    placeholder="Search..."
                />

            </div>

        </flux:card>


        {{-- Schedule Table --}}
        <flux:card>

            <flux:table :paginate="$schedules">

                <flux:table.columns>

                    <flux:table.column class="text-center">
                        Area Office
                    </flux:table.column>

                    <flux:table.column class="text-center">
                        From
                    </flux:table.column>

                    <flux:table.column class="text-center">
                        To
                    </flux:table.column>

                    <flux:table.column class="text-center">
                        Assets
                    </flux:table.column>

                    <flux:table.column class="text-center">
                        Status
                    </flux:table.column>

                    <flux:table.column class="text-center">
                        Remarks
                    </flux:table.column>

                    <flux:table.column class="text-center">
                        Actions
                    </flux:table.column>

                </flux:table.columns>


                <flux:table.rows>

                    @forelse ($schedules as $schedule)

                        <flux:table.row>

                            <flux:table.cell class="text-center">
                                {{ $schedule->areaOffice?->area_office ?? '-' }}
                            </flux:table.cell>

                            <flux:table.cell class="text-center">
                                {{ \Carbon\Carbon::parse($schedule->from)->format('M d, Y') }}
                            </flux:table.cell>

                            <flux:table.cell class="text-center">
                                {{ \Carbon\Carbon::parse($schedule->to)->format('M d, Y') }}
                            </flux:table.cell>

                            <flux:table.cell class="text-center">
                                {{ $schedule->assets_count ?? 0 }}
                            </flux:table.cell>

                            <flux:table.cell class="text-center">

                                @php
                                    $statusColor = match (strtolower($schedule->status)) {
                                        'done' => 'emerald',
                                        'pending' => 'amber',
                                        'in progress' => 'blue',
                                        'cancelled' => 'red',
                                        default => 'zinc',
                                    };
                                @endphp

                                <flux:badge
                                    variant="solid"
                                    :color="$statusColor"
                                >
                                    {{ $schedule->status }}
                                </flux:badge>

                            </flux:table.cell>

                            <flux:table.cell class="text-center">
                                {{ $schedule->remarks ?? '-' }}
                            </flux:table.cell>

                            <flux:table.cell class="text-center">

                                <div class="flex justify-center">

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
                                                icon="eye"
                                                wire:click="view({{ $schedule->id }})"
                                            >
                                                View
                                            </flux:menu.item>

                                            <flux:menu.item
                                                icon="pencil-square"
                                                wire:click="edit({{ $schedule->id }})"
                                            >
                                                Edit
                                            </flux:menu.item>

                                            <flux:menu.separator />

                                            <flux:menu.item
                                                icon="trash"
                                                variant="danger"
                                                wire:click="confirmDelete({{ $schedule->id }})"
                                            >
                                                Delete
                                            </flux:menu.item>

                                        </flux:menu>

                                    </flux:dropdown>

                                </div>

                            </flux:table.cell>

                        </flux:table.row>

                    @empty

                        <flux:table.row>

                            <flux:table.cell
                                colspan="7"
                                class="text-center py-8"
                            >
                                No maintenance schedules found.
                            </flux:table.cell>

                        </flux:table.row>

                    @endforelse

                </flux:table.rows>

            </flux:table>

        </flux:card>

    </div>


    {{-- Create Schedule Modal --}}
    <flux:modal
        name="create-maintenance-schedule"
        class="max-w-5xl"
    >

        <div class="space-y-6">

            <div>
                <flux:heading size="lg">
                    Create Maintenance Schedule
                </flux:heading>

                <flux:text class="mt-1">
                    Select the area office, maintenance dates, and assets.
                </flux:text>
            </div>


            {{-- Schedule Details --}}
            <div class="grid grid-cols-3 gap-4">

                <flux:select
                    wire:model.live="area_office_id"
                    label="Area Office"
                >

                    <flux:select.option value="">
                        Select Area Office
                    </flux:select.option>

                    @foreach ($offices as $office)
                        <flux:select.option value="{{ $office->id }}">
                            {{ $office->area_office }}
                        </flux:select.option>
                    @endforeach

                </flux:select>


                <flux:input
                    type="date"
                    wire:model="from"
                    label="From"
                />

                <flux:input
                    type="date"
                    wire:model="to"
                    label="To"
                />

            </div>


            {{-- Assets --}}
            <div class="border rounded-lg overflow-hidden">

                <div class="px-4 py-3 border-b bg-zinc-50 dark:bg-zinc-800">

                    <flux:heading size="sm">
                        Assets Assigned to Area Office
                    </flux:heading>

                </div>


                @if ($area_office_id)

                    <flux:table>

                        <flux:table.columns>

                            <flux:table.column class="w-10 text-center">
                                Select
                            </flux:table.column>

                            <flux:table.column class="text-center">
                                IT Equipment
                            </flux:table.column>

                            <flux:table.column class="text-center">
                                Tag Number
                            </flux:table.column>

                            <flux:table.column class="text-center">
                                User / Location
                            </flux:table.column>

                        </flux:table.columns>


                        <flux:table.rows>

                            @forelse ($this->availableAssets as $asset)

                                <flux:table.row>

                                    <flux:table.cell class="text-center">

                                        <div class="flex justify-center">

                                            <flux:checkbox
                                                wire:model.live="selectedAssets"
                                                value="{{ $asset->id }}"
                                            />

                                        </div>

                                    </flux:table.cell>

                                    <flux:table.cell class="text-center">
                                        {{ $asset->assetType?->asset_type ?? '-' }}
                                    </flux:table.cell>

                                    <flux:table.cell class="text-center">
                                        {{ $asset->tag_number }}
                                    </flux:table.cell>

                                    <flux:table.cell class="text-center">
                                        {{ $asset->user_location ?? '-' }}
                                    </flux:table.cell>

                                </flux:table.row>

                            @empty

                                <flux:table.row>

                                    <flux:table.cell
                                        colspan="4"
                                        class="text-center py-6"
                                    >
                                        No assets assigned to this area office.
                                    </flux:table.cell>

                                </flux:table.row>

                            @endforelse

                        </flux:table.rows>

                    </flux:table>

                @else

                    <div class="py-8 text-center">

                        <flux:text>
                            Select an area office to display its assets.
                        </flux:text>

                    </div>

                @endif

            </div>


            {{-- Remarks --}}
            <flux:textarea
                wire:model="remarks"
                label="Remarks"
                placeholder="Optional remarks..."
            />


            {{-- Actions --}}
            <div class="flex justify-end gap-2">

                <flux:button
                    variant="ghost"
                    x-on:click="$flux.modal('create-maintenance-schedule').close()"
                >
                    Cancel
                </flux:button>

                <flux:button
                    variant="primary"
                    icon="calendar-days"
                    wire:click="saveSchedule"
                >
                    Schedule Maintenance
                </flux:button>

            </div>

        </div>

    </flux:modal>

</div>