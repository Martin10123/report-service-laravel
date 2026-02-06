<?php

namespace App\Repositories;

use App\Models\ConteoDeSobres;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ConteoDeSobresRepository
{
    /**
     * Obtener conteo de sobres por servicio ID.
     *
     * @param int $servicioId
     * @return ConteoDeSobres|null
     */
    public function findByServicioId(int $servicioId): ?ConteoDeSobres
    {
        return ConteoDeSobres::where('servicio_id', $servicioId)
            ->with('servicio.sede')
            ->first();
    }

    /**
     * Crear un nuevo conteo de sobres.
     *
     * @param array $data
     * @return ConteoDeSobres
     * @throws Exception
     */
    public function create(array $data): ConteoDeSobres
    {
        try {
            DB::beginTransaction();

            // Calcular totales automáticamente
            $totales = ConteoDeSobres::calcularTotales(
                $data['ofrendas'],
                $data['protemplo'],
                $data['iglekids']
            );
            
            $conteoSobres = ConteoDeSobres::create([
                'servicio_id' => $data['servicio_id'],
                'canastas' => $data['canastas'],
                'ofrendas' => $data['ofrendas'],
                'protemplo' => $data['protemplo'],
                'iglekids' => $data['iglekids'],
                'total_ofrendas' => $totales['total_ofrendas'],
                'final_ofrendas' => $totales['final_ofrendas'],
                'total_protemplo' => $totales['total_protemplo'],
                'final_protemplo' => $totales['final_protemplo'],
                'total_iglekids' => $totales['total_iglekids'],
                'final_iglekids' => $totales['final_iglekids'],
                'completado' => $data['completado'] ?? false,
            ]);

            DB::commit();

            // Recargar relaciones
            return $conteoSobres->load('servicio.sede');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear conteo de sobres: ' . $e->getMessage(), [
                'data' => $data,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Actualizar un conteo de sobres existente.
     *
     * @param int $id
     * @param array $data
     * @return ConteoDeSobres
     * @throws Exception
     */
    public function update(int $id, array $data): ConteoDeSobres
    {
        try {
            DB::beginTransaction();

            $conteoSobres = ConteoDeSobres::findOrFail($id);

            // Calcular totales automáticamente
            $totales = ConteoDeSobres::calcularTotales(
                $data['ofrendas'],
                $data['protemplo'],
                $data['iglekids']
            );

            $conteoSobres->update([
                'canastas' => $data['canastas'],
                'ofrendas' => $data['ofrendas'],
                'protemplo' => $data['protemplo'],
                'iglekids' => $data['iglekids'],
                'total_ofrendas' => $totales['total_ofrendas'],
                'final_ofrendas' => $totales['final_ofrendas'],
                'total_protemplo' => $totales['total_protemplo'],
                'final_protemplo' => $totales['final_protemplo'],
                'total_iglekids' => $totales['total_iglekids'],
                'final_iglekids' => $totales['final_iglekids'],
                'completado' => $data['completado'] ?? $conteoSobres->completado,
            ]);

            DB::commit();

            // Recargar relaciones
            return $conteoSobres->fresh(['servicio.sede']);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar conteo de sobres: ' . $e->getMessage(), [
                'id' => $id,
                'data' => $data,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Crear o actualizar conteo de sobres.
     *
     * @param int $servicioId
     * @param array $data
     * @return ConteoDeSobres
     * @throws Exception
     */
    public function createOrUpdate(int $servicioId, array $data): ConteoDeSobres
    {
        $conteoSobres = $this->findByServicioId($servicioId);

        if ($conteoSobres) {
            return $this->update($conteoSobres->id, $data);
        }

        $data['servicio_id'] = $servicioId;
        return $this->create($data);
    }

    /**
     * Eliminar conteo de sobres.
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        try {
            DB::beginTransaction();

            $conteoSobres = ConteoDeSobres::findOrFail($id);
            $deleted = $conteoSobres->delete();

            DB::commit();

            return $deleted;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar conteo de sobres: ' . $e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Obtener todos los conteos de sobres con paginación.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15)
    {
        return ConteoDeSobres::with('servicio.sede')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Obtener estadísticas de conteos de sobres.
     *
     * @param int $servicioId
     * @return array
     */
    public function getEstadisticas(int $servicioId): array
    {
        $conteoSobres = $this->findByServicioId($servicioId);

        if (!$conteoSobres) {
            return [];
        }

        return [
            'canastas_entregadas' => $conteoSobres->canastas['entregadas'] ?? 0,
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
        ];
    }
}
