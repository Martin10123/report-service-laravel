<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe Final - {{ $fecha }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 20px;
            color: #000000;
            background-color: #ffffff;
        }

        .page-break {
            page-break-after: always;
        }

        /* Títulos de sección */
        .section-title {
            background-color: #ffffff;
            color: #000000;
            padding: 10px 15px;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            margin: 20px 0 12px 0;
            text-transform: uppercase;
            border-bottom: 2px solid #000000;
        }

        .section-title.blue {
            border-bottom-color: #0066CC;
        }

        /* Info box */
        .info-box {
            background-color: #f9f9f9;
            border: 2px solid #000000;
            padding: 15px;
            margin-bottom: 20px;
        }

        .info-row {
            font-size: 12px;
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            width: 120px;
            display: inline-block;
            color: #000000;
        }

        /* Tablas */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 20px 0;
        }

        th {
            background-color: #f5f5f5;
            color: #000000;
            padding: 8px 10px;
            font-weight: bold;
            font-size: 12px;
            border: 1px solid #000000;
            text-align: center;
        }

        td {
            padding: 8px 10px;
            border: 1px solid #000000;
            font-size: 12px;
            text-align: center;
        }

        td.label {
            text-align: left;
            font-weight: normal;
            color: #000000;
        }

        td.label-bold {
            text-align: left;
            font-weight: bold;
            color: #000000;
        }

        /* Subsecciones */
        .subsection {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            border: 1px solid #000000;
        }

        /* Totales en azul */
        tr.total td {
            background-color: #0066CC !important;
            color: #ffffff;
            font-weight: bold;
        }

        .highlight-bg {
            background-color: #0066CC !important;
            color: #ffffff;
            font-weight: bold;
        }

        .gray-bg {
            background-color: #0066CC !important;
            color: #ffffff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- INFORMACIÓN DEL SERVICIO -->
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">SEDE:</span> {{ $sedeNombre }}
        </div>
        <div class="info-row">
            <span class="info-label">FECHA:</span> {{ $fecha }}
        </div>
        <div class="info-row">
            <span class="info-label">SERVICIO:</span> {{ $diaSemana ? strtoupper($diaSemana) : '' }} {{ $hora }}
        </div>
    </div>

    <!-- PRIMER CONTEO -->
    <div class="section-title">PRIMER CONTEO</div>
    @if($primerConteo)
        @php
            $areasData = $primerConteo->areas ?? [];
            $totalAdultos = $primerConteo->total_adultos ?? 0;
            $totalNinos = $primerConteo->total_ninos ?? 0;
            $totalAsistencia = $primerConteo->total_asistencia ?? 0;
        @endphp
        <table>
            @foreach($areasData as $areaItem)
                <tr>
                    <td class="subsection" colspan="2">{{ $areaItem['area'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="label">Adultos:</td>
                    <td>{{ $areaItem['adultos'] ?? 0 }}</td>
                </tr>
                <tr>
                    <td class="label">Niños:</td>
                    <td>{{ $areaItem['ninos'] ?? 0 }}</td>
                </tr>
            @endforeach
            
            <tr style="border-top: 2px solid #000;">
                <td class="label-bold">Total Adultos:</td>
                <td class="highlight-bg">{{ $totalAdultos }}</td>
            </tr>
            <tr>
                <td class="label-bold">Total Niños:</td>
                <td class="highlight-bg">{{ $totalNinos }}</td>
            </tr>
            <tr>
                <td class="label-bold">Total Asistencia:</td>
                <td class="highlight-bg">{{ $totalAsistencia }}</td>
            </tr>
        </table>
    @else
        <p style="text-align: center; padding: 15px;">No hay datos de primer conteo</p>
    @endif

    <!-- ÁREA 1 -->
    <div class="section-title green">ÁREA 1</div>
    @if($conteoA1)
        @php
            $a1 = $conteoA1->areas ?? [];
            $sillas = $a1['sillas'] ?? [];
            $servidores = $a1['servidores'] ?? [];
        @endphp
        <table>
            <tr>
                <td class="subsection" colspan="2">ÁREA DE SILLAS</td>
            </tr>
            <tr>
                <td class="label">Total Sillas:</td>
                <td>{{ $sillas['totalSillas'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Sillas Vacías:</td>
                <td>{{ $sillas['sillasVacias'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Total Personas:</td>
                <td class="gray-bg">{{ $sillas['totalPersonas'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Total Niños:</td>
                <td>{{ $sillas['totalNinos'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="subsection" colspan="2">ÁREA DE SERVIDORES</td>
            </tr>
            <tr>
                <td class="label">Servidores:</td>
                <td>{{ $servidores['servidores'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Comunicaciones:</td>
                <td>{{ $servidores['comunicaciones'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Logística:</td>
                <td>{{ $servidores['logistica'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Alabanza:</td>
                <td>{{ $servidores['alabanza'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">VIP:</td>
                <td>1</td>
            </tr>
            <tr>
                <td class="label-bold">Total Servidores:</td>
                <td class="gray-bg">{{ ($servidores['servidores'] ?? 0) + ($servidores['comunicaciones'] ?? 0) + ($servidores['logistica'] ?? 0) + ($servidores['alabanza'] ?? 0) + 1 }}</td>
            </tr>
        </table>
    @else
        <p style="text-align: center; padding: 10px;">No hay datos de Área 1</p>
    @endif

    <div class="page-break"></div>

    <!-- ÁREA 2 -->
    <div class="section-title green">ÁREA 2</div>
    @if($conteoA2)
        @php
            $a2 = $conteoA2->areas ?? [];
            $sillas = $a2['sillas'] ?? [];
            $servidores = $a2['servidores'] ?? [];
        @endphp
        <table>
            <tr>
                <td class="subsection" colspan="2">ÁREA DE SILLAS</td>
            </tr>
            <tr>
                <td class="label">Total Sillas:</td>
                <td>{{ $sillas['totalSillas'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Sillas Vacías:</td>
                <td>{{ $sillas['sillasVacias'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Total Personas:</td>
                <td class="gray-bg">{{ $sillas['totalPersonas'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Total Niños:</td>
                <td>{{ $sillas['totalNinos'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="subsection" colspan="2">ÁREA DE SERVIDORES</td>
            </tr>
            <tr>
                <td class="label">Servidores:</td>
                <td>{{ $servidores['servidores'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Jesus Place:</td>
                <td>{{ $servidores['jesusPlace'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Logística:</td>
                <td>{{ $servidores['logistica'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Datafono:</td>
                <td>{{ $servidores['datafono'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Ministerial:</td>
                <td>{{ $servidores['ministerial'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label-bold">Total Servidores:</td>
                <td class="gray-bg">{{ ($servidores['servidores'] ?? 0) + ($servidores['jesusPlace'] ?? 0) + ($servidores['logistica'] ?? 0) + ($servidores['datafono'] ?? 0) + ($servidores['ministerial'] ?? 0) }}</td>
            </tr>
        </table>
    @else
        <p style="text-align: center; padding: 10px;">No hay datos de Área 2</p>
    @endif

    <!-- ÁREA 3 -->
    <div class="section-title green">ÁREA 3</div>
    @if($conteoA3)
        @php
            $a3 = $conteoA3->areas ?? [];
            $sillas = $a3['sillas'] ?? [];
            $servidores = $a3['servidores'] ?? [];
        @endphp
        <table>
            <tr>
                <td class="subsection" colspan="2">ÁREA DE SILLAS</td>
            </tr>
            <tr>
                <td class="label">Total Sillas:</td>
                <td>{{ $sillas['totalSillas'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Sillas Vacías:</td>
                <td>{{ $sillas['sillasVacias'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Total Personas:</td>
                <td class="gray-bg">{{ $sillas['totalPersonas'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Total Niños:</td>
                <td>{{ $sillas['totalNinos'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="subsection" colspan="2">ÁREA DE SERVIDORES</td>
            </tr>
            <tr>
                <td class="label">Servidores:</td>
                <td>{{ $servidores['servidores'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Consolidación:</td>
                <td>{{ $servidores['consolidacion'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Logística:</td>
                <td>{{ $servidores['logistica'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label-bold">Total Servidores:</td>
                <td class="gray-bg">{{ ($servidores['servidores'] ?? 0) + ($servidores['consolidacion'] ?? 0) + ($servidores['logistica'] ?? 0) }}</td>
            </tr>
        </table>
    @else
        <p style="text-align: center; padding: 10px;">No hay datos de Área 3</p>
    @endif

    <div class="page-break"></div>

    <!-- ÁREA 4 -->
    <div class="section-title">ÁREA 4</div>
    @if($conteoA4)
        @php
            $a4 = $conteoA4->areas ?? [];
            $exteriores = $a4['exteriores'] ?? [];
            $vehiculos = $a4['vehiculos'] ?? [];
            $iglekids = $a4['iglekids'] ?? [];
            
            $totalIglekids = ($iglekids['coordinadoras'] ?? 0) + 
                            ($iglekids['supervisoras'] ?? 0) + 
                            ($iglekids['maestros'] ?? 0) + 
                            ($iglekids['recrearte'] ?? 0) + 
                            ($iglekids['regikids'] ?? 0) + 
                            ($iglekids['logikids'] ?? 0) + 
                            ($iglekids['saludKids'] ?? 0) + 
                            ($iglekids['yoSoy'] ?? 0);
        @endphp
        <table>
            <tr>
                <td class="subsection" colspan="2">ÁREA DE EXTERIORES</td>
            </tr>
            <tr>
                <td class="label">Servidores:</td>
                <td>{{ $exteriores['servidores'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Logística:</td>
                <td>{{ $exteriores['logistica'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Coffee:</td>
                <td>{{ $exteriores['coffee'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label-bold">Total Exteriores:</td>
                <td class="gray-bg">{{ ($exteriores['servidores'] ?? 0) + ($exteriores['logistica'] ?? 0) + ($exteriores['coffee'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="subsection" colspan="2">ÁREA DE VEHICULOS</td>
            </tr>
            <tr>
                <td class="label">Carros:</td>
                <td>{{ $vehiculos['carros'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Motos:</td>
                <td>{{ $vehiculos['motos'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Bicicletas:</td>
                <td>{{ $vehiculos['bicicletas'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label-bold">Total Vehículos:</td>
                <td class="gray-bg">{{ ($vehiculos['carros'] ?? 0) + ($vehiculos['motos'] ?? 0) + ($vehiculos['bicicletas'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="subsection" colspan="2">IGLEKIDS</td>
            </tr>
            <tr>
                <td class="label">Coordinadoras:</td>
                <td>{{ $iglekids['coordinadoras'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Supervisoras:</td>
                <td>{{ $iglekids['supervisoras'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Maestros:</td>
                <td>{{ $iglekids['maestros'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Recrearte:</td>
                <td>{{ $iglekids['recrearte'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Regikids:</td>
                <td>{{ $iglekids['regikids'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Logikids:</td>
                <td>{{ $iglekids['logikids'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Salud Kids:</td>
                <td>{{ $iglekids['saludKids'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Yo Soy:</td>
                <td>{{ $iglekids['yoSoy'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label-bold">Total Iglekids:</td>
                <td class="gray-bg">{{ $totalIglekids }}</td>
            </tr>
            <tr>
                <td class="label">Personal de Apoyo:</td>
                <td>{{ $iglekids['personalApoyo'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Total Mayores de 3 años (asistencia):</td>
                <td>{{ $iglekids['mayoresTres'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Total Menores de 3 años (bebes):</td>
                <td>{{ $iglekids['menoresTres'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label-bold">Total de Asistencia Iglekids:</td>
                <td class="green-bg">{{ ($iglekids['mayoresTres'] ?? 0) + ($iglekids['menoresTres'] ?? 0) }}</td>
            </tr>
        </table>
    @else
        <p style="text-align: center; padding: 10px;">No hay datos de Área 4</p>
    @endif

    <div class="page-break"></div>

    <!-- INVENTARIO DE SOBRES -->
    <div class="section-title">INVENTARIO DE SOBRES</div>
    @if($conteoSobres)
        @php
            $sobres = $conteoSobres->sobres ?? [];
        @endphp
        <table class="full-width">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Inicial</th>
                    <th>Recibidos</th>
                    <th>Entregados</th>
                    <th>Final</th>
                </tr>
            </thead>
            <tbody>
                @foreach(['compromiso', 'diezmo', 'ofrenda', 'misionera', 'construccion', 'regalo'] as $tipo)
                    @php
                        $sobreData = $sobres[$tipo] ?? [];
                        $inicial = $sobreData['inicial'] ?? 0;
                        $recibidos = $sobreData['recibidos'] ?? 0;
                        $entregados = $sobreData['entregados'] ?? 0;
                        $final = $inicial + $recibidos - $entregados;
                    @endphp
                    <tr>
                        <td class="label">{{ ucfirst($tipo) }}</td>
                        <td>{{ $inicial }}</td>
                        <td>{{ $recibidos }}</td>
                        <td>{{ $entregados }}</td>
                        <td>{{ $final }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; padding: 10px;">No hay datos de sobres</p>
    @endif

    <!-- CONSOLIDADO -->
    <div class="section-title blue">CONSOLIDADO</div>
    @php
        $a1Areas = $conteoA1 ? ($conteoA1->areas['sillas'] ?? []) : [];
        $a2Areas = $conteoA2 ? ($conteoA2->areas['sillas'] ?? []) : [];
        $a3Areas = $conteoA3 ? ($conteoA3->areas['sillas'] ?? []) : [];
        
        $sillasA1 = $a1Areas['totalSillas'] ?? 0;
        $sillasA2 = $a2Areas['totalSillas'] ?? 0;
        $sillasA3 = $a3Areas['totalSillas'] ?? 0;
        
        $servA1 = $conteoA1 ? ($conteoA1->areas['servidores'] ?? []) : [];
        $servA2 = $conteoA2 ? ($conteoA2->areas['servidores'] ?? []) : [];
        $servA3 = $conteoA3 ? ($conteoA3->areas['servidores'] ?? []) : [];
        $servA4 = $conteoA4 ? ($conteoA4->areas['servidores'] ?? []) : [];
        $extA4 = $conteoA4 ? ($conteoA4->areas['exteriores'] ?? []) : [];
        $igkA4 = $conteoA4 ? ($conteoA4->areas['iglekids'] ?? []) : [];
        
        $totalIglekidsA4 = ($igkA4['coordinadoras'] ?? 0) + ($igkA4['supervisoras'] ?? 0) + 
                          ($igkA4['maestros'] ?? 0) + ($igkA4['recrearte'] ?? 0) + 
                          ($igkA4['regikids'] ?? 0) + ($igkA4['logikids'] ?? 0) + 
                          ($igkA4['saludKids'] ?? 0) + ($igkA4['yoSoy'] ?? 0);
    @endphp
    
    <table class="full-width" style="margin-top: 10px;">
        <thead>
            <tr>
                <th colspan="6" style="background-color: #00B0F0;">AUDITORIO</th>
            </tr>
            <tr>
                <th>RESUMEN GENERAL</th>
                <th>A1</th>
                <th>A2</th>
                <th>A3</th>
                <th>A4</th>
                <th>TOTALES</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="label">Sillas del área.</td>
                <td>{{ $sillasA1 }}</td>
                <td>{{ $sillasA2 }}</td>
                <td>{{ $sillasA3 }}</td>
                <td></td>
                <td>{{ $sillasA1 + $sillasA2 + $sillasA3 }}</td>
            </tr>
            <tr>
                <td class="label">Silla vacías</td>
                <td>{{ $a1Areas['sillasVacias'] ?? 0 }}</td>
                <td>{{ $a2Areas['sillasVacias'] ?? 0 }}</td>
                <td>{{ $a3Areas['sillasVacias'] ?? 0 }}</td>
                <td></td>
                <td>{{ ($a1Areas['sillasVacias'] ?? 0) + ($a2Areas['sillasVacias'] ?? 0) + ($a3Areas['sillasVacias'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label">Total Personas</td>
                <td>{{ $a1Areas['totalPersonas'] ?? 0 }}</td>
                <td>{{ $a2Areas['totalPersonas'] ?? 0 }}</td>
                <td>{{ $a3Areas['totalPersonas'] ?? 0 }}</td>
                <td></td>
                <td>{{ ($a1Areas['totalPersonas'] ?? 0) + ($a2Areas['totalPersonas'] ?? 0) + ($a3Areas['totalPersonas'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label">Total niños</td>
                <td>{{ $a1Areas['totalNinos'] ?? 0 }}</td>
                <td>{{ $a2Areas['totalNinos'] ?? 0 }}</td>
                <td>{{ $a3Areas['totalNinos'] ?? 0 }}</td>
                <td></td>
                <td>{{ ($a1Areas['totalNinos'] ?? 0) + ($a2Areas['totalNinos'] ?? 0) + ($a3Areas['totalNinos'] ?? 0) }}</td>
            </tr>
            <tr class="total">
                <td class="label">Total AUDITORIO</td>
                <td>{{ $sillasA1 }}</td>
                <td>{{ $sillasA2 }}</td>
                <td>{{ $sillasA3 }}</td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <table class="full-width" style="margin-top: 10px;">
        <thead>
            <tr>
                <th colspan="6" style="background-color: #00B0F0;">SERVIDORES</th>
            </tr>
            <tr>
                <th>Areas</th>
                <th>A1</th>
                <th>A2</th>
                <th>A3</th>
                <th>A4</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="label">Servidores:</td>
                <td>{{ $servA1['servidores'] ?? 0 }}</td>
                <td>{{ $servA2['servidores'] ?? 0 }}</td>
                <td>{{ $servA3['servidores'] ?? 0 }}</td>
                <td>{{ $extA4['servidores'] ?? 0 }}</td>
                <td>{{ ($servA1['servidores'] ?? 0) + ($servA2['servidores'] ?? 0) + ($servA3['servidores'] ?? 0) + ($extA4['servidores'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label">Consolidación:</td>
                <td></td>
                <td></td>
                <td>{{ $servA3['consolidacion'] ?? 0 }}</td>
                <td></td>
                <td>{{ $servA3['consolidacion'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Comunicaciones:</td>
                <td>{{ $servA1['comunicaciones'] ?? 0 }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $servA1['comunicaciones'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Logistica:</td>
                <td>{{ $servA1['logistica'] ?? 0 }}</td>
                <td>{{ $servA2['logistica'] ?? 0 }}</td>
                <td>{{ $servA3['logistica'] ?? 0 }}</td>
                <td>{{ $extA4['logistica'] ?? 0 }}</td>
                <td>{{ ($servA1['logistica'] ?? 0) + ($servA2['logistica'] ?? 0) + ($servA3['logistica'] ?? 0) + ($extA4['logistica'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label">Jesus place:</td>
                <td></td>
                <td>{{ $servA2['jesusPlace'] ?? 0 }}</td>
                <td></td>
                <td></td>
                <td>{{ $servA2['jesusPlace'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Datafono:</td>
                <td></td>
                <td>{{ $servA2['datafono'] ?? 0 }}</td>
                <td></td>
                <td></td>
                <td>{{ $servA2['datafono'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Coffee:</td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $extA4['coffee'] ?? 0 }}</td>
                <td>{{ $extA4['coffee'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Ministerial:</td>
                <td></td>
                <td>{{ $servA2['ministerial'] ?? 0 }}</td>
                <td></td>
                <td></td>
                <td>{{ $servA2['ministerial'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Alabanza:</td>
                <td>{{ $servA1['alabanza'] ?? 0 }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $servA1['alabanza'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">VIP:</td>
                <td>1</td>
                <td></td>
                <td></td>
                <td></td>
                <td>1</td>
            </tr>
            <tr>
                <td class="label">Iglekids:</td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $totalIglekidsA4 }}</td>
                <td>{{ $totalIglekidsA4 }}</td>
            </tr>
            <tr class="total">
                <td class="label">Total Servidores</td>
                <td>{{ ($servA1['servidores'] ?? 0) + ($servA1['comunicaciones'] ?? 0) + ($servA1['logistica'] ?? 0) + ($servA1['alabanza'] ?? 0) + 1 }}</td>
                <td>{{ ($servA2['servidores'] ?? 0) + ($servA2['jesusPlace'] ?? 0) + ($servA2['logistica'] ?? 0) + ($servA2['datafono'] ?? 0) + ($servA2['ministerial'] ?? 0) }}</td>
                <td>{{ ($servA3['servidores'] ?? 0) + ($servA3['consolidacion'] ?? 0) + ($servA3['logistica'] ?? 0) }}</td>
                <td>{{ ($extA4['servidores'] ?? 0) + ($extA4['logistica'] ?? 0) + ($extA4['coffee'] ?? 0) + $totalIglekidsA4 }}</td>
                <td>{{ ($servA1['servidores'] ?? 0) + ($servA1['comunicaciones'] ?? 0) + ($servA1['logistica'] ?? 0) + ($servA1['alabanza'] ?? 0) + 1 + ($servA2['servidores'] ?? 0) + ($servA2['jesusPlace'] ?? 0) + ($servA2['logistica'] ?? 0) + ($servA2['datafono'] ?? 0) + ($servA2['ministerial'] ?? 0) + ($servA3['servidores'] ?? 0) + ($servA3['consolidacion'] ?? 0) + ($servA3['logistica'] ?? 0) + ($extA4['servidores'] ?? 0) + ($extA4['logistica'] ?? 0) + ($extA4['coffee'] ?? 0) + $totalIglekidsA4 }}</td>
            </tr>
        </tbody>
    </table>

    @if($conteoA4)
        @php
            $vehA4 = $conteoA4->areas['vehiculos'] ?? [];
        @endphp
        <table style="margin-top: 10px; width: 50%;">
            <thead>
                <tr>
                    <th colspan="2" style="background-color: #00B0F0;">PARQUEADERO - VEHICULOS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="label">Carros</td>
                    <td>{{ $vehA4['carros'] ?? 0 }}</td>
                </tr>
                <tr>
                    <td class="label">Motos</td>
                    <td>{{ $vehA4['motos'] ?? 0 }}</td>
                </tr>
                <tr>
                    <td class="label">Bicicletas</td>
                    <td>{{ $vehA4['bicicletas'] ?? 0 }}</td>
                </tr>
                <tr class="total">
                    <td class="label">Total Vehiculos</td>
                    <td>{{ ($vehA4['carros'] ?? 0) + ($vehA4['motos'] ?? 0) + ($vehA4['bicicletas'] ?? 0) }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <div class="page-break"></div>

    <!-- INFORME FINAL -->
    <div class="section-title blue">INFORME FINAL</div>
    @php
        $asistencia = $datosConsolidados['asistencia'] ?? [];
        $servidores = $asistencia['servidores'] ?? [];
        $vehiculos = $datosConsolidados['vehiculos'] ?? [];
    @endphp
    <table style="margin-top: 10px; width: 80%;">
        <tr>
            <td class="subsection" colspan="2">Asistencia Personas</td>
        </tr>
        <tr>
            <td class="label">En Sillas:</td>
            <td>{{ $asistencia['enSillas'] ?? 0 }}</td>
        </tr>
        <tr>
            <td class="label">En Gradas:</td>
            <td>{{ $asistencia['enGradas'] ?? 0 }}</td>
        </tr>
        <tr>
            <td class="label">Niños Auditorio:</td>
            <td>{{ $asistencia['ninosAuditorio'] ?? 0 }}</td>
        </tr>
        <tr>
            <td class="label">Niños Iglekids:</td>
            <td>{{ $asistencia['ninosIglekids'] ?? 0 }}</td>
        </tr>
        <tr class="total">
            <td class="label-bold">Total Auditorio:</td>
            <td class="highlight-bg">{{ $asistencia['totalAuditorio'] ?? 0 }}</td>
        </tr>
        <tr>
            <td class="subsection" colspan="2">Área De Servidores</td>
        </tr>
        <tr>
            <td class="label">Servidores:</td>
            <td>{{ $servidores['servidores'] ?? 0 }}</td>
        </tr>
        <tr>
            <td class="label">Consolidación:</td>
            <td>{{ $servidores['consolidacion'] ?? 0 }}</td>
        </tr>
        <tr>
            <td class="label">Comunicaciones:</td>
            <td>{{ $servidores['comunicaciones'] ?? 0 }}</td>
        </tr>
        <tr>
            <td class="label">Logística:</td>
            <td>{{ $servidores['logistica'] ?? 0 }}</td>
        </tr>
        <tr>
            <td class="label">Jesus Place:</td>
            <td>{{ $servidores['jesusPlace'] ?? 0 }}</td>
        </tr>
        <tr>
            <td class="label">Datafono:</td>
            <td>{{ $servidores['datafono'] ?? 0 }}</td>
        </tr>
        <tr>
            <td class="label">Coffee:</td>
            <td>{{ $servidores['coffee'] ?? 0 }}</td>
        </tr>
        <tr>
            <td class="label">Ministerial:</td>
            <td>{{ $servidores['ministerial'] ?? 0 }}</td>
        </tr>
        <tr>
            <td class="label">Alabanza:</td>
            <td>{{ $servidores['alabanza'] ?? 0 }}</td>
        </tr>
        <tr>
            <td class="label">VIP:</td>
            <td>{{ $servidores['vip'] ?? 0 }}</td>
        </tr>
        <tr>
            <td class="label">Iglekids:</td>
            <td>{{ $servidores['iglekids'] ?? 0 }}</td>
        </tr>
        <tr class="total">
            <td class="label-bold">Total Servidores:</td>
            <td class="gray-bg">{{ $asistencia['totalServidores'] ?? 0 }}</td>
        </tr>
        <tr class="total">
            <td class="label-bold">Total Personas Iglesia:</td>
            <td class="highlight-bg">{{ $asistencia['totalPersonasIglesia'] ?? 0 }}</td>
        </tr>
    </table>

    <table style="margin-top: 15px; width: 60%;">
        <tr>
            <td class="subsection" colspan="2">Vehículos</td>
        </tr>
        <tr>
            <td class="label">Carros:</td>
            <td>{{ $vehiculos['carros'] ?? 0 }}</td>
        </tr>
        <tr>
            <td class="label">Motos:</td>
            <td>{{ $vehiculos['motos'] ?? 0 }}</td>
        </tr>
        <tr>
            <td class="label">Bicicletas:</td>
            <td>{{ $vehiculos['bicicletas'] ?? 0 }}</td>
        </tr>
        <tr class="total">
            <td class="label-bold">Total Vehículos:</td>
            <td class="gray-bg">{{ $vehiculos['total'] ?? 0 }}</td>
        </tr>
    </table>
</body>
</html>
