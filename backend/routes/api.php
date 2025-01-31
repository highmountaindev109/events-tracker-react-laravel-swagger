<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;

Route::post('/authenticate', [EventController::class, 'getToken']);

Route::post('/events', [EventController::class, 'createEvent']);

Route::get('/events', [EventController::class, 'getAllEvents']);

Route::get('/events/{id}', [EventController::class, 'getEvent']);
