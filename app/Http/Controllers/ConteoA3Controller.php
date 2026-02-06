<?php

namespace App\Http\Controllers;

use App\Http\Traits\RequiresService;
use App\Models\Service;
use App\Repositories\ConteoA3Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ConteoA3Controller extends Controller
{
    use RequiresService;

    protected ConteoA3Repository $conteoA3Repository;

    public function __construct(ConteoA3Repository $conteoA3Repository)
    {
        $this->conteoA3Repository = $conteoA3Repository;
    }

    /**
     * Display the form for conteo A3.
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

            // Obtener conteo A3 existente si existe
            $conteoA3 = $this->conteoA3Repository->findByServicioId($servicio->id);

            // Datos iniciales o existentes
            $data = null;
            $completado = false;
            if ($conteoA3) {
                $data = $conteoA3->areas;
                $completado = $conteoA3->completado;
            }

            return Inertia::render('Areas/A3', [
                'servicio' => [
                    'id' => $servicio->id,
                    'sede' => $servicio->sede->nombre ?? $servicio->sede,
                    'fecha' => $servicio->fecha->format('Y-m-d'),
                    'numero_servicio' => $servicio->numero_servicio,
                ],
                'conteoA3' => $data ? array_merge($data, ['completado' => $completado]) : null,
                'servicio_id' => $servicio->id,
            ]);

        } catch (Exception $e) {
            Log::error('Error al cargar conteo A3: ' . $e->getMessage(), [
                'servicio_id' => $request->input('servicio_id'),
                'exception' => $e,
            ]);

            return Inertia::render('Areas/A3', [
                'error' => 'Error al cargar el conteo A3',
            ]);
        }
    }

    /**
     * Store or update conteo A3.
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
                'servidores.consolidacion' => 'required|integer|min:0',
                'servidores.logistica' => 'required|integer|min:0',
                'completado' => 'sometimes|boolean',
            ]);

            // Crear estructura de áreas para guardar
            $areas = [
                'sillas' => $validated['sillas'],
                'servidores' => $validated['servidores'],
            ];

            // Calcular totales
            $totalAdultos = $validated['sillas']['totalPersonas'] - $validated['sillas']['totalNinos'];
            $totalNinos = $validated['sillas']['totalNinos'];
            $totalServidores = $validated['servidores']['servidores'] 
                + $validated['servidores']['consolidacion']
                + $validated['servidores']['logistica'];

            // Crear o actualizar conteo A3
            $conteoA3 = $this->conteoA3Repository->createOrUpdate(
                $validated['servicio_id'],
                [
                    'areas' => $areas,
                    'completado' => $validated['completado'] ?? false,
                ]
            );

            return redirect()->back()->with('success', [
                'message' => 'Conteo A3 guardado exitosamente',
                'data' => [
                    'total_personas' => $validated['sillas']['totalPersonas'],
                    'total_servidores' => $totalServidores,
                ],
            ]);

        } catch (ValidationException $e) {
            Log::warning('Validación fallida en conteo A3', [
                'errors' => $e->errors(),
                'data' => $request->all(),
            ]);
            throw $e;

        } catch (Exception $e) {
            Log::error('Error al guardar conteo A3: ' . $e->getMessage(), [
                'data' => $request->all(),
                'exception' => $e,
            ]);

            return redirect()->back()->withErrors([
                'error' => 'Error al guardar el conteo A3. Por favor, intente de nuevo.',
            ]);
        }
    }

    /**
     * Get conteo A3 statistics.
     */
    public function show(int $servicioId)
    {
        try {
            $conteoA3 = $this->conteoA3Repository->findByServicioId($servicioId);

            if (!$conteoA3) {
                return response()->json([
                    'message' => 'No se encontró el conteo A3',
                ], 404);
            }

            return response()->json([
                'data' => [
                    'id' => $conteoA3->id,
                    'servicio_id' => $conteoA3->servicio_id,
                    'areas' => $conteoA3->areas,
                    'total_adultos' => $conteoA3->total_adultos,
                    'total_ninos' => $conteoA3->total_ninos,
                    'total_asistencia' => $conteoA3->total_asistencia,
                    'completado' => $conteoA3->completado,
                    'actualizado_en' => $conteoA3->updated_at,
                ],
            ]);

        } catch (Exception $e) {
            Log::error('Error al obtener conteo A3: ' . $e->getMessage(), [
                'servicio_id' => $servicioId,
                'exception' => $e,
            ]);

            return response()->json([
                'message' => 'Error al obtener el conteo A3',
            ], 500);
        }
    }

    /**
     * Delete conteo A3.
     */
    public function destroy(int $id)
    {
        try {
            $deleted = $this->conteoA3Repository->delete($id);

            if ($deleted) {
                return redirect()->back()->with('success', [
                    'message' => 'Conteo A3 eliminado exitosamente',
                ]);
            }

            return redirect()->back()->withErrors([
                'error' => 'No se pudo eliminar el conteo A3',
            ]);

        } catch (Exception $e) {
            Log::error('Error al eliminar conteo A3: ' . $e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);

            return redirect()->back()->withErrors([
                'error' => 'Error al eliminar el conteo A3. Por favor, intente de nuevo.',
            ]);
        }
    }
}
