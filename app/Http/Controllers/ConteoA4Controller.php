<?php

namespace App\Http\Controllers;

use App\Http\Traits\RequiresService;
use App\Models\Service;
use App\Repositories\ConteoA4Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ConteoA4Controller extends Controller
{
    use RequiresService;

    protected ConteoA4Repository $conteoA4Repository;

    public function __construct(ConteoA4Repository $conteoA4Repository)
    {
        $this->conteoA4Repository = $conteoA4Repository;
    }

    /**
     * Display the form for conteo A4.
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

            // Obtener conteo A4 existente si existe
            $conteoA4 = $this->conteoA4Repository->findByServicioId($servicio->id);

            // Datos iniciales o existentes
            $data = null;
            $completado = false;
            if ($conteoA4) {
                $data = $conteoA4->areas;
                $completado = $conteoA4->completado;
            }

            return Inertia::render('Areas/A4', [
                'servicio' => [
                    'id' => $servicio->id,
                    'sede' => $servicio->sede->nombre ?? $servicio->sede,
                    'fecha' => $servicio->fecha->format('Y-m-d'),
                    'numero_servicio' => $servicio->numero_servicio,
                ],
                'conteoA4' => $data ? array_merge($data, ['completado' => $completado]) : null,
                'servicio_id' => $servicio->id,
            ]);

        } catch (Exception $e) {
            Log::error('Error al cargar conteo A4: ' . $e->getMessage(), [
                'servicio_id' => $request->input('servicio_id'),
                'exception' => $e,
            ]);

            return Inertia::render('Areas/A4', [
                'error' => 'Error al cargar el conteo A4',
            ]);
        }
    }

    /**
     * Store or update conteo A4.
     */
    public function store(Request $request)
    {
        try {
            // Validar datos
            $validated = $request->validate([
                'servicio_id' => 'required|exists:services,id',
                'exteriores' => 'required|array',
                'exteriores.servidores' => 'required|integer|min:0',
                'exteriores.logistica' => 'required|integer|min:0',
                'exteriores.coffee' => 'required|integer|min:0',
                'exteriores.container' => 'required|integer|min:0',
                'vehiculos' => 'required|array',
                'vehiculos.carros' => 'required|integer|min:0',
                'vehiculos.motos' => 'required|integer|min:0',
                'vehiculos.bicicletas' => 'required|integer|min:0',
                'iglekids' => 'required|array',
                'iglekids.coordinadoras' => 'required|integer|min:0',
                'iglekids.supervisoras' => 'required|integer|min:0',
                'iglekids.maestros' => 'required|integer|min:0',
                'iglekids.recrearte' => 'required|integer|min:0',
                'iglekids.regikids' => 'required|integer|min:0',
                'iglekids.logikids' => 'required|integer|min:0',
                'iglekids.saludKids' => 'required|integer|min:0',
                'iglekids.yoSoy' => 'required|integer|min:0',
                'iglekids.ninos' => 'required|integer|min:0',
                'completado' => 'sometimes|boolean',
            ]);

            // Crear estructura de áreas para guardar
            $areas = [
                'exteriores' => $validated['exteriores'],
                'vehiculos' => $validated['vehiculos'],
                'iglekids' => $validated['iglekids'],
            ];

            // Calcular totales
            $totalExteriores = array_sum($validated['exteriores']);
            $totalVehiculos = array_sum($validated['vehiculos']);
            $totalIglekidsPersonal = $validated['iglekids']['coordinadoras'] 
                + $validated['iglekids']['supervisoras']
                + $validated['iglekids']['maestros']
                + $validated['iglekids']['recrearte']
                + $validated['iglekids']['regikids']
                + $validated['iglekids']['logikids']
                + $validated['iglekids']['saludKids']
                + $validated['iglekids']['yoSoy'];

            // Crear o actualizar conteo A4
            $conteoA4 = $this->conteoA4Repository->createOrUpdate(
                $validated['servicio_id'],
                [
                    'areas' => $areas,
                    'completado' => $validated['completado'] ?? false,
                ]
            );

            return redirect()->back()->with('success', [
                'message' => 'Conteo A4 guardado exitosamente',
                'data' => [
                    'total_exteriores' => $totalExteriores,
                    'total_vehiculos' => $totalVehiculos,
                    'total_iglekids_personal' => $totalIglekidsPersonal,
                    'total_ninos' => $validated['iglekids']['ninos'],
                ],
            ]);

        } catch (ValidationException $e) {
            Log::warning('Validación fallida en conteo A4', [
                'errors' => $e->errors(),
                'data' => $request->all(),
            ]);
            throw $e;

        } catch (Exception $e) {
            Log::error('Error al guardar conteo A4: ' . $e->getMessage(), [
                'data' => $request->all(),
                'exception' => $e,
            ]);

            return redirect()->back()->withErrors([
                'error' => 'Error al guardar el conteo A4. Por favor, intente de nuevo.',
            ]);
        }
    }

    /**
     * Get conteo A4 statistics.
     */
    public function show(int $servicioId)
    {
        try {
            $conteoA4 = $this->conteoA4Repository->findByServicioId($servicioId);

            if (!$conteoA4) {
                return response()->json([
                    'message' => 'No se encontró el conteo A4',
                ], 404);
            }

            return response()->json([
                'data' => [
                    'id' => $conteoA4->id,
                    'servicio_id' => $conteoA4->servicio_id,
                    'areas' => $conteoA4->areas,
                    'total_adultos' => $conteoA4->total_adultos,
                    'total_ninos' => $conteoA4->total_ninos,
                    'total_asistencia' => $conteoA4->total_asistencia,
                    'completado' => $conteoA4->completado,
                    'actualizado_en' => $conteoA4->updated_at,
                ],
            ]);

        } catch (Exception $e) {
            Log::error('Error al obtener conteo A4: ' . $e->getMessage(), [
                'servicio_id' => $servicioId,
                'exception' => $e,
            ]);

            return response()->json([
                'message' => 'Error al obtener el conteo A4',
            ], 500);
        }
    }

    /**
     * Delete conteo A4.
     */
    public function destroy(int $id)
    {
        try {
            $deleted = $this->conteoA4Repository->delete($id);

            if ($deleted) {
                return redirect()->back()->with('success', [
                    'message' => 'Conteo A4 eliminado exitosamente',
                ]);
            }

            return redirect()->back()->withErrors([
                'error' => 'No se pudo eliminar el conteo A4',
            ]);

        } catch (Exception $e) {
            Log::error('Error al eliminar conteo A4: ' . $e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);

            return redirect()->back()->withErrors([
                'error' => 'Error al eliminar el conteo A4. Por favor, intente de nuevo.',
            ]);
        }
    }
}
