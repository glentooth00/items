<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');


    Route::livewire('/assets', 'assets.index')->name('assets.index');
    Route::livewire('/users', 'users.index')->name('users.index'); 

    Route::livewire('/equipments', 'equipments.index')->name('equipments.index');
    Route::livewire('/asset-types', 'asset-types.index')->name('asset-types.index');

    Route::livewire('/inventory', 'inventory.index')->name('inventory.index');


    //menu items for asset types
    Route::livewire('/laptop', 'laptop.index')->name('laptop.index');
    Route::livewire('/wifi', 'wifi.index')->name('wifi.index');
    Route::livewire('/desktop', 'ap.index')->name('ap.index');
    Route::livewire('/camera', 'camera.index')->name('camera.index');
    Route::livewire('/network-switch', 'network-switch.index')->name('network-switch.index');
    Route::livewire('/ccvr', 'ccvr.index')->name('ccvr.index');
    Route::livewire('/external', 'external.index')->name('external.index');
    Route::livewire('/ups', 'ups.index')->name('ups.index');
    Route::livewire('/router', 'router.index')->name('router.index');
    Route::livewire('/printer', 'printer.index')->name('printer.index');
    Route::livewire('/printerDM', 'printerDM.index')->name('printerDM.index');
    Route::livewire('/system-unit', 'system-unit.index')->name('system-unit.index');
    Route::livewire('/server', 'server.index')->name('server.index');
    Route::livewire('/laser-printer', 'laser-printer.index')->name('laser-printer.index');
});

require __DIR__.'/settings.php';
