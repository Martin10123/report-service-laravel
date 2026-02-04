<script setup>
import { ref, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardTitle from '@/Components/CardTitle.vue';
import CardContent from '@/Components/CardContent.vue';
import SillasPersonasSection from '@/Components/Areas/SillasPersonasSection.vue';
import ServidoresGridCard from '@/Components/Areas/ServidoresGridCard.vue';
import ServidorasPastoraCard from '@/Components/Areas/ServidorasPastoraCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useSillasPersonas } from '@/Composables/useSillasPersonas';

const props = defineProps({
    servicio_id: [String, Number],
});

const INITIAL_SILLAS = {
    totalSillas: 270,
    sillasVacias: 0,
    totalPersonas: 2,
    totalNinos: 0,
};

const sillasPersonas = useSillasPersonas(INITIAL_SILLAS);

const servidores = ref({
    servidores: 0,
    comunicaciones: 0,
    logistica: 0,
    alabanza: 0,
});

const servidorasPastora = ref(['']);

const updateServidor = (key, value) => {
    servidores.value[key] = value;
};

const totalServidores = computed(() => {
    const totalServ =
        servidores.value.servidores +
        servidores.value.comunicaciones +
        servidores.value.logistica +
        servidores.value.alabanza +
        servidorasPastora.value.filter((n) => n.trim()).length;
    return totalServ;
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
    sillas: sillasPersonas.data,
    servidores: servidores.value,
    servidorasPastora: servidorasPastora.value,
});

const guardar = () => {
    form.post(route('areas.a1.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Mostrar mensaje de éxito
        },
    });
};

const updateServidorasPastora = (index, value) => {
    servidorasPastora.value[index] = value;
};

const addServidoraPastora = () => {
    servidorasPastora.value.push('');
};

const removeServidoraPastora = (index) => {
    servidorasPastora.value.splice(index, 1);
};
</script>

<template>
    <AppLayout title="Área A1">
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
                    Área A1
                </h1>
                <div class="flex items-center gap-2">
                    <p class="text-xs text-gray-500">{{ fechaHoraActual }}</p>
                </div>
            </div>

            <!-- Summary -->
            <Card variant="accent">
                <CardHeader compact>
                    <CardTitle size="sm">Resumen A1</CardTitle>
                </CardHeader>
                <CardContent compact class="pt-2">
                    <div class="grid grid-cols-2 gap-2 text-center">
                        <div class="rounded-lg bg-white/60 px-2 py-2">
                            <p class="text-lg font-bold text-gray-900">{{ sillasPersonas.data.totalPersonas }}</p>
                            <p class="text-xs text-gray-600">Total Personas</p>
                        </div>
                        <div class="rounded-lg bg-white/60 px-2 py-2">
                            <p class="text-lg font-bold text-blue-600">{{ totalServidores }}</p>
                            <p class="text-xs text-gray-600">Servidores</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Grid Principal: Personas | Servidores -->
            <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                <!-- Columna Izquierda: Personas -->
                <div class="space-y-3">
                    <div class="rounded-lg border border-gray-200 bg-white p-3 shadow-sm">
                        <h2 class="mb-3 text-sm font-bold text-gray-900">Personas</h2>
                        <SillasPersonasSection
                            :data="sillasPersonas.data"
                            ninos-label="Niños A1"
                            @update="sillasPersonas.update"
                        />
                    </div>
                </div>

                <!-- Columna Derecha: Servidores -->
                <div class="space-y-3">
                    <div class="rounded-lg border border-gray-200 bg-white p-3 shadow-sm">
                        <h2 class="mb-3 text-sm font-bold text-gray-900">Servidores</h2>
                        <div class="space-y-3">
                            <ServidoresGridCard
                                title="Servidores"
                                :fields="[
                                    { label: 'Servidores', value: servidores.servidores, onChange: (v) => updateServidor('servidores', v) },
                                    { label: 'Comunicaciones', value: servidores.comunicaciones, onChange: (v) => updateServidor('comunicaciones', v) },
                                    { label: 'Logística', value: servidores.logistica, onChange: (v) => updateServidor('logistica', v) },
                                    { label: 'Alabanza', value: servidores.alabanza, onChange: (v) => updateServidor('alabanza', v) },
                                ]"
                            />

                            <ServidorasPastoraCard
                                :nombres="servidorasPastora"
                                @update="updateServidorasPastora"
                                @add="addServidoraPastora"
                                @remove="removeServidoraPastora"
                            />
                        </div>
                    </div>
                </div>
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
