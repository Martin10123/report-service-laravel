<?php

namespace App\Repositories;

use App\Models\PrimerConteo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PrimerConteoRepository
{
    /**
     * Obtener primer conteo por servicio ID.
     *
     * @param int $servicioId
     * @return PrimerConteo|null
     */
    public function findByServicioId(int $servicioId): ?PrimerConteo
    {
        return PrimerConteo::where('servicio_id', $servicioId)
            ->with('servicio.sede')
            ->first();
    }

    /**
     * Crear un nuevo primer conteo.
     *
     * @param array $data
     * @return PrimerConteo
     * @throws Exception
     */
    public function create(array $data): PrimerConteo
    {
        try {
            DB::beginTransaction();

            // Calcular totales automáticamente
            $totales = PrimerConteo::calcularTotales($data['areas']);
            
            $primerConteo = PrimerConteo::create([
                'servicio_id' => $data['servicio_id'],
                'areas' => $data['areas'],
                'total_adultos' => $totales['total_adultos'],
                'total_ninos' => $totales['total_ninos'],
                'total_asistencia' => $totales['total_asistencia'],
                'completado' => $data['completado'] ?? false,
            ]);

            DB::commit();

            // Recargar relaciones
            return $primerConteo->load('servicio.sede');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear primer conteo: ' . $e->getMessage(), [
                'data' => $data,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Actualizar un primer conteo existente.
     *
     * @param int $id
     * @param array $data
     * @return PrimerConteo
     * @throws Exception
     */
    public function update(int $id, array $data): PrimerConteo
    {
        try {
            DB::beginTransaction();

            $primerConteo = PrimerConteo::findOrFail($id);

            // Calcular totales automáticamente
            $totales = PrimerConteo::calcularTotales($data['areas']);

            $primerConteo->update([
                'areas' => $data['areas'],
                'total_adultos' => $totales['total_adultos'],
                'total_ninos' => $totales['total_ninos'],
                'total_asistencia' => $totales['total_asistencia'],
                'completado' => $data['completado'] ?? $primerConteo->completado,
            ]);

            DB::commit();

            // Recargar relaciones
            return $primerConteo->fresh(['servicio.sede']);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar primer conteo: ' . $e->getMessage(), [
                'id' => $id,
                'data' => $data,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Crear o actualizar primer conteo (upsert).
     *
     * @param int $servicioId
     * @param array $data
     * @return PrimerConteo
     * @throws Exception
     */
    public function createOrUpdate(int $servicioId, array $data): PrimerConteo
    {
        $existing = $this->findByServicioId($servicioId);

        if ($existing) {
            return $this->update($existing->id, $data);
        }

        $data['servicio_id'] = $servicioId;
        return $this->create($data);
    }

    /**
     * Eliminar un primer conteo.
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        try {
            DB::beginTransaction();

            $primerConteo = PrimerConteo::findOrFail($id);
            $deleted = $primerConteo->delete();

            DB::commit();

            return $deleted;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar primer conteo: ' . $e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Obtener estadísticas del primer conteo.
     *
     * @param int $servicioId
     * @return array|null
     */
    public function getEstadisticas(int $servicioId): ?array
    {
        $primerConteo = $this->findByServicioId($servicioId);

        if (!$primerConteo) {
            return null;
        }

        return [
            'total_adultos' => $primerConteo->total_adultos,
            'total_ninos' => $primerConteo->total_ninos,
            'total_asistencia' => $primerConteo->total_asistencia,
            'completado' => $primerConteo->completado,
            'actualizado_en' => $primerConteo->updated_at,
        ];
    }
}
