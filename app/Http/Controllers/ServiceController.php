<?php

namespace App\Http\Controllers;

use App\Models\Sede;
use App\Repositories\ServiceRepository;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
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
        $filters = [
            'sede' => $request->input('sede'),
            'estado' => $request->input('estado'),
            'busqueda' => $request->input('busqueda'),
            'fecha_inicio' => $request->input('fecha_inicio'),
            'fecha_fin' => $request->input('fecha_fin'),
        ];

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
        
        return Inertia::render('Servicios/Create', [
            'sedes' => $sedes,
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

        // TODO: Obtener los conteos relacionados cuando se implemente
        $conteos = [
            'primer_conteo' => null,
            'area_a1' => null,
            'area_a2' => null,
            'area_a3' => null,
            'area_a4' => null,
            'conteo_sobres' => null,
        ];

        return Inertia::render('Servicios/Show', [
            'servicio' => $servicio,
            'conteos' => $conteos,
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

        return Inertia::render('Servicios/Edit', [
            'servicio' => $servicio,
            'sedes' => $sedes,
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
