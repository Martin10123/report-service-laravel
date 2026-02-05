<?php

namespace App\Repositories;

use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ServiceRepository
{
    /**
     * Obtener todos los servicios paginados.
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Service::query()
            ->with('sede')
            ->orderBy('created_at', 'asc');

        // Aplicar filtros
        if (!empty($filters['sede'])) {
            $query->whereHas('sede', function ($q) use ($filters) {
                $q->where('nombre', $filters['sede']);
            });
        }

        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        if (!empty($filters['fecha_inicio']) && !empty($filters['fecha_fin'])) {
            $query->entreFechas($filters['fecha_inicio'], $filters['fecha_fin']);
        }

        if (!empty($filters['busqueda'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('numero_servicio', 'like', "%{$filters['busqueda']}%")
                  ->orWhere('observaciones', 'like', "%{$filters['busqueda']}%")
                  ->orWhereHas('sede', function ($sq) use ($filters) {
                      $sq->where('nombre', 'like', "%{$filters['busqueda']}%");
                  });
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Obtener todos los servicios sin paginar.
     *
     * @param array $filters
     * @return Collection
     */
    public function getAll(array $filters = []): Collection
    {
        $query = Service::query()->with('sede')->orderBy('fecha', 'desc');

        // Aplicar filtros
        if (!empty($filters['sede'])) {
            $query->whereHas('sede', function ($q) use ($filters) {
                $q->where('nombre', $filters['sede']);
            });
        }

        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        return $query->get();
    }

    /**
     * Obtener servicios activos.
     *
     * @return Collection
     */
    public function getActivos(): Collection
    {
        return Service::activos()
            ->with('sede')
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->get();
    }

    /**
     * Obtener un servicio por ID.
     *
     * @param int $id
     * @return Service|null
     */
    public function findById(int $id): ?Service
    {
        return Service::with('sede')->find($id);
    }

    /**
     * Crear un nuevo servicio.
     *
     * @param array $data
     * @return Service
     */
    public function create(array $data): Service
    {
        // Generar día de la semana automáticamente
        if (empty($data['dia_semana'])) {
            $data['dia_semana'] = $this->getDiaSemana($data['fecha']);
        }

        // Generar número de servicio automáticamente si no se proporciona
        if (!isset($data['numero_servicio']) || $data['numero_servicio'] === null) {
            $data['numero_servicio'] = $this->generateNumeroServicio($data['sede_id'], $data['fecha']);
        }

        // Compatibilidad: llenar campo 'sede' legacy si existe sede_id
        if (isset($data['sede_id']) && !isset($data['sede'])) {
            $sede = \App\Models\Sede::find($data['sede_id']);
            if ($sede) {
                $data['sede'] = $sede->nombre;
            }
        }

        $servicio = Service::create($data);
        
        // Recargar para obtener la relación
        return $servicio->load('sede');
    }

    /**
     * Actualizar un servicio.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $servicio = $this->findById($id);

        if (!$servicio) {
            return false;
        }

        // Actualizar día de la semana si cambió la fecha
        if (!empty($data['fecha']) && $data['fecha'] !== $servicio->fecha->format('Y-m-d')) {
            $data['dia_semana'] = $this->getDiaSemana($data['fecha']);
        }

        return $servicio->update($data);
    }

    /**
     * Eliminar un servicio (soft delete).
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $servicio = $this->findById($id);

        if (!$servicio) {
            return false;
        }

        // Cambiar estado a cancelado antes de eliminar
        $servicio->update(['estado' => 'cancelado']);

        return $servicio->delete();
    }

    /**
     * Cambiar el estado de un servicio.
     *
     * @param int $id
     * @param string $estado
     * @return bool
     */
    public function cambiarEstado(int $id, string $estado): bool
    {
        $servicio = $this->findById($id);

        if (!$servicio) {
            return false;
        }

        return $servicio->update(['estado' => $estado]);
    }

    /**
     * Generar el número de servicio automáticamente.
     *
     * @param int $sedeId
     * @param string $fecha
     * @return int
     */
    private function generateNumeroServicio(int $sedeId, string $fecha): int
    {
        // Obtener el último número de servicio para esta sede (sin filtrar por año)
        $ultimoServicio = Service::where('sede_id', $sedeId)
            ->orderBy('numero_servicio', 'desc')
            ->first();

        return $ultimoServicio ? $ultimoServicio->numero_servicio + 1 : 1;
    }

    /**
     * Obtener el día de la semana en español.
     *
     * @param string $fecha
     * @return string
     */
    private function getDiaSemana(string $fecha): string
    {
        $dias = [
            'Monday' => 'LUNES',
            'Tuesday' => 'MARTES',
            'Wednesday' => 'MIÉRCOLES',
            'Thursday' => 'JUEVES',
            'Friday' => 'VIERNES',
            'Saturday' => 'SÁBADO',
            'Sunday' => 'DOMINGO',
        ];

        $diaIngles = date('l', strtotime($fecha));
        return $dias[$diaIngles] ?? 'DESCONOCIDO';
    }

    /**
     * Obtener estadísticas de servicios.
     *
     * @param array $filters
     * @return array
     */
    public function getEstadisticas(array $filters = []): array
    {
        $query = Service::query();

        if (!empty($filters['sede'])) {
            $query->whereHas('sede', function ($q) use ($filters) {
                $q->where('nombre', $filters['sede']);
            });
        }

        if (!empty($filters['fecha_inicio']) && !empty($filters['fecha_fin'])) {
            $query->entreFechas($filters['fecha_inicio'], $filters['fecha_fin']);
        }

        return [
            'total' => $query->count(),
            'activos' => (clone $query)->activos()->count(),
            'finalizados' => (clone $query)->finalizados()->count(),
            'cancelados' => (clone $query)->where('estado', 'cancelado')->count(),
        ];
    }
}
