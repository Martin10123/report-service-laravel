<?php

namespace App\Http\Controllers;

use App\Http\Traits\RequiresService;
use App\Models\Service;
use App\Repositories\ConteoA1Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ConteoA1Controller extends Controller
{
    use RequiresService;

    protected ConteoA1Repository $conteoA1Repository;

    public function __construct(ConteoA1Repository $conteoA1Repository)
    {
        $this->conteoA1Repository = $conteoA1Repository;
    }

    /**
     * Display the form for conteo A1.
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

            // Obtener conteo A1 existente si existe
            $conteoA1 = $this->conteoA1Repository->findByServicioId($servicio->id);

            // Datos iniciales o existentes
            $data = null;
            $completado = false;
            if ($conteoA1) {
                $data = $conteoA1->areas;
                $completado = $conteoA1->completado;
            }

            return Inertia::render('Areas/A1', [
                'servicio' => [
                    'id' => $servicio->id,
                    'sede' => $servicio->sede->nombre ?? $servicio->sede,
                    'fecha' => $servicio->fecha->format('Y-m-d'),
                    'numero_servicio' => $servicio->numero_servicio,
                ],
                'conteoA1' => $data ? array_merge($data, ['completado' => $completado]) : null,
                'servicio_id' => $servicio->id,
            ]);

        } catch (Exception $e) {
            Log::error('Error al cargar conteo A1: ' . $e->getMessage(), [
                'servicio_id' => $request->input('servicio_id'),
                'exception' => $e,
            ]);

            return Inertia::render('Areas/A1', [
                'error' => 'Error al cargar el conteo A1',
            ]);
        }
    }

    /**
     * Store or update conteo A1.
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
                'servidores' => 'required|array',
                'servidores.servidores' => 'required|integer|min:0',
                'servidores.comunicaciones' => 'required|integer|min:0',
                'servidores.logistica' => 'required|integer|min:0',
                'servidores.alabanza' => 'required|integer|min:0',
                'servidorasPastora' => 'required|array',
                'completado' => 'sometimes|boolean',
            ]);

            // Crear estructura de áreas para guardar
            $areas = [
                'sillas' => $validated['sillas'],
                'servidores' => $validated['servidores'],
                'servidorasPastora' => array_values(array_filter($validated['servidorasPastora'], fn($n) => !empty(trim($n)))),
            ];

            // Calcular totales
            $totalAdultos = $validated['sillas']['totalPersonas'] - $validated['sillas']['totalNinos'];
            $totalNinos = $validated['sillas']['totalNinos'];
            $totalServidores = $validated['servidores']['servidores'] 
                + $validated['servidores']['comunicaciones']
                + $validated['servidores']['logistica']
                + $validated['servidores']['alabanza']
                + count($areas['servidorasPastora']);

            // Crear o actualizar conteo A1
            $conteoA1 = $this->conteoA1Repository->createOrUpdate(
                $validated['servicio_id'],
                [
                    'areas' => $areas,
                    'completado' => $validated['completado'] ?? false,
                ]
            );

            return redirect()->back()->with('success', [
                'message' => 'Conteo A1 guardado exitosamente',
                'data' => [
                    'total_personas' => $validated['sillas']['totalPersonas'],
                    'total_servidores' => $totalServidores,
                ],
            ]);

        } catch (ValidationException $e) {
            Log::warning('Validación fallida en conteo A1', [
                'errors' => $e->errors(),
                'data' => $request->all(),
            ]);
            throw $e;

        } catch (Exception $e) {
            Log::error('Error al guardar conteo A1: ' . $e->getMessage(), [
                'data' => $request->all(),
                'exception' => $e,
            ]);

            return redirect()->back()->withErrors([
                'error' => 'Error al guardar el conteo A1. Por favor, intente de nuevo.',
            ]);
        }
    }

    /**
     * Get conteo A1 statistics.
     */
    public function show(int $servicioId)
    {
        try {
            $conteoA1 = $this->conteoA1Repository->findByServicioId($servicioId);

            if (!$conteoA1) {
                return response()->json([
                    'message' => 'No se encontró el conteo A1',
                ], 404);
            }

            return response()->json([
                'data' => [
                    'id' => $conteoA1->id,
                    'servicio_id' => $conteoA1->servicio_id,
                    'areas' => $conteoA1->areas,
                    'total_adultos' => $conteoA1->total_adultos,
                    'total_ninos' => $conteoA1->total_ninos,
                    'total_asistencia' => $conteoA1->total_asistencia,
                    'completado' => $conteoA1->completado,
                    'actualizado_en' => $conteoA1->updated_at,
                ],
            ]);

        } catch (Exception $e) {
            Log::error('Error al obtener conteo A1: ' . $e->getMessage(), [
                'servicio_id' => $servicioId,
                'exception' => $e,
            ]);

            return response()->json([
                'message' => 'Error al obtener el conteo A1',
            ], 500);
        }
    }

    /**
     * Delete conteo A1.
     */
    public function destroy(int $id)
    {
        try {
            $deleted = $this->conteoA1Repository->delete($id);

            if ($deleted) {
                return redirect()->back()->with('success', [
                    'message' => 'Conteo A1 eliminado exitosamente',
                ]);
            }

            return redirect()->back()->withErrors([
                'error' => 'No se pudo eliminar el conteo A1',
            ]);

        } catch (Exception $e) {
            Log::error('Error al eliminar conteo A1: ' . $e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);

            return redirect()->back()->withErrors([
                'error' => 'Error al eliminar el conteo A1',
            ]);
        }
    }
}
