<script setup>
import { ref, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardTitle from '@/Components/CardTitle.vue';
import CardContent from '@/Components/CardContent.vue';
import ExterioresCard from '@/Components/Areas/ExterioresCard.vue';
import VehiculosCard from '@/Components/Areas/VehiculosCard.vue';
import IglekidsCard from '@/Components/Areas/IglekidsCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    servicio_id: [String, Number],
});

const exteriores = ref({
    servidores: 0,
    logistica: 0,
    coffee: 0,
    container: 0,
});

const vehiculos = ref({
    carros: 0,
    motos: 0,
    bicicletas: 0,
});

const iglekids = ref({
    coordinadoras: 0,
    supervisoras: 0,
    maestros: 0,
    recrearte: 0,
    regikids: 0,
    logikids: 0,
    saludKids: 0,
    yoSoy: 0,
    ninos: 0,
});

const updateExterior = (key, value) => {
    exteriores.value[key] = value;
};

const updateVehiculo = (key, value) => {
    vehiculos.value[key] = value;
};

const updateIglekids = (key, value) => {
    iglekids.value[key] = value;
};

const totalExteriores = computed(() => {
    return Object.values(exteriores.value).reduce((sum, val) => sum + val, 0);
});

const totalVehiculos = computed(() => {
    return vehiculos.value.carros + vehiculos.value.motos + vehiculos.value.bicicletas;
});

const totalIglekidsPersonal = computed(() => {
    return (
        iglekids.value.coordinadoras +
        iglekids.value.supervisoras +
        iglekids.value.maestros +
        iglekids.value.recrearte +
        iglekids.value.regikids +
        iglekids.value.logikids +
        iglekids.value.saludKids +
        iglekids.value.yoSoy
    );
});

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

const form = useForm({
    exteriores: exteriores.value,
    vehiculos: vehiculos.value,
    iglekids: iglekids.value,
});

const guardar = () => {
    form.post(route('areas.a4.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Mostrar mensaje de éxito
        },
    });
};
</script>

<template>
    <AppLayout title="Área A4">
        <div class="space-y-3">
            <!-- Breadcrumb/Back button -->
            <Link
                v-if="servicio_id"
                :href="route('servicios.show', servicio_id)"
                class="inline-flex items-center text-sm text-gray-500 transition hover:text-gray-700"
            >
                <svg class="mr-1 size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Volver al servicio
            </Link>

            <!-- Header -->
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="m-0 text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">
                    Área A4
                </h1>
                <div class="flex items-center gap-2">
                    <p class="text-xs text-gray-500">{{ fechaHoraActual }}</p>
                </div>
            </div>

            <!-- Summary -->
            <Card variant="accent">
                <CardHeader compact>
                    <CardTitle size="sm">Resumen A4</CardTitle>
                </CardHeader>
                <CardContent compact class="pt-2">
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="rounded-lg bg-white/60 px-2 py-2">
                            <p class="text-lg font-bold text-gray-900">{{ totalExteriores }}</p>
                            <p class="text-xs text-gray-600">Exteriores</p>
                        </div>
                        <div class="rounded-lg bg-white/60 px-2 py-2">
                            <p class="text-lg font-bold text-gray-900">{{ totalVehiculos }}</p>
                            <p class="text-xs text-gray-600">Vehículos</p>
                        </div>
                        <div class="rounded-lg bg-white/60 px-2 py-2">
                            <p class="text-lg font-bold text-blue-600">{{ totalIglekidsPersonal }}</p>
                            <p class="text-xs text-gray-600">Iglekids</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Exteriores -->
            <ExterioresCard
                title="Exteriores"
                :fields="[
                    { label: 'Servidores', value: exteriores.servidores, onChange: (v) => updateExterior('servidores', v) },
                    { label: 'Logística', value: exteriores.logistica, onChange: (v) => updateExterior('logistica', v) },
                    { label: 'Coffee', value: exteriores.coffee, onChange: (v) => updateExterior('coffee', v) },
                    { label: 'Container', value: exteriores.container, onChange: (v) => updateExterior('container', v) },
                ]"
            />

            <!-- Vehículos e Iglekids -->
            <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                <VehiculosCard :vehiculos="vehiculos" @update="updateVehiculo" />
                <IglekidsCard :data="iglekids" @update="updateIglekids" />
            </div>

            <!-- Guardar -->
            <div class="flex justify-end gap-2 pt-1">
                <Link
                    v-if="servicio_id"
                    :href="route('servicios.show', servicio_id)"
                >
                    <SecondaryButton>
                        Cancelar
                    </SecondaryButton>
                </Link>
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
