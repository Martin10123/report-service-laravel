<?php

namespace App\Http\Controllers;

use App\Http\Traits\RequiresService;
use App\Models\Service;
use App\Repositories\ConteoDeSobresRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ConteoDeSobresController extends Controller
{
    use RequiresService;

    protected ConteoDeSobresRepository $conteoSobresRepository;

    public function __construct(ConteoDeSobresRepository $conteoSobresRepository)
    {
        $this->conteoSobresRepository = $conteoSobresRepository;
    }

    /**
     * Display the form for conteo de sobres.
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

            // Obtener conteo de sobres existente si existe
            $conteoSobres = $this->conteoSobresRepository->findByServicioId($servicio->id);

            // Datos iniciales o existentes
            $data = null;
            if ($conteoSobres) {
                $data = [
                    'canastas' => $conteoSobres->canastas,
                    'ofrendas' => $conteoSobres->ofrendas,
                    'protemplo' => $conteoSobres->protemplo,
                    'iglekids' => $conteoSobres->iglekids,
                    'completado' => $conteoSobres->completado,
                ];
            }

            return Inertia::render('ConteoDeSobres', [
                'servicio' => [
                    'id' => $servicio->id,
                    'sede' => $servicio->sede->nombre ?? $servicio->sede,
                    'fecha' => $servicio->fecha->format('Y-m-d'),
                    'numero_servicio' => $servicio->numero_servicio,
                ],
                'conteoSobres' => $data,
                'servicio_id' => $servicio->id,
            ]);

        } catch (Exception $e) {
            Log::error('Error al cargar conteo de sobres: ' . $e->getMessage(), [
                'servicio_id' => $request->input('servicio_id'),
                'exception' => $e,
            ]);

            return Inertia::render('ConteoDeSobres', [
                'error' => 'Error al cargar el conteo de sobres',
            ]);
        }
    }

    /**
     * Store or update conteo de sobres.
     */
    public function store(Request $request)
    {
        try {
            // Validar datos
            $validated = $request->validate([
                'servicio_id' => 'required|exists:services,id',
                'canastas' => 'required|array',
                'canastas.entregadas' => 'required|integer|min:0',
                'ofrendas' => 'required|array',
                'ofrendas.inicial' => 'required|integer|min:0',
                'ofrendas.recibidos' => 'required|integer|min:0',
                'ofrendas.entregados' => 'required|integer|min:0',
                'protemplo' => 'required|array',
                'protemplo.inicial' => 'required|integer|min:0',
                'protemplo.recibidos' => 'required|integer|min:0',
                'protemplo.entregados' => 'required|integer|min:0',
                'iglekids' => 'required|array',
                'iglekids.inicial' => 'required|integer|min:0',
                'iglekids.recibidos' => 'required|integer|min:0',
                'iglekids.entregados' => 'required|integer|min:0',
                'completado' => 'sometimes|boolean',
            ]);

            // Validar que los entregados no superen el total
            $totalOfrendas = $validated['ofrendas']['inicial'] + $validated['ofrendas']['recibidos'];
            if ($validated['ofrendas']['entregados'] > $totalOfrendas) {
                return redirect()->back()->withErrors([
                    'ofrendas.entregados' => 'Los sobres entregados no pueden superar el total disponible.',
                ]);
            }

            $totalProtemplo = $validated['protemplo']['inicial'] + $validated['protemplo']['recibidos'];
            if ($validated['protemplo']['entregados'] > $totalProtemplo) {
                return redirect()->back()->withErrors([
                    'protemplo.entregados' => 'Los sobres entregados no pueden superar el total disponible.',
                ]);
            }

            $totalIglekids = $validated['iglekids']['inicial'] + $validated['iglekids']['recibidos'];
            if ($validated['iglekids']['entregados'] > $totalIglekids) {
                return redirect()->back()->withErrors([
                    'iglekids.entregados' => 'Los sobres entregados no pueden superar el total disponible.',
                ]);
            }

            // Crear o actualizar conteo de sobres
            $conteoSobres = $this->conteoSobresRepository->createOrUpdate(
                $validated['servicio_id'],
                [
                    'canastas' => $validated['canastas'],
                    'ofrendas' => $validated['ofrendas'],
                    'protemplo' => $validated['protemplo'],
                    'iglekids' => $validated['iglekids'],
                    'completado' => $validated['completado'] ?? false,
                ]
            );

            return redirect()->back()->with('success', [
                'message' => 'Conteo de sobres guardado exitosamente',
                'data' => [
                    'canastas_entregadas' => $conteoSobres->canastas['entregadas'],
                    'final_ofrendas' => $conteoSobres->final_ofrendas,
                    'final_protemplo' => $conteoSobres->final_protemplo,
                    'final_iglekids' => $conteoSobres->final_iglekids,
                ],
            ]);

        } catch (ValidationException $e) {
            Log::warning('Validación fallida en conteo de sobres', [
                'errors' => $e->errors(),
                'data' => $request->all(),
            ]);
            throw $e;

        } catch (Exception $e) {
            Log::error('Error al guardar conteo de sobres: ' . $e->getMessage(), [
                'data' => $request->all(),
                'exception' => $e,
            ]);

            return redirect()->back()->withErrors([
                'error' => 'Error al guardar el conteo de sobres. Por favor, intente de nuevo.',
            ]);
        }
    }

    /**
     * Get conteo de sobres statistics.
     */
    public function show(int $servicioId)
    {
        try {
            $conteoSobres = $this->conteoSobresRepository->findByServicioId($servicioId);

            if (!$conteoSobres) {
                return response()->json([
                    'message' => 'No se encontró el conteo de sobres',
                ], 404);
            }

            return response()->json([
                'data' => [
                    'id' => $conteoSobres->id,
                    'servicio_id' => $conteoSobres->servicio_id,
                    'canastas' => $conteoSobres->canastas,
                    'ofrendas' => $conteoSobres->ofrendas,
                    'protemplo' => $conteoSobres->protemplo,
                    'iglekids' => $conteoSobres->iglekids,
                    'totales' => [
                        'ofrendas' => [
                            'total' => $conteoSobres->total_ofrendas,
                            'final' => $conteoSobres->final_ofrendas,
                        ],
                        'protemplo' => [
                            'total' => $conteoSobres->total_protemplo,
                            'final' => $conteoSobres->final_protemplo,
                        ],
                        'iglekids' => [
                            'total' => $conteoSobres->total_iglekids,
                            'final' => $conteoSobres->final_iglekids,
                        ],
                    ],
                    'completado' => $conteoSobres->completado,
                    'actualizado_en' => $conteoSobres->updated_at,
                ],
            ]);

        } catch (Exception $e) {
            Log::error('Error al obtener conteo de sobres: ' . $e->getMessage(), [
                'servicio_id' => $servicioId,
                'exception' => $e,
            ]);

            return response()->json([
                'message' => 'Error al obtener el conteo de sobres',
            ], 500);
        }
    }

    /**
     * Delete conteo de sobres.
     */
    public function destroy(int $id)
    {
        try {
            $deleted = $this->conteoSobresRepository->delete($id);

            if ($deleted) {
                return redirect()->back()->with('success', [
                    'message' => 'Conteo de sobres eliminado exitosamente',
                ]);
            }

            return redirect()->back()->withErrors([
                'error' => 'No se pudo eliminar el conteo de sobres',
            ]);

        } catch (Exception $e) {
            Log::error('Error al eliminar conteo de sobres: ' . $e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);

            return redirect()->back()->withErrors([
                'error' => 'Error al eliminar el conteo de sobres. Por favor, intente de nuevo.',
            ]);
        }
    }
}
