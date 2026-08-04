@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand :name="config('app.name', 'ITEMS')" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md">
            <img src="{{ asset('images/sideBarLogo.png') }}" class="h-9 w-12" alt="{{ config('app.name', 'ITEMS') }}" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand :name="config('app.name', 'ITEMS')" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md">
            <img src="{{ asset('images/sideBarLogo.png') }}" >
        </x-slot>
    </flux:brand>
@endif
