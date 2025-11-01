<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('events.index');
});
Route::get('/events/list', [EventController::class, 'list'])->name('events.list');
Route::resource('events', EventController::class);
