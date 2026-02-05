<?php

namespace App\Http\Middleware;

use App\Models\Sede;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // Obtener la sede seleccionada (desde sesión o desde servicio actual)
        $sedeActualId = session('sede_actual_id');
        $sedeActual = null;

        if ($sedeActualId) {
            $sedeActual = Sede::find($sedeActualId);
        }

        // Si hay un servicio seleccionado en sesión, usar su sede
        $servicioActualId = session('servicio_actual_id');
        if ($servicioActualId) {
            $servicio = \App\Models\Service::with('sede')->find($servicioActualId);
            if ($servicio && $servicio->sede) {
                $sedeActual = $servicio->sede;
            }
        }

        return [
            ...parent::share($request),
            'sedes' => fn () => Sede::activas()->orderBy('nombre')->get(),
            'sedeActual' => fn () => $sedeActual,
            'opcionesDisponibles' => fn () => $sedeActual ? $sedeActual->getOpcionesDisponibles() : [],
        ];
    }
}
