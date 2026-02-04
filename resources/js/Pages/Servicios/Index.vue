<script setup>
import { ref, computed } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';

// Props desde el backend
const props = defineProps({
    servicios: {
        type: Object, // Paginación de Laravel
        required: true,
    },
    estadisticas: {
        type: Object,
        default: () => ({ total: 0, activos: 0, finalizados: 0, cancelados: 0 }),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    sedes: {
        type: Array,
        required: true,
    },
});

const isSuperUser = computed(() => true);

const sedeFiltro = ref(props.filters.sede || null);
const estadoFiltro = ref(props.filters.estado || null);
const busqueda = ref(props.filters.busqueda || '');
const mostrarFormulario = ref(false);
const mostrarModalEliminar = ref(false);
const servicioEditar = ref(null);
const servicioEliminar = ref(null);

const form = useForm({
    sede_id: null,
    fecha: null,
    numero_servicio: null,
    hora: null,
    observaciones: '',
});

const aplicarFiltros = () => {
    router.get(route('servicios.index'), {
        sede: sedeFiltro.value,
        estado: estadoFiltro.value,
        busqueda: busqueda.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const limpiarFiltros = () => {
    sedeFiltro.value = null;
    estadoFiltro.value = null;
    busqueda.value = '';
    router.get(route('servicios.index'), {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const crearServicio = () => {
    const url = servicioEditar.value 
        ? route('servicios.update', servicioEditar.value.id)
        : route('servicios.store');
    
    const method = servicioEditar.value ? 'put' : 'post';
    
    form[method](url, {
        preserveScroll: true,
        onSuccess: () => {
            mostrarFormulario.value = false;
            form.reset();
            servicioEditar.value = null;
            // Cuando haya backend, recargar servicios
        },
    });
};

const abrirEditar = (servicio) => {
    servicioEditar.value = servicio;
    form.sede_id = servicio.sede_id;
    form.fecha = servicio.fecha || '';
    form.numero_servicio = servicio.numero_servicio;
    form.hora = servicio.hora || '';
    form.observaciones = servicio.observaciones || '';
    mostrarFormulario.value = true;
};

const abrirNuevo = () => {
    servicioEditar.value = null;
    form.reset();
    mostrarFormulario.value = true;
};

const confirmarEliminar = (servicio) => {
    servicioEliminar.value = servicio;
    mostrarModalEliminar.value = true;
};

const eliminarServicio = () => {
    if (!servicioEliminar.value) return;
    
    // Placeholder: cuando haya backend, eliminar servicio
    const index = servicios.value.findIndex(s => s.id === servicioEliminar.value.id);
    if (index !== -1) {
        servicios.value.splice(index, 1);
    }
    
    mostrarModalEliminar.value = false;
    servicioEliminar.value = null;
};

const cerrarModal = () => {
    mostrarFormulario.value = false;
    servicioEditar.value = null;
    form.reset();
};

const formatearFecha = (fecha) => {
    const d = new Date(fecha);
    return d.toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' });
};

const formatearHora = (hora) => {
    if (!hora) return '';
    
    // Si es una fecha ISO completa (2026-02-04T19:00:00.000000Z)
    if (hora.includes('T')) {
        const d = new Date(hora);
        return d.toLocaleTimeString('es-ES', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: false 
        });
    }
    
    // Si ya es formato HH:MM, retornar tal cual
    return hora;
};

const obtenerNombreSede = (sede) => {
    // Si sede es un objeto con la relación cargada
    if (typeof sede === 'object' && sede !== null) {
        return sede.nombre;
    }
    // Si es string (legacy)
    return sede;
};
</script>

<template>
    <AppLayout title="Servicios">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-gray-800">
                        Servicios
                    </h1>
                    <p class="mt-0.5 text-sm text-gray-500">
                        Gestiona los servicios de las sedes.
                    </p>
                </div>
                <PrimaryButton @click="abrirNuevo">
                    <svg class="mr-2 size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo servicio
                </PrimaryButton>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Filtros -->
            <div class="grid gap-4 sm:grid-cols-4">
                <div v-if="isSuperUser">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sede</label>
                    <select
                        v-model="sedeFiltro"
                        @change="aplicarFiltros"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    >
                        <option :value="null">Todas las sedes</option>
                        <option v-for="sede in sedes" :key="sede.id" :value="sede.nombre">
                            {{ sede.nombre }}
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select
                        v-model="estadoFiltro"
                        @change="aplicarFiltros"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    >
                        <option :value="null">Todos los estados</option>
                        <option value="activo">Activo</option>
                        <option value="finalizado">Finalizado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                    <input
                        v-model="busqueda"
                        @input="aplicarFiltros"
                        type="text"
                        placeholder="Sede, número..."
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    />
                </div>
            </div>

            <!-- Listado de servicios -->
            <div v-if="servicios.data && servicios.data.length > 0" class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="servicio in servicios.data"
                        :key="servicio.id"
                        class="group rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition hover:border-primary-300 hover:shadow"
                    >
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
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ formatearFecha(servicio.fecha) }}
                                </p>
                                <p class="mt-0.5 text-xs text-gray-500">
                                    {{ servicio.dia_semana }} {{ formatearHora(servicio.hora) }}
                                </p>
                            </Link>
                            <div class="flex shrink-0 gap-1">
                                <button
                                    @click="abrirEditar(servicio)"
                                    class="rounded p-1 text-gray-400 transition hover:bg-blue-50 hover:text-blue-600"
                                    title="Editar servicio"
                                >
                                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button
                                    @click.stop="confirmarEliminar(servicio)"
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
                </div>
            </div>

            <!-- Estado vacío -->
            <div v-else class="rounded-lg border border-gray-200 bg-white p-12 text-center">
                <svg class="mx-auto size-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-4 text-sm font-medium text-gray-900">
                    No hay servicios
                </h3>
                <p class="mt-2 text-sm text-gray-500">
                    Comienza creando tu primer servicio.
                </p>
                <div class="mt-6">
                    <PrimaryButton @click="abrirNuevo">
                        Nuevo servicio
                    </PrimaryButton>
                </div>
            </div>
        </div>

        <!-- Modal crear/editar servicio -->
        <Modal :show="mostrarFormulario" @close="cerrarModal">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ servicioEditar ? 'Editar servicio' : 'Nuevo servicio' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ servicioEditar ? 'Modifica la información del servicio.' : 'Completa la información para crear un nuevo servicio.' }}
                </p>

                <form @submit.prevent="crearServicio" class="mt-6 space-y-4">
                    <div>
                        <InputLabel for="sede" value="Sede" />
                        <select
                            id="sede"
                            v-model="form.sede_id"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            required
                        >
                            <option :value="null">Selecciona una sede</option>
                            <option v-for="sede in sedes" :key="sede.id" :value="sede.id">
                                {{ sede.nombre }}
                            </option>
                        </select>
                        <InputError :message="form.errors.sede_id" class="mt-1" />
                    </div>

                    <div>
                        <InputLabel for="fecha" value="Fecha" />
                        <input
                            id="fecha"
                            v-model="form.fecha"
                            type="date"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            required
                        />
                        <InputError :message="form.errors.fecha" class="mt-1" />
                    </div>

                    <div>
                        <InputLabel for="hora" value="Hora" />
                        <input
                            id="hora"
                            v-model="form.hora"
                            type="time"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            required
                        />
                        <InputError :message="form.errors.hora" class="mt-1" />
                    </div>

                    <div>
                        <InputLabel for="observaciones" value="Observaciones (opcional)" />
                        <textarea
                            id="observaciones"
                            v-model="form.observaciones"
                            rows="3"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        ></textarea>
                        <InputError :message="form.errors.observaciones" class="mt-1" />
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <SecondaryButton type="button" @click="cerrarModal">
                            Cancelar
                        </SecondaryButton>
                        <PrimaryButton :disabled="form.processing">
                            {{ servicioEditar ? 'Actualizar' : 'Crear servicio' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Modal confirmar eliminación -->
        <Modal :show="mostrarModalEliminar" @close="mostrarModalEliminar = false">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100">
                        <svg class="size-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg font-semibold text-gray-900">
                            ¿Eliminar servicio?
                        </h2>
                        <p class="mt-2 text-sm text-gray-600">
                            ¿Estás seguro de que deseas eliminar el servicio 
                            <span class="font-semibold">{{ obtenerNombreSede(servicioEliminar?.sede) }} N° {{ servicioEliminar?.numero_servicio }}</span>?
                            Esta acción no se puede deshacer y se perderán todos los datos asociados.
                        </p>
                        <div class="mt-6 flex justify-end gap-3">
                            <SecondaryButton @click="mostrarModalEliminar = false">
                                Cancelar
                            </SecondaryButton>
                            <button
                                @click="eliminarServicio"
                                class="inline-flex items-center justify-center rounded-lg border border-transparent bg-red-600 px-4 py-2.5 text-xs font-bold uppercase tracking-wide text-white shadow-sm transition-all duration-200 ease-in-out hover:bg-red-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 active:scale-95"
                            >
                                Eliminar servicio
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>
