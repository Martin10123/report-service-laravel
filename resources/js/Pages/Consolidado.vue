<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardTitle from '@/Components/CardTitle.vue';
import CardContent from '@/Components/CardContent.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    servicio_id: [String, Number],
    servicio: {
        type: Object,
        default: () => ({
            sede: '',
            fecha: new Date().toISOString().split('T')[0],
            numero_servicio: 0,
        }),
    },
    data: {
        type: Object,
        default: () => ({
            fecha: '',
            areas: [],
            auditorio: {},
            servidores: {},
            parqueadero: {},
        }),
    },
});

const fechaFormateada = computed(() => {
    if (!props.data?.fecha) return '';
    const fecha = new Date(props.data.fecha);
    return fecha.toLocaleDateString('es-ES', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
});

const areas = computed(() => props.data.areas || []);
const tieneMultiplesAreas = computed(() => areas.value.length > 1);

const exportarPDF = () => {
    window.open(route('consolidado.pdf', { servicio_id: props.servicio_id }), '_blank');
};

const exportarExcel = () => {
    window.location.href = route('consolidado.excel', { servicio_id: props.servicio_id });
};
</script>

<template>
    <AppLayout title="Consolidado">
        <div class="space-y-4">
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
            <Card variant="accent" class="border-l-4 border-l-blue-600">
                <CardContent class="p-4">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex-1">
                            <div class="mb-3 inline-flex items-center rounded-full bg-blue-100 px-3 py-1">
                                <svg class="mr-2 h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <span class="text-xs font-semibold text-blue-600">Consolidado de Servicio</span>
                            </div>
                            <div class="mt-2 flex items-center gap-6">
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Fecha</p>
                                    <p class="text-lg font-bold text-gray-900">{{ fechaFormateada }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Sede</p>
                                    <p class="text-lg font-bold text-gray-900">{{ servicio.sede }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 sm:flex-row">
                            <SecondaryButton @click="exportarExcel" class="justify-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Excel
                            </SecondaryButton>
                            <PrimaryButton @click="exportarPDF" class="justify-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                PDF
                            </PrimaryButton>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- AUDITORIO -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-white">AUDITORIO</CardTitle>
                </CardHeader>
                <CardContent class="overflow-x-auto p-0">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-300 bg-gray-50">
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">RESUMEN GENERAL</th>
                                <th v-for="area in areas" :key="area" class="px-4 py-3 text-center text-sm font-semibold text-gray-700">
                                    {{ area }}
                                </th>
                                <th v-if="tieneMultiplesAreas" class="px-4 py-3 text-center text-sm font-semibold text-gray-900 bg-blue-50">
                                    TOTALES
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">Sillas del área.</td>
                                <td v-for="area in areas" :key="`sillas-${area}`" class="px-4 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ data.auditorio[area]?.sillas || 0 }}
                                </td>
                                <td v-if="tieneMultiplesAreas" class="px-4 py-3 text-center text-sm font-bold text-gray-900 bg-blue-50">
                                    {{ data.auditorio.totales?.sillas || 0 }}
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">Sillas vacías</td>
                                <td v-for="area in areas" :key="`vacias-${area}`" class="px-4 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ data.auditorio[area]?.sillas_vacias || 0 }}
                                </td>
                                <td v-if="tieneMultiplesAreas" class="px-4 py-3 text-center text-sm font-bold text-gray-900 bg-blue-50">
                                    {{ data.auditorio.totales?.sillas_vacias || 0 }}
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">Total Personas</td>
                                <td v-for="area in areas" :key="`personas-${area}`" class="px-4 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ data.auditorio[area]?.total_personas || 0 }}
                                </td>
                                <td v-if="tieneMultiplesAreas" class="px-4 py-3 text-center text-sm font-bold text-gray-900 bg-blue-50">
                                    {{ data.auditorio.totales?.total_personas || 0 }}
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">Total niños</td>
                                <td v-for="area in areas" :key="`ninos-${area}`" class="px-4 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ data.auditorio[area]?.total_ninos || 0 }}
                                </td>
                                <td v-if="tieneMultiplesAreas" class="px-4 py-3 text-center text-sm font-bold text-gray-900 bg-blue-50">
                                    {{ data.auditorio.totales?.total_ninos || 0 }}
                                </td>
                            </tr>
                            <tr class="bg-blue-100 font-semibold">
                                <td class="px-4 py-3 text-sm text-gray-900">Total AUDITORIO</td>
                                <td v-for="area in areas" :key="`total-${area}`" class="px-4 py-3 text-center text-sm font-bold text-gray-900">
                                    {{ data.auditorio[area]?.total_area || 0 }}
                                </td>
                                <td v-if="tieneMultiplesAreas" class="px-4 py-3 text-center text-sm font-bold text-gray-900">
                                    
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </CardContent>
            </Card>

            <!-- SERVIDORES -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-white">SERVIDORES</CardTitle>
                </CardHeader>
                <CardContent class="overflow-x-auto p-0">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-300 bg-gray-50">
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Áreas</th>
                                <th v-for="area in areas" :key="area" class="px-4 py-3 text-center text-sm font-semibold text-gray-700">
                                    {{ area }}
                                </th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-900 bg-blue-50">
                                    TOTAL
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">Servidores:</td>
                                <td v-for="area in areas" :key="`serv-${area}`" class="px-4 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ data.servidores[area]?.servidores || '' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-900 bg-blue-50">
                                    {{ data.servidores.totales?.servidores || 0 }}
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">Consolidación:</td>
                                <td v-for="area in areas" :key="`cons-${area}`" class="px-4 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ data.servidores[area]?.consolidacion || '' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-900 bg-blue-50">
                                    {{ data.servidores.totales?.consolidacion || 0 }}
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">Comunicaciones:</td>
                                <td v-for="area in areas" :key="`com-${area}`" class="px-4 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ data.servidores[area]?.comunicaciones || '' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-900 bg-blue-50">
                                    {{ data.servidores.totales?.comunicaciones || 0 }}
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">Logística:</td>
                                <td v-for="area in areas" :key="`log-${area}`" class="px-4 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ data.servidores[area]?.logistica || '' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-900 bg-blue-50">
                                    {{ data.servidores.totales?.logistica || 0 }}
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">Jesus place:</td>
                                <td v-for="area in areas" :key="`jp-${area}`" class="px-4 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ data.servidores[area]?.jesusPlace || '' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-900 bg-blue-50">
                                    {{ data.servidores.totales?.jesusPlace || 0 }}
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">Datáfono:</td>
                                <td v-for="area in areas" :key="`dat-${area}`" class="px-4 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ data.servidores[area]?.datafono || '' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-900 bg-blue-50">
                                    {{ data.servidores.totales?.datafono || 0 }}
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">Coffee:</td>
                                <td v-for="area in areas" :key="`cof-${area}`" class="px-4 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ data.servidores[area]?.coffee || '' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-900 bg-blue-50">
                                    {{ data.servidores.totales?.coffee || 0 }}
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">Ministerial:</td>
                                <td v-for="area in areas" :key="`min-${area}`" class="px-4 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ data.servidores[area]?.ministerial || '' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-900 bg-blue-50">
                                    {{ data.servidores.totales?.ministerial || 0 }}
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">Alabanza:</td>
                                <td v-for="area in areas" :key="`ala-${area}`" class="px-4 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ data.servidores[area]?.alabanza || '' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-900 bg-blue-50">
                                    {{ data.servidores.totales?.alabanza || 0 }}
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">VIP:</td>
                                <td v-for="area in areas" :key="`vip-${area}`" class="px-4 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ data.servidores[area]?.vip || '' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-900 bg-blue-50">
                                    {{ data.servidores.totales?.vip || 0 }}
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">Iglekids:</td>
                                <td v-for="area in areas" :key="`igk-${area}`" class="px-4 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ data.servidores[area]?.iglekids || '' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-900 bg-blue-50">
                                    {{ data.servidores.totales?.iglekids || 0 }}
                                </td>
                            </tr>
                            <tr class="bg-blue-100 font-semibold">
                                <td class="px-4 py-3 text-sm text-gray-900">Total Servidores</td>
                                <td v-for="area in areas" :key="`total-serv-${area}`" class="px-4 py-3 text-center text-sm font-bold text-gray-900">
                                    {{ data.servidores[area]?.total || 0 }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-900 bg-blue-50">
                                    {{ data.servidores.totales?.total || 0 }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </CardContent>
            </Card>

            <!-- PARQUEADERO (solo si tiene datos) -->
            <Card v-if="data.parqueadero && Object.keys(data.parqueadero).length > 0">
                <CardHeader>
                    <CardTitle class="text-white">PARQUEADERO</CardTitle>
                </CardHeader>
                <CardContent class="overflow-x-auto p-0">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-300 bg-gray-50">
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">VEHÍCULOS</th>
                                <th v-for="area in areas" :key="area" class="px-4 py-3 text-center text-sm font-semibold text-gray-700">
                                    {{ area }}
                                </th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-900 bg-blue-50">
                                    TOTAL
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">Carros</td>
                                <td v-for="area in areas" :key="`carros-${area}`" class="px-4 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ data.parqueadero[area]?.carros || '' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-900 bg-blue-50">
                                    {{ data.parqueadero.totales?.carros || 0 }}
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">Motos</td>
                                <td v-for="area in areas" :key="`motos-${area}`" class="px-4 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ data.parqueadero[area]?.motos || '' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-900 bg-blue-50">
                                    {{ data.parqueadero.totales?.motos || 0 }}
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">Bicicletas</td>
                                <td v-for="area in areas" :key="`bici-${area}`" class="px-4 py-3 text-center text-sm font-medium text-gray-900">
                                    {{ data.parqueadero[area]?.bicicletas || '' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-900 bg-blue-50">
                                    {{ data.parqueadero.totales?.bicicletas || 0 }}
                                </td>
                            </tr>
                            <tr class="bg-blue-100 font-semibold">
                                <td class="px-4 py-3 text-sm text-gray-900">Total Vehículos</td>
                                <td v-for="area in areas" :key="`total-veh-${area}`" class="px-4 py-3 text-center text-sm font-bold text-gray-900">
                                    {{ data.parqueadero[area]?.total || 0 }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-900 bg-blue-50">
                                    {{ data.parqueadero.totales?.total || 0 }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
