<?php

namespace App\Http\Middleware;

use App\Models\Service;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleServiceSession
{
    public function handle(Request $request, Closure $next): Response
    {
        // Limpiar servicio si se solicita
        if ($request->boolean('clear_servicio')) {
            session()->forget('servicio_actual_id');
            return $next($request);
        }

        // Establecer servicio actual si viene en la URL
        if ($request->has('servicio_id')) {
            $servicioId = $request->integer('servicio_id');
            
            // Validar que el ID sea válido y que el servicio exista
            if ($servicioId > 0 && Service::where('id', $servicioId)->exists()) {
                session(['servicio_actual_id' => $servicioId]);
            }
        }

        return $next($request);
    }
}