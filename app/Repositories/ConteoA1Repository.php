<?php

namespace App\Repositories;

use App\Models\ConteoA1;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ConteoA1Repository
{
    /**
     * Obtener conteo A1 por servicio ID.
     *
     * @param int $servicioId
     * @return ConteoA1|null
     */
    public function findByServicioId(int $servicioId): ?ConteoA1
    {
        return ConteoA1::where('servicio_id', $servicioId)
            ->with('servicio.sede')
            ->first();
    }

    /**
     * Crear un nuevo conteo A1.
     *
     * @param array $data
     * @return ConteoA1
     * @throws Exception
     */
    public function create(array $data): ConteoA1
    {
        try {
            DB::beginTransaction();

            // Calcular totales automáticamente
            $totales = ConteoA1::calcularTotales($data['areas']);
            
            $conteoA1 = ConteoA1::create([
                'servicio_id' => $data['servicio_id'],
                'areas' => $data['areas'],
                'total_adultos' => $totales['total_adultos'],
                'total_ninos' => $totales['total_ninos'],
                'total_asistencia' => $totales['total_asistencia'],
                'completado' => $data['completado'] ?? false,
            ]);

            DB::commit();

            // Recargar relaciones
            return $conteoA1->load('servicio.sede');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear conteo A1: ' . $e->getMessage(), [
                'data' => $data,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Actualizar un conteo A1 existente.
     *
     * @param int $id
     * @param array $data
     * @return ConteoA1
     * @throws Exception
     */
    public function update(int $id, array $data): ConteoA1
    {
        try {
            DB::beginTransaction();

            $conteoA1 = ConteoA1::findOrFail($id);

            // Calcular totales automáticamente
            $totales = ConteoA1::calcularTotales($data['areas']);

            $conteoA1->update([
                'areas' => $data['areas'],
                'total_adultos' => $totales['total_adultos'],
                'total_ninos' => $totales['total_ninos'],
                'total_asistencia' => $totales['total_asistencia'],
                'completado' => $data['completado'] ?? $conteoA1->completado,
            ]);

            DB::commit();

            // Recargar relaciones
            return $conteoA1->fresh(['servicio.sede']);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar conteo A1: ' . $e->getMessage(), [
                'id' => $id,
                'data' => $data,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Crear o actualizar conteo A1 (upsert).
     *
     * @param int $servicioId
     * @param array $data
     * @return ConteoA1
     * @throws Exception
     */
    public function createOrUpdate(int $servicioId, array $data): ConteoA1
    {
        $existing = $this->findByServicioId($servicioId);

        if ($existing) {
            return $this->update($existing->id, $data);
        }

        $data['servicio_id'] = $servicioId;
        return $this->create($data);
    }

    /**
     * Eliminar un conteo A1.
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        try {
            DB::beginTransaction();

            $conteoA1 = ConteoA1::findOrFail($id);
            $deleted = $conteoA1->delete();

            DB::commit();

            return $deleted;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar conteo A1: ' . $e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Obtener estadísticas del conteo A1.
     *
     * @param int $servicioId
     * @return array|null
     */
    public function getEstadisticas(int $servicioId): ?array
    {
        $conteoA1 = $this->findByServicioId($servicioId);

        if (!$conteoA1) {
            return null;
        }

        return [
            'total_adultos' => $conteoA1->total_adultos,
            'total_ninos' => $conteoA1->total_ninos,
            'total_asistencia' => $conteoA1->total_asistencia,
            'completado' => $conteoA1->completado,
            'actualizado_en' => $conteoA1->updated_at,
        ];
    }
}
