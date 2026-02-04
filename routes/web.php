<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/configuraciones', function () {
        return Inertia::render('Settings/Index');
    })->name('configuraciones.index');

    Route::get('/servicios', function () {
        return Inertia::render('Servicios/Index');
    })->name('servicios.index');

    Route::post('/servicios', function () {
        // Placeholder: cuando haya backend, crear el servicio
        return redirect()->route('servicios.index');
    })->name('servicios.store');

    Route::get('/servicios/{id}', function ($id) {
        // Placeholder: cuando haya backend, mostrar detalles del servicio
        return Inertia::render('Servicios/Show', ['id' => $id]);
    })->name('servicios.show');
});
