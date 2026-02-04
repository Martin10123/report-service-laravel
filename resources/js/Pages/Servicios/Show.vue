<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { computed } from 'vue';

// Placeholder: cuando haya backend, recibir desde props
const props = defineProps({
    id: [String, Number],
});

// Datos de ejemplo - cuando haya backend vendrán desde el servidor
const servicio = {
    id: props.id,
    sede: 'Villa Grande',
    fecha: '2026-02-04',
    numero_servicio: 1,
    hora: '08:00',
    dia_semana: 'MIÉRCOLES',
    conteos: {
        primer_conteo: { completado: true, actualizado_en: '2026-02-04 08:15:00' },
        area_a1: { completado: true, actualizado_en: '2026-02-04 08:30:00' },
        area_a2: { completado: false, actualizado_en: null },
        area_a3: { completado: false, actualizado_en: null },
        area_a4: { completado: false, actualizado_en: null },
        sobres: { completado: false, actualizado_en: null },
    }
};

const formatearFecha = (fecha) => {
    const d = new Date(fecha);
    return d.toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' });
};

const formatearFechaHora = (fecha) => {
    if (!fecha) return null;
    const d = new Date(fecha);
    return d.toLocaleString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};

const progreso = computed(() => {
    const total = Object.keys(servicio.conteos).length;
    const completados = Object.values(servicio.conteos).filter(c => c.completado).length;
    return Math.round((completados / total) * 100);
});

const secciones = [
    { 
        key: 'primer_conteo', 
        titulo: 'Primer Conteo', 
        descripcion: 'Conteo inicial del servicio',
        ruta: 'primer-conteo',
        icono: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'
    },
    { 
        key: 'area_a1', 
        titulo: 'Área A1', 
        descripcion: 'Conteo de personas y servidores',
        ruta: 'conteo-a1',
        icono: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
    },
    { 
        key: 'area_a2', 
        titulo: 'Área A2', 
        descripcion: 'Conteo de personas y servidores',
        ruta: 'conteo-a2',
        icono: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
    },
    { 
        key: 'area_a3', 
        titulo: 'Área A3', 
        descripcion: 'Conteo de personas y servidores',
        ruta: 'conteo-a3',
        icono: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
    },
    { 
        key: 'area_a4', 
        titulo: 'Área A4', 
        descripcion: 'Exteriores, vehículos e Iglekids',
        ruta: 'conteo-a4',
        icono: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
    },
    { 
        key: 'sobres', 
        titulo: 'Conteo de Sobres', 
        descripcion: 'Ofrendas, Pro-Templo e Iglekids',
        ruta: 'conteo-sobres',
        icono: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'
    },
];
</script>

<template>
    <AppLayout title="Detalles del servicio">
        <div class="space-y-4">
            <!-- Header -->
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <Link
                        :href="route('servicios.index')"
                        class="mb-2 inline-flex items-center text-sm text-gray-500 transition hover:text-gray-700"
                    >
                        <svg class="mr-1 size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Volver a servicios
                    </Link>
                    <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">
                        {{ servicio.sede }} - Servicio N° {{ servicio.numero_servicio }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ formatearFecha(servicio.fecha) }} • {{ servicio.dia_semana }} {{ servicio.hora }}
                    </p>
                </div>
                <Link :href="route('informe-final')" class="inline-flex">
                    <PrimaryButton>
                        <svg class="mr-2 size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Ver Informe Final
                    </PrimaryButton>
                </Link>
            </div>

            <!-- Progreso -->
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Progreso del reporte</h3>
                        <p class="mt-1 text-xs text-gray-500">{{ Object.values(servicio.conteos).filter(c => c.completado).length }} de {{ Object.keys(servicio.conteos).length }} secciones completadas</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-blue-600">{{ progreso }}%</p>
                    </div>
                </div>
                <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-gray-200">
                    <div class="h-full bg-blue-600 transition-all" :style="{ width: `${progreso}%` }"></div>
                </div>
            </div>

            <!-- Secciones de conteo -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="seccion in secciones"
                    :key="seccion.key"
                    :href="route(seccion.ruta, { servicio_id: servicio.id })"
                    class="group relative rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition hover:border-blue-300 hover:shadow-md"
                >
                    <!-- Badge de estado -->
                    <div class="absolute right-3 top-3">
                        <span
                            v-if="servicio.conteos[seccion.key].completado"
                            class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700"
                        >
                            <svg class="mr-1 size-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Completado
                        </span>
                        <span
                            v-else
                            class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600"
                        >
                            Pendiente
                        </span>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600 transition group-hover:bg-blue-600 group-hover:text-white">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="seccion.icono" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-gray-900">
                                {{ seccion.titulo }}
                            </h3>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ seccion.descripcion }}
                            </p>
                            <p v-if="servicio.conteos[seccion.key].actualizado_en" class="mt-2 text-xs text-gray-400">
                                Actualizado: {{ formatearFechaHora(servicio.conteos[seccion.key].actualizado_en) }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-3 flex items-center text-sm font-medium text-blue-600 group-hover:text-blue-700">
                        {{ servicio.conteos[seccion.key].completado ? 'Editar' : 'Completar' }}
                        <svg class="ml-1 size-4 transition group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
