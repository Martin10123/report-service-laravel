<script setup>
import { Link } from '@inertiajs/vue3';
import { formatearFecha, formatearHora } from '@/Utils/dateHelpers';

const props = defineProps({
    servicio: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['edit', 'delete']);

const obtenerNombreSede = (sede) => {
    if (typeof sede === 'object' && sede !== null) {
        return sede.nombre;
    }
    return sede;
};
</script>

<template>
    <div class="group rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition hover:border-primary-300 hover:shadow">
        <div class="flex items-start justify-between">
            <Link
                :href="route('servicios.show', servicio.id)"
                class="min-w-0 flex-1"
            >
                <div class="flex items-center gap-2">
                    <h3 class="font-semibold text-gray-900">
                        {{ obtenerNombreSede(servicio.sede) }}
                    </h3>
                    <span class="rounded bg-primary-50 px-2 py-0.5 text-xs font-medium text-primary-700">
                        N° {{ servicio.numero_servicio }}
                    </span>
                </div>
                <div class="mt-1 flex items-center gap-2">
                    <p class="text-sm text-gray-600">
                        {{ formatearFecha(servicio.fecha) }}
                    </p>
                    <span 
                        v-if="servicio.estado === 'activo'"
                        class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700"
                    >
                        Activo
                    </span>
                    <span 
                        v-else-if="servicio.estado === 'finalizado'"
                        class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700"
                    >
                        Finalizado
                    </span>
                </div>
                <p class="mt-0.5 text-xs text-gray-500">
                    {{ servicio.dia_semana }} {{ formatearHora(servicio.hora) }}
                </p>
            </Link>
            <div class="flex shrink-0 gap-1">
                <button
                    @click="emit('edit', servicio)"
                    class="rounded p-1 text-gray-400 transition hover:bg-blue-50 hover:text-blue-600"
                    title="Editar servicio"
                >
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </button>
                <button
                    @click.stop="emit('delete', servicio)"
                    class="rounded p-1 text-gray-400 transition hover:bg-red-50 hover:text-red-600"
                    title="Eliminar servicio"
                >
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>
