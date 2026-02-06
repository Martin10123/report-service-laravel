<?php

namespace App\Http\Controllers;

use App\Models\Sede;
use App\Models\Service;
use App\Repositories\ServiceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    protected ServiceRepository $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        // Si hay una sede seleccionada en sesión, filtrar por ella
        $sedeActualId = session('sede_actual_id');
        
        // Si se solicita limpiar el servicio, hacerlo
        if ($request->has('clear_servicio')) {
            session()->forget('servicio_actual_id');
        }
        
        $filters = [
            'sede' => $request->input('sede'),
            'estado' => $request->input('estado'),
            'busqueda' => $request->input('busqueda'),
            'fecha_inicio' => $request->input('fecha_inicio'),
            'fecha_fin' => $request->input('fecha_fin'),
        ];

        // Si hay sede seleccionada y no hay filtro manual, aplicar filtro automático
        if ($sedeActualId && !$filters['sede']) {
            $filters['sede'] = $sedeActualId;
        }

        $servicios = $this->serviceRepository->getAllPaginated(15, $filters);
        $estadisticas = $this->serviceRepository->getEstadisticas($filters);
        $sedes = Sede::activas()->orderBy('nombre')->get();

        return Inertia::render('Servicios/Index', [
            'servicios' => $servicios,
            'estadisticas' => $estadisticas,
            'filters' => $filters,
            'sedes' => $sedes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $sedes = Sede::activas()->orderBy('nombre')->get();
        
        // Si hay una sede seleccionada, pre-seleccionarla y bloquearla
        $sedeActualId = session('sede_actual_id');
        $sedePreseleccionada = $sedeActualId ? Sede::find($sedeActualId) : null;
        
        return Inertia::render('Servicios/Create', [
            'sedes' => $sedes,
            'sedePreseleccionada' => $sedePreseleccionada,
            'sedeBloqueada' => $sedeActualId ? true : false,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sede_id' => 'required|exists:sedes,id',
            'numero_servicio' => 'nullable|integer|min:1',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        // Validar unicidad de numero_servicio por sede (solo si se proporciona)
        if (!empty($validated['numero_servicio'])) {
            $existe = Service::where('sede_id', $validated['sede_id'])
                ->where('numero_servicio', $validated['numero_servicio'])
                ->exists();

            if ($existe) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['numero_servicio' => 'Este número de servicio ya existe para la sede seleccionada.']);
            }
        }
        
        try {
            DB::beginTransaction();

            $servicio = $this->serviceRepository->create($validated);
            
            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Servicio creado exitosamente.',
                    'data' => $servicio,
                ], 201);
            }

            return redirect()
                ->route('servicios.index')
                ->with('success', 'Servicio creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating service:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el servicio: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el servicio. Por favor, intenta de nuevo.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): Response
    {
        $servicio = $this->serviceRepository->findById($id);

        if (!$servicio) {
            abort(404, 'Servicio no encontrado');
        }

        // Persistir servicio seleccionado en sesión
        session(['servicio_actual_id' => $servicio->id]);

        // Cargar conteos relacionados
        $primerConteo = $servicio->primerConteo;
        $conteoA1 = $servicio->conteoA1;
        $conteoA2 = $servicio->conteoA2;
        $conteoA3 = $servicio->conteoA3;
        $conteoA4 = $servicio->conteoA4;
        $conteoSobres = $servicio->conteoSobres;

        // Obtener las áreas disponibles para la sede desde la BD
        $sede = $servicio->sede;
        
        if ($sede) {
            // Obtener áreas desde la relación en la base de datos como objetos
            $areasDisponibles = $sede->areas()->select('areas.codigo')->pluck('codigo')->toArray();
            $tieneParqueadero = $sede->tiene_parqueadero;
            
            Log::info('Áreas disponibles para sede', [
                'sede_id' => $sede->id,
                'sede_nombre' => $sede->nombre,
                'areas' => $areasDisponibles
            ]);
        } else {
            // Fallback si no hay sede
            $areasDisponibles = [];
            $tieneParqueadero = false;
            Log::warning('Servicio sin sede', ['servicio_id' => $id]);
        }

        $conteos = [
            'primer_conteo' => $primerConteo ? [
                'completado' => $primerConteo->completado,
                'actualizado_en' => $primerConteo->updated_at,
            ] : ['completado' => false, 'actualizado_en' => null],
        ];

        // Agregar solo las áreas disponibles para esta sede
        foreach ($areasDisponibles as $area) {
            $key = 'area_' . strtolower($area);
            
            // Cargar el estado real del conteo según el área
            if ($area === 'A1' && $conteoA1) {
                $conteos[$key] = [
                    'completado' => $conteoA1->completado,
                    'actualizado_en' => $conteoA1->updated_at,
                ];
            } elseif ($area === 'A2' && $conteoA2) {
                $conteos[$key] = [
                    'completado' => $conteoA2->completado,
                    'actualizado_en' => $conteoA2->updated_at,
                ];
            } elseif ($area === 'A3' && $conteoA3) {
                $conteos[$key] = [
                    'completado' => $conteoA3->completado,
                    'actualizado_en' => $conteoA3->updated_at,
                ];
            } elseif ($area === 'A4' && $conteoA4) {
                $conteos[$key] = [
                    'completado' => $conteoA4->completado,
                    'actualizado_en' => $conteoA4->updated_at,
                ];
            } else {
                $conteos[$key] = [
                    'completado' => false, 
                    'actualizado_en' => null
                ];
            }
        }

        // Siempre incluir sobres
        $conteos['sobres'] = $conteoSobres ? [
            'completado' => $conteoSobres->completado,
            'actualizado_en' => $conteoSobres->updated_at,
        ] : ['completado' => false, 'actualizado_en' => null];

        return Inertia::render('Servicios/Show', [
            'servicio' => $servicio,
            'conteos' => $conteos,
            // areasDisponibles ya viene del middleware global, no necesitamos pasarlo aquí
            'tieneParqueadero' => $tieneParqueadero,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): Response
    {
        $servicio = $this->serviceRepository->findById($id);

        if (!$servicio) {
            abort(404, 'Servicio no encontrado');
        }

        $sedes = Sede::activas()->orderBy('nombre')->get();
        
        // Si hay una sede seleccionada en sesión, bloquear la sede
        $sedeActualId = session('sede_actual_id');
        $sedeBloqueada = $sedeActualId ? true : false;

        return Inertia::render('Servicios/Edit', [
            'servicio' => $servicio,
            'sedes' => $sedes,
            'sedeBloqueada' => $sedeBloqueada,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'sede_id' => 'required|exists:sedes,id',
            'numero_servicio' => 'required|integer|min:1',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'estado' => 'nullable|in:activo,finalizado,cancelado',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        // Validar unicidad de numero_servicio por sede (excluyendo el servicio actual)
        $existe = Service::where('sede_id', $validated['sede_id'])
            ->where('numero_servicio', $validated['numero_servicio'])
            ->where('id', '!=', $id)
            ->exists();

        if ($existe) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['numero_servicio' => 'Este número de servicio ya existe para la sede seleccionada.']);
        }

        try {
            DB::beginTransaction();

            $updated = $this->serviceRepository->update($id, $validated);

            if (!$updated) {
                DB::rollBack();
                
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se pudo actualizar el servicio.',
                    ], 404);
                }
                return redirect()
                    ->back()
                    ->with('error', 'No se pudo actualizar el servicio.');
            }

            $servicio = $this->serviceRepository->findById($id);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Servicio actualizado exitosamente.',
                    'data' => $servicio,
                ]);
            }

            return redirect()
                ->route('servicios.index')
                ->with('success', 'Servicio actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el servicio: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el servicio. Por favor, intenta de nuevo.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        try {
            DB::beginTransaction();

            $deleted = $this->serviceRepository->delete($id);

            if (!$deleted) {
                DB::rollBack();
                
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se pudo eliminar el servicio.',
                    ], 404);
                }
                return redirect()
                    ->back()
                    ->with('error', 'No se pudo eliminar el servicio.');
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Servicio eliminado exitosamente.',
                ]);
            }

            return redirect()
                ->route('servicios.index')
                ->with('success', 'Servicio eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar el servicio: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Error al eliminar el servicio. Por favor, intenta de nuevo.');
        }
    }

    /**
     * Cambiar el estado de un servicio.
     */
    public function cambiarEstado(Request $request, int $id)
    {
        $validated = $request->validate([
            'estado' => 'required|in:activo,finalizado,cancelado',
        ]);

        try {
            DB::beginTransaction();

            $updated = $this->serviceRepository->cambiarEstado($id, $validated['estado']);

            if (!$updated) {
                DB::rollBack();
                
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se pudo cambiar el estado del servicio.',
                    ], 404);
                }
                return redirect()
                    ->back()
                    ->with('error', 'No se pudo cambiar el estado del servicio.');
            }

            $servicio = $this->serviceRepository->findById($id);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Estado del servicio actualizado exitosamente.',
                    'data' => $servicio,
                ]);
            }

            return redirect()
                ->back()
                ->with('success', 'Estado del servicio actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al cambiar el estado: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Error al cambiar el estado del servicio. Por favor, intenta de nuevo.');
        }
    }
}
