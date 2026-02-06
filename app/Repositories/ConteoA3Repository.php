<?php

namespace App\Repositories;

use App\Models\ConteoA3;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ConteoA3Repository
{
    /**
     * Obtener conteo A3 por servicio ID.
     *
     * @param int $servicioId
     * @return ConteoA3|null
     */
    public function findByServicioId(int $servicioId): ?ConteoA3
    {
        return ConteoA3::where('servicio_id', $servicioId)
            ->with('servicio.sede')
            ->first();
    }

    /**
     * Crear un nuevo conteo A3.
     *
     * @param array $data
     * @return ConteoA3
     * @throws Exception
     */
    public function create(array $data): ConteoA3
    {
        try {
            DB::beginTransaction();

            // Calcular totales automáticamente
            $totales = ConteoA3::calcularTotales($data['areas']);
            
            $conteoA3 = ConteoA3::create([
                'servicio_id' => $data['servicio_id'],
                'areas' => $data['areas'],
                'total_adultos' => $totales['total_adultos'],
                'total_ninos' => $totales['total_ninos'],
                'total_asistencia' => $totales['total_asistencia'],
                'completado' => $data['completado'] ?? false,
            ]);

            DB::commit();

            // Recargar relaciones
            return $conteoA3->load('servicio.sede');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear conteo A3: ' . $e->getMessage(), [
                'data' => $data,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Actualizar un conteo A3 existente.
     *
     * @param int $id
     * @param array $data
     * @return ConteoA3
     * @throws Exception
     */
    public function update(int $id, array $data): ConteoA3
    {
        try {
            DB::beginTransaction();

            $conteoA3 = ConteoA3::findOrFail($id);

            // Calcular totales automáticamente
            $totales = ConteoA3::calcularTotales($data['areas']);

            $conteoA3->update([
                'areas' => $data['areas'],
                'total_adultos' => $totales['total_adultos'],
                'total_ninos' => $totales['total_ninos'],
                'total_asistencia' => $totales['total_asistencia'],
                'completado' => $data['completado'] ?? $conteoA3->completado,
            ]);

            DB::commit();

            // Recargar relaciones
            return $conteoA3->fresh(['servicio.sede']);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar conteo A3: ' . $e->getMessage(), [
                'id' => $id,
                'data' => $data,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Crear o actualizar conteo A3 (upsert).
     *
     * @param int $servicioId
     * @param array $data
     * @return ConteoA3
     * @throws Exception
     */
    public function createOrUpdate(int $servicioId, array $data): ConteoA3
    {
        $existing = $this->findByServicioId($servicioId);

        if ($existing) {
            return $this->update($existing->id, $data);
        }

        $data['servicio_id'] = $servicioId;
        return $this->create($data);
    }

    /**
     * Eliminar un conteo A3.
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        try {
            DB::beginTransaction();

            $conteoA3 = ConteoA3::findOrFail($id);
            $deleted = $conteoA3->delete();

            DB::commit();

            return $deleted;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar conteo A3: ' . $e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Obtener todos los conteos A3.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return ConteoA3::with('servicio.sede')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener conteos A3 por rango de fechas.
     *
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByDateRange(string $startDate, string $endDate)
    {
        return ConteoA3::whereHas('servicio', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('fecha', [$startDate, $endDate]);
        })
            ->with('servicio.sede')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
