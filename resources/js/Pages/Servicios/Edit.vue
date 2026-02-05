<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    servicio: Object,
    sedes: Array,
    sedeBloqueada: Boolean,
});

const form = useForm({
    sede_id: props.servicio.sede_id,
    numero_servicio: props.servicio.numero_servicio,
    fecha: props.servicio.fecha,
    hora: props.servicio.hora,
    observaciones: props.servicio.observaciones,
});

const submit = () => {
    form.put(route('servicios.update', props.servicio.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout title="Editar Servicio">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Editar Servicio</h1>
                    <p class="mt-1 text-sm text-gray-600">Modifica la información del servicio</p>
                </div>
            </div>
        </template>

        <div class="mx-auto max-w-3xl">
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <form @submit.prevent="submit" class="space-y-6 p-6">
                    <!-- Sede -->
                    <div>
                        <label for="sede_id" class="block text-sm font-medium text-gray-700">
                            Sede <span class="text-red-500">*</span>
                        </label>
                        <div v-if="sedeBloqueada" class="mt-1">
                            <div class="flex items-center gap-2 rounded-lg border border-gray-300 bg-gray-50 px-3 py-2">
                                <svg class="size-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span class="font-medium text-gray-700">{{ servicio.sede?.nombre || servicio.sede }}</span>
                                <span class="text-xs text-gray-500">(Sede bloqueada por filtro actual)</span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                La sede está bloqueada porque tienes un filtro activo. Para cambiarla, primero deselecciona la sede actual.
                            </p>
                        </div>
                        <select
                            v-else
                            id="sede_id"
                            v-model="form.sede_id"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            :class="{ 'border-red-300': form.errors.sede_id }"
                            required
                        >
                            <option value="">Seleccionar sede</option>
                            <option v-for="sede in sedes" :key="sede.id" :value="sede.id">
                                {{ sede.nombre }}
                            </option>
                        </select>
                        <p v-if="form.errors.sede_id" class="mt-1 text-sm text-red-600">{{ form.errors.sede_id }}</p>
                    </div>

                    <!-- Número de Servicio -->
                    <div>
                        <label for="numero_servicio" class="block text-sm font-medium text-gray-700">
                            Número de Servicio
                        </label>
                        <input
                            id="numero_servicio"
                            v-model="form.numero_servicio"
                            type="number"
                            min="1"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            :class="{ 'border-red-300': form.errors.numero_servicio }"
                            placeholder="Ej: 1234"
                        />
                        <p v-if="form.errors.numero_servicio" class="mt-1 text-sm text-red-600">{{ form.errors.numero_servicio }}</p>
                    </div>

                    <!-- Fecha y Hora -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="fecha" class="block text-sm font-medium text-gray-700">
                                Fecha <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="fecha"
                                v-model="form.fecha"
                                type="date"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                :class="{ 'border-red-300': form.errors.fecha }"
                                required
                            />
                            <p v-if="form.errors.fecha" class="mt-1 text-sm text-red-600">{{ form.errors.fecha }}</p>
                        </div>

                        <div>
                            <label for="hora" class="block text-sm font-medium text-gray-700">
                                Hora <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="hora"
                                v-model="form.hora"
                                type="time"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                :class="{ 'border-red-300': form.errors.hora }"
                                required
                            />
                            <p v-if="form.errors.hora" class="mt-1 text-sm text-red-600">{{ form.errors.hora }}</p>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div>
                        <label for="observaciones" class="block text-sm font-medium text-gray-700">
                            Observaciones
                        </label>
                        <textarea
                            id="observaciones"
                            v-model="form.observaciones"
                            rows="4"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            :class="{ 'border-red-300': form.errors.observaciones }"
                            placeholder="Notas o comentarios adicionales..."
                        />
                        <p v-if="form.errors.observaciones" class="mt-1 text-sm text-red-600">{{ form.errors.observaciones }}</p>
                    </div>

                    <!-- Botones -->
                    <div class="flex items-center justify-end gap-3 border-t pt-6">
                        <a
                            :href="route('servicios.show', servicio.id)"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Cancelar
                        </a>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 disabled:opacity-50"
                        >
                            <span v-if="form.processing">Guardando...</span>
                            <span v-else>Guardar Cambios</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
