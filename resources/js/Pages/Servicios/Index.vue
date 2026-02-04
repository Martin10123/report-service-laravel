<script setup>
import { ref, computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';

// Placeholder: cuando haya backend, recibir desde props
const isSuperUser = computed(() => true);
const sedes = [
    { id: 1, name: 'Villa Grande', slug: 'villa-grande' },
    { id: 2, name: 'Turbaco', slug: 'turbaco' },
    { id: 3, name: 'Bocagrande', slug: 'bocagrande' },
];

// Servicios de ejemplo (placeholder)
const servicios = ref([
    {
        id: 1,
        sede: 'Villa Grande',
        fecha: '2026-02-04',
        numero_servicio: 1,
        hora: '08:00',
        dia_semana: 'MIÉRCOLES',
        created_at: '2026-02-04 10:30:00',
    },
    {
        id: 2,
        sede: 'Turbaco',
        fecha: '2026-02-04',
        numero_servicio: 1,
        hora: '08:00',
        dia_semana: 'MIÉRCOLES',
        created_at: '2026-02-04 09:15:00',
    },
]);

const sedeFiltro = ref(null);
const mostrarFormulario = ref(false);
const mostrarModalEliminar = ref(false);
const servicioEditar = ref(null);
const servicioEliminar = ref(null);

const form = useForm({
    sede_id: '',
    fecha: '',
    numero_servicio: 1,
    hora: '08:00',
    dia_semana: '',
});

const serviciosFiltrados = computed(() => {
    if (!sedeFiltro.value) return servicios.value;
    return servicios.value.filter(s => s.sede === sedeFiltro.value);
});

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
    form.sede_id = sedes.find(s => s.name === servicio.sede)?.id || '';
    form.fecha = servicio.fecha;
    form.numero_servicio = servicio.numero_servicio;
    form.hora = servicio.hora;
    form.dia_semana = servicio.dia_semana;
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

const obtenerNombreSede = (sedeSlug) => {
    return sedes.find(s => s.slug === sedeSlug)?.name || sedeSlug;
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
            <div v-if="isSuperUser" class="flex items-center gap-3">
                <label class="text-sm font-medium text-gray-700">Filtrar por sede:</label>
                <select
                    v-model="sedeFiltro"
                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                >
                    <option :value="null">Todas las sedes</option>
                    <option v-for="sede in sedes" :key="sede.id" :value="sede.name">
                        {{ sede.name }}
                    </option>
                </select>
            </div>

            <!-- Listado de servicios -->
            <div v-if="serviciosFiltrados.length > 0" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="servicio in serviciosFiltrados"
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
                                    {{ servicio.sede }}
                                </h3>
                                <span class="rounded bg-primary-50 px-2 py-0.5 text-xs font-medium text-primary-700">
                                    N° {{ servicio.numero_servicio }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ formatearFecha(servicio.fecha) }}
                            </p>
                            <p class="mt-0.5 text-xs text-gray-500">
                                {{ servicio.dia_semana }} {{ servicio.hora }}
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
                        <InputLabel for="sede_id" value="Sede" />
                        <select
                            id="sede_id"
                            v-model="form.sede_id"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            required
                        >
                            <option value="">Selecciona una sede</option>
                            <option v-for="sede in sedes" :key="sede.id" :value="sede.id">
                                {{ sede.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.sede_id" class="mt-1" />
                    </div>

                    <div>
                        <InputLabel for="fecha" value="Fecha" />
                        <TextInput
                            id="fecha"
                            v-model="form.fecha"
                            type="date"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.fecha" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="numero_servicio" value="N° Servicio" />
                            <TextInput
                                id="numero_servicio"
                                v-model.number="form.numero_servicio"
                                type="number"
                                min="1"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.numero_servicio" class="mt-1" />
                        </div>

                        <div>
                            <InputLabel for="hora" value="Hora" />
                            <TextInput
                                id="hora"
                                v-model="form.hora"
                                type="time"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.hora" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="dia_semana" value="Día de la semana (opcional)" />
                        <select
                            id="dia_semana"
                            v-model="form.dia_semana"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        >
                            <option value="">Selecciona un día</option>
                            <option value="LUNES">Lunes</option>
                            <option value="MARTES">Martes</option>
                            <option value="MIÉRCOLES">Miércoles</option>
                            <option value="JUEVES">Jueves</option>
                            <option value="VIERNES">Viernes</option>
                            <option value="SÁBADO">Sábado</option>
                            <option value="DOMINGO">Domingo</option>
                        </select>
                        <InputError :message="form.errors.dia_semana" class="mt-1" />
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
                            <span class="font-semibold">{{ servicioEliminar?.sede }} N° {{ servicioEliminar?.numero_servicio }}</span>?
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
