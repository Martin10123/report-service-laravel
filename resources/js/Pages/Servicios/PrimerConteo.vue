<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardTitle from '@/Components/CardTitle.vue';
import CardDescription from '@/Components/CardDescription.vue';
import CardContent from '@/Components/CardContent.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

// Props (cuando venga del backend)
const props = defineProps({
    servicio: {
        type: Object,
        default: () => ({
            id: 1,
            sede: 'Villa Grande',
            fecha: '2026-02-04',
            numero_servicio: 1,
            areas: ['A1', 'A2', 'A3', 'A4'],
        }),
    },
});

// Estado local de las áreas
const areas = ref(
    props.servicio.areas.map((area) => ({
        area,
        adultos: 0,
        ninos: 0,
    }))
);

// Computed totales
const totalAdultos = computed(() =>
    areas.value.reduce((sum, a) => sum + (a.adultos || 0), 0)
);

const totalNinos = computed(() =>
    areas.value.reduce((sum, a) => sum + (a.ninos || 0), 0)
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

// Handle cambios en las áreas
const handleAreaChange = (idx, field, value) => {
    areas.value[idx][field] = value;
};

// Form para guardar
const form = useForm({
    areas: areas,
});

const guardar = () => {
    form.post(route('primer-conteo.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Mostrar mensaje de éxito
        },
    });
};

const refrescar = () => {
    window.location.reload();
};
</script>

<template>
    <AppLayout title="Primer Conteo">
        <div class="space-y-2 sm:space-y-3 animate-in fade-in">
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
                                    @input="handleAreaChange(idx, 'adultos', area.adultos)"
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
                                    @input="handleAreaChange(idx, 'ninos', area.ninos)"
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
            <div class="flex justify-end pt-1">
                <PrimaryButton
                    class="w-full px-6 sm:w-auto"
                    :disabled="form.processing"
                    @click="guardar"
                >
                    Guardar
                </PrimaryButton>
            </div>
        </div>
    </AppLayout>
</template>
