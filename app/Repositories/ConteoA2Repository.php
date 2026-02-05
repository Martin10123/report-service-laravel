<?php

namespace App\Repositories;

use App\Models\ConteoA2;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ConteoA2Repository
{
    /**
     * Obtener conteo A2 por servicio ID.
     *
     * @param int $servicioId
     * @return ConteoA2|null
     */
    public function findByServicioId(int $servicioId): ?ConteoA2
    {
        return ConteoA2::where('servicio_id', $servicioId)
            ->with('servicio.sede')
            ->first();
    }

    /**
     * Crear un nuevo conteo A2.
     *
     * @param array $data
     * @return ConteoA2
     * @throws Exception
     */
    public function create(array $data): ConteoA2
    {
        try {
            DB::beginTransaction();

            // Calcular totales automáticamente
            $totales = ConteoA2::calcularTotales($data['areas']);
            
            $conteoA2 = ConteoA2::create([
                'servicio_id' => $data['servicio_id'],
                'areas' => $data['areas'],
                'total_adultos' => $totales['total_adultos'],
                'total_ninos' => $totales['total_ninos'],
                'total_asistencia' => $totales['total_asistencia'],
                'completado' => $data['completado'] ?? false,
            ]);

            DB::commit();

            // Recargar relaciones
            return $conteoA2->load('servicio.sede');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear conteo A2: ' . $e->getMessage(), [
                'data' => $data,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Actualizar un conteo A2 existente.
     *
     * @param int $id
     * @param array $data
     * @return ConteoA2
     * @throws Exception
     */
    public function update(int $id, array $data): ConteoA2
    {
        try {
            DB::beginTransaction();

            $conteoA2 = ConteoA2::findOrFail($id);

            // Calcular totales automáticamente
            $totales = ConteoA2::calcularTotales($data['areas']);

            $conteoA2->update([
                'areas' => $data['areas'],
                'total_adultos' => $totales['total_adultos'],
                'total_ninos' => $totales['total_ninos'],
                'total_asistencia' => $totales['total_asistencia'],
                'completado' => $data['completado'] ?? $conteoA2->completado,
            ]);

            DB::commit();

            // Recargar relaciones
            return $conteoA2->fresh(['servicio.sede']);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar conteo A2: ' . $e->getMessage(), [
                'id' => $id,
                'data' => $data,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Crear o actualizar conteo A2 (upsert).
     *
     * @param int $servicioId
     * @param array $data
     * @return ConteoA2
     * @throws Exception
     */
    public function createOrUpdate(int $servicioId, array $data): ConteoA2
    {
        $existing = $this->findByServicioId($servicioId);

        if ($existing) {
            return $this->update($existing->id, $data);
        }

        $data['servicio_id'] = $servicioId;
        return $this->create($data);
    }

    /**
     * Eliminar un conteo A2.
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        try {
            DB::beginTransaction();

            $conteoA2 = ConteoA2::findOrFail($id);
            $deleted = $conteoA2->delete();

            DB::commit();

            return $deleted;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar conteo A2: ' . $e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Obtener estadísticas del conteo A2.
     *
     * @param int $servicioId
     * @return array|null
     */
    public function getEstadisticas(int $servicioId): ?array
    {
        $conteoA2 = $this->findByServicioId($servicioId);

        if (!$conteoA2) {
            return null;
        }

        return [
            'total_adultos' => $conteoA2->total_adultos,
            'total_ninos' => $conteoA2->total_ninos,
            'total_asistencia' => $conteoA2->total_asistencia,
            'completado' => $conteoA2->completado,
            'actualizado_en' => $conteoA2->updated_at,
        ];
    }
}
