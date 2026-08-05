<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');


    Route::livewire('/assets', 'assets.index')->name('assets.index');
    Route::livewire('/users', 'users.index')->name('users.index'); 

    Route::livewire('/asset-types', 'asset-types.index')->name('asset-types.index');

    Route::livewire('/inventory', 'inventory.index')->name('inventory.index');

});

require __DIR__.'/settings.php';
