# Plan de implementación — Report Services

Plan por fases para implementar el sistema de informes de servicio (Villa Grande, Turbaco, Bocagrande): modelo de datos, captura por área, consolidado e informe final.

---

## Requisitos previos

- PHP 8.2+, Composer
- Node.js 18+, npm
- MySQL 8 (o MariaDB)
- Opcional: Docker (ya existe `docker-compose.yml`)

Comandos típicos:

```bash
composer install
cp .env.example .env && php artisan key:generate
# Configurar DB_* en .env
php artisan migrate
npm install && npm run dev
php artisan serve
```

---

## Fase 1: Modelo de datos y configuración de sedes

### 1.1 Entidades principales

| Entidad      | Descripción |
|-------------|-------------|
| `sede`      | Villa Grande, Turbaco, Bocagrande. Define nombre, slug, si tiene varias áreas, si tiene parqueadero/gradas. |
| `area`      | A1, A2, A3, A4. Pertenece a una sede. Villa Grande tiene 4; Turbaco y Bocagrande tienen solo A1. |
| `servicio`  | Un servicio = sede + fecha + número de servicio (ej. 4/feb/2026, N° 1). Punto de agrupación de todo el reporte. |
| `service_area_auditorio` | Datos de auditorio por servicio y área (sillas, vacías, personas, niños). |
| `service_area_servidores`| Conteo de servidores por tipo, por servicio y área. |
| `service_iglekids`       | Datos Iglekids por servicio (y por área si se quiere detalle; en Villa Grande va con A4). |
| `service_parqueadero`   | Vehículos por servicio (solo Villa Grande). |
| `service_sobres`        | Inventario de sobres (ofrenda, protemplo, iglekids) y canastas por servicio. |
| `service_conteo`       | Primer/Segundo conteo por servicio/área (adultos, niños, total). |

### 1.2 Migraciones sugeridas

**`sedes`**

- `id`, `name`, `slug` (villa-grande, turbaco, bocagrande)
- `areas_count` (1 o 4) o derivar de relación con `areas`
- `has_parqueadero` (boolean), `has_gradas` (boolean)
- `timestamps`

**`areas`**

- `id`, `sede_id`, `code` (A1, A2, A3, A4), `name` (opcional)
- `timestamps`

**`servicios`**

- `id`, `sede_id`, `fecha` (date), `numero_servicio` (int), `hora` (time nullable), `dia_semana` (nullable)
- `timestamps`
- Unique: `(sede_id, fecha, numero_servicio)`

**`service_area_auditorio`**

- `id`, `servicio_id`, `area_id`
- `total_sillas`, `sillas_vacias`, `total_personas`, `total_niños`
- `timestamps`
- Unique: `(servicio_id, area_id)`

**`service_area_servidores`**

- `id`, `servicio_id`, `area_id`
- Columnas por rol: `servidores`, `consolidacion`, `comunicaciones`, `logistica`, `jesus_place`, `datafono`, `coffee`, `ministerial`, `alabanza`, `vip`, `iglekids`, `servidora_pastora` (nullable int)
- `timestamps`
- Unique: `(servicio_id, area_id)`

**`service_iglekids`**

- `id`, `servicio_id` (, `area_id` opcional si se quiere por área)
- Coordinadoras, supervisoras, maestros
- Recrearte, regikids, logikids, salud_kids, yo_soy, total_apoyo
- `total_area_iglekids`, `niños`
- `timestamps`
- Unique: `servicio_id` (o servicio_id + area_id)

**`service_parqueadero`**

- `id`, `servicio_id`
- `carros`, `motos`, `bicicletas`
- `timestamps`
- Unique: `servicio_id`

**`service_sobres`**

- `id`, `servicio_id`
- Ofrenda: `inicial`, `recibidos`, `entregados` (totales se calculan)
- Protemplo: `inicial`, `recibidos`, `entregados`
- Iglekids: `inicial`, `recibidos`, `entregados`
- `canastas_entregadas`
- `timestamps`
- Unique: `servicio_id`

**`service_conteos`** (primer/segundo conteo)

- `id`, `servicio_id`, `area_id` (nullable para total final), `tipo` (enum: primer, segundo, total_final)
- `adultos`, `niños`, `total` (o solo total si se calcula)
- `timestamps`

Ajustar nombres de tablas/columnas al convenio Laravel (snake_case, plural para tablas).

### 1.3 Seeders

- **SedeSeeder:** crear las 3 sedes con flags (áreas, parqueadero, gradas).
- **AreaSeeder:** crear A1 para las 3; A2, A3, A4 solo para Villa Grande.

### 1.4 Modelos Eloquent

- `Sede` (hasMany Areas, hasMany Servicios)
- `Area` (belongsTo Sede; hasMany service_area_auditorio, service_area_servidores)
- `Servicio` (belongsTo Sede; hasMany/morph auditorio, servidores, hasOne iglekids, parqueadero, sobres, conteos)
- Relaciones inversas en los modelos de detalle (ServiceAreaAuditorio, ServiceAreaServidores, ServiceIglekids, ServiceParqueadero, ServiceSobres, ServiceConteo).

---

## Fase 2: Servicios y selección de sede/fecha

### 2.1 Servicios

- Listado de servicios por sede (y filtro por fecha).
- Crear servicio: sede + fecha + número de servicio (y opcional hora, día).
- Página o modal “Nuevo servicio” / “Seleccionar servicio” para usar en todo el flujo.

### 2.2 Rutas y controlador

- `ServicioController@index` (listado).
- `ServicioController@store` (crear).
- Rutas bajo middleware `auth` y prefijo coherente (ej. `/servicios`, `/sedes/{sede}/servicios`).

### 2.3 Frontend

- Vista listado de servicios (por sede).
- Formulario crear servicio (sede, fecha, número).
- Desde el menú general, al elegir “Conteo A1” (etc.) o “Informe final”, primero elegir o crear el servicio.

---

## Fase 3: Captura por área (Auditorio + Servidores)

### 3.1 Backend

- **ServiceAreaAuditorioController** (o método en `ServicioController`):  
  - `edit(servicio, area)` → formulario  
  - `update()` → guardar/actualizar total_sillas, sillas_vacias, total_personas, total_niños
- **ServiceAreaServidoresController** (o mismo controlador):  
  - Misma idea por área: guardar conteos por rol según lo que aplique a esa área/sede.

Validación: solo permitir áreas que pertenezcan a la sede del servicio.

### 3.2 Frontend

- Página “Conteo A1” (A2, A3, A4):  
  - Bloque Auditorio (sillas, vacías, personas, niños).  
  - Bloque Servidores (inputs por rol según sede/área).  
  - En Villa Grande A4: además bloque Exteriores (servidores, logística, coffee, container si aplica), Vehículos (carros, motos, bicicletas), Iglekids (coordinadoras, supervisoras, maestros, apoyo, total área, niños).
- Una ruta por área o una sola ruta con parámetro `area` (ej. `/servicios/{servicio}/conteo/{area}`).

### 3.3 Iglekids y Parqueadero

- Si Iglekids/Parqueadero van en la misma pantalla de A4 (Villa Grande), reutilizar esa vista y guardar en `service_iglekids` y `service_parqueadero` asociados al mismo `servicio_id`.
- Para Turbaco/Bocagrande: página “Iglekids” asociada al servicio (sin área o con área A1) que guarde en `service_iglekids`.

---

## Fase 4: Primer conteo y conteo de sobres

### 4.1 Primer (y segundo) conteo

- Si se desea guardar explícitamente primer y segundo conteo:
  - Formulario “Primer conteo”: por cada área, adultos y niños (guardar en `service_conteos` con tipo `primer`).
  - Formulario “Segundo conteo” o total final: totales por servicio (tipo `segundo` o `total_final`).
- Alternativa: que el “Total final asistencia” se calcule desde `service_area_auditorio` + `service_iglekids` y no se guarde en tabla aparte; entonces solo persistir “primer conteo” si el negocio lo requiere.

### 4.2 Conteo sobres

- **ServiceSobresController:** `edit(servicio)`, `update()`.
- Campos: canastas entregadas; por cada tipo (Ofrenda, Protemplo, Iglekids): inicial, recibidos, entregados. Totales (Total, Final) se calculan en vista o en atributos del modelo.
- Página “Conteo sobres” vinculada al servicio actual.

---

## Fase 5: Consolidado

### 5.1 Cálculo

- **ConsolidadoController@show** (o `ConsolidadoController@index` con fecha y sede):  
  - Recuperar servicio(s) por sede y fecha (o un solo servicio por fecha).  
  - Para AUDITORIO: por cada área del servicio, leer `service_area_auditorio` y armar tabla (A1..A4 + TOTALES).  
  - Para SERVIDORES: por cada área, leer `service_area_servidores` y armar tabla por rol con totales.  
  - Para PARQUEADERO: si la sede tiene parqueadero, leer `service_parqueadero` y mostrar carros, motos, bicicletas, total.

### 5.2 Vista

- Página Vue que reciba el consolidado (por Inertia) y lo muestre en tablas (mismo formato que el documento de referencia: AUDITORIO, SERVIDORES, PARQUEADERO).
- Ruta ej.: `/servicios/{servicio}/consolidado` o `/consolidado?sede=&fecha=`.

---

## Fase 6: Informe final

### 6.1 Cálculo

- **InformeFinalController@show(servicio)**:  
  - Asistencia: sumar personas y niños de `service_area_auditorio`; si hay gradas, incluir; sumar niños de `service_iglekids`; total auditorio y total área servidores; total personas iglesia.  
  - Vehículos: desde `service_parqueadero` si aplica.  
  - Ofrendas: canastas y sobres desde `service_sobres` (inicial, recibidos, total, entregados, final por tipo).

### 6.2 Vista y exportación

- Página “Informe final” con el mismo texto y estructura que el ejemplo (1 Asistencia personas, 2 Vehículos, 3 Ofrendas).  
- Opcional: exportar a PDF o Excel (Laravel: DomPDF, Snappy, o Excel export) con el mismo layout.

Ruta ej.: `/servicios/{servicio}/informe-final`.

---

## Fase 7: Menú general y navegación

### 7.1 Menú

- Layout (ej. en `AppLayout.vue` o componente de menú) que muestre:
  - Selector de sede (o “estás en sede X”).
  - PRIMER CONTEO → lleva a formulario primer conteo (servicio + áreas).
  - CONTEO A1, A2, A3, A4 → enlaces a `/servicios/{servicio}/conteo/A1` etc., habilitados solo para las áreas de la sede actual.
  - INFORME FINAL → `/servicios/{servicio}/informe-final`.
  - CONTEO SOBRES → `/servicios/{servicio}/sobres`.
- Si no hay servicio seleccionado, redirigir a “Seleccionar o crear servicio” (listado de servicios por sede).

### 7.2 Permisos (opcional)

- Si hay roles (admin, sede Villa Grande, sede Turbaco, etc.), middleware o policy para que solo se vean/editen servicios de la sede del usuario.
- Seed de usuarios por sede para pruebas.

---

## Fase 8: Ajustes y cierre

### 8.1 Validaciones

- Que no se dupliquen servicios (sede + fecha + número).
- Que las áreas pertenezcan a la sede del servicio.
- Números no negativos en todos los conteos.

### 8.2 Pruebas

- Crear un servicio de prueba por sede.
- Completar auditorio y servidores por área.
- Registrar sobres e Iglekids/parqueadero donde aplique.
- Verificar consolidado e informe final con los mismos números que en los ejemplos.

### 8.3 Documentación

- README actualizado con cómo correr el proyecto y qué hace cada pantalla.
- PLAN.md (este archivo) como referencia del diseño y del orden de implementación.

---

## Orden sugerido de desarrollo

1. Migraciones y modelos (Fase 1).  
2. Seeders sedes y áreas (Fase 1).  
3. CRUD básico de servicios y selección de servicio (Fase 2).  
4. Captura auditorio + servidores por área (Fase 3).  
5. Iglekids y parqueadero (Fase 3).  
6. Conteo sobres (Fase 4).  
7. Primer conteo si se mantiene (Fase 4).  
8. Vista consolidado (Fase 5).  
9. Vista informe final (Fase 6).  
10. Menú y flujo completo (Fase 7).  
11. Validaciones y pruebas (Fase 8).

---

## Resumen de archivos a crear/modificar

| Tipo        | Archivos |
|------------|----------|
| Migraciones| `create_sedes_table`, `create_areas_table`, `create_servicios_table`, `create_service_area_auditorio_table`, `create_service_area_servidores_table`, `create_service_iglekids_table`, `create_service_parqueadero_table`, `create_service_sobres_table`, `create_service_conteos_table` |
| Modelos    | `Sede`, `Area`, `Servicio`, `ServiceAreaAuditorio`, `ServiceAreaServidores`, `ServiceIglekids`, `ServiceParqueadero`, `ServiceSobres`, `ServiceConteo` |
| Controladores | `ServicioController`, `ServiceAreaController` (o separados por recurso), `ConsolidadoController`, `InformeFinalController`, `ServiceSobresController` |
| Rutas      | `web.php`: rutas para servicios, conteo por área, consolidado, informe final, sobres |
| Vue/Inertia | Páginas: Listado servicios, Crear servicio, Conteo por área (A1–A4), Conteo sobres, Consolidado, Informe final; componente Menú general |
| Seeders    | `SedeSeeder`, `AreaSeeder` (o integrados en `DatabaseSeeder`) |

Con este plan se puede implementar el sistema de forma ordenada y alineado con los reportes que necesitas para las tres sedes.
