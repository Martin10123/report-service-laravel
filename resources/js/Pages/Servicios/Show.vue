<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

// Placeholder: cuando haya backend, recibir desde props
const props = defineProps({
    id: [String, Number],
});

// Datos de ejemplo
const servicio = {
    id: props.id,
    sede: 'Villa Grande',
    fecha: '2026-02-04',
    numero_servicio: 1,
    hora: '08:00',
    dia_semana: 'MIÉRCOLES',
};

const formatearFecha = (fecha) => {
    const d = new Date(fecha);
    return d.toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' });
};
</script>

<template>
    <AppLayout title="Detalles del servicio">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <Link
                        :href="route('servicios.index')"
                        class="mb-2 inline-flex items-center text-sm text-gray-500 hover:text-gray-700"
                    >
                        <svg class="mr-1 size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Volver a servicios
                    </Link>
                    <h1 class="text-xl font-semibold text-gray-800">
                        {{ servicio.sede }} - {{ formatearFecha(servicio.fecha) }}
                    </h1>
                    <p class="mt-0.5 text-sm text-gray-500">
                        Servicio N° {{ servicio.numero_servicio }} • {{ servicio.dia_semana }} {{ servicio.hora }}
                    </p>
                </div>
                <PrimaryButton>
                    Continuar con conteo
                </PrimaryButton>
            </div>
        </template>

        <div class="space-y-6">
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">
                    Información del servicio
                </h2>
                <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Sede</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ servicio.sede }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Fecha</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ formatearFecha(servicio.fecha) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">N° Servicio</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ servicio.numero_servicio }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Hora</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ servicio.hora }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">
                    Estado del reporte
                </h2>
                <p class="mt-2 text-sm text-gray-500">
                    El reporte completo estará disponible cuando se completen todos los conteos.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
