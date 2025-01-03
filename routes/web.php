<?php

use App\Livewire\Talonador;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
});

Volt::route('/alcaldes', 'alcaldes');
Volt::route('/concejales', 'concejales');
Volt::route('/resultados', 'resultados');
Route::get('/talonador', Talonador::class);
