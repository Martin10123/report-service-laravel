<?php

namespace App\Http\Controllers;

use App\Http\Traits\RequiresService;
use App\Models\Service;
use App\Repositories\ConteoA2Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Exception;

class ConteoA2Controller extends Controller
{
    use RequiresService;

    protected ConteoA2Repository $conteoA2Repository;

    public function __construct(ConteoA2Repository $conteoA2Repository)
    {
        $this->conteoA2Repository = $conteoA2Repository;
    }

    /**
     * Display the form for conteo A2.
     */
    public function index(Request $request)
    {
        try {
            // Usar el trait para obtener el servicio o redirigir
            $servicio = $this->requireService($request);
            
            // Si es una redirección, retornarla
            if ($servicio instanceof \Illuminate\Http\RedirectResponse) {
                return $servicio;
            }

            // Obtener conteo A2 existente si existe
            $conteoA2 = $this->conteoA2Repository->findByServicioId($servicio->id);

            // Datos iniciales o existentes
            $data = null;
            $completado = false;
            if ($conteoA2) {
                $data = $conteoA2->areas;
                $completado = $conteoA2->completado;
            }

            return Inertia::render('Areas/A2', [
                'servicio' => [
                    'id' => $servicio->id,
                    'sede' => $servicio->sede->nombre ?? $servicio->sede,
                    'fecha' => $servicio->fecha->format('Y-m-d'),
                    'numero_servicio' => $servicio->numero_servicio,
                ],
                'conteoA2' => $data ? array_merge($data, ['completado' => $completado]) : null,
                'servicio_id' => $servicio->id,
            ]);

        } catch (Exception $e) {
            Log::error('Error al cargar conteo A2: ' . $e->getMessage(), [
                'servicio_id' => $request->input('servicio_id'),
                'exception' => $e,
            ]);

            return Inertia::render('Areas/A2', [
                'error' => 'Error al cargar el conteo A2',
            ]);
        }
    }

    /**
     * Store or update conteo A2.
     */
    public function store(Request $request)
    {
        try {
            // Validar datos
            $validated = $request->validate([
                'servicio_id' => 'required|exists:services,id',
                'sillas' => 'required|array',
                'sillas.totalSillas' => 'required|integer|min:0',
                'sillas.sillasVacias' => 'required|integer|min:0',
                'sillas.totalPersonas' => 'required|integer|min:0',
                'sillas.totalNinos' => 'required|integer|min:0',
                'servidores' => 'nullable|array',
                'servidores.servidores' => 'nullable|integer|min:0',
                'servidores.logistica' => 'nullable|integer|min:0',
                'servidores.jesusPlace' => 'nullable|integer|min:0',
                'servidores.datafono' => 'nullable|integer|min:0',
                'servidores.ministerial' => 'nullable|integer|min:0',
                'completado' => 'sometimes|boolean',
            ]);

            // Crear estructura de áreas para guardar
            $areas = [
                'sillas' => $validated['sillas'],
                'servidores' => $validated['servidores'] ?? [],
            ];

            // Calcular totales
            $totalAdultos = $validated['sillas']['totalPersonas'] - $validated['sillas']['totalNinos'];
            $totalNinos = $validated['sillas']['totalNinos'];
            $servidoresData = $validated['servidores'] ?? [];
            $totalServidores = ($servidoresData['servidores'] ?? 0)
                + ($servidoresData['logistica'] ?? 0)
                + ($servidoresData['jesusPlace'] ?? 0)
                + ($servidoresData['datafono'] ?? 0)
                + ($servidoresData['ministerial'] ?? 0);

            // Crear o actualizar conteo A2
            $conteoA2 = $this->conteoA2Repository->createOrUpdate(
                $validated['servicio_id'],
                [
                    'areas' => $areas,
                    'completado' => $validated['completado'] ?? false,
                ]
            );

            return redirect()->back()->with('success', [
                'message' => 'Conteo A2 guardado exitosamente',
                'data' => [
                    'total_personas' => $validated['sillas']['totalPersonas'],
                    'total_servidores' => $totalServidores,
                ],
            ]);

        } catch (ValidationException $e) {
            Log::warning('Validación fallida en conteo A2', [
                'errors' => $e->errors(),
                'data' => $request->all(),
            ]);
            throw $e;

        } catch (Exception $e) {
            Log::error('Error al guardar conteo A2: ' . $e->getMessage(), [
                'data' => $request->all(),
                'exception' => $e,
            ]);

            return redirect()->back()->withErrors([
                'error' => 'Error al guardar el conteo A2. Por favor, intente de nuevo.',
            ]);
        }
    }

    /**
     * Get conteo A2 statistics.
     */
    public function show(int $servicioId)
    {
        try {
            $conteoA2 = $this->conteoA2Repository->findByServicioId($servicioId);

            if (!$conteoA2) {
                return response()->json([
                    'message' => 'No se encontró el conteo A2',
                ], 404);
            }

            return response()->json([
                'data' => [
                    'id' => $conteoA2->id,
                    'servicio_id' => $conteoA2->servicio_id,
                    'areas' => $conteoA2->areas,
                    'total_adultos' => $conteoA2->total_adultos,
                    'total_ninos' => $conteoA2->total_ninos,
                    'total_asistencia' => $conteoA2->total_asistencia,
                    'completado' => $conteoA2->completado,
                    'actualizado_en' => $conteoA2->updated_at,
                ],
            ]);

        } catch (Exception $e) {
            Log::error('Error al obtener conteo A2: ' . $e->getMessage(), [
                'servicio_id' => $servicioId,
                'exception' => $e,
            ]);

            return response()->json([
                'message' => 'Error al obtener el conteo A2',
            ], 500);
        }
    }

    /**
     * Delete conteo A2.
     */
    public function destroy(int $id)
    {
        try {
            $deleted = $this->conteoA2Repository->delete($id);

            if ($deleted) {
                return redirect()->back()->with('success', [
                    'message' => 'Conteo A2 eliminado exitosamente',
                ]);
            }

            return redirect()->back()->withErrors([
                'error' => 'No se pudo eliminar el conteo A2',
            ]);

        } catch (Exception $e) {
            Log::error('Error al eliminar conteo A2: ' . $e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);

            return redirect()->back()->withErrors([
                'error' => 'Error al eliminar el conteo A2',
            ]);
        }
    }
}
