<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapfileController;
use App\Http\Controllers\MapController;


Route::get('/geojson', [MapController::class, 'show'])->name('geojson.show');
Route::get('/map', [MapController::class, 'view'])->name('peta');


