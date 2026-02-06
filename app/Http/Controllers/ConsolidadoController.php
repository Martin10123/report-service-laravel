<?php

namespace App\Http\Controllers;

use App\Http\Traits\RequiresService;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ConsolidadoController extends Controller
{
    use RequiresService;

    /**
     * Display the consolidated report for a service.
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
                'conteoA1',
                'conteoA2',
                'conteoA3',
                'conteoA4',
            ]);

            // Consolidar datos por área
            $datosConsolidados = $this->consolidarDatosPorArea($servicio);

            return Inertia::render('Consolidado', [
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
            Log::error('Error al cargar consolidado: ' . $e->getMessage(), [
                'servicio_id' => $request->input('servicio_id'),
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('servicios.index')
                ->with('error', 'Error al cargar el consolidado. Por favor, intente de nuevo.');
        }
    }

    /**
     * Consolidar todos los datos por área (A1, A2, A3, A4).
     */
    private function consolidarDatosPorArea(Service $servicio): array
    {
        $conteoA1 = $servicio->conteoA1;
        $conteoA2 = $servicio->conteoA2;
        $conteoA3 = $servicio->conteoA3;
        $conteoA4 = $servicio->conteoA4;

        $conteos = [
            'A1' => $conteoA1,
            'A2' => $conteoA2,
            'A3' => $conteoA3,
            'A4' => $conteoA4,
        ];

        // Siempre mostrar todas las áreas
        $areasConDatos = ['A1', 'A2', 'A3', 'A4'];

        return [
            'fecha' => $servicio->fecha->format('Y-m-d'),
            'areas' => $areasConDatos,
            'auditorio' => $this->consolidarAuditorio($conteos),
            'servidores' => $this->consolidarServidoresPorArea($conteos),
            'parqueadero' => $this->consolidarParqueadero($conteos),
        ];
    }

    /**
     * Consolidar datos del auditorio por área.
     */
    private function consolidarAuditorio(array $conteos): array
    {
        $auditorio = [];
        $totales = [
            'sillas' => 0,
            'sillas_vacias' => 0,
            'total_personas' => 0,
            'total_ninos' => 0,
            'total_area' => 0,
        ];

        foreach ($conteos as $area => $conteo) {
            $areas = $conteo->areas ?? [];
            $sillasData = $areas['sillas'] ?? [];
            
            // Extraer datos del objeto sillas
            $sillas = $sillasData['totalSillas'] ?? 0;
            $sillasVacias = $sillasData['sillasVacias'] ?? 0;
            $totalPersonas = $sillasData['totalPersonas'] ?? 0;
            $totalNinos = $sillasData['totalNinos'] ?? 0;
            
            // Total área = total_personas (que es la suma de adultos + niños en el área)
            $totalArea = $totalPersonas;

            $auditorio[$area] = [
                'sillas' => $sillas,
                'sillas_vacias' => $sillasVacias,
                'total_personas' => $totalPersonas,
                'total_ninos' => $totalNinos,
                'total_area' => $totalArea,
            ];

            // Acumular totales
            $totales['sillas'] += $sillas;
            $totales['sillas_vacias'] += $sillasVacias;
            $totales['total_personas'] += $totalPersonas;
            $totales['total_ninos'] += $totalNinos;
            $totales['total_area'] += $totalArea;
        }

        $auditorio['totales'] = $totales;

        return $auditorio;
    }

    /**
     * Consolidar servidores por área.
     */
    private function consolidarServidoresPorArea(array $conteos): array
    {
        $servidores = [];
        $totales = [
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
            'total' => 0,
        ];

        foreach ($conteos as $area => $conteo) {
            $areas = $conteo->areas ?? [];
            
            // Datos de servidores (nested object en A1, A2, A3)
            $servidoresData = $areas['servidores'] ?? [];
            
            // Datos de exteriores (solo en A4)
            $ext = $areas['exteriores'] ?? [];
            
            // Datos de iglekids (en A4)
            $igk = $areas['iglekids'] ?? [];

            // Calcular total iglekids (suma de todo el personal)
            $totalIglekids = ($igk['coordinadoras'] ?? 0) +
                            ($igk['supervisoras'] ?? 0) +
                            ($igk['maestros'] ?? 0) +
                            ($igk['recrearte'] ?? 0) +
                            ($igk['regikids'] ?? 0) +
                            ($igk['logikids'] ?? 0) +
                            ($igk['saludKids'] ?? 0) +
                            ($igk['yoSoy'] ?? 0);

            // Calcular VIP (total de servidoras de pastora)
            $servidorasPastora = $areas['servidorasPastora'] ?? [];
            $totalVip = is_array($servidorasPastora) ? count($servidorasPastora) : 0;

            // Construir datos del área
            // A1: servidores, comunicaciones, logistica, alabanza + VIP (servidoras pastora)
            // A2: servidores, logistica, jesusPlace, datafono, ministerial
            // A3: servidores, consolidacion, logistica
            // A4: exteriores (servidores, logistica, coffee, container) + iglekids
            $servidoresArea = [
                'servidores' => $ext['servidores'] ?? $servidoresData['servidores'] ?? null,
                'consolidacion' => $servidoresData['consolidacion'] ?? null,
                'comunicaciones' => $servidoresData['comunicaciones'] ?? null,
                'logistica' => $ext['logistica'] ?? $servidoresData['logistica'] ?? null,
                'jesusPlace' => $ext['container'] ?? $servidoresData['jesusPlace'] ?? null,
                'datafono' => $servidoresData['datafono'] ?? null,
                'coffee' => $ext['coffee'] ?? null,
                'ministerial' => $servidoresData['ministerial'] ?? null,
                'alabanza' => $servidoresData['alabanza'] ?? null,
                'vip' => $totalVip > 0 ? $totalVip : null,
                'iglekids' => $totalIglekids > 0 ? $totalIglekids : null,
            ];

            // Calcular total del área (solo valores no nulos)
            $totalArea = 0;
            foreach ($servidoresArea as $key => $valor) {
                if ($valor !== null) {
                    $totalArea += $valor;
                }
            }
            $servidoresArea['total'] = $totalArea;

            $servidores[$area] = $servidoresArea;

            // Acumular totales
            foreach ($servidoresArea as $key => $valor) {
                if ($key !== 'total' && $valor !== null) {
                    $totales[$key] += $valor;
                }
            }
            $totales['total'] += $totalArea;
        }

        $servidores['totales'] = $totales;

        return $servidores;
    }

    /**
     * Consolidar parqueadero por área.
     */
    private function consolidarParqueadero(array $conteos): array
    {
        $parqueadero = [];
        $totales = [
            'carros' => 0,
            'motos' => 0,
            'bicicletas' => 0,
            'total' => 0,
        ];

        $hayDatos = false;

        foreach ($conteos as $area => $conteo) {
            $areas = $conteo->areas ?? [];
            $veh = $areas['vehiculos'] ?? [];

            $carros = $veh['carros'] ?? null;
            $motos = $veh['motos'] ?? null;
            $bicicletas = $veh['bicicletas'] ?? null;

            // Si hay al menos un dato, consideramos que hay información de parqueadero
            if ($carros !== null || $motos !== null || $bicicletas !== null) {
                $hayDatos = true;
            }

            $totalArea = ($carros ?? 0) + ($motos ?? 0) + ($bicicletas ?? 0);

            $parqueadero[$area] = [
                'carros' => $carros,
                'motos' => $motos,
                'bicicletas' => $bicicletas,
                'total' => $totalArea,
            ];

            // Acumular totales
            $totales['carros'] += $carros ?? 0;
            $totales['motos'] += $motos ?? 0;
            $totales['bicicletas'] += $bicicletas ?? 0;
            $totales['total'] += $totalArea;
        }

        // Siempre retornar datos del parqueadero (aunque estén en 0)
        if (!$hayDatos) {
            // Si no hay datos, agregar las áreas vacías
            foreach (['A1', 'A2', 'A3', 'A4'] as $area) {
                if (!isset($parqueadero[$area])) {
                    $parqueadero[$area] = [
                        'carros' => null,
                        'motos' => null,
                        'bicicletas' => null,
                        'total' => 0,
                    ];
                }
            }
        }

        $parqueadero['totales'] = $totales;

        return $parqueadero;
    }

    /**
     * Generate PDF report for the consolidated data.
     */
    public function exportPDF(Request $request)
    {
        try {
            $servicio = $this->requireService($request);
            
            if ($servicio instanceof RedirectResponse) {
                return $servicio;
            }

            // TODO: Implementar generación de PDF
            return response()->json([
                'message' => 'Generación de PDF en desarrollo',
                'servicio_id' => $servicio->id
            ]);

        } catch (Exception $e) {
            Log::error('Error al generar PDF consolidado: ' . $e->getMessage());
            return response()->json(['error' => 'Error al generar PDF'], 500);
        }
    }

    /**
     * Generate Excel report for the consolidated data.
     */
    public function exportExcel(Request $request)
    {
        try {
            $servicio = $this->requireService($request);
            
            if ($servicio instanceof RedirectResponse) {
                return $servicio;
            }

            // TODO: Implementar generación de Excel
            return response()->json([
                'message' => 'Generación de Excel en desarrollo',
                'servicio_id' => $servicio->id
            ]);

        } catch (Exception $e) {
            Log::error('Error al generar Excel consolidado: ' . $e->getMessage());
            return response()->json(['error' => 'Error al generar Excel'], 500);
        }
    }
}
