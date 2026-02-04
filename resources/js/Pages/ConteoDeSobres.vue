<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardTitle from '@/Components/CardTitle.vue';
import CardContent from '@/Components/CardContent.vue';
import SobreCard from '@/Components/Sobres/SobreCard.vue';
import NumberInput from '@/Components/NumberInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const canastas = ref({
    entregadas: 0,
});

const ofrendas = ref({
    inicial: 0,
    recibidos: 0,
    entregados: 0,
});

const protemplo = ref({
    inicial: 0,
    recibidos: 0,
    entregados: 0,
});

const iglekids = ref({
    inicial: 0,
    recibidos: 0,
    entregados: 0,
});

const updateOfrenda = (key, value) => {
    ofrendas.value[key] = value;
};

const updateProtemplo = (key, value) => {
    protemplo.value[key] = value;
};

const updateIglekids = (key, value) => {
    iglekids.value[key] = value;
};

const totalOfrendas = computed(() => ofrendas.value.inicial + ofrendas.value.recibidos);
const finalOfrendas = computed(() => totalOfrendas.value - ofrendas.value.entregados);

const totalProtemplo = computed(() => protemplo.value.inicial + protemplo.value.recibidos);
const finalProtemplo = computed(() => totalProtemplo.value - protemplo.value.entregados);

const totalIglekids = computed(() => iglekids.value.inicial + iglekids.value.recibidos);
const finalIglekids = computed(() => totalIglekids.value - iglekids.value.entregados);

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
    canastas: canastas.value,
    ofrendas: ofrendas.value,
    protemplo: protemplo.value,
    iglekids: iglekids.value,
});

const guardar = () => {
    form.post(route('conteo-sobres.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Mostrar mensaje de éxito
        },
    });
};
</script>

<template>
    <AppLayout title="Conteo de Sobres">
        <div class="space-y-3">
            <!-- Header -->
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="m-0 text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">
                    Conteo de Sobres
                </h1>
                <div class="flex items-center gap-2">
                    <p class="text-xs text-gray-500">{{ fechaHoraActual }}</p>
                </div>
            </div>

            <!-- Resumen -->
            <Card variant="accent">
                <CardContent compact class="pb-3 pt-3">
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div>
                            <p class="text-xl font-bold text-gray-900">{{ finalOfrendas }}</p>
                            <p class="text-xs text-gray-600">Ofrendas</p>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-gray-900">{{ finalProtemplo }}</p>
                            <p class="text-xs text-gray-600">Protemplo</p>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-blue-600">{{ finalIglekids }}</p>
                            <p class="text-xs text-gray-600">Iglekids</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Canastas -->
            <Card>
                <CardHeader compact>
                    <CardTitle size="sm">Canastas Ofrendas</CardTitle>
                </CardHeader>
                <CardContent compact>
                    <NumberInput
                        label="Entregadas"
                        v-model="canastas.entregadas"
                    />
                </CardContent>
            </Card>

            <!-- Sobres Grid -->
            <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                <SobreCard
                    title="Sobres Ofrendas"
                    :data="ofrendas"
                    @update="updateOfrenda"
                />
                <SobreCard
                    title="Sobres Protemplo"
                    :data="protemplo"
                    @update="updateProtemplo"
                />
                <SobreCard
                    title="Sobres Iglekids"
                    :data="iglekids"
                    @update="updateIglekids"
                />
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
