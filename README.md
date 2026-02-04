# Report Services — Sistema de Informes de Servicio Grupo 3

Sistema web para capturar, consolidar y generar informes de servicio de las sedes de iglesia (Villa Grande, Turbaco y Bocagrande). Permite registrar asistencia por área, servidores, Iglekids, vehículos y ofrendas, y producir consolidados e informes finales por servicio.

---

## Índice

1. [Contexto y objetivo](#contexto-y-objetivo)
2. [Sedes y áreas](#sedes-y-áreas)
3. [Estructura de datos por sede](#estructura-de-datos-por-sede)
4. [Flujos y menú](#flujos-y-menú)
5. [Reportes: Consolidado e Informe Final](#reportes-consolidado-e-informe-final)
6. [Stack técnico](#stack-técnico)
7. [Plan de implementación](#plan-de-implementación)

---

## Contexto y objetivo

Se requiere una aplicación donde:

- **Usuarios** (por sede) capturen los datos de cada servicio: asistencia en auditorio, conteo por áreas, servidores, Iglekids, parqueadero (donde aplique) e inventario de sobres.
- El sistema **consolide** esos datos por fecha y sede en tablas resumen.
- Se pueda generar el **Informe Final** de cada servicio (asistencia, servidores, total personas, vehículos, ofrendas) listo para compartir o imprimir.

Cada **sede** tiene una o varias **áreas** (A1, A2, A3, A4). Villa Grande trabaja con 4 áreas; Turbaco y Bocagrande solo con A1. El menú y los formularios deben adaptarse a la sede seleccionada.

---

## Sedes y áreas

| Sede         | Áreas   | Auditorio | Servidores | Iglekids | Parqueadero | Sobres |
|-------------|---------|-----------|------------|----------|-------------|--------|
| **Villa Grande** | A1, A2, A3, A4 | Por área (4) | Por área (4) | En A4    | En A4       | Sí     |
| **Turbaco**      | A1             | Una sola   | Una sola   | Sí       | No          | Sí     |
| **Bocagrande**   | A1             | Una sola   | Una sola   | Sí       | No          | Sí     |

- **Villa Grande**: varias áreas; A4 incluye Exteriores, Vehículos e Iglekids.
- **Turbaco y Bocagrande**: solo área A1; incluyen Iglekids y sobres, sin parqueadero ni “En gradas”.

---

## Estructura de datos por sede

### 1. Auditorio (por área)

Para cada área (A1, A2, A3, A4 según la sede):

| Campo                    | Descripción              |
|--------------------------|--------------------------|
| Total sillas del área    | Capacidad de sillas      |
| Total sillas vacías      | Sillas sin ocupar        |
| Total personas           | Personas en sillas       |
| Total niños (auditorio)  | Niños en esa área        |

**Nota:** En Villa Grande el informe final además distingue “En Sillas” y “En Gradas”; en Turbaco/Bocagrande solo “En Sillas”.

### 2. Servidores (por área)

Conteo por tipo de rol (los que apliquen en cada área):

- Servidores  
- Consolidación  
- Comunicaciones (dentro del auditorio)  
- Logística  
- Jesus Place  
- Datafono  
- Coffee  
- Ministerial  
- Alabanza  
- VIP  
- Iglekids (personal del área Iglekids)  
- Servidora de Pastora (solo en sedes con un solo área, ej. Turbaco/Bocagrande)

### 3. Iglekids (donde aplique)

- **Directivos:** Coordinadoras, Supervisoras, Maestros  
- **Personal de apoyo:** Recrearte, Regikids, Logikids, Salud Kids, Yo Soy  
- **Totales:** Total área Iglekids, Niños

En Villa Grande esto va asociado al área A4; en Turbaco/Bocagrande es un bloque aparte para la sede.

### 4. Parqueadero (solo Villa Grande, área A4)

- Carros  
- Motos  
- Bicicletas  
- Total vehículos  

### 5. Inventario de sobres (por servicio/sede)

Para cada tipo de sobre:

| Tipo      | Campos típicos                          |
|-----------|-----------------------------------------|
| Ofrenda   | Inicial, Recibidos, Total, Entregados, Final |
| Protemplo | Inicial, Recibidos, Total, Entregados, Final |
| Iglekids  | Inicial, Recibidos, Total, Entregados, Final |

Además: **Canastas entregadas** (número).

### 6. Conteos (Primer Conteo / Segundo Conteo)

Algunos reportes distinguen:

- **Primer conteo:** por área (A1–A4): adultos y niños.  
- **Segundo conteo:** totales (adultos, niños, total asistencia).  
- **Total final asistencia:** total adultos, total niños, total personas (usado en informe final).

El sistema debe permitir guardar al menos un “conteo” por servicio/área que alimente el consolidado y el informe final.

---

## Flujos y menú

Menú general previsto (según referencia “MENU GENERAL”):

1. **PRIMER CONTEO**  
   Entrada del primer conteo (por área: adultos/niños o equivalente).

2. **CONTEO A1**, **CONTEO A2**, **CONTEO A3**, **CONTEO A4**  
   Formularios por área: auditorio + servidores (y en A4 Villa Grande: exteriores, vehículos, Iglekids).  
   Según la sede se muestran solo las áreas que aplican (ej. solo A1 para Turbaco/Bocagrande).

3. **INFORME FINAL**  
   Vista/descarga del informe final del servicio (fecha, sede, número de servicio): asistencia, servidores, total personas, vehículos, ofrendas.

4. **CONTEO SOBRES**  
   Registro del inventario de sobres (ofrenda, protemplo, iglekids) y canastas.

Flujo lógico:

- Usuario elige **sede** y **fecha/servicio**.
- Según la sede, accede a **Primer Conteo** y a **Conteo A1** (y A2, A3, A4 si es Villa Grande).
- Registra **Conteo sobres** para ese servicio.
- Con todos los datos guardados, puede ver el **Consolidado** y generar el **Informe Final**.

---

## Reportes: Consolidado e Informe Final

### Consolidado (por fecha y sede)

- **AUDITORIO:** tabla por áreas (A1…A4) con: sillas del área, sillas vacías, total personas, total niños; columna TOTALES.
- **SERVIDORES:** tabla por áreas con cada tipo de rol y total por área y total general.
- **PARQUEADERO** (solo Villa Grande): carros, motos, bicicletas, total vehículos.

No incluye texto narrativo; es la tabla numérica que resume lo capturado por área.

### Informe final (por servicio)

- Encabezado: título “Informe de Servicio Grupo 3”, fecha, N° servicio, sede, día y hora.
- **1. Asistencia personas:** En sillas, En gradas (si aplica), Niños auditorio, Niños Iglekids, Total auditorio, Área servidores (desglose de roles), Total personas iglesia.
- **2. Vehículos** (si aplica): carros, motos, bicicletas, total.
- **3. Ofrendas:** canastas entregadas; sobres (Ofrenda, Protemplo, Iglekids) con inicial, recibidos, total, entregados, final.

El sistema debe calcular totales a partir de los datos guardados (por área y por tipo) y mostrarlos en este formato.

---

## Stack técnico

- **Backend:** Laravel (PHP)  
- **Frontend:** Vue 3 + Inertia.js  
- **Auth / UI:** Laravel Jetstream (Fortify, equipos opcionales)  
- **Estilos:** Tailwind CSS  
- **Base de datos:** MySQL (según `config/database.php` y `.env`)  
- **Build:** Vite  

---

## Plan de implementación

El detalle del plan (modelos, migraciones, controladores, páginas y orden de desarrollo) está en **[PLAN.md](./PLAN.md)**.

Resumen de fases:

1. **Modelo de datos y sedes**  
   Sedes, áreas por sede, servicios (fecha, sede, número).

2. **Captura por área**  
   Auditorio, servidores, Iglekids, parqueadero (A4 Villa Grande), primer/segundo conteo.

3. **Conteo de sobres**  
   Inventario por tipo y canastas por servicio.

4. **Consolidado**  
   Cálculo y vista del consolidado por fecha/sede.

5. **Informe final**  
   Cálculo y vista/exportación del informe final por servicio.

6. **Menú y permisos**  
   Menú general por sede y control de acceso por rol/sede si se requiere.

Para ejecutar el proyecto en local: ver [PLAN.md](./PLAN.md) (sección de requisitos y comandos).
