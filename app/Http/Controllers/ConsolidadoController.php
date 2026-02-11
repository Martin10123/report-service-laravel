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
            
            // Total área = suma de todas las filas (sillas + sillas_vacias + total_personas + total_ninos)
            $totalArea = $sillas + $sillasVacias + $totalPersonas + $totalNinos;

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
            // A4: exteriores (servidores, logistica, coffee) + iglekids (NO incluir container en jesusPlace)
            $servidoresArea = [
                'servidores' => $ext['servidores'] ?? $servidoresData['servidores'] ?? null,
                'consolidacion' => $servidoresData['consolidacion'] ?? null,
                'comunicaciones' => $servidoresData['comunicaciones'] ?? null,
                'logistica' => $ext['logistica'] ?? $servidoresData['logistica'] ?? null,
                'jesusPlace' => $servidoresData['jesusPlace'] ?? null, // Solo jesusPlace real, NO container
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

            // Cargar relaciones
            $servicio->load([
                'sede',
                'conteoA1',
                'conteoA2',
                'conteoA3',
                'conteoA4',
            ]);

            // Consolidar datos
            $datosConsolidados = $this->consolidarDatosPorArea($servicio);

            // Datos para la vista
            $datos = [
                'servicio' => $servicio,
                'fecha' => \Carbon\Carbon::parse($datosConsolidados['fecha'])->locale('es')->isoFormat('DD/MMM/YYYY'),
                'auditorio' => $datosConsolidados['auditorio'],
                'servidores' => $datosConsolidados['servidores'],
                'parqueadero' => $datosConsolidados['parqueadero'],
            ];

            // Generar PDF usando Browsershot o similar
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.consolidado', $datos);
            $pdf->setPaper('letter', 'portrait');
            
            // Nombre del archivo
            $fecha = $servicio->fecha->format('d-m-Y');
            $sede = str_replace(' ', '_', $servicio->sede->nombre ?? 'reporte');
            $filename = "consolidado_{$sede}_{$fecha}.pdf";

            return $pdf->download($filename);

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

            // Cargar relaciones
            $servicio->load([
                'sede',
                'conteoA1',
                'conteoA2',
                'conteoA3',
                'conteoA4',
            ]);

            // Consolidar datos
            $datosConsolidados = $this->consolidarDatosPorArea($servicio);

            // Generar el Excel
            $excel = $this->generateConsolidadoExcel($servicio, $datosConsolidados);

            // Nombre del archivo
            $fecha = $servicio->fecha->format('d-m-Y');
            $sede = str_replace(' ', '_', $servicio->sede->nombre ?? 'reporte');
            $filename = "consolidado_{$sede}_{$fecha}.xlsx";

            // Descargar el archivo
            return response()->streamDownload(function() use ($excel) {
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($excel);
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);

        } catch (Exception $e) {
            Log::error('Error al generar Excel consolidado: ' . $e->getMessage());
            return response()->json(['error' => 'Error al generar Excel'], 500);
        }
    }

    /**
     * Generate the Excel spreadsheet with consolidated data.
     */
    private function generateConsolidadoExcel(Service $servicio, array $datos): \PhpOffice\PhpSpreadsheet\Spreadsheet
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Nombre de la hoja
        $sheet->setTitle('consolidado');

        // Configuración general
        $sheet->getDefaultRowDimension()->setRowHeight(15);
        
        // Color de fondo azul para headers
        $azulHeader = 'FF00B0F0';
        $azulSeccion = 'FF00B0F0';
        
        $fila = 1;

        // ============================================
        // INFORMACIÓN DEL SERVICIO
        // ============================================
        // SEDE
        $sheet->setCellValue('A' . $fila, 'SEDE');
        $sheet->setCellValue('B' . $fila, $servicio->sede->nombre ?? '');
        $sheet->getStyle('A' . $fila)->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $azulHeader]],
            'font' => ['bold' => true, 'size' => 11],
        ]);
        $fila++;
        
        // SERVICIO
        $sheet->setCellValue('A' . $fila, 'SERVICIO');
        $sheet->setCellValue('B' . $fila, $servicio->numero_servicio ?? '');
        $sheet->getStyle('A' . $fila)->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $azulHeader]],
            'font' => ['bold' => true, 'size' => 11],
        ]);
        $fila++;
        
        // FECHA
        $sheet->setCellValue('A' . $fila, 'FECHA');
        $fechaFormateada = \Carbon\Carbon::parse($datos['fecha'])->locale('es')->isoFormat('DD/MMM/YYYY');
        $sheet->setCellValue('B' . $fila, $fechaFormateada);
        $sheet->getStyle('A' . $fila)->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $azulHeader]],
            'font' => ['bold' => true, 'size' => 11],
        ]);
        $fila++;
        
        // HORA
        $sheet->setCellValue('A' . $fila, 'HORA');
        $sheet->setCellValue('B' . $fila, $servicio->hora ?? '');
        $sheet->getStyle('A' . $fila)->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $azulHeader]],
            'font' => ['bold' => true, 'size' => 11],
        ]);
        
        $fila += 2; // Espacio

        // ============================================
        // AUDITORIO
        // ============================================
        $sheet->setCellValue('A' . $fila, 'AUDITORIO');
        $sheet->mergeCells('A' . $fila . ':F' . $fila);
        $sheet->getStyle('A' . $fila)->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $azulSeccion]],
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $fila++;

        // Headers AUDITORIO
        $sheet->setCellValue('A' . $fila, 'RESUMEN GENERAL');
        $sheet->setCellValue('B' . $fila, 'A1');
        $sheet->setCellValue('C' . $fila, 'A2');
        $sheet->setCellValue('D' . $fila, 'A3');
        $sheet->setCellValue('E' . $fila, 'A4');
        $sheet->setCellValue('F' . $fila, 'TOTALES');
        
        $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $azulHeader]],
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);
        $fila++;

        // Filas de datos AUDITORIO
        $auditorio = $datos['auditorio'];
        $filasAuditorio = [
            ['label' => 'Sillas del área.', 'key' => 'sillas'],
            ['label' => 'Silla vacías', 'key' => 'sillas_vacias'],
            ['label' => 'Total Personas', 'key' => 'total_personas'],
            ['label' => 'Total niños', 'key' => 'total_ninos'],
        ];

        foreach ($filasAuditorio as $row) {
            $sheet->setCellValue('A' . $fila, $row['label']);
            $sheet->setCellValue('B' . $fila, $auditorio['A1'][$row['key']] ?? 0);
            $sheet->setCellValue('C' . $fila, $auditorio['A2'][$row['key']] ?? 0);
            $sheet->setCellValue('D' . $fila, $auditorio['A3'][$row['key']] ?? 0);
            $sheet->setCellValue('E' . $fila, $auditorio['A4'][$row['key']] ?? 0);
            $sheet->setCellValue('F' . $fila, $auditorio['totales'][$row['key']] ?? 0);
            
            // Bordes
            $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ]);
            $fila++;
        }

        // Total AUDITORIO
        $sheet->setCellValue('A' . $fila, 'Total AUDITORIO');
        $sheet->setCellValue('B' . $fila, $auditorio['A1']['total_area'] ?? 0);
        $sheet->setCellValue('C' . $fila, $auditorio['A2']['total_area'] ?? 0);
        $sheet->setCellValue('D' . $fila, $auditorio['A3']['total_area'] ?? 0);
        $sheet->setCellValue('E' . $fila, $auditorio['A4']['total_area'] ?? 0);
        $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray([
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);
        $fila += 2; // Espacio

        // ============================================
        // SERVIDORES
        // ============================================
        $sheet->setCellValue('A' . $fila, 'SERVIDORES');
        $sheet->mergeCells('A' . $fila . ':F' . $fila);
        $sheet->getStyle('A' . $fila)->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $azulSeccion]],
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $fila++;

        // Headers SERVIDORES
        $sheet->setCellValue('A' . $fila, 'Areas');
        $sheet->setCellValue('B' . $fila, 'A1');
        $sheet->setCellValue('C' . $fila, 'A2');
        $sheet->setCellValue('D' . $fila, 'A3');
        $sheet->setCellValue('E' . $fila, 'A4');
        $sheet->setCellValue('F' . $fila, 'TOTAL');
        
        $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $azulHeader]],
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);
        $fila++;

        // Filas de datos SERVIDORES
        $servidores = $datos['servidores'];
        $filasServidores = [
            ['label' => 'Servidores:', 'key' => 'servidores'],
            ['label' => 'Consolidación:', 'key' => 'consolidacion'],
            ['label' => 'Comunicaciones:', 'key' => 'comunicaciones'],
            ['label' => 'Logistica:', 'key' => 'logistica'],
            ['label' => 'Jesus place:', 'key' => 'jesusPlace'],
            ['label' => 'Datafono:', 'key' => 'datafono'],
            ['label' => 'Coffee:', 'key' => 'coffee'],
            ['label' => 'Ministerial:', 'key' => 'ministerial'],
            ['label' => 'Alabanza:', 'key' => 'alabanza'],
            ['label' => 'VIP:', 'key' => 'vip'],
            ['label' => 'Iglekids:', 'key' => 'iglekids'],
        ];

        foreach ($filasServidores as $row) {
            $sheet->setCellValue('A' . $fila, $row['label']);
            $sheet->setCellValue('B' . $fila, $servidores['A1'][$row['key']] ?? '');
            $sheet->setCellValue('C' . $fila, $servidores['A2'][$row['key']] ?? '');
            $sheet->setCellValue('D' . $fila, $servidores['A3'][$row['key']] ?? '');
            $sheet->setCellValue('E' . $fila, $servidores['A4'][$row['key']] ?? '');
            $sheet->setCellValue('F' . $fila, $servidores['totales'][$row['key']] ?? '');
            
            // Bordes y fondo gris para celdas vacías opcionales
            $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ]);
            
            // Aplicar gris a celdas específicas según el diseño
            $grises = $this->obtenerCeldasGrisesServidores($row['key']);
            foreach ($grises as $col) {
                $sheet->getStyle($col . $fila)->applyFromArray([
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFC0C0C0']],
                ]);
            }
            
            $fila++;
        }

        // Total Servidores
        $sheet->setCellValue('A' . $fila, 'Total Servidores');
        $sheet->setCellValue('B' . $fila, $servidores['A1']['total'] ?? 0);
        $sheet->setCellValue('C' . $fila, $servidores['A2']['total'] ?? 0);
        $sheet->setCellValue('D' . $fila, $servidores['A3']['total'] ?? 0);
        $sheet->setCellValue('E' . $fila, $servidores['A4']['total'] ?? 0);
        $sheet->setCellValue('F' . $fila, $servidores['totales']['total'] ?? 0);
        $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray([
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);
        $fila += 2; // Espacio

        // ============================================
        // PARQUEADERO
        // ============================================
        $sheet->setCellValue('A' . $fila, 'PARQUEADERO');
        $sheet->mergeCells('A' . $fila . ':F' . $fila);
        $sheet->getStyle('A' . $fila)->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $azulSeccion]],
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $fila++;

        // VEHICULOS header
        $sheet->setCellValue('A' . $fila, 'VEHICULOS');
        $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $azulHeader]],
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);
        $fila++;

        // Filas de PARQUEADERO
        $parqueadero = $datos['parqueadero'];
        $filasParqueadero = [
            ['label' => 'Carros', 'key' => 'carros'],
            ['label' => 'Motos', 'key' => 'motos'],
            ['label' => 'Bicicletas', 'key' => 'bicicletas'],
        ];

        foreach ($filasParqueadero as $row) {
            $sheet->setCellValue('A' . $fila, $row['label']);
            // Parqueadero solo aparece en A4
            $sheet->mergeCells('B' . $fila . ':D' . $fila);
            $sheet->getStyle('B' . $fila . ':D' . $fila)->applyFromArray([
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFC0C0C0']],
            ]);
            $sheet->setCellValue('E' . $fila, $parqueadero['A4'][$row['key']] ?? 0);
            
            $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ]);
            $fila++;
        }

        // Total Vehiculos
        $sheet->setCellValue('A' . $fila, 'Total Vehiculos');
        $sheet->mergeCells('B' . $fila . ':D' . $fila);
        $sheet->getStyle('B' . $fila . ':D' . $fila)->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFC0C0C0']],
        ]);
        $sheet->setCellValue('E' . $fila, $parqueadero['A4']['total'] ?? 0);
        $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray([
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);

        // Ajustar anchos de columnas
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(12);

        return $spreadsheet;
    }

    /**
     * Obtener columnas que deben estar en gris para cada fila de servidores.
     */
    private function obtenerCeldasGrisesServidores(string $key): array
    {
        $mapeo = [
            'servidores' => [],
            'consolidacion' => ['B', 'E'], // Solo en A3
            'comunicaciones' => ['C', 'D', 'E'], // Solo en A1
            'logistica' => [],
            'jesusPlace' => ['B', 'D', 'E'], // Solo en A2
            'datafono' => ['B', 'D', 'E'], // Solo en A2
            'coffee' => ['B', 'C', 'D'], // Solo en A4
            'ministerial' => ['B', 'D', 'E'], // Solo en A2
            'alabanza' => ['C', 'D', 'E'], // Solo en A1
            'vip' => ['C', 'D', 'E'], // Solo en A1
            'iglekids' => ['B', 'C', 'D'], // Solo en A4
        ];

        return $mapeo[$key] ?? [];
    }
}
