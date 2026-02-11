<?php

namespace App\Http\Traits;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;

trait RequiresService
{
    /**
     * Verificar que existe un servicio_id válido en la petición.
     * Si no existe, redirige a la página de servicios con un mensaje.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $paramName El nombre del parámetro (por defecto 'servicio_id')
     * @return Service|RedirectResponse Retorna el servicio si existe, o una redirección
     */
    protected function requireService($request, string $paramName = 'servicio_id')
    {
        $servicioId = $request->input($paramName);

        \Illuminate\Support\Facades\Log::info('RequiresService::requireService - Verificando servicio', [
            'param_name' => $paramName,
            'servicio_id' => $servicioId,
            'all_input' => $request->all(),
            'query_params' => $request->query(),
            'route_params' => $request->route() ? $request->route()->parameters() : [],
        ]);

        if (!$servicioId) {
            \Illuminate\Support\Facades\Log::warning('RequiresService::requireService - No se encontró servicio_id');
            return redirect()
                ->route('servicios.index')
                ->with('warning', 'Por favor seleccione un servicio primero.');
        }

        try {
            $servicio = Service::with('sede')->findOrFail($servicioId);
            \Illuminate\Support\Facades\Log::info('RequiresService::requireService - Servicio encontrado', [
                'servicio_id' => $servicio->id,
                'servicio_numero' => $servicio->numero_servicio,
            ]);
            return $servicio;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Illuminate\Support\Facades\Log::error('RequiresService::requireService - Servicio no encontrado en BD', [
                'servicio_id' => $servicioId,
            ]);
            return redirect()
                ->route('servicios.index')
                ->with('error', 'El servicio seleccionado no existe.');
        }
    }

    /**
     * Verificar si hay un servicio válido, pero sin lanzar excepción.
     * Útil cuando quieres manejar el caso de forma diferente.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $paramName
     * @return Service|null
     */
    protected function findService($request, string $paramName = 'servicio_id'): ?Service
    {
        $servicioId = $request->input($paramName);

        if (!$servicioId) {
            return null;
        }

        try {
            return Service::with('sede')->find($servicioId);
        } catch (\Exception $e) {
            return null;
        }
    }
}
