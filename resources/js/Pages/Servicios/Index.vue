<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import ServiceCard from '@/Components/ServiceCard.vue';
import { useServiceFilters } from '@/Composables/useServiceFilters';
import { useServiceForm } from '@/Composables/useServiceForm';

const props = defineProps({
    servicios: {
        type: Object,
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

const page = usePage();
const isSuperUser = computed(() => false);

// Obtener la sede seleccionada desde las props globales
const sedeActual = computed(() => page.props.sedeActual || null);

// Usar composables
const {
    sedeFiltro,
    estadoFiltro,
    busqueda,
    serviciosFiltrados,
    aplicarFiltros,
} = useServiceFilters(props);

const {
    form,
    mostrarFormulario,
    mostrarModalEliminar,
    servicioEditar,
    servicioEliminar,
    crearServicio,
    abrirEditar,
    abrirNuevo,
    confirmarEliminar,
    eliminarServicio,
    cerrarModal,
    obtenerNombreSede,
} = useServiceForm();

// Pre-seleccionar la sede actual cuando se abre el formulario
const abrirNuevoConSede = () => {
    if (sedeActual.value) {
        form.sede_id = sedeActual.value.id;
    }
    abrirNuevo();
};

const soloNumeros = (event) => {
    const charCode = event.which || event.keyCode;
    // Permitir solo números (0-9)
    if (charCode < 48 || charCode > 57) {
        event.preventDefault();
    }
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
                <PrimaryButton @click="abrirNuevoConSede">
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
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                    <input
                        v-model="busqueda"
                        type="text"
                        placeholder="Sede, número, fecha..."
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    />
                </div>
            </div>

            <!-- Listado de servicios -->
            <div v-if="serviciosFiltrados.length > 0" class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <ServiceCard
                        v-for="servicio in serviciosFiltrados"
                        :key="servicio.id"
                        :servicio="servicio"
                        @edit="abrirEditar"
                        @delete="confirmarEliminar"
                    />
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
                    <PrimaryButton @click="abrirNuevoConSede">
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
                        <div v-if="sedeActual" class="mt-1">
                            <div class="flex items-center gap-2 rounded-lg border border-gray-300 bg-gray-50 px-3 py-2">
                                <svg class="size-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span class="font-medium text-gray-700">{{ sedeActual.nombre }}</span>
                                <span class="ml-auto text-xs text-gray-500">(Sede bloqueada)</span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                La sede está bloqueada porque tienes un filtro activo.
                            </p>
                        </div>
                        <select
                            v-else
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
                        <InputLabel for="numero_servicio" value="Número de servicio" />
                        <input
                            id="numero_servicio"
                            v-model.number="form.numero_servicio"
                            type="number"
                            min="1"
                            step="1"
                            placeholder="1"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            @keypress="soloNumeros"
                        />
                        <InputError :message="form.errors.numero_servicio" class="mt-1" />
                        <p class="mt-1 text-xs text-gray-500">Déjalo vacío para generar automáticamente</p>
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
                            <span class="font-semibold">{{ obtenerNombreSede(servicioEliminar?.sede) }}</span>?
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
