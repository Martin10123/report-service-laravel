<?php

namespace App\Repositories;

use App\Models\ConteoA4;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ConteoA4Repository
{
    /**
     * Obtener conteo A4 por servicio ID.
     *
     * @param int $servicioId
     * @return ConteoA4|null
     */
    public function findByServicioId(int $servicioId): ?ConteoA4
    {
        return ConteoA4::where('servicio_id', $servicioId)
            ->with('servicio.sede')
            ->first();
    }

    /**
     * Crear un nuevo conteo A4.
     *
     * @param array $data
     * @return ConteoA4
     * @throws Exception
     */
    public function create(array $data): ConteoA4
    {
        try {
            DB::beginTransaction();

            // Calcular totales automáticamente
            $totales = ConteoA4::calcularTotales($data['areas']);
            
            $conteoA4 = ConteoA4::create([
                'servicio_id' => $data['servicio_id'],
                'areas' => $data['areas'],
                'total_adultos' => $totales['total_adultos'],
                'total_ninos' => $totales['total_ninos'],
                'total_asistencia' => $totales['total_asistencia'],
                'completado' => $data['completado'] ?? false,
            ]);

            DB::commit();

            // Recargar relaciones
            return $conteoA4->load('servicio.sede');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear conteo A4: ' . $e->getMessage(), [
                'data' => $data,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Actualizar un conteo A4 existente.
     *
     * @param int $id
     * @param array $data
     * @return ConteoA4
     * @throws Exception
     */
    public function update(int $id, array $data): ConteoA4
    {
        try {
            DB::beginTransaction();

            $conteoA4 = ConteoA4::findOrFail($id);

            // Calcular totales automáticamente
            $totales = ConteoA4::calcularTotales($data['areas']);

            $conteoA4->update([
                'areas' => $data['areas'],
                'total_adultos' => $totales['total_adultos'],
                'total_ninos' => $totales['total_ninos'],
                'total_asistencia' => $totales['total_asistencia'],
                'completado' => $data['completado'] ?? $conteoA4->completado,
            ]);

            DB::commit();

            // Recargar relaciones
            return $conteoA4->fresh(['servicio.sede']);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar conteo A4: ' . $e->getMessage(), [
                'id' => $id,
                'data' => $data,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Crear o actualizar conteo A4 (upsert).
     *
     * @param int $servicioId
     * @param array $data
     * @return ConteoA4
     * @throws Exception
     */
    public function createOrUpdate(int $servicioId, array $data): ConteoA4
    {
        $existing = $this->findByServicioId($servicioId);

        if ($existing) {
            return $this->update($existing->id, $data);
        }

        $data['servicio_id'] = $servicioId;
        return $this->create($data);
    }

    /**
     * Eliminar un conteo A4.
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        try {
            DB::beginTransaction();

            $conteoA4 = ConteoA4::findOrFail($id);
            $deleted = $conteoA4->delete();

            DB::commit();

            return $deleted;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar conteo A4: ' . $e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Obtener todos los conteos A4.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return ConteoA4::with('servicio.sede')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener conteos A4 por rango de fechas.
     *
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByDateRange(string $startDate, string $endDate)
    {
        return ConteoA4::whereHas('servicio', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('fecha', [$startDate, $endDate]);
        })
            ->with('servicio.sede')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
