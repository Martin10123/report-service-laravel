<?php

namespace App\Http\Controllers;

use App\Http\Traits\RequiresService;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;

class InformeFinalController extends Controller
{
    use RequiresService;

    /**
     * Display the informe final for a service.
     */
    public function index(Request $request)
    {
        try {
            // Usar el trait para obtener el servicio o redirigir
            $servicio = $this->requireService($request);
            
            // Si es una redirección, retornarla
            if ($servicio instanceof RedirectResponse) {
                return $servicio;
            }

            // Cargar todas las relaciones necesarias
            $servicio->load([
                'sede',
                'primerConteo',
                'conteoA1',
                'conteoA2',
                'conteoA3',
                'conteoA4',
                'conteoSobres'
            ]);

            // Consolidar datos
            $datosConsolidados = $this->consolidarDatos($servicio);

            return Inertia::render('InformeFinal', [
                'servicio' => [
                    'id' => $servicio->id,
                    'sede' => $servicio->sede->nombre ?? $servicio->sede,
                    'fecha' => $servicio->fecha->format('Y-m-d'),
                    'numero_servicio' => $servicio->numero_servicio,
                    'dia_semana' => strtoupper($servicio->dia_semana ?? $servicio->fecha->locale('es')->isoFormat('dddd')),
                    'hora' => $servicio->hora ?? '',
                ],
                'data' => $datosConsolidados,
                'servicio_id' => $servicio->id,
            ]);

        } catch (Exception $e) {
            Log::error('Error al cargar informe final: ' . $e->getMessage(), [
                'servicio_id' => $request->input('servicio_id'),
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            // Retornar con datos vacíos en lugar de solo error
            return redirect()->route('servicios.index')
                ->with('error', 'Error al cargar el informe final. Por favor, intente de nuevo.');
        }
    }

    /**
     * Consolidar todos los datos del servicio.
     */
    private function consolidarDatos(Service $servicio): array
    {
        $primerConteo = $servicio->primerConteo;
        $conteoA1 = $servicio->conteoA1;
        $conteoA2 = $servicio->conteoA2;
        $conteoA3 = $servicio->conteoA3;
        $conteoA4 = $servicio->conteoA4;
        $conteoSobres = $servicio->conteoSobres;

        // 1. Asistencia en Auditorio (del Primer Conteo)
        $primerConteoAreas = $primerConteo?->areas ?? [];
        $enSillas = $primerConteoAreas['sillas'] ?? 0;
        $enGradas = $primerConteoAreas['gradas'] ?? 0;
        $ninosAuditorio = $primerConteoAreas['ninos_auditorio'] ?? 0;
        $ninosIglekids = 0;

        // Obtener niños de Iglekids de las diferentes áreas
        if ($conteoA1) {
            $areasA1 = $conteoA1->areas ?? [];
            $iglekidsA1 = $areasA1['iglekids'] ?? [];
            $ninosIglekids += $iglekidsA1['ninos'] ?? 0;
        }
        if ($conteoA2) {
            $areasA2 = $conteoA2->areas ?? [];
            $iglekidsA2 = $areasA2['iglekids'] ?? [];
            $ninosIglekids += $iglekidsA2['ninos'] ?? 0;
        }
        if ($conteoA3) {
            $areasA3 = $conteoA3->areas ?? [];
            $iglekidsA3 = $areasA3['iglekids'] ?? [];
            $ninosIglekids += $iglekidsA3['ninos'] ?? 0;
        }
        if ($conteoA4) {
            $areasA4 = $conteoA4->areas ?? [];
            $iglekidsA4 = $areasA4['iglekids'] ?? [];
            $ninosIglekids += $iglekidsA4['ninos'] ?? 0;
        }

        $totalAuditorio = $enSillas + $enGradas + $ninosAuditorio + $ninosIglekids;

        // 2. Consolidar Servidores de todas las áreas
        $servidores = $this->consolidarServidores($primerConteo, $conteoA1, $conteoA2, $conteoA3, $conteoA4);
        $totalServidores = array_sum($servidores);

        // 3. Total de personas en la iglesia
        $totalPersonasIglesia = $totalAuditorio + $totalServidores;

        // 4. Consolidar Vehículos
        $vehiculos = $this->consolidarVehiculos($conteoA1, $conteoA2, $conteoA3, $conteoA4);

        // 5. Consolidar Ofrendas y Sobres
        $ofrendas = $this->consolidarOfrendas($conteoSobres);

        return [
            'asistencia' => [
                'enSillas' => $enSillas,
                'enGradas' => $enGradas,
                'ninosAuditorio' => $ninosAuditorio,
                'ninosIglekids' => $ninosIglekids,
                'totalAuditorio' => $totalAuditorio,
                'servidores' => $servidores,
                'totalServidores' => $totalServidores,
                'totalPersonasIglesia' => $totalPersonasIglesia,
            ],
            'vehiculos' => $vehiculos,
            'ofrendas' => $ofrendas,
        ];
    }

    /**
     * Consolidar servidores de todas las áreas.
     */
    private function consolidarServidores($primerConteo, $conteoA1, $conteoA2, $conteoA3, $conteoA4): array
    {
        $servidores = [
            'servidores' => 0,
            'consolidacion' => 0,
            'comunicaciones' => 0,
            'logistica' => 0,
            'jesusPlace' => 0,
            'datafono' => 0,
            'coffee' => 0,
            'ministerial' => 0,
            'alabanza' => 0,
            'vip' => 0,
            'iglekids' => 0,
        ];

        // Primer Conteo
        if ($primerConteo) {
            $areas = $primerConteo->areas ?? [];
            $servidores['servidores'] += $areas['servidores'] ?? 0;
            $servidores['consolidacion'] += $areas['consolidacion'] ?? 0;
            $servidores['comunicaciones'] += $areas['comunicaciones'] ?? 0;
            $servidores['ministerial'] += $areas['ministerial'] ?? 0;
            $servidores['alabanza'] += $areas['alabanza'] ?? 0;
            $servidores['vip'] += $areas['vip'] ?? 0;
            $servidores['datafono'] += $areas['datafono'] ?? 0;
        }

        // Áreas A1, A2, A3, A4 - Exteriores
        foreach ([$conteoA1, $conteoA2, $conteoA3, $conteoA4] as $conteo) {
            if ($conteo) {
                $areas = $conteo->areas ?? [];
                $ext = $areas['exteriores'] ?? [];
                $servidores['servidores'] += $ext['servidores'] ?? 0;
                $servidores['logistica'] += $ext['logistica'] ?? 0;
                $servidores['coffee'] += $ext['coffee'] ?? 0;
                $servidores['jesusPlace'] += $ext['container'] ?? 0; // Container se mapea a Jesus Place

                // Iglekids personal
                $igk = $areas['iglekids'] ?? [];
                $servidores['iglekids'] += ($igk['coordinadoras'] ?? 0) +
                                          ($igk['supervisoras'] ?? 0) +
                                          ($igk['maestros'] ?? 0) +
                                          ($igk['recrearte'] ?? 0) +
                                          ($igk['regikids'] ?? 0) +
                                          ($igk['logikids'] ?? 0) +
                                          ($igk['saludKids'] ?? 0) +
                                          ($igk['yoSoy'] ?? 0);
            }
        }

        return $servidores;
    }

    /**
     * Consolidar vehículos de todas las áreas.
     */
    private function consolidarVehiculos($conteoA1, $conteoA2, $conteoA3, $conteoA4): array
    {
        $carros = 0;
        $motos = 0;
        $bicicletas = 0;

        foreach ([$conteoA1, $conteoA2, $conteoA3, $conteoA4] as $conteo) {
            if ($conteo) {
                $areas = $conteo->areas ?? [];
                $veh = $areas['vehiculos'] ?? [];
                $carros += $veh['carros'] ?? 0;
                $motos += $veh['motos'] ?? 0;
                $bicicletas += $veh['bicicletas'] ?? 0;
            }
        }

        return [
            'carros' => $carros,
            'motos' => $motos,
            'bicicletas' => $bicicletas,
            'total' => $carros + $motos + $bicicletas,
        ];
    }

    /**
     * Consolidar ofrendas y sobres.
     */
    private function consolidarOfrendas($conteoSobres): array
    {
        $canastas = 0;
        $ofrendas = ['inicial' => 0, 'recibidos' => 0, 'entregados' => 0];
        $protemplo = ['inicial' => 0, 'recibidos' => 0, 'entregados' => 0];
        $iglekids = ['inicial' => 0, 'recibidos' => 0, 'entregados' => 0];

        if ($conteoSobres) {
            $canastas = $conteoSobres->canastas['entregadas'] ?? 0;
            $ofrendas = $conteoSobres->ofrendas ?? $ofrendas;
            $protemplo = $conteoSobres->protemplo ?? $protemplo;
            $iglekids = $conteoSobres->iglekids ?? $iglekids;
        }

        return [
            'canastas' => $canastas,
            'sobresOfrendas' => [
                'inicial' => $ofrendas['inicial'] ?? 0,
                'recibidos' => $ofrendas['recibidos'] ?? 0,
                'total' => ($ofrendas['inicial'] ?? 0) + ($ofrendas['recibidos'] ?? 0),
                'entregados' => $ofrendas['entregados'] ?? 0,
                'final' => (($ofrendas['inicial'] ?? 0) + ($ofrendas['recibidos'] ?? 0)) - ($ofrendas['entregados'] ?? 0),
            ],
            'sobresProtemplo' => [
                'inicial' => $protemplo['inicial'] ?? 0,
                'recibidos' => $protemplo['recibidos'] ?? 0,
                'total' => ($protemplo['inicial'] ?? 0) + ($protemplo['recibidos'] ?? 0),
                'entregados' => $protemplo['entregados'] ?? 0,
                'final' => (($protemplo['inicial'] ?? 0) + ($protemplo['recibidos'] ?? 0)) - ($protemplo['entregados'] ?? 0),
            ],
            'sobresIglekids' => [
                'inicial' => $iglekids['inicial'] ?? 0,
                'recibidos' => $iglekids['recibidos'] ?? 0,
                'total' => ($iglekids['inicial'] ?? 0) + ($iglekids['recibidos'] ?? 0),
                'entregados' => $iglekids['entregados'] ?? 0,
                'final' => (($iglekids['inicial'] ?? 0) + ($iglekids['recibidos'] ?? 0)) - ($iglekids['entregados'] ?? 0),
            ],
        ];
    }
}
