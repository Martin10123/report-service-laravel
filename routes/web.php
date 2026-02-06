<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return auth()->check() 
        ? redirect()->route('servicios.index') 
        : redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/configuraciones', function () {
        return Inertia::render('Settings/Index');
    })->name('configuraciones.index');

    Route::get('/roles-permisos', function () {
        return Inertia::render('RolesPermisos/Index');
    })->name('roles-permisos.index');

    Route::get('/auditorias', function () {
        return Inertia::render('Auditorias/Index');
    })->name('auditorias.index');

    // Sedes - Configuración
    Route::get('/sedes', [\App\Http\Controllers\SedeController::class, 'index'])
        ->name('sedes.index');
    Route::put('/sedes/{sede}', [\App\Http\Controllers\SedeController::class, 'update'])
        ->name('sedes.update');
    Route::post('/sedes/{sede}/toggle-active', [\App\Http\Controllers\SedeController::class, 'toggleActive'])
        ->name('sedes.toggle-active');

    // Sede - Cambio de sede (solo super usuarios)
    Route::get('/sede/switch/{slug}', [\App\Http\Controllers\SedeController::class, 'switch'])
        ->name('sede.switch');
    Route::get('/sede/clear', [\App\Http\Controllers\SedeController::class, 'clear'])
        ->name('sede.clear');

    // Servicios - Resource Controller
    Route::resource('servicios', \App\Http\Controllers\ServiceController::class);
    Route::post('/servicios/{id}/cambiar-estado', [\App\Http\Controllers\ServiceController::class, 'cambiarEstado'])
        ->name('servicios.cambiar-estado');

    // Primer Conteo
    Route::get('/primer-conteo', [\App\Http\Controllers\PrimerConteoController::class, 'index'])
        ->name('primer-conteo');
    Route::post('/primer-conteo', [\App\Http\Controllers\PrimerConteoController::class, 'store'])
        ->name('primer-conteo.store');
    Route::get('/primer-conteo/{servicio_id}', [\App\Http\Controllers\PrimerConteoController::class, 'show'])
        ->name('primer-conteo.show');
    Route::delete('/primer-conteo/{id}', [\App\Http\Controllers\PrimerConteoController::class, 'destroy'])
        ->name('primer-conteo.destroy');

    // Conteo A1
    Route::get('/conteo-a1', [\App\Http\Controllers\ConteoA1Controller::class, 'index'])
        ->name('conteo-a1');
    Route::post('/conteo-a1', [\App\Http\Controllers\ConteoA1Controller::class, 'store'])
        ->name('conteo-a1.store');
    Route::get('/conteo-a1/{servicio_id}', [\App\Http\Controllers\ConteoA1Controller::class, 'show'])
        ->name('conteo-a1.show');
    Route::delete('/conteo-a1/{id}', [\App\Http\Controllers\ConteoA1Controller::class, 'destroy'])
        ->name('conteo-a1.destroy');

    // Conteo A2
    Route::get('/conteo-a2', [\App\Http\Controllers\ConteoA2Controller::class, 'index'])
        ->name('conteo-a2');
    Route::post('/conteo-a2', [\App\Http\Controllers\ConteoA2Controller::class, 'store'])
        ->name('conteo-a2.store');
    Route::get('/conteo-a2/{servicio_id}', [\App\Http\Controllers\ConteoA2Controller::class, 'show'])
        ->name('conteo-a2.show');
    Route::delete('/conteo-a2/{id}', [\App\Http\Controllers\ConteoA2Controller::class, 'destroy'])
        ->name('conteo-a2.destroy');

    Route::get('/conteo-a3', function () {
        $servicioId = request('servicio_id');
        
        if (!$servicioId) {
            return redirect()
                ->route('servicios.index')
                ->with('warning', 'Por favor seleccione un servicio primero.');
        }
        
        return Inertia::render('Areas/A3', ['servicio_id' => $servicioId]);
    })->name('conteo-a3');

    Route::post('/conteo-a3', function () {
        return redirect()->back();
    })->name('areas.a3.store');

    Route::get('/conteo-a4', function () {
        $servicioId = request('servicio_id');
        
        if (!$servicioId) {
            return redirect()
                ->route('servicios.index')
                ->with('warning', 'Por favor seleccione un servicio primero.');
        }
        
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
