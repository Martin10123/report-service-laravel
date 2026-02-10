<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
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

const toast = useToast();

const props = defineProps({
    servicio_id: [String, Number],
    servicio: Object,
    conteoA1: Object,
});

const INITIAL_SILLAS = props.conteoA1?.sillas || {
    totalSillas: 0,
    sillasVacias: 0,
    totalPersonas: 0,
    totalNinos: 0,
};

const sillasPersonas = useSillasPersonas(INITIAL_SILLAS);

const servidores = ref(props.conteoA1?.servidores || {
    servidores: 0,
    comunicaciones: 0,
    logistica: 0,
    alabanza: 0,
});

const servidorasPastora = ref(props.conteoA1?.servidorasPastora?.length > 0 ? props.conteoA1.servidorasPastora : ['']);

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

// Calcular automáticamente el total de personas
const totalPersonasCalculado = computed(() => {
    const sillasOcupadas = sillasPersonas.data.totalSillas - sillasPersonas.data.sillasVacias;
    const servidoresTotal = servidores.value.servidores + servidores.value.logistica + servidorasPastora.value.filter((n) => n.trim()).length;
    const resultado = Math.max(0, sillasOcupadas - servidoresTotal);
    
    console.log('=== A1 Cálculo Total Personas ===');
    console.log('Total sillas:', sillasPersonas.data.totalSillas);
    console.log('Sillas vacías:', sillasPersonas.data.sillasVacias);
    console.log('Sillas ocupadas:', sillasOcupadas);
    console.log('Servidores:', servidores.value.servidores);
    console.log('Logística:', servidores.value.logistica);
    console.log('Servidor de Pastora:', servidorasPastora.value.filter((n) => n.trim()).length);
    console.log('Servidores total:', servidoresTotal);
    console.log('Resultado final (Total Personas incluye niños):', resultado);
    console.log('Niños A1 (subconjunto de Total Personas):', sillasPersonas.data.totalNinos);
    console.log('===================================');
    
    return resultado;
});

// Calcular límites máximos para servidores (sin considerar niños en A1)
const limiteServidores = computed(() => {
    const sillasOcupadas = sillasPersonas.data.totalSillas - sillasPersonas.data.sillasVacias;
    return Math.max(0, sillasOcupadas);
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
    servicio_id: props.servicio_id,
    sillas: { ...sillasPersonas.data },
    servidores: { ...servidores.value },
    servidorasPastora: [...servidorasPastora.value],
    completado: props.conteoA1?.completado || false,
});

// Sincronizar el valor calculado con sillasPersonas.data y form
watch([() => sillasPersonas.data.totalSillas, () => sillasPersonas.data.sillasVacias, () => servidores.value.servidores, () => servidores.value.logistica, servidorasPastora], () => {
    console.log('🔵 [A1 Watch Principal] Ejecutándose');
    console.log('🔵 Valor calculado:', totalPersonasCalculado.value);
    
    sillasPersonas.data.totalPersonas = totalPersonasCalculado.value;
    form.sillas.totalPersonas = totalPersonasCalculado.value;
    
    console.log('🔵 Después - totalPersonas:', sillasPersonas.data.totalPersonas);
}, { immediate: true, deep: true });

// Sincronizar cambios manuales de sillasPersonas (excepto totalPersonas que es calculado)
watch([() => sillasPersonas.data.totalSillas, () => sillasPersonas.data.sillasVacias, () => sillasPersonas.data.totalNinos], () => {
    form.sillas.totalSillas = sillasPersonas.data.totalSillas;
    form.sillas.sillasVacias = sillasPersonas.data.sillasVacias;
    form.sillas.totalNinos = sillasPersonas.data.totalNinos;
    // NO sincronizamos totalPersonas aquí porque se calcula automáticamente
});

// Sincronizar servidores con form.servidores
watch([() => servidores.value.servidores, () => servidores.value.comunicaciones, () => servidores.value.logistica, () => servidores.value.alabanza], () => {
    form.servidores = { ...servidores.value };
});

// Sincronizar servidorasPastora con form.servidorasPastora
watch(servidorasPastora, (newValue) => {
    form.servidorasPastora = [...newValue];
}, { deep: true });

const guardar = () => {
    console.log('💾 [A1] Guardando form.sillas:', JSON.stringify(form.sillas));
    console.log('💾 [A1] totalPersonas a guardar:', form.sillas.totalPersonas);
    form.post(route('conteo-a1.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Redirigir al detalle del servicio después de guardar
            if (props.servicio_id) {
                router.visit(route('servicios.show', props.servicio_id));
            }
        },
        onError: (errors) => {
            console.error('Error al guardar:', errors);
            // Mostrar los errores al usuario con toast
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
                            :readonly-total-personas="true"
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
                                    { 
                                        label: 'Servidores', 
                                        value: servidores.servidores, 
                                        max: limiteServidores,
                                        error: servidores.servidores > limiteServidores ? `Máximo ${limiteServidores} (sillas ocupadas)` : '',
                                        onChange: (v) => updateServidor('servidores', v) 
                                    },
                                    { 
                                        label: 'Comunicaciones', 
                                        value: servidores.comunicaciones, 
                                        onChange: (v) => updateServidor('comunicaciones', v) 
                                    },
                                    { 
                                        label: 'Logística', 
                                        value: servidores.logistica, 
                                        max: limiteServidores,
                                        error: servidores.logistica > limiteServidores ? `Máximo ${limiteServidores} (sillas ocupadas)` : '',
                                        onChange: (v) => updateServidor('logistica', v) 
                                    },
                                    { 
                                        label: 'Alabanza', 
                                        value: servidores.alabanza, 
                                        onChange: (v) => updateServidor('alabanza', v) 
                                    },
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
