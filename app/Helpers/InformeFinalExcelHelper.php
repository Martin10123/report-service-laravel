<?php

namespace App\Helpers;

use App\Models\Service;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class InformeFinalExcelHelper
{
    /**
     * Generate the Excel spreadsheet with informe final data.
     */
    public static function generate(Service $servicio, array $datosConsolidados): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        
        // Colores para contenido
        $azul = 'FF00B0F0';
        $amarillo = 'FFFFFF00';
        $naranja = 'FFFFC000';
        $rojo = 'FFFF0000';
        $verde = 'FF00FF00';
        $verdeClaro = 'FF92D050';

        // ============================================
        // HOJA 1: INFORMACIÓN GENERAL Y PRIMER CONTEO - ROJO
        // ============================================
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Información General');
        $sheet->getTabColor()->setRGB('FF0000'); // ROJO
        $sheet->getDefaultRowDimension()->setRowHeight(18);
        
        $fila = 1;
        self::agregarInfoServicio($sheet, $servicio, $fila, $azul);
        $fila += 2;
        
        if ($servicio->primerConteo) {
            $fila = self::agregarPrimerConteo($sheet, $servicio, $datosConsolidados, $fila, $amarillo, $azul);
        }
        
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ============================================
        // HOJA 2: ÁREA A1 - VERDE
        // ============================================
        if ($servicio->conteoA1) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Área A1');
            $sheet->getTabColor()->setRGB('00FF00'); // VERDE
            $sheet->getDefaultRowDimension()->setRowHeight(18);
            $fila = 1;
            self::agregarDetalleArea($sheet, $servicio->conteoA1, 'A1', $servicio->fecha, $fila, $azul, $rojo, $verde, $amarillo);
            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        // ============================================
        // HOJA 3: ÁREA A2 - VERDE
        // ============================================
        if ($servicio->conteoA2) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Área A2');
            $sheet->getTabColor()->setRGB('00FF00'); // VERDE
            $sheet->getDefaultRowDimension()->setRowHeight(18);
            $fila = 1;
            self::agregarDetalleArea($sheet, $servicio->conteoA2, 'A2', $servicio->fecha, $fila, $azul, $rojo, $verde, $amarillo);
            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        // ============================================
        // HOJA 4: ÁREA A3 - VERDE
        // ============================================
        if ($servicio->conteoA3) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Área A3');
            $sheet->getTabColor()->setRGB('00FF00'); // VERDE
            $sheet->getDefaultRowDimension()->setRowHeight(18);
            $fila = 1;
            self::agregarDetalleArea($sheet, $servicio->conteoA3, 'A3', $servicio->fecha, $fila, $azul, $rojo, $verde, $amarillo);
            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        // ============================================
        // HOJA 5: ÁREA A4 - VERDE
        // ============================================
        if ($servicio->conteoA4) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Área A4');
            $sheet->getTabColor()->setRGB('00FF00'); // VERDE
            $sheet->getDefaultRowDimension()->setRowHeight(18);
            $fila = 1;
            self::agregarDetalleAreaA4($sheet, $servicio->conteoA4, $servicio->fecha, $fila, $azul, $verde, $verdeClaro, $naranja);
            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        // ============================================
        // HOJA 6: INVENTARIO DE SOBRES - VERDE
        // ============================================
        if ($servicio->conteoSobres) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Inventario Sobres');
            $sheet->getTabColor()->setRGB('00FF00'); // VERDE
            $sheet->getDefaultRowDimension()->setRowHeight(18);
            $fila = 1;
            self::agregarInventarioSobres($sheet, $servicio->conteoSobres, $fila, $azul, $naranja, $verde);
            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        // ============================================
        // HOJA 7: CONSOLIDADO - AZUL
        // ============================================
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Consolidado');
        $sheet->getTabColor()->setRGB('00B0F0'); // AZUL
        $sheet->getDefaultRowDimension()->setRowHeight(18);
        $fila = 1;
        self::agregarConsolidado($sheet, $servicio, $datosConsolidados, $fila);
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ============================================
        // HOJA 8: INFORME FINAL (CONSOLIDADO DETALLADO) - AZUL
        // ============================================
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Informe Final');
        $sheet->getTabColor()->setRGB('00B0F0'); // AZUL
        $sheet->getDefaultRowDimension()->setRowHeight(18);
        $fila = 1;
        self::agregarInformeFinal($sheet, $servicio, $datosConsolidados, $fila);
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Activar la primera hoja
        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private static function agregarInfoServicio($sheet, Service $servicio, &$fila, $azul)
    {
        $labels = ['SEDE', 'SERVICIO', 'FECHA', 'HORA'];
        $valores = [
            $servicio->sede->nombre ?? '',
            $servicio->numero_servicio ?? '',
            \Carbon\Carbon::parse($servicio->fecha)->locale('es')->isoFormat('DD/MMM/YYYY'),
            $servicio->hora ?? ''
        ];

        foreach ($labels as $i => $label) {
            $sheet->setCellValue('B' . $fila, $label);
            $sheet->setCellValue('C' . $fila, $valores[$i]);
            $sheet->getStyle('B' . $fila)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $azul]],
                'font' => ['bold' => true],
            ]);
            $fila++;
        }
    }

    private static function agregarPrimerConteo($sheet, Service $servicio, array $datosConsolidados, $fila, $amarillo, $azul)
    {
        $primerConteo = $servicio->primerConteo;
        
        if (!$primerConteo) {
            return $fila;
        }

        // Header Primer Conteo
        $sheet->setCellValue('B' . $fila, 'Primer Conteo');
        $sheet->mergeCells('B' . $fila . ':C' . $fila);
        $sheet->getStyle('B' . $fila)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $amarillo]],
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $fila++;

        // Los datos vienen como array de objetos: [["area" => "A1", "adultos" => 22, "ninos" => 34], ...]
        $areasData = $primerConteo->areas ?? [];
        
        // Totales del primer conteo
        $totalAdultosPrimerConteo = 0;
        $totalNinosPrimerConteo = 0;

        foreach ($areasData as $areaData) {
            $area = $areaData['area'] ?? '';
            $adultos = $areaData['adultos'] ?? 0;
            $ninos = $areaData['ninos'] ?? 0;
            
            $totalAdultosPrimerConteo += $adultos;
            $totalNinosPrimerConteo += $ninos;
            
            // Header Área
            $sheet->setCellValue('B' . $fila, 'Area:');
            $sheet->setCellValue('C' . $fila, $area);
            $sheet->getStyle('B' . $fila . ':C' . $fila)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $azul]],
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $fila++;
            
            // Adultos
            $sheet->setCellValue('B' . $fila, 'Adultos :');
            $sheet->setCellValue('C' . $fila, $adultos);
            $sheet->getStyle('B' . $fila . ':C' . $fila)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $fila++;
            
            // Niños
            $sheet->setCellValue('B' . $fila, 'Niños :');
            $sheet->setCellValue('C' . $fila, $ninos);
            $sheet->getStyle('B' . $fila . ':C' . $fila)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $fila += 2;
        }

        // Información del servicio
        $diaSemana = strtoupper($servicio->dia_semana ?? $servicio->fecha->locale('es')->isoFormat('dddd'));
        $hora = $servicio->hora ?? '08:00 AM';
        $fecha = \Carbon\Carbon::parse($servicio->fecha)->locale('es')->isoFormat('DD/MMM/YYYY');
        
        // Resumen Primer Conteo
        $sheet->setCellValue('B' . $fila, 'Servicio');
        $sheet->setCellValue('D' . $fila, $diaSemana);
        $fila++;
        $sheet->setCellValue('B' . $fila, $fecha);
        $sheet->setCellValue('D' . $fila, $hora);
        $fila++;
        $sheet->setCellValue('B' . $fila, 'Primer Conteo');
        $fila++;
        $sheet->setCellValue('B' . $fila, 'Total Adultos:');
        $sheet->setCellValue('D' . $fila, $totalAdultosPrimerConteo);
        $fila++;
        $sheet->setCellValue('B' . $fila, 'Total Niños:');
        $sheet->setCellValue('D' . $fila, $totalNinosPrimerConteo);
        $fila++;
        $sheet->setCellValue('B' . $fila, 'Total Asistencia:');
        $sheet->setCellValue('D' . $fila, $totalAdultosPrimerConteo + $totalNinosPrimerConteo);
        $fila += 2;
        
        // Calcular segundo conteo desde los conteos de áreas (A1-A4)
        $totalAdultosSegundoConteo = $datosConsolidados['asistencia']['enSillas'] + $datosConsolidados['asistencia']['enGradas'];
        $totalNinosSegundoConteo = $datosConsolidados['asistencia']['ninosAuditorio'] + $datosConsolidados['asistencia']['ninosIglekids'];
        
        // Total Final Asistencia (Primer + Segundo conteo)
        $totalAdultosFinales = $totalAdultosPrimerConteo + $totalAdultosSegundoConteo;
        $totalNinosFinales = $totalNinosPrimerConteo + $totalNinosSegundoConteo;
        
        $fila++;
        $sheet->setCellValue('B' . $fila, 'Total Final Asistencia');
        $sheet->getStyle('B' . $fila)->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
        ]);
        $fila++;
        $sheet->setCellValue('B' . $fila, 'Total Adultos:');
        $sheet->setCellValue('D' . $fila, $totalAdultosFinales);
        $fila++;
        $sheet->setCellValue('B' . $fila, 'Total Niños:');
        $sheet->setCellValue('D' . $fila, $totalNinosFinales);
        $fila++;
        $sheet->setCellValue('B' . $fila, 'Total Asistencia:');
        $sheet->setCellValue('D' . $fila, $totalAdultosFinales + $totalNinosFinales);
        $fila += 2;
        
        // Segundo Conteo (suma de áreas A1-A4)
        $fila++;
        $sheet->setCellValue('B' . $fila, 'Servicio');
        $sheet->setCellValue('D' . $fila, $diaSemana);
        $fila++;
        $sheet->setCellValue('B' . $fila, $fecha);
        $sheet->setCellValue('D' . $fila, $hora);
        $fila++;
        $sheet->setCellValue('B' . $fila, 'Segundo Conteo');
        $fila++;
        $sheet->setCellValue('B' . $fila, 'Total Adultos:');
        $sheet->setCellValue('D' . $fila, $totalAdultosSegundoConteo);
        $fila++;
        $sheet->setCellValue('B' . $fila, 'Total Niños:');
        $sheet->setCellValue('D' . $fila, $totalNinosSegundoConteo);
        $fila++;
        $sheet->setCellValue('B' . $fila, 'Total Asistencia:');
        $sheet->setCellValue('D' . $fila, $totalAdultosSegundoConteo + $totalNinosSegundoConteo);
        
        return $fila + 1;
    }

    private static function agregarDetalleArea($sheet, $conteo, $areaName, $fecha, $fila, $azul, $rojo, $verde, $amarillo)
    {
        $areas = $conteo->areas ?? [];
        $sillas = $areas['sillas'] ?? [];
        $servidores = $areas['servidores'] ?? [];
        $servidorasPastora = $areas['servidorasPastora'] ?? [];
        
        $fechaFormateada = \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('DD/MMM/YYYY');

        // Header GRUPO 3
        $sheet->setCellValue('B' . $fila, 'GRUPO 3');
        $sheet->mergeCells('B' . $fila . ':E' . $fila);
        $sheet->getStyle('B' . $fila)->applyFromArray([
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $fila++;

        // Header AREA
        $sheet->setCellValue('B' . $fila, 'AREA : ' . $areaName);
        $sheet->setCellValue('D' . $fila, 'FECHA:');
        $sheet->setCellValue('E' . $fila, $fechaFormateada);
        $sheet->getStyle('B' . $fila . ':E' . $fila)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $azul]],
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $fila++;

        // Sillas
        $filaSillas = [
            ['Total sillas del área.', $sillas['totalSillas'] ?? 0, ''],
            ['Total silla vacías', $sillas['sillasVacias'] ?? 0, $rojo],
            ['Total personas', $sillas['totalPersonas'] ?? 0, $verde],
            ['Total niños ' . $areaName, $sillas['totalNinos'] ?? 0, ''],
        ];

        foreach ($filaSillas as $row) {
            $sheet->setCellValue('B' . $fila, $row[0]);
            $sheet->setCellValue('E' . $fila, $row[1]);
            $sheet->getStyle('B' . $fila . ':E' . $fila)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            if ($row[2]) {
                $sheet->getStyle('E' . $fila)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $row[2]]],
                ]);
            }
            $fila++;
        }

        $fila++; // Espacio

        // Servidores
        $servidoresData = [
            ['Servidores', $servidores['servidores'] ?? 0],
            ['Comunicaciones (dentro del auditorio)', $servidores['comunicaciones'] ?? 0],
            ['Logística', $servidores['logistica'] ?? 0],
            ['Alabanza', $servidores['alabanza'] ?? 0],
        ];

        if ($areaName == 'A2') {
            $servidoresData = [
                ['Servidores', $servidores['servidores'] ?? 0],
                ['Logística', $servidores['logistica'] ?? 0],
                ['Jesus Place', $servidores['jesusPlace'] ?? 0],
                ['Datáfono', $servidores['datafono'] ?? 0],
                ['Ministerial', $servidores['ministerial'] ?? 0],
            ];
        } elseif ($areaName == 'A3') {
            $servidoresData = [
                ['Servidores', $servidores['servidores'] ?? 0],
                ['Logística', $servidores['logistica'] ?? 0],
                ['Consolidación', $servidores['consolidacion'] ?? 0],
            ];
        }

        foreach ($servidoresData as $row) {
            $sheet->setCellValue('B' . $fila, $row[0]);
            $sheet->setCellValue('E' . $fila, $row[1]);
            $sheet->getStyle('B' . $fila . ':E' . $fila)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $fila++;
        }

        // Servidora de Pastora (solo A1)
        if ($areaName == 'A1' && count($servidorasPastora) > 0) {
            $fila++; // Espacio
            $sheet->setCellValue('B' . $fila, 'Servidora de Pastora:');
            $sheet->setCellValue('E' . $fila, count($servidorasPastora));
            $sheet->getStyle('E' . $fila)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $amarillo]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $fila++;
            
            foreach ($servidorasPastora as $nombre) {
                $sheet->setCellValue('B' . $fila, $nombre);
                $sheet->getStyle('B' . $fila)->applyFromArray([
                    'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $fila++;
            }
        }

        return $fila;
    }

    private static function agregarDetalleAreaA4($sheet, $conteo, $fecha, $fila, $azul, $verde, $verdeClaro, $naranja)
    {
        $areas = $conteo->areas ?? [];
        $exteriores = $areas['exteriores'] ?? [];
        $vehiculos = $areas['vehiculos'] ?? [];
        $iglekids = $areas['iglekids'] ?? [];
        
        $fechaFormateada = \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('DD/MMM/YYYY');

        // Header GRUPO 3
        $sheet->setCellValue('B' . $fila, 'GRUPO 3');
        $sheet->mergeCells('B' . $fila . ':E' . $fila);
        $sheet->getStyle('B' . $fila)->applyFromArray([
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $fila++;

        // Header AREA
        $sheet->setCellValue('B' . $fila, 'AREA : A4');
        $sheet->setCellValue('D' . $fila, 'FECHA:');
        $sheet->setCellValue('E' . $fila, $fechaFormateada);
        $sheet->getStyle('B' . $fila . ':E' . $fila)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $azul]],
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $fila++;

        // EXTERIORES
        $sheet->setCellValue('B' . $fila, 'EXTERIORES');
        $sheet->mergeCells('B' . $fila . ':E' . $fila);
        $sheet->getStyle('B' . $fila)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $azul]],
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $fila++;

        $extData = [
            ['Servidores', $exteriores['servidores'] ?? 0],
            ['Logística', $exteriores['logistica'] ?? 0],
            ['Coffe.', $exteriores['coffee'] ?? 0],
            ['Container', $exteriores['container'] ?? 0],
        ];

        foreach ($extData as $row) {
            $sheet->setCellValue('B' . $fila, $row[0]);
            $sheet->setCellValue('E' . $fila, $row[1]);
            $sheet->getStyle('B' . $fila . ':E' . $fila)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $fila++;
        }

        $fila++; // Espacio

        // Vehículos (sin header de PARQUEADERO)
        $carros = $vehiculos['carros'] ?? 0;
        $motos = $vehiculos['motos'] ?? 0;
        $bicicletas = $vehiculos['bicicletas'] ?? 0;
        $totalVeh = $carros + $motos + $bicicletas;

        $vehData = [
            ['Vehículos', $carros],
            ['Motos', $motos],
            ['Bicicletas', $bicicletas],
            ['TOTAL VEHICULOS', $totalVeh, $verde],
        ];

        foreach ($vehData as $row) {
            $sheet->setCellValue('B' . $fila, $row[0]);
            $sheet->setCellValue('E' . $fila, $row[1]);
            $sheet->getStyle('B' . $fila . ':E' . $fila)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            if (isset($row[2])) {
                $sheet->getStyle('E' . $fila)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $row[2]]],
                    'font' => ['bold' => true],
                ]);
            }
            $fila++;
        }

        $fila++; // Espacio

        // IGLEKIDS
        $sheet->setCellValue('B' . $fila, 'IGLEKIDS');
        $sheet->mergeCells('B' . $fila . ':E' . $fila);
        $sheet->getStyle('B' . $fila)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $azul]],
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $fila++;

        // Primeras filas (Coordinadoras, Supervisoras, Maestros)
        $igkData1 = [
            ['Coordinadoras:', $iglekids['coordinadoras'] ?? 0],
            ['Supervisoras:', $iglekids['supervisoras'] ?? 0],
            ['Maestros:', $iglekids['maestros'] ?? 0],
        ];

        foreach ($igkData1 as $row) {
            $sheet->setCellValue('B' . $fila, $row[0]);
            $sheet->setCellValue('E' . $fila, $row[1]);
            $sheet->getStyle('B' . $fila . ':E' . $fila)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $fila++;
        }

        // Personal de Apoyo (con label)
        $sheet->setCellValue('B' . $fila, 'Personal de Apoyo');
        $sheet->getStyle('B' . $fila)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $fila++;

        $totalApoyo = ($iglekids['recrearte'] ?? 0) + ($iglekids['regikids'] ?? 0) + ($iglekids['logikids'] ?? 0) + 
                     ($iglekids['saludKids'] ?? 0) + ($iglekids['yoSoy'] ?? 0);
        $totalAreaIglekids = ($iglekids['coordinadoras'] ?? 0) + ($iglekids['supervisoras'] ?? 0) + 
                            ($iglekids['maestros'] ?? 0) + $totalApoyo;

        $igkData2 = [
            ['Recrearte:', $iglekids['recrearte'] ?? 0],
            ['Regikids:', $iglekids['regikids'] ?? 0],
            ['Logikids:', $iglekids['logikids'] ?? 0],
            ['Salud Kids:', $iglekids['saludKids'] ?? 0],
            ['Yo Soy', $iglekids['yoSoy'] ?? 0],
            ['Total Apoyo:', $totalApoyo, $verdeClaro],
        ];

        foreach ($igkData2 as $row) {
            $sheet->setCellValue('B' . $fila, $row[0]);
            $sheet->setCellValue('E' . $fila, $row[1]);
            $sheet->getStyle('B' . $fila . ':E' . $fila)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            if (isset($row[2])) {
                $sheet->getStyle('E' . $fila)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $row[2]]],
                    'font' => ['bold' => true],
                ]);
            }
            $fila++;
        }

        // Totales finales
        $finalIgkData = [
            ['Total área Iglekids:', $totalAreaIglekids, $verdeClaro],
            ['Niños:', $iglekids['ninos'] ?? 0, $naranja],
        ];

        foreach ($finalIgkData as $row) {
            $sheet->setCellValue('B' . $fila, $row[0]);
            $sheet->setCellValue('E' . $fila, $row[1]);
            $sheet->getStyle('B' . $fila . ':E' . $fila)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            if (isset($row[2])) {
                $sheet->getStyle('E' . $fila)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $row[2]]],
                    'font' => ['bold' => true],
                ]);
            }
            $fila++;
        }

        return $fila;
    }

    private static function agregarInventarioSobres($sheet, $conteoSobres, $fila, $azul, $naranja, $verde)
    {
        // Header
        $sheet->setCellValue('B' . $fila, 'Inventario de Sobres');
        $sheet->mergeCells('B' . $fila . ':G' . $fila);
        $sheet->getStyle('B' . $fila)->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $fila++;

        // Headers tabla
        $headers = ['Clase', 'Inv.nicial', 'Ingresados', 'Tot.Inicial', 'Entregados', 'Inv.Final'];
        foreach ($headers as $i => $header) {
            $col = chr(66 + $i); // B, C, D, E, F, G
            $sheet->setCellValue($col . $fila, $header);
        }
        $sheet->getStyle('B' . $fila . ':G' . $fila)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $azul]],
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $fila++;

        if ($conteoSobres) {
            $ofrendas = $conteoSobres->ofrendas ?? [];
            $protemplo = $conteoSobres->protemplo ?? [];
            $iglekids = $conteoSobres->iglekids ?? [];

            $sobresData = [
                ['Ofrenda', 
                 $ofrendas['inicial'] ?? 0, 
                 $ofrendas['recibidos'] ?? 0, 
                 ($ofrendas['inicial'] ?? 0) + ($ofrendas['recibidos'] ?? 0),
                 $ofrendas['entregados'] ?? 0,
                 (($ofrendas['inicial'] ?? 0) + ($ofrendas['recibidos'] ?? 0)) - ($ofrendas['entregados'] ?? 0)
                ],
                ['Protemplo', 
                 $protemplo['inicial'] ?? 0, 
                 $protemplo['recibidos'] ?? 0, 
                 ($protemplo['inicial'] ?? 0) + ($protemplo['recibidos'] ?? 0),
                 $protemplo['entregados'] ?? 0,
                 (($protemplo['inicial'] ?? 0) + ($protemplo['recibidos'] ?? 0)) - ($protemplo['entregados'] ?? 0)
                ],
                ['Iglekids', 
                 $iglekids['inicial'] ?? 0, 
                 $iglekids['recibidos'] ?? 0, 
                 ($iglekids['inicial'] ?? 0) + ($iglekids['recibidos'] ?? 0),
                 $iglekids['entregados'] ?? 0,
                 (($iglekids['inicial'] ?? 0) + ($iglekids['recibidos'] ?? 0)) - ($iglekids['entregados'] ?? 0)
                ],
            ];

            foreach ($sobresData as $row) {
                foreach ($row as $i => $value) {
                    $col = chr(66 + $i); // B, C, D, E, F, G
                    $sheet->setCellValue($col . $fila, $value);
                }
                $sheet->getStyle('B' . $fila . ':G' . $fila)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $naranja]],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $fila++;
            }

            // Canastas
            $fila++;
            $sheet->setCellValue('B' . $fila, 'Canastas:');
            $sheet->setCellValue('C' . $fila, $conteoSobres->canastas['entregadas'] ?? 0);
            $sheet->getStyle('B' . $fila . ':C' . $fila)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $verde]],
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
        }

        return $fila + 1;
    }

    private static function agregarInformeFinal($sheet, Service $servicio, array $datos, $fila)
    {
        $asistencia = $datos['asistencia'];
        $vehiculos = $datos['vehiculos'];
        $ofrendas = $datos['ofrendas'];

        // Header
        $sheet->setCellValue('B' . $fila, '*Informe de Servicio Grupo 3*');
        $sheet->mergeCells('B' . $fila . ':D' . $fila);
        $sheet->getStyle('B' . $fila)->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $fila += 2;

        // Info servicio
        $fechaFormateada = \Carbon\Carbon::parse($servicio->fecha)->locale('es')->isoFormat('DD/MM/YYYY');
        $diaSemana = strtoupper($servicio->dia_semana ?? $servicio->fecha->locale('es')->isoFormat('dddd'));
        
        $sheet->setCellValue('B' . $fila, $fechaFormateada);
        $fila++;
        $sheet->setCellValue('B' . $fila, 'N° Servicio:');
        $sheet->setCellValue('D' . $fila, $servicio->numero_servicio ?? 1);
        $fila++;
        $sheet->setCellValue('B' . $fila, 'Sede');
        $sheet->setCellValue('D' . $fila, $servicio->sede->nombre ?? '');
        $fila++;
        $sheet->setCellValue('B' . $fila, $diaSemana);
        $sheet->setCellValue('D' . $fila, $servicio->hora ?? '');
        $fila += 2;

        // [1] Asistencia Personas
        $sheet->setCellValue('B' . $fila, '[1] Asistencia Personas');
        $sheet->getStyle('B' . $fila)->applyFromArray(['font' => ['bold' => true]]);
        $fila += 2;

        $sheet->setCellValue('B' . $fila, 'Asistencia Personas');
        $sheet->getStyle('B' . $fila)->applyFromArray(['font' => ['bold' => true, 'color' => ['argb' => 'FF000000']]]);
        $fila += 2;

        $asistenciaData = [
            ['En Sillas:', $asistencia['enSillas']],
            ['En Gradas:', $asistencia['enGradas']],
            ['Niños Auditorio:', $asistencia['ninosAuditorio']],
            ['Niños Iglekids', $asistencia['ninosIglekids']],
        ];

        foreach ($asistenciaData as $row) {
            $sheet->setCellValue('B' . $fila, $row[0]);
            $sheet->setCellValue('D' . $fila, $row[1]);
            $fila++;
        }

        $fila++;
        $sheet->setCellValue('B' . $fila, 'Total Auditorio:');
        $sheet->setCellValue('D' . $fila, $asistencia['totalAuditorio']);
        $sheet->getStyle('B' . $fila . ':D' . $fila)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFFF00']],
            'font' => ['bold' => true],
        ]);
        $fila += 2;

        // Área de Servidores
        $sheet->setCellValue('B' . $fila, 'Área De Servidores:');
        $sheet->getStyle('B' . $fila)->applyFromArray(['font' => ['bold' => true, 'color' => ['argb' => 'FF000000']]]);
        $fila += 2;

        $servidoresLabels = [
            'servidores' => 'Servidores:',
            'consolidacion' => 'Consolidación:',
            'comunicaciones' => 'Comunicaciones:',
            'logistica' => 'Logística:',
            'jesusPlace' => 'Jesus place:',
            'datafono' => 'Datafono:',
            'coffee' => 'Coffee:',
            'ministerial' => 'Ministerial:',
            'alabanza' => 'Alabanza:',
            'vip' => 'VIP:',
            'iglekids' => 'Iglekids:',
        ];

        foreach ($servidoresLabels as $key => $label) {
            $sheet->setCellValue('B' . $fila, $label);
            $sheet->setCellValue('D' . $fila, $asistencia['servidores'][$key]);
            $fila++;
        }

        $fila++;
        $sheet->setCellValue('B' . $fila, 'Total Área Servidores:');
        $sheet->setCellValue('D' . $fila, $asistencia['totalServidores']);
        $sheet->getStyle('B' . $fila)->applyFromArray(['font' => ['bold' => true]]);
        $fila += 2;

        $sheet->setCellValue('B' . $fila, 'Total Personas Iglesia:');
        $sheet->setCellValue('D' . $fila, $asistencia['totalPersonasIglesia']);
        $sheet->getStyle('B' . $fila)->applyFromArray(['font' => ['bold' => true]]);
        $fila += 2;

        // [2] Vehículos
        $sheet->setCellValue('B' . $fila, '[2] Vehículos:');
        $sheet->getStyle('B' . $fila)->applyFromArray(['font' => ['bold' => true]]);
        $fila += 2;

        $vehData = [
            ['Carros:', $vehiculos['carros']],
            ['Motos:', $vehiculos['motos']],
            ['Bicicletas:', $vehiculos['bicicletas']],
        ];

        foreach ($vehData as $row) {
            $sheet->setCellValue('B' . $fila, $row[0]);
            $sheet->setCellValue('D' . $fila, $row[1]);
            $fila++;
        }

        $fila++;
        $sheet->setCellValue('B' . $fila, 'Total Vehículos:');
        $sheet->setCellValue('D' . $fila, $vehiculos['total']);
        $sheet->getStyle('B' . $fila)->applyFromArray(['font' => ['bold' => true]]);
        $fila += 2;

        // [3] Ofrendas
        $sheet->setCellValue('B' . $fila, '[3] Ofrendas');
        $sheet->getStyle('B' . $fila)->applyFromArray(['font' => ['bold' => true]]);
        $fila += 2;

        $sheet->setCellValue('B' . $fila, 'Canastas Ofrendas:');
        $sheet->getStyle('B' . $fila)->applyFromArray(['font' => ['bold' => true, 'color' => ['argb' => 'FF000000']]]);
        $fila += 2;

        $sheet->setCellValue('B' . $fila, 'Entregadas:');
        $sheet->setCellValue('D' . $fila, $ofrendas['canastas']);
        $fila += 2;

        // Sobres Ofrendas
        $sheet->setCellValue('B' . $fila, 'Sobres Ofrendas');
        $sheet->getStyle('B' . $fila)->applyFromArray(['font' => ['bold' => true, 'color' => ['argb' => 'FF000000']]]);
        $fila += 2;

        $sobresOf = $ofrendas['sobresOfrendas'];
        $sobresOfData = [
            ['Inicial Ofrendas:', $sobresOf['inicial']],
            ['Recibidos:', $sobresOf['recibidos']],
            ['Total:', $sobresOf['total']],
            ['Entregados:', $sobresOf['entregados']],
            ['Final Ofrendas:', $sobresOf['final']],
        ];

        foreach ($sobresOfData as $row) {
            $sheet->setCellValue('B' . $fila, $row[0]);
            $sheet->setCellValue('D' . $fila, $row[1]);
            if (strpos($row[0], 'Total') !== false || strpos($row[0], 'Final') !== false) {
                $sheet->getStyle('B' . $fila)->applyFromArray(['font' => ['bold' => true]]);
            }
            $fila++;
        }

        $fila += 2;

        // Sobres Protemplo
        $sheet->setCellValue('B' . $fila, 'Sobres Protemplo');
        $sheet->getStyle('B' . $fila)->applyFromArray(['font' => ['bold' => true, 'color' => ['argb' => 'FF000000']]]);
        $fila += 2;

        $sobresPt = $ofrendas['sobresProtemplo'];
        $sobresPtData = [
            ['Inicial Primicias:', $sobresPt['inicial']],
            ['Recibidos:', $sobresPt['recibidos']],
            ['Total:', $sobresPt['total']],
            ['Entregados:', $sobresPt['entregados']],
            ['Final Protemplo:', $sobresPt['final']],
        ];

        foreach ($sobresPtData as $row) {
            $sheet->setCellValue('B' . $fila, $row[0]);
            $sheet->setCellValue('D' . $fila, $row[1]);
            if (strpos($row[0], 'Total') !== false || strpos($row[0], 'Final') !== false) {
                $sheet->getStyle('B' . $fila)->applyFromArray(['font' => ['bold' => true]]);
            }
            $fila++;
        }

        $fila += 2;

        // Sobres Iglekids
        $sheet->setCellValue('B' . $fila, 'Sobres Iglekids');
        $sheet->getStyle('B' . $fila)->applyFromArray(['font' => ['bold' => true, 'color' => ['argb' => 'FF000000']]]);
        $fila += 2;

        $sobresIgk = $ofrendas['sobresIglekids'];
        $sobresIgkData = [
            ['Inicial Iglekids:', $sobresIgk['inicial']],
            ['Recibidos: 0', $sobresIgk['recibidos']],
            ['Total:', $sobresIgk['total']],
            ['Entregados:', $sobresIgk['entregados']],
            ['Final Iglekids:', $sobresIgk['final']],
        ];

        foreach ($sobresIgkData as $row) {
            $sheet->setCellValue('B' . $fila, $row[0]);
            $sheet->setCellValue('D' . $fila, $row[1]);
            if (strpos($row[0], 'Total') !== false || strpos($row[0], 'Final') !== false) {
                $sheet->getStyle('B' . $fila)->applyFromArray(['font' => ['bold' => true]]);
            }
            $fila++;
        }
    }

    private static function agregarConsolidado($sheet, Service $servicio, array $datos, $fila)
    {
        $azul = 'FF00B0F0';
        
        // FECHA
        $fechaFormateada = \Carbon\Carbon::parse($servicio->fecha)->locale('es')->isoFormat('DD/MMM/YYYY');
        $sheet->setCellValue('B' . $fila, 'FECHA');
        $sheet->setCellValue('C' . $fila, $fechaFormateada);
        $sheet->getStyle('B' . $fila)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $azul]],
            'font' => ['bold' => true],
        ]);
        $fila += 2;

        // ===== AUDITORIO =====
        $sheet->setCellValue('B' . $fila, 'AUDITORIO');
        $sheet->mergeCells('B' . $fila . ':G' . $fila);
        $sheet->getStyle('B' . $fila)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $azul]],
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $fila++;

        // Headers RESUMEN GENERAL
        $sheet->setCellValue('B' . $fila, 'RESUMEN GENERAL');
        $sheet->setCellValue('C' . $fila, 'A1');
        $sheet->setCellValue('D' . $fila, 'A2');
        $sheet->setCellValue('E' . $fila, 'A3');
        $sheet->setCellValue('F' . $fila, 'A4');
        $sheet->setCellValue('G' . $fila, 'TOTALES');
        $sheet->getStyle('B' . $fila . ':G' . $fila)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $azul]],
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $fila++;

        // Extraer datos de cada área
        $a1 = $servicio->conteoA1 ? ($servicio->conteoA1->areas['sillas'] ?? []) : [];
        $a2 = $servicio->conteoA2 ? ($servicio->conteoA2->areas['sillas'] ?? []) : [];
        $a3 = $servicio->conteoA3 ? ($servicio->conteoA3->areas['sillas'] ?? []) : [];
        
        $sillasA1 = $a1['totalSillas'] ?? 0;
        $sillasA2 = $a2['totalSillas'] ?? 0;
        $sillasA3 = $a3['totalSillas'] ?? 0;
        $totalSillas = $sillasA1 + $sillasA2 + $sillasA3;
        
        $vaciasA1 = $a1['sillasVacias'] ?? 0;
        $vaciasA2 = $a2['sillasVacias'] ?? 0;
        $vaciasA3 = $a3['sillasVacias'] ?? 0;
        $totalVacias = $vaciasA1 + $vaciasA2 + $vaciasA3;
        
        $personasA1 = $a1['totalPersonas'] ?? 0;
        $personasA2 = $a2['totalPersonas'] ?? 0;
        $personasA3 = $a3['totalPersonas'] ?? 0;
        $totalPersonas = $personasA1 + $personasA2 + $personasA3;
        
        $ninosA1 = $a1['totalNinos'] ?? 0;
        $ninosA2 = $a2['totalNinos'] ?? 0;
        $ninosA3 = $a3['totalNinos'] ?? 0;
        $totalNinos = $ninosA1 + $ninosA2 + $ninosA3;
        
        $totalAudA1 = $sillasA1;
        $totalAudA2 = $sillasA2;
        $totalAudA3 = $sillasA3;

        // Filas de datos
        $auditData = [
            ['Sillas del área.', $sillasA1, $sillasA2, $sillasA3, '', $totalSillas],
            ['Silla vacías', $vaciasA1, $vaciasA2, $vaciasA3, '', $totalVacias],
            ['Total Personas', $personasA1, $personasA2, $personasA3, '', $totalPersonas],
            ['Total niños', $ninosA1, $ninosA2, $ninosA3, '', $totalNinos],
            ['Total AUDITORIO', $totalAudA1, $totalAudA2, $totalAudA3, '', ''],
        ];

        foreach ($auditData as $row) {
            $sheet->setCellValue('B' . $fila, $row[0]);
            $sheet->setCellValue('C' . $fila, $row[1]);
            $sheet->setCellValue('D' . $fila, $row[2]);
            $sheet->setCellValue('E' . $fila, $row[3]);
            $sheet->setCellValue('F' . $fila, $row[4]);
            $sheet->setCellValue('G' . $fila, $row[5]);
            $sheet->getStyle('B' . $fila . ':G' . $fila)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $fila++;
        }
        $fila++;

        // ===== SERVIDORES =====
        $sheet->setCellValue('B' . $fila, 'SERVIDORES');
        $sheet->mergeCells('B' . $fila . ':G' . $fila);
        $sheet->getStyle('B' . $fila)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $azul]],
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $fila++;

        // Headers Areas
        $sheet->setCellValue('B' . $fila, 'Areas');
        $sheet->setCellValue('C' . $fila, 'A1');
        $sheet->setCellValue('D' . $fila, 'A2');
        $sheet->setCellValue('E' . $fila, 'A3');
        $sheet->setCellValue('F' . $fila, 'A4');
        $sheet->setCellValue('G' . $fila, 'TOTAL');
        $sheet->getStyle('B' . $fila . ':G' . $fila)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $azul]],
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $fila++;

        // Extraer servidores por área
        $servA1 = $servicio->conteoA1 ? ($servicio->conteoA1->areas['servidores'] ?? []) : [];
        $servA2 = $servicio->conteoA2 ? ($servicio->conteoA2->areas['servidores'] ?? []) : [];
        $servA3 = $servicio->conteoA3 ? ($servicio->conteoA3->areas['servidores'] ?? []) : [];
        $servA4 = $servicio->conteoA4 ? ($servicio->conteoA4->areas['servidores'] ?? []) : [];
        $extA4 = $servicio->conteoA4 ? ($servicio->conteoA4->areas['exteriores'] ?? []) : [];
        $igkA4 = $servicio->conteoA4 ? ($servicio->conteoA4->areas['iglekids'] ?? []) : [];

        $totalIglekidsA4 = ($igkA4['coordinadoras'] ?? 0) + ($igkA4['supervisoras'] ?? 0) + 
                          ($igkA4['maestros'] ?? 0) + ($igkA4['recrearte'] ?? 0) + 
                          ($igkA4['regikids'] ?? 0) + ($igkA4['logikids'] ?? 0) + 
                          ($igkA4['saludKids'] ?? 0) + ($igkA4['yoSoy'] ?? 0);

        $servidoresData = [
            ['Servidores:', 
             ($servA1['servidores'] ?? 0), 
             ($servA2['servidores'] ?? 0), 
             ($servA3['servidores'] ?? 0), 
             ($extA4['servidores'] ?? 0),
             ($servA1['servidores'] ?? 0) + ($servA2['servidores'] ?? 0) + ($servA3['servidores'] ?? 0) + ($extA4['servidores'] ?? 0)],
            ['Consolidación:', 
             0, 
             0, 
             ($servA3['consolidacion'] ?? 0), 
             0,
             ($servA3['consolidacion'] ?? 0)],
            ['Comunicaciones:', 
             ($servA1['comunicaciones'] ?? 0), 
             0, 
             0, 
             0,
             ($servA1['comunicaciones'] ?? 0)],
            ['Logistica:', 
             ($servA1['logistica'] ?? 0), 
             ($servA2['logistica'] ?? 0), 
             ($servA3['logistica'] ?? 0), 
             ($extA4['logistica'] ?? 0),
             ($servA1['logistica'] ?? 0) + ($servA2['logistica'] ?? 0) + ($servA3['logistica'] ?? 0) + ($extA4['logistica'] ?? 0)],
            ['Jesus place:', 
             0, 
             ($servA2['jesusPlace'] ?? 0), 
             0, 
             0,
             ($servA2['jesusPlace'] ?? 0)],
            ['Datafono:', 
             0, 
             ($servA2['datafono'] ?? 0), 
             0, 
             0,
             ($servA2['datafono'] ?? 0)],
            ['Coffee:', 
             0, 
             0, 
             0, 
             ($extA4['coffee'] ?? 0),
             ($extA4['coffee'] ?? 0)],
            ['Ministerial:', 
             0, 
             ($servA2['ministerial'] ?? 0), 
             0, 
             0,
             ($servA2['ministerial'] ?? 0)],
            ['Alabanza:', 
             ($servA1['alabanza'] ?? 0), 
             0, 
             0, 
             0,
             ($servA1['alabanza'] ?? 0)],
            ['VIP:', 
             1, 
             0, 
             0, 
             0,
             1],
            ['Iglekids:', 
             0, 
             0, 
             0, 
             $totalIglekidsA4,
             $totalIglekidsA4],
        ];

        $totalServA1 = ($servA1['servidores'] ?? 0) + ($servA1['comunicaciones'] ?? 0) + 
                      ($servA1['logistica'] ?? 0) + ($servA1['alabanza'] ?? 0) + 1;
        $totalServA2 = ($servA2['servidores'] ?? 0) + ($servA2['jesusPlace'] ?? 0) + 
                      ($servA2['logistica'] ?? 0) + ($servA2['datafono'] ?? 0) + ($servA2['ministerial'] ?? 0);
        $totalServA3 = ($servA3['servidores'] ?? 0) + ($servA3['consolidacion'] ?? 0) + ($servA3['logistica'] ?? 0);
        $totalServA4 = ($extA4['servidores'] ?? 0) + ($extA4['logistica'] ?? 0) + ($extA4['coffee'] ?? 0) + $totalIglekidsA4;
        $totalServ = $totalServA1 + $totalServA2 + $totalServA3 + $totalServA4;

        foreach ($servidoresData as $row) {
            $sheet->setCellValue('B' . $fila, $row[0]);
            $sheet->setCellValue('C' . $fila, $row[1]);
            $sheet->setCellValue('D' . $fila, $row[2]);
            $sheet->setCellValue('E' . $fila, $row[3]);
            $sheet->setCellValue('F' . $fila, $row[4]);
            $sheet->setCellValue('G' . $fila, $row[5]);
            $sheet->getStyle('B' . $fila . ':G' . $fila)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $fila++;
        }

        // Total Servidores
        $sheet->setCellValue('B' . $fila, 'Total Servidores');
        $sheet->setCellValue('C' . $fila, $totalServA1);
        $sheet->setCellValue('D' . $fila, $totalServA2);
        $sheet->setCellValue('E' . $fila, $totalServA3);
        $sheet->setCellValue('F' . $fila, $totalServA4);
        $sheet->setCellValue('G' . $fila, $totalServ);
        $sheet->getStyle('B' . $fila . ':G' . $fila)->applyFromArray([
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $fila += 2;

        // ===== PARQUEADERO =====
        $sheet->setCellValue('B' . $fila, 'PARQUEADERO');
        $sheet->mergeCells('B' . $fila . ':G' . $fila);
        $sheet->getStyle('B' . $fila)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $azul]],
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $fila++;

        $sheet->setCellValue('B' . $fila, 'VEHICULOS');
        $sheet->getStyle('B' . $fila)->applyFromArray([
            'font' => ['bold' => true],
        ]);
        $fila++;

        $vehA4 = $servicio->conteoA4 ? ($servicio->conteoA4->areas['vehiculos'] ?? []) : [];
        $carros = $vehA4['carros'] ?? 0;
        $motos = $vehA4['motos'] ?? 0;
        $bicicletas = $vehA4['bicicletas'] ?? 0;
        $totalVeh = $carros + $motos + $bicicletas;

        $vehData = [
            ['Carros', $carros],
            ['Motos', $motos],
            ['Bicicletas', $bicicletas],
            ['Total Vehiculos', $totalVeh],
        ];

        foreach ($vehData as $row) {
            $sheet->setCellValue('B' . $fila, $row[0]);
            $sheet->setCellValue('F' . $fila, $row[1]);
            $sheet->getStyle('B' . $fila . ':F' . $fila)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            if ($row[0] === 'Total Vehiculos') {
                $sheet->getStyle('B' . $fila . ':F' . $fila)->applyFromArray([
                    'font' => ['bold' => true],
                ]);
            }
            $fila++;
        }
    }
}
