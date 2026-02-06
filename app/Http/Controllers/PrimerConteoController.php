<?php

namespace App\Http\Controllers;

use App\Http\Traits\RequiresService;
use App\Models\Service;
use App\Repositories\PrimerConteoRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Exception;

class PrimerConteoController extends Controller
{
    use RequiresService;

    protected PrimerConteoRepository $primerConteoRepository;

    public function __construct(PrimerConteoRepository $primerConteoRepository)
    {
        $this->primerConteoRepository = $primerConteoRepository;
    }

    /**
     * Display the form for primer conteo.
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

            // Obtener primer conteo existente si existe
            $primerConteo = $this->primerConteoRepository->findByServicioId($servicio->id);

            // Si no existe, crear estructura inicial con las áreas del servicio
            if (!$primerConteo) {
                $areas = collect(['A1', 'A2', 'A3', 'A4'])->map(function ($area) {
                    return [
                        'area' => $area,
                        'adultos' => 0,
                        'ninos' => 0,
                    ];
                })->toArray();
            } else {
                $areas = $primerConteo->areas;
            }

            return Inertia::render('Servicios/PrimerConteo', [
                'servicio' => [
                    'id' => $servicio->id,
                    'sede' => $servicio->sede->nombre ?? $servicio->sede,
                    'fecha' => $servicio->fecha->format('Y-m-d'),
                    'numero_servicio' => $servicio->numero_servicio,
                    'areas' => ['A1', 'A2', 'A3', 'A4'],
                ],
                'primerConteo' => $primerConteo ? [
                    'id' => $primerConteo->id,
                    'areas' => $primerConteo->areas,
                    'total_adultos' => $primerConteo->total_adultos,
                    'total_ninos' => $primerConteo->total_ninos,
                    'total_asistencia' => $primerConteo->total_asistencia,
                    'completado' => $primerConteo->completado,
                ] : null,
                'areas' => $areas,
            ]);

        } catch (Exception $e) {
            Log::error('Error al cargar primer conteo: ' . $e->getMessage(), [
                'servicio_id' => $request->input('servicio_id'),
                'exception' => $e,
            ]);

            return Inertia::render('Servicios/PrimerConteo', [
                'error' => 'Error al cargar el primer conteo',
            ]);
        }
    }

    /**
     * Store or update primer conteo.
     */
    public function store(Request $request)
    {
        try {
            // Validar datos
            $validated = $request->validate([
                'servicio_id' => 'required|exists:services,id',
                'areas' => 'required|array|min:1',
                'areas.*.area' => 'required|string',
                'areas.*.adultos' => 'required|integer|min:0',
                'areas.*.ninos' => 'required|integer|min:0',
                'completado' => 'sometimes|boolean',
            ]);

            // Crear o actualizar primer conteo
            $primerConteo = $this->primerConteoRepository->createOrUpdate(
                $validated['servicio_id'],
                [
                    'areas' => $validated['areas'],
                    'completado' => $validated['completado'] ?? false,
                ]
            );

            return redirect()->back()->with('success', [
                'message' => 'Primer conteo guardado exitosamente',
                'data' => [
                    'total_adultos' => $primerConteo->total_adultos,
                    'total_ninos' => $primerConteo->total_ninos,
                    'total_asistencia' => $primerConteo->total_asistencia,
                ],
            ]);

        } catch (ValidationException $e) {
            Log::warning('Validación fallida en primer conteo', [
                'errors' => $e->errors(),
                'data' => $request->all(),
            ]);
            throw $e;

        } catch (Exception $e) {
            Log::error('Error al guardar primer conteo: ' . $e->getMessage(), [
                'data' => $request->all(),
                'exception' => $e,
            ]);

            return redirect()->back()->withErrors([
                'error' => 'Error al guardar el primer conteo. Por favor, intente de nuevo.',
            ]);
        }
    }

    /**
     * Get primer conteo statistics.
     */
    public function show(int $servicioId)
    {
        try {
            $primerConteo = $this->primerConteoRepository->findByServicioId($servicioId);

            if (!$primerConteo) {
                return response()->json([
                    'message' => 'No se encontró el primer conteo',
                ], 404);
            }

            return response()->json([
                'data' => [
                    'id' => $primerConteo->id,
                    'servicio_id' => $primerConteo->servicio_id,
                    'areas' => $primerConteo->areas,
                    'total_adultos' => $primerConteo->total_adultos,
                    'total_ninos' => $primerConteo->total_ninos,
                    'total_asistencia' => $primerConteo->total_asistencia,
                    'completado' => $primerConteo->completado,
                    'actualizado_en' => $primerConteo->updated_at,
                ],
            ]);

        } catch (Exception $e) {
            Log::error('Error al obtener primer conteo: ' . $e->getMessage(), [
                'servicio_id' => $servicioId,
                'exception' => $e,
            ]);

            return response()->json([
                'message' => 'Error al obtener el primer conteo',
            ], 500);
        }
    }

    /**
     * Delete primer conteo.
     */
    public function destroy(int $id)
    {
        try {
            $deleted = $this->primerConteoRepository->delete($id);

            if ($deleted) {
                return redirect()->back()->with('success', [
                    'message' => 'Primer conteo eliminado exitosamente',
                ]);
            }

            return redirect()->back()->withErrors([
                'error' => 'No se pudo eliminar el primer conteo',
            ]);

        } catch (Exception $e) {
            Log::error('Error al eliminar primer conteo: ' . $e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);

            return redirect()->back()->withErrors([
                'error' => 'Error al eliminar el primer conteo',
            ]);
        }
    }
}
