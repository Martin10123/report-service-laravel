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

    Route::put('/servicios/{id}', function ($id) {
        // Placeholder: cuando haya backend, actualizar el servicio
        return redirect()->route('servicios.index');
    })->name('servicios.update');

    Route::delete('/servicios/{id}', function ($id) {
        // Placeholder: cuando haya backend, eliminar el servicio
        return redirect()->route('servicios.index');
    })->name('servicios.destroy');

    Route::get('/servicios/{id}', function ($id) {
        // Placeholder: cuando haya backend, mostrar detalles del servicio
        return Inertia::render('Servicios/Show', ['id' => $id]);
    })->name('servicios.show');

    Route::get('/servicios/{id}', function ($id) {
        // Placeholder: cuando haya backend, mostrar detalles del servicio
        return Inertia::render('Servicios/Show', ['id' => $id]);
    })->name('servicios.show');

    Route::get('/primer-conteo', function () {
        $servicioId = request('servicio_id');
        return Inertia::render('Servicios/PrimerConteo', ['servicio_id' => $servicioId]);
    })->name('primer-conteo');

    Route::post('/primer-conteo', function () {
        return redirect()->back();
    })->name('primer-conteo.store');

    Route::get('/conteo-a1', function () {
        $servicioId = request('servicio_id');
        return Inertia::render('Areas/A1', ['servicio_id' => $servicioId]);
    })->name('conteo-a1');

    Route::post('/conteo-a1', function () {
        return redirect()->back();
    })->name('areas.a1.store');

    Route::get('/conteo-a2', function () {
        $servicioId = request('servicio_id');
        return Inertia::render('Areas/A2', ['servicio_id' => $servicioId]);
    })->name('conteo-a2');

    Route::post('/conteo-a2', function () {
        return redirect()->back();
    })->name('areas.a2.store');

    Route::get('/conteo-a3', function () {
        $servicioId = request('servicio_id');
        return Inertia::render('Areas/A3', ['servicio_id' => $servicioId]);
    })->name('conteo-a3');

    Route::post('/conteo-a3', function () {
        return redirect()->back();
    })->name('areas.a3.store');

    Route::get('/conteo-a4', function () {
        $servicioId = request('servicio_id');
        return Inertia::render('Areas/A4', ['servicio_id' => $servicioId]);
    })->name('conteo-a4');

    Route::post('/conteo-a4', function () {
        return redirect()->back();
    })->name('areas.a4.store');

    Route::get('/conteo-sobres', function () {
        $servicioId = request('servicio_id');
        return Inertia::render('ConteoDeSobres', ['servicio_id' => $servicioId]);
    })->name('conteo-sobres');

    Route::post('/conteo-sobres', function () {
        return redirect()->back();
    })->name('conteo-sobres.store');

    Route::get('/informe-final', function () {
        return Inertia::render('InformeFinal');
    })->name('informe-final');

    Route::get('/informe-final/pdf', function () {
        // Placeholder: generar PDF
        return response()->json(['message' => 'Generando PDF...']);
    })->name('informe-final.pdf');

    Route::get('/informe-final/excel', function () {
        // Placeholder: generar Excel
        return response()->json(['message' => 'Generando Excel...']);
    })->name('informe-final.excel');
});
