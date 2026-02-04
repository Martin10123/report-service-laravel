<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardTitle from '@/Components/CardTitle.vue';
import CardContent from '@/Components/CardContent.vue';

// Props
const props = defineProps({
    auditorias: {
        type: Array,
        default: () => [
            {
                id: 1,
                user: 'Juan Pérez',
                action: 'create',
                module: 'Servicios',
                description: 'Creó un nuevo servicio "Servicio Domingo 4 de Febrero 2026"',
                ip: '192.168.1.100',
                created_at: '2026-02-04 10:30:15'
            },
            {
                id: 2,
                user: 'María García',
                action: 'update',
                module: 'Conteos',
                description: 'Actualizó el conteo del Área A1 para el servicio del domingo',
                ip: '192.168.1.101',
                created_at: '2026-02-04 11:15:42'
            },
            {
                id: 3,
                user: 'Carlos López',
                action: 'delete',
                module: 'Servicios',
                description: 'Eliminó el servicio "Servicio Miércoles 3 de Febrero 2026"',
                ip: '192.168.1.102',
                created_at: '2026-02-04 09:20:30'
            },
            {
                id: 4,
                user: 'Ana Martínez',
                action: 'export',
                module: 'Reportes',
                description: 'Exportó el reporte final del servicio en formato Excel',
                ip: '192.168.1.103',
                created_at: '2026-02-04 14:45:18'
            },
            {
                id: 5,
                user: 'Luis Rodríguez',
                action: 'view',
                module: 'Reportes',
                description: 'Consultó el informe final del servicio del domingo',
                ip: '192.168.1.104',
                created_at: '2026-02-04 15:10:05'
            },
            {
                id: 6,
                user: 'Juan Pérez',
                action: 'update',
                module: 'Usuarios',
                description: 'Cambió el rol de "Carlos López" a Coordinador',
                ip: '192.168.1.100',
                created_at: '2026-02-04 08:30:22'
            },
            {
                id: 7,
                user: 'María García',
                action: 'create',
                module: 'Conteos',
                description: 'Registró el primer conteo del Área A3',
                ip: '192.168.1.101',
                created_at: '2026-02-04 10:05:30'
            },
            {
                id: 8,
                user: 'Ana Martínez',
                action: 'update',
                module: 'Conteos',
                description: 'Modificó el conteo de sobres del servicio',
                ip: '192.168.1.103',
                created_at: '2026-02-04 12:40:15'
            },
        ]
    }
});

const filtroModulo = ref('todos');
const filtroAccion = ref('todas');
const busqueda = ref('');

const modulos = ['todos', 'Servicios', 'Conteos', 'Reportes', 'Usuarios', 'Configuración'];
const acciones = ['todas', 'create', 'update', 'delete', 'view', 'export'];

const auditoriasFiltradas = computed(() => {
    let resultado = props.auditorias || [];

    if (filtroModulo.value !== 'todos') {
        resultado = resultado.filter(a => a.module === filtroModulo.value);
    }

    if (filtroAccion.value !== 'todas') {
        resultado = resultado.filter(a => a.action === filtroAccion.value);
    }

    if (busqueda.value) {
        const termino = busqueda.value.toLowerCase();
        resultado = resultado.filter(a =>
            a.user.toLowerCase().includes(termino) ||
            a.description.toLowerCase().includes(termino)
        );
    }

    return resultado;
});

const getActionIcon = (action) => {
    const icons = {
        create: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />',
        update: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />',
        delete: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />',
        view: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />',
        export: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />',
    };
    return icons[action] || icons.view;
};

const getActionColor = (action) => {
    const colors = {
        create: 'bg-green-100 text-green-800',
        update: 'bg-blue-100 text-blue-800',
        delete: 'bg-red-100 text-red-800',
        view: 'bg-gray-100 text-gray-800',
        export: 'bg-purple-100 text-purple-800',
    };
    return colors[action] || colors.view;
};

const getActionLabel = (action) => {
    const labels = {
        create: 'Creación',
        update: 'Actualización',
        delete: 'Eliminación',
        view: 'Consulta',
        export: 'Exportación',
    };
    return labels[action] || action;
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);

    if (minutes < 1) return 'Ahora mismo';
    if (minutes < 60) return `Hace ${minutes} minuto${minutes !== 1 ? 's' : ''}`;
    if (hours < 24) return `Hace ${hours} hora${hours !== 1 ? 's' : ''}`;
    if (days < 7) return `Hace ${days} día${days !== 1 ? 's' : ''}`;

    return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <AppLayout title="Auditorías">
        <div class="space-y-4">
            <!-- Header -->
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('configuraciones.index')"
                        class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                        aria-label="Volver a configuraciones"
                    >
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">
                            Auditorías
                        </h1>
                        <p class="mt-0.5 text-sm text-gray-600">
                            Registro completo de actividades del sistema
                        </p>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <Card>
                <CardContent class="p-4">
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                            <div class="relative">
                                <input
                                    v-model="busqueda"
                                    type="text"
                                    placeholder="Usuario o descripción..."
                                    class="block w-full rounded-lg border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                />
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="size-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Módulo</label>
                            <select
                                v-model="filtroModulo"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option v-for="modulo in modulos" :key="modulo" :value="modulo">
                                    {{ modulo === 'todos' ? 'Todos los módulos' : modulo }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Acción</label>
                            <select
                                v-model="filtroAccion"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option v-for="accion in acciones" :key="accion" :value="accion">
                                    {{ accion === 'todas' ? 'Todas las acciones' : getActionLabel(accion) }}
                                </option>
                            </select>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Resultados -->
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-600">
                    {{ auditoriasFiltradas.length }} registro{{ auditoriasFiltradas.length !== 1 ? 's' : '' }} encontrado{{ auditoriasFiltradas.length !== 1 ? 's' : '' }}
                </p>
                <button class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Exportar
                </button>
            </div>

            <!-- Timeline -->
            <div class="space-y-3">
                <Card v-for="auditoria in auditoriasFiltradas" :key="auditoria.id">
                    <CardContent class="p-4">
                        <div class="flex gap-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div :class="['flex size-10 items-center justify-center rounded-full', getActionColor(auditoria.action)]">
                                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="getActionIcon(auditoria.action)"></svg>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="text-sm font-semibold text-gray-900">{{ auditoria.user }}</p>
                                            <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-medium', getActionColor(auditoria.action)]">
                                                {{ getActionLabel(auditoria.action) }}
                                            </span>
                                            <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">
                                                {{ auditoria.module }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-2">
                                            {{ auditoria.description }}
                                        </p>
                                        <div class="flex items-center gap-4 text-xs text-gray-500">
                                            <span class="flex items-center gap-1">
                                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ formatDate(auditoria.created_at) }}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                                </svg>
                                                {{ auditoria.ip }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div v-if="auditoriasFiltradas.length === 0" class="text-center py-12">
                    <svg class="mx-auto size-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No se encontraron auditorías</h3>
                    <p class="mt-1 text-sm text-gray-500">Intenta ajustar los filtros de búsqueda</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
