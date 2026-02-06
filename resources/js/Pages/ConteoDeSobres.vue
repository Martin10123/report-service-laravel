<script setup>
import { ref, computed } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardTitle from '@/Components/CardTitle.vue';
import CardContent from '@/Components/CardContent.vue';
import SobreCard from '@/Components/Sobres/SobreCard.vue';
import NumberInput from '@/Components/NumberInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const toast = useToast();

const props = defineProps({
    servicio_id: [String, Number],
    servicio: Object,
    conteoSobres: Object,
});

const canastas = ref(props.conteoSobres?.canastas || {
    entregadas: 0,
});

const ofrendas = ref(props.conteoSobres?.ofrendas || {
    inicial: 0,
    recibidos: 0,
    entregados: 0,
});

const protemplo = ref(props.conteoSobres?.protemplo || {
    inicial: 0,
    recibidos: 0,
    entregados: 0,
});

const iglekids = ref(props.conteoSobres?.iglekids || {
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
    servicio_id: props.servicio_id,
    canastas: canastas.value,
    ofrendas: ofrendas.value,
    protemplo: protemplo.value,
    iglekids: iglekids.value,
    completado: props.conteoSobres?.completado || false,
});

const guardar = () => {
    form.canastas = canastas.value;
    form.ofrendas = ofrendas.value;
    form.protemplo = protemplo.value;
    form.iglekids = iglekids.value;
    
    form.post(route('conteo-sobres.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Éxito',
                detail: 'Conteo de sobres guardado exitosamente',
                life: 3000
            });
            if (props.servicio_id) {
                router.visit(route('servicios.show', props.servicio_id));
            }
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
</script>

<template>
    <AppLayout title="Conteo de Sobres">
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
                <div class="flex justify-end gap-2">
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
                        <svg class="mr-2 size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ form.completado ? 'Guardar y completar' : 'Guardar' }}
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
