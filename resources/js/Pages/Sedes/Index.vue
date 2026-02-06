<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardTitle from '@/Components/CardTitle.vue';
import CardContent from '@/Components/CardContent.vue';

const props = defineProps({
    sedes: Array,
});

const editingSede = ref(null);
const editForm = useForm({
    nombre: '',
    tiene_areas_multiples: false,
    tiene_parqueadero: false,
    tiene_gradas: false,
    numero_areas: 1,
});

const startEdit = (sede) => {
    editingSede.value = sede.id;
    editForm.nombre = sede.nombre;
    editForm.tiene_areas_multiples = sede.tiene_areas_multiples;
    editForm.tiene_parqueadero = sede.tiene_parqueadero;
    editForm.tiene_gradas = sede.tiene_gradas;
    editForm.numero_areas = sede.numero_areas;
};

const cancelEdit = () => {
    editingSede.value = null;
    editForm.reset();
};

const saveEdit = (sedeId) => {
    editForm.put(route('sedes.update', sedeId), {
        preserveScroll: true,
        onSuccess: () => {
            editingSede.value = null;
            editForm.reset();
        },
    });
};

const toggleActive = (sedeId) => {
    useForm({}).post(route('sedes.toggle-active', sedeId), {
        preserveScroll: true,
    });
};

const getSedeAreas = (sede) => {
    const areas = [];
    for (let i = 1; i <= sede.numero_areas; i++) {
        areas.push(`A${i}`);
    }
    return areas.join(', ');
};
</script>

<template>
    <AppLayout title="Configuración de Sedes">
        <div class="space-y-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">
                        Configuración de Sedes
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Administra las sedes, sus características y áreas asociadas.
                    </p>
                </div>
            </div>

            <!-- Sedes List -->
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <Card v-for="sede in sedes" :key="sede.id">
                    <CardContent>
                        <div v-if="editingSede === sede.id" class="space-y-4 p-4">
                            <!-- Edit Form -->
                            <div class="grid gap-3 lg:grid-cols-3">
                                <div class="lg:col-span-3">
                                    <label class="block text-xs font-medium text-gray-700">
                                        Nombre de la sede
                                    </label>
                                    <input
                                        v-model="editForm.nombre"
                                        type="text"
                                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        placeholder="Nombre de la sede"
                                    >
                                    <div v-if="editForm.errors.nombre" class="mt-1 text-xs text-red-600">
                                        {{ editForm.errors.nombre }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700">
                                        Número de áreas
                                    </label>
                                    <input
                                        v-model.number="editForm.numero_areas"
                                        type="number"
                                        min="1"
                                        max="10"
                                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-1.5 text-center text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    >
                                    <p class="mt-1 text-center text-xs text-gray-500">
                                        {{ getSedeAreas({ numero_areas: editForm.numero_areas }) }}
                                    </p>
                                    <div v-if="editForm.errors.numero_areas" class="mt-1 text-center text-xs text-red-600">
                                        {{ editForm.errors.numero_areas }}
                                    </div>
                                </div>

                                <div>
                                    <label class="flex items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-gray-50 p-2 transition hover:bg-gray-100">
                                        <input
                                            v-model="editForm.tiene_areas_multiples"
                                            type="checkbox"
                                            class="size-3.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        >
                                        <span class="text-xs font-medium text-gray-700">Áreas múltiples</span>
                                    </label>
                                </div>

                                <div>
                                    <label class="flex items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-gray-50 p-2 transition hover:bg-gray-100">
                                        <input
                                            v-model="editForm.tiene_parqueadero"
                                            type="checkbox"
                                            class="size-3.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        >
                                        <span class="text-xs font-medium text-gray-700">Parqueadero</span>
                                    </label>
                                </div>

                                <div class="lg:col-span-3">
                                    <label class="flex items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-gray-50 p-2 transition hover:bg-gray-100">
                                        <input
                                            v-model="editForm.tiene_gradas"
                                            type="checkbox"
                                            class="size-3.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        >
                                        <span class="text-xs font-medium text-gray-700">Gradas</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex justify-center gap-2 border-t pt-3">
                                <button
                                    type="button"
                                    @click="cancelEdit"
                                    class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 transition hover:bg-gray-50"
                                    :disabled="editForm.processing"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="button"
                                    @click="saveEdit(sede.id)"
                                    class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-blue-700 disabled:opacity-50"
                                    :disabled="editForm.processing"
                                >
                                    {{ editForm.processing ? 'Guardando...' : 'Guardar' }}
                                </button>
                            </div>
                        </div>

                        <div v-else class="p-4">
                            <!-- Sede Header -->
                            <div class="mb-3 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-semibold text-gray-900">
                                        {{ sede.nombre }}
                                    </h3>
                                    <span
                                        :class="[
                                            'rounded-full px-2 py-0.5 text-xs font-medium',
                                            sede.activa
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-gray-100 text-gray-800'
                                        ]"
                                    >
                                        {{ sede.activa ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex gap-1.5">
                                    <button
                                        type="button"
                                        @click="startEdit(sede)"
                                        class="rounded-lg border border-gray-300 bg-white p-1.5 text-gray-700 transition hover:bg-gray-50 hover:border-gray-400"
                                        title="Editar sede"
                                    >
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button
                                        type="button"
                                        @click="toggleActive(sede.id)"
                                        :class="[
                                            'rounded-lg border p-1.5 transition',
                                            sede.activa
                                                ? 'border-red-300 bg-red-50 text-red-700 hover:bg-red-100'
                                                : 'border-green-300 bg-green-50 text-green-700 hover:bg-green-100'
                                        ]"
                                        :title="sede.activa ? 'Desactivar sede' : 'Activar sede'"
                                    >
                                        <svg v-if="sede.activa" class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                        <svg v-else class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Sede Info -->
                            <div class="grid gap-2 grid-cols-3">
                                <div class="flex flex-col items-center justify-center rounded-lg border border-gray-200 bg-gray-50 p-2 text-center">
                                    <svg class="mb-1 size-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ sede.numero_areas }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ getSedeAreas(sede) }}</span>
                                </div>

                                <div :class="[
                                    'flex flex-col items-center justify-center rounded-lg border p-2 text-center transition',
                                    sede.tiene_areas_multiples
                                        ? 'border-green-200 bg-green-50'
                                        : 'border-gray-200 bg-gray-50'
                                ]">
                                    <svg class="mb-1 size-4" :class="sede.tiene_areas_multiples ? 'text-green-600' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span :class="[
                                        'text-xs font-medium',
                                        sede.tiene_areas_multiples ? 'text-green-900' : 'text-gray-500'
                                    ]">
                                        Múltiples
                                    </span>
                                </div>

                                <div :class="[
                                    'flex flex-col items-center justify-center rounded-lg border p-2 text-center transition',
                                    sede.tiene_parqueadero
                                        ? 'border-green-200 bg-green-50'
                                        : 'border-gray-200 bg-gray-50'
                                ]">
                                    <svg class="mb-1 size-4" :class="sede.tiene_parqueadero ? 'text-green-600' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span :class="[
                                        'text-xs font-medium',
                                        sede.tiene_parqueadero ? 'text-green-900' : 'text-gray-500'
                                    ]">
                                        Parqueadero
                                    </span>
                                </div>

                                <div :class="[
                                    'flex flex-col items-center justify-center rounded-lg border p-2 text-center transition col-span-3',
                                    sede.tiene_gradas
                                        ? 'border-green-200 bg-green-50'
                                        : 'border-gray-200 bg-gray-50'
                                ]">
                                    <svg class="mb-1 size-4" :class="sede.tiene_gradas ? 'text-green-600' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span :class="[
                                        'text-xs font-medium',
                                        sede.tiene_gradas ? 'text-green-900' : 'text-gray-500'
                                    ]">
                                        Gradas
                                    </span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-if="!sedes || sedes.length === 0" class="rounded-lg border-2 border-dashed border-gray-300 p-12 text-center">
                <svg class="mx-auto size-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900">No hay sedes</h3>
                <p class="mt-1 text-sm text-gray-500">No se encontraron sedes registradas en el sistema.</p>
            </div>
        </div>
    </AppLayout>
</template>
