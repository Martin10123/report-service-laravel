<?php

namespace App\Http\Controllers;

use App\Helpers\InformeFinalExcelHelper;
use App\Http\Traits\RequiresService;
use App\Models\Service;
use Barryvdh\DomPDF\Facade\Pdf;
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
                Log::warning('InformeFinalController::index - Redirigiendo por falta de servicio');
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

        // 1. Asistencia en Auditorio (de los conteos de áreas)
        // En Sillas = Total Personas de A1 + A2
        $enSillas = 0;
        if ($conteoA1) {
            $areasA1 = $conteoA1->areas ?? [];
            $sillasA1 = $areasA1['sillas'] ?? [];
            $enSillas += $sillasA1['totalPersonas'] ?? 0;
        }
        if ($conteoA2) {
            $areasA2 = $conteoA2->areas ?? [];
            $sillasA2 = $areasA2['sillas'] ?? [];
            $enSillas += $sillasA2['totalPersonas'] ?? 0;
        }

        // En Gradas = Total Personas de A3
        $enGradas = 0;
        if ($conteoA3) {
            $areasA3 = $conteoA3->areas ?? [];
            $sillasA3 = $areasA3['sillas'] ?? [];
            $enGradas = $sillasA3['totalPersonas'] ?? 0;
        }

        // Niños Auditorio = Total niños de A1 + A2 + A3
        $ninosAuditorio = 0;
        if ($conteoA1) {
            $areasA1 = $conteoA1->areas ?? [];
            $sillasA1 = $areasA1['sillas'] ?? [];
            $ninosAuditorio += $sillasA1['totalNinos'] ?? 0;
        }
        if ($conteoA2) {
            $areasA2 = $conteoA2->areas ?? [];
            $sillasA2 = $areasA2['sillas'] ?? [];
            $ninosAuditorio += $sillasA2['totalNinos'] ?? 0;
        }
        if ($conteoA3) {
            $areasA3 = $conteoA3->areas ?? [];
            $sillasA3 = $areasA3['sillas'] ?? [];
            $ninosAuditorio += $sillasA3['totalNinos'] ?? 0;
        }

        // Niños Iglekids = Total niños del campo 'ninos' de iglekids de todas las áreas
        $ninosIglekids = 0;
        foreach ([$conteoA1, $conteoA2, $conteoA3, $conteoA4] as $conteo) {
            if ($conteo) {
                $areas = $conteo->areas ?? [];
                $iglekids = $areas['iglekids'] ?? [];
                $ninosIglekids += $iglekids['ninos'] ?? 0;
            }
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
            $servidores['datafono'] += $areas['datafono'] ?? 0;
        }

        // Áreas A1, A2, A3, A4
        foreach ([$conteoA1, $conteoA2, $conteoA3, $conteoA4] as $conteo) {
            if ($conteo) {
                $areas = $conteo->areas ?? [];
                
                // Servidores internos (A1, A2, A3)
                $servidoresData = $areas['servidores'] ?? [];
                $servidores['servidores'] += $servidoresData['servidores'] ?? 0;
                $servidores['consolidacion'] += $servidoresData['consolidacion'] ?? 0;
                $servidores['comunicaciones'] += $servidoresData['comunicaciones'] ?? 0;
                $servidores['logistica'] += $servidoresData['logistica'] ?? 0;
                $servidores['jesusPlace'] += $servidoresData['jesusPlace'] ?? 0;
                $servidores['datafono'] += $servidoresData['datafono'] ?? 0;
                $servidores['ministerial'] += $servidoresData['ministerial'] ?? 0;
                $servidores['alabanza'] += $servidoresData['alabanza'] ?? 0;
                
                // VIP = contar servidoras pastora (array)
                $servidorasPastora = $areas['servidorasPastora'] ?? [];
                $servidores['vip'] += is_array($servidorasPastora) ? count($servidorasPastora) : 0;
                
                // Exteriores (A4)
                $ext = $areas['exteriores'] ?? [];
                $servidores['servidores'] += $ext['servidores'] ?? 0;
                $servidores['logistica'] += $ext['logistica'] ?? 0;
                $servidores['coffee'] += $ext['coffee'] ?? 0;
                // Container NO se suma a jesusPlace

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

    /**
     * Generate Excel report for the informe final.
     */
    public function exportExcel(Request $request)
    {
        try {
            $servicio = $this->requireService($request);
            
            if ($servicio instanceof RedirectResponse) {
                return $servicio;
            }

            // Cargar relaciones
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

            // Generar el Excel usando el helper
            $excel = InformeFinalExcelHelper::generate($servicio, $datosConsolidados);

            // Nombre del archivo con timestamp
            $fecha = $servicio->fecha->format('d-m-Y');
            $sede = str_replace(' ', '_', $servicio->sede->nombre ?? 'reporte');
            $timestamp = now()->format('His'); // Hora, Minuto, Segundo
            $filename = "informe_final_{$sede}_{$fecha}_{$timestamp}.xlsx";

            // Descargar el archivo con headers anti-caché
            return response()->streamDownload(function() use ($excel) {
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($excel);
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);

        } catch (Exception $e) {
            Log::error('Error al generar Excel informe final: ' . $e->getMessage());
            return response()->json(['error' => 'Error al generar Excel'], 500);
        }
    }

    /**
     * Generate PDF report for the informe final.
     */
    public function exportPDF(Request $request)
    {
        try {
            $servicio = $this->requireService($request);
            
            if ($servicio instanceof RedirectResponse) {
                return $servicio;
            }

            // Cargar relaciones
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

            // Datos para la vista - asegurar que son valores simples
            $datos = [
                'servicio' => $servicio,
                'fecha' => \Carbon\Carbon::parse($servicio->fecha)->locale('es')->isoFormat('DD/MMM/YYYY'),
                'sedeNombre' => $servicio->sede ? $servicio->sede->nombre : 'N/A',
                'diaSemana' => $servicio->dia_semana ?? '',
                'hora' => $servicio->hora ?? '',
                'primerConteo' => $servicio->primerConteo,
                'conteoA1' => $servicio->conteoA1,
                'conteoA2' => $servicio->conteoA2,
                'conteoA3' => $servicio->conteoA3,
                'conteoA4' => $servicio->conteoA4,
                'conteoSobres' => $servicio->conteoSobres,
                'datosConsolidados' => $datosConsolidados,
            ];

            // Generar PDF
            $pdf = Pdf::loadView('pdf.informe-final', $datos);
            $pdf->setPaper('letter', 'portrait');
            
            // Nombre del archivo
            $fecha = $servicio->fecha->format('d-m-Y');
            $sedeNombre = $servicio->sede ? $servicio->sede->nombre : 'reporte';
            $sede = str_replace(' ', '_', $sedeNombre);
            $filename = "informe_final_{$sede}_{$fecha}.pdf";

            return $pdf->download($filename);

        } catch (Exception $e) {
            Log::error('Error al generar PDF informe final: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            return response()->json(['error' => 'Error al generar PDF'], 500);
        }
    }
}
