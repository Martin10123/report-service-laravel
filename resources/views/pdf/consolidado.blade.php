<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Consolidado</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            padding: 20px;
        }
        
        .fecha-header {
            background-color: #00B0F0;
            color: white;
            padding: 5px 10px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        .fecha-value {
            display: inline-block;
            padding: 5px 10px;
            margin-left: 5px;
        }
        
        .section-title {
            background-color: #00B0F0;
            color: white;
            padding: 8px;
            font-weight: bold;
            font-size: 13px;
            text-align: center;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        
        th {
            background-color: #00B0F0;
            color: white;
            font-weight: bold;
        }
        
        td.label {
            text-align: left;
            padding-left: 10px;
        }
        
        tr.total {
            font-weight: bold;
        }
        
        .gray-cell {
            background-color: #C0C0C0;
        }
        
        .empty-cell {
            background-color: white;
        }
    </style>
</head>
<body>
    <!-- INFORMACIÓN DEL SERVICIO -->
    <table style="width: 100%; margin-bottom: 20px; border: none;">
        <tr>
            <td style="background-color: #00B0F0; color: white; font-weight: bold; padding: 8px; width: 25%; border: 1px solid #000;">SEDE</td>
            <td style="padding: 8px; width: 25%; border: 1px solid #000;">{{ $servicio->sede->nombre ?? '' }}</td>
            <td style="background-color: #00B0F0; color: white; font-weight: bold; padding: 8px; width: 25%; border: 1px solid #000;">SERVICIO</td>
            <td style="padding: 8px; width: 25%; border: 1px solid #000;">{{ $servicio->numero_servicio ?? '' }}</td>
        </tr>
        <tr>
            <td style="background-color: #00B0F0; color: white; font-weight: bold; padding: 8px; border: 1px solid #000;">FECHA</td>
            <td style="padding: 8px; border: 1px solid #000;">{{ $fecha }}</td>
            <td style="background-color: #00B0F0; color: white; font-weight: bold; padding: 8px; border: 1px solid #000;">HORA</td>
            <td style="padding: 8px; border: 1px solid #000;">{{ $servicio->hora ?? '' }}</td>
        </tr>
    </table>

    <!-- AUDITORIO -->
    <div class="section-title">AUDITORIO</div>
    <table>
        <thead>
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
                <td>{{ $auditorio['A1']['sillas'] ?? 0 }}</td>
                <td>{{ $auditorio['A2']['sillas'] ?? 0 }}</td>
                <td>{{ $auditorio['A3']['sillas'] ?? 0 }}</td>
                <td>{{ $auditorio['A4']['sillas'] ?? 0 }}</td>
                <td>{{ $auditorio['totales']['sillas'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Silla vacías</td>
                <td>{{ $auditorio['A1']['sillas_vacias'] ?? 0 }}</td>
                <td>{{ $auditorio['A2']['sillas_vacias'] ?? 0 }}</td>
                <td>{{ $auditorio['A3']['sillas_vacias'] ?? 0 }}</td>
                <td>{{ $auditorio['A4']['sillas_vacias'] ?? 0 }}</td>
                <td>{{ $auditorio['totales']['sillas_vacias'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Total Personas</td>
                <td>{{ $auditorio['A1']['total_personas'] ?? 0 }}</td>
                <td>{{ $auditorio['A2']['total_personas'] ?? 0 }}</td>
                <td>{{ $auditorio['A3']['total_personas'] ?? 0 }}</td>
                <td>{{ $auditorio['A4']['total_personas'] ?? 0 }}</td>
                <td>{{ $auditorio['totales']['total_personas'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Total niños</td>
                <td>{{ $auditorio['A1']['total_ninos'] ?? 0 }}</td>
                <td>{{ $auditorio['A2']['total_ninos'] ?? 0 }}</td>
                <td>{{ $auditorio['A3']['total_ninos'] ?? 0 }}</td>
                <td>{{ $auditorio['A4']['total_ninos'] ?? 0 }}</td>
                <td>{{ $auditorio['totales']['total_ninos'] ?? 0 }}</td>
            </tr>
            <tr class="total">
                <td class="label">Total AUDITORIO</td>
                <td>{{ $auditorio['A1']['total_area'] ?? 0 }}</td>
                <td>{{ $auditorio['A2']['total_area'] ?? 0 }}</td>
                <td>{{ $auditorio['A3']['total_area'] ?? 0 }}</td>
                <td>{{ $auditorio['A4']['total_area'] ?? 0 }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <!-- SERVIDORES -->
    <div class="section-title">SERVIDORES</div>
    <table>
        <thead>
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
                <td>{{ $servidores['A1']['servidores'] ?? '' }}</td>
                <td>{{ $servidores['A2']['servidores'] ?? '' }}</td>
                <td>{{ $servidores['A3']['servidores'] ?? '' }}</td>
                <td>{{ $servidores['A4']['servidores'] ?? '' }}</td>
                <td>{{ $servidores['totales']['servidores'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">Consolidación:</td>
                <td class="gray-cell"></td>
                <td class="empty-cell"></td>
                <td>{{ $servidores['A3']['consolidacion'] ?? '' }}</td>
                <td class="gray-cell"></td>
                <td>{{ $servidores['totales']['consolidacion'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">Comunicaciones:</td>
                <td>{{ $servidores['A1']['comunicaciones'] ?? '' }}</td>
                <td class="gray-cell"></td>
                <td class="gray-cell"></td>
                <td class="gray-cell"></td>
                <td>{{ $servidores['totales']['comunicaciones'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">Logistica:</td>
                <td>{{ $servidores['A1']['logistica'] ?? '' }}</td>
                <td>{{ $servidores['A2']['logistica'] ?? '' }}</td>
                <td>{{ $servidores['A3']['logistica'] ?? '' }}</td>
                <td>{{ $servidores['A4']['logistica'] ?? '' }}</td>
                <td>{{ $servidores['totales']['logistica'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">Jesus place:</td>
                <td class="gray-cell"></td>
                <td>{{ $servidores['A2']['jesusPlace'] ?? '' }}</td>
                <td class="gray-cell"></td>
                <td class="gray-cell"></td>
                <td>{{ $servidores['totales']['jesusPlace'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">Datafono:</td>
                <td class="gray-cell"></td>
                <td>{{ $servidores['A2']['datafono'] ?? '' }}</td>
                <td class="gray-cell"></td>
                <td class="gray-cell"></td>
                <td>{{ $servidores['totales']['datafono'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">Coffee:</td>
                <td class="gray-cell"></td>
                <td class="gray-cell"></td>
                <td class="gray-cell"></td>
                <td>{{ $servidores['A4']['coffee'] ?? '' }}</td>
                <td>{{ $servidores['totales']['coffee'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">Ministerial:</td>
                <td class="gray-cell"></td>
                <td>{{ $servidores['A2']['ministerial'] ?? '' }}</td>
                <td class="gray-cell"></td>
                <td class="gray-cell"></td>
                <td>{{ $servidores['totales']['ministerial'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">Alabanza:</td>
                <td>{{ $servidores['A1']['alabanza'] ?? '' }}</td>
                <td class="gray-cell"></td>
                <td class="gray-cell"></td>
                <td class="gray-cell"></td>
                <td>{{ $servidores['totales']['alabanza'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">VIP:</td>
                <td>{{ $servidores['A1']['vip'] ?? '' }}</td>
                <td class="gray-cell"></td>
                <td class="gray-cell"></td>
                <td class="gray-cell"></td>
                <td>{{ $servidores['totales']['vip'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">Iglekids:</td>
                <td class="gray-cell"></td>
                <td class="gray-cell"></td>
                <td class="gray-cell"></td>
                <td>{{ $servidores['A4']['iglekids'] ?? '' }}</td>
                <td>{{ $servidores['totales']['iglekids'] ?? '' }}</td>
            </tr>
            <tr class="total">
                <td class="label">Total Servidores</td>
                <td>{{ $servidores['A1']['total'] ?? 0 }}</td>
                <td>{{ $servidores['A2']['total'] ?? 0 }}</td>
                <td>{{ $servidores['A3']['total'] ?? 0 }}</td>
                <td>{{ $servidores['A4']['total'] ?? 0 }}</td>
                <td>{{ $servidores['totales']['total'] ?? 0 }}</td>
            </tr>
        </tbody>
    </table>

    <!-- PARQUEADERO -->
    <div class="section-title">PARQUEADERO</div>
    <table>
        <thead>
            <tr>
                <th colspan="6">VEHICULOS</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="label">Carros</td>
                <td class="gray-cell" colspan="3"></td>
                <td>{{ $parqueadero['A4']['carros'] ?? 0 }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="label">Motos</td>
                <td class="gray-cell" colspan="3"></td>
                <td>{{ $parqueadero['A4']['motos'] ?? 0 }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="label">Bicicletas</td>
                <td class="gray-cell" colspan="3"></td>
                <td>{{ $parqueadero['A4']['bicicletas'] ?? 0 }}</td>
                <td></td>
            </tr>
            <tr class="total">
                <td class="label">Total Vehiculos</td>
                <td class="gray-cell" colspan="3"></td>
                <td>{{ $parqueadero['A4']['total'] ?? 0 }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
