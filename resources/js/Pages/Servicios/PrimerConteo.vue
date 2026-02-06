<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardTitle from '@/Components/CardTitle.vue';
import CardDescription from '@/Components/CardDescription.vue';
import CardContent from '@/Components/CardContent.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import NoServiceSelected from '@/Components/NoServiceSelected.vue';
import { router } from '@inertiajs/vue3';

const toast = useToast();

const page = usePage();

// Props del backend
const props = defineProps({
    servicio: {
        type: Object,
        required: false,
        default: null,
    },
    primerConteo: {
        type: Object,
        default: null,
    },
    areas: {
        type: Array,
        required: false,
        default: () => [],
    },
    error: {
        type: String,
        default: null,
    },
});

// Estado local de las áreas - inicializar con datos del backend si existen
const areas = ref(props.areas || []);

// Computed totales
const totalAdultos = computed(() =>
    areas.value.reduce((sum, a) => sum + (parseInt(a.adultos) || 0), 0)
);

const totalNinos = computed(() =>
    areas.value.reduce((sum, a) => sum + (parseInt(a.ninos) || 0), 0)
);

const totalAsistencia = computed(() => totalAdultos.value + totalNinos.value);

// Formato de fecha/hora actual
const fechaHoraActual = computed(() => {
    const fecha = new Date();
    return fecha.toLocaleString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
});

// Form para guardar (solo si hay servicio)
const form = props.servicio ? useForm({
    servicio_id: props.servicio.id,
    areas: areas.value,
    completado: props.primerConteo?.completado || false,
}) : null;

// Sincronizar cambios de areas con el form
if (form) {
    watch(areas, (newAreas) => {
        form.areas = newAreas;
    }, { deep: true });
}

const guardar = () => {
    if (!form) return;
    
    form.post(route('primer-conteo.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Redirigir al detalle del servicio después de guardar
            router.visit(route('servicios.show', props.servicio.id));
        },
        onError: (errors) => {
            console.error('Error al guardar:', errors);
            const errorMessages = Object.values(errors);
            errorMessages.forEach(msg => {
                toast.add({
                    severity: 'error',
                    summary: 'Error de validación',
                    detail: msg,
                    life: 5000
                });
            });
        },
    });
};

const refrescar = () => {
    router.reload({ only: ['primerConteo', 'areas'] });
};
</script>

<template>
    <AppLayout title="Primer Conteo">
        <!-- Mostrar mensaje si no hay servicio seleccionado -->
        <NoServiceSelected v-if="!servicio" />

        <!-- Contenido normal cuando hay servicio -->
        <div v-else class="space-y-2 sm:space-y-3 animate-in fade-in">
            <!-- Header -->
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="m-0 text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">
                    Primer Conteo
                </h1>
                <div class="flex items-center gap-2">
                    <p class="text-xs text-gray-500">
                        Fecha y hora: {{ fechaHoraActual }}
                    </p>
                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-500"
                        @click="refrescar"
                    >
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Bloques por área -->
            <div class="grid grid-cols-2 gap-2 sm:gap-3 lg:grid-cols-4">
                <Card
                    v-for="(area, idx) in areas"
                    :key="area.area"
                    variant="primary"
                >
                    <CardHeader compact>
                        <CardTitle size="xs" class="text-blue-800">
                            Área: {{ area.area }}
                        </CardTitle>
                    </CardHeader>
                    <CardContent compact>
                        <div class="space-y-2">
                            <div class="space-y-1">
                                <InputLabel :for="`adultos-${area.area}`" class="text-xs">
                                    Adultos
                                </InputLabel>
                                <TextInput
                                    :id="`adultos-${area.area}`"
                                    v-model.number="area.adultos"
                                    type="number"
                                    min="0"
                                    placeholder="0"
                                    class="h-8 w-full text-sm"
                                />
                            </div>
                            <div class="space-y-1">
                                <InputLabel :for="`ninos-${area.area}`" class="text-xs">
                                    Niños
                                </InputLabel>
                                <TextInput
                                    :id="`ninos-${area.area}`"
                                    v-model.number="area.ninos"
                                    type="number"
                                    min="0"
                                    placeholder="0"
                                    class="h-8 w-full text-sm"
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Resumen y Total final -->
            <div class="grid grid-cols-1 gap-2 sm:gap-3 lg:grid-cols-2">
                <!-- Resumen Primer Conteo -->
                <Card variant="accent">
                    <CardHeader compact>
                        <CardTitle size="base" class="text-gray-900">
                            Resumen primer conteo
                        </CardTitle>
                        <CardDescription>
                            {{ servicio.sede }} · Servicio N° {{ servicio.numero_servicio }} · {{ servicio.fecha }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent compact>
                        <div class="grid grid-cols-2 gap-2 pt-2">
                            <div class="flex flex-col rounded-lg bg-white/60 px-3 py-2">
                                <span class="text-xs text-gray-600 sm:text-sm">Total Adultos</span>
                                <span class="text-lg font-bold text-gray-900 sm:text-xl">
                                    {{ totalAdultos }}
                                </span>
                            </div>
                            <div class="flex flex-col rounded-lg bg-white/60 px-3 py-2">
                                <span class="text-xs text-gray-600 sm:text-sm">Total Niños</span>
                                <span class="text-lg font-bold text-gray-900 sm:text-xl">
                                    {{ totalNinos }}
                                </span>
                            </div>
                            <div class="col-span-2 flex flex-col rounded-lg bg-white/60 px-3 py-2">
                                <span class="text-xs text-gray-600 sm:text-sm">Total Asistencia</span>
                                <span class="text-lg font-bold text-gray-900 sm:text-xl">
                                    {{ totalAsistencia }}
                                </span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Total final asistencia -->
                <Card variant="accent">
                    <CardHeader compact>
                        <CardTitle size="base" class="text-gray-900">
                            Total final asistencia
                        </CardTitle>
                    </CardHeader>
                    <CardContent compact>
                        <div class="grid grid-cols-2 gap-2 pt-2">
                            <div class="flex flex-col rounded-lg bg-white/60 px-3 py-2">
                                <span class="text-xs text-gray-600 sm:text-sm">Total Adultos</span>
                                <span class="text-lg font-bold text-gray-900 sm:text-xl">
                                    {{ totalAdultos }}
                                </span>
                            </div>
                            <div class="flex flex-col rounded-lg bg-white/60 px-3 py-2">
                                <span class="text-xs text-gray-600 sm:text-sm">Total Niños</span>
                                <span class="text-lg font-bold text-gray-900 sm:text-xl">
                                    {{ totalNinos }}
                                </span>
                            </div>
                            <div class="col-span-2 flex flex-col rounded-lg bg-white/60 px-3 py-2">
                                <span class="text-xs text-gray-600 sm:text-sm">Total Asistencia</span>
                                <span class="text-lg font-bold text-gray-900 sm:text-xl">
                                    {{ totalAsistencia }}
                                </span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Guardar -->
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input
                        v-model="form.completado"
                        type="checkbox"
                        class="size-4 rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                    />
                    <span class="text-sm font-medium text-gray-700">
                        Marcar como completado
                    </span>
                </label>
                <PrimaryButton
                    class="w-full px-6 sm:w-auto"
                    :disabled="form.processing"
                    @click="guardar"
                >
                    <svg class="mr-2 size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ form.completado ? 'Guardar y completar' : 'Guardar' }}
                </PrimaryButton>
            </div>
        </div>
    </AppLayout>
</template>
