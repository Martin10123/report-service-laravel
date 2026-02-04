<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardTitle from '@/Components/CardTitle.vue';
import CardContent from '@/Components/CardContent.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

// Props placeholder (cuando venga del backend)
const props = defineProps({
    servicio: {
        type: Object,
        default: () => ({
            sede: 'Villa Grande',
            fecha: '2026-02-04',
            numero_servicio: 1,
            dia_semana: 'MIÉRCOLES',
            hora: '08:00 AM',
        }),
    },
    data: {
        type: Object,
        default: () => ({
            asistencia: {
                enSillas: 467,
                enGradas: 56,
                ninosAuditorio: 18,
                ninosIglekids: 35,
                totalAuditorio: 576,
                servidores: {
                    servidores: 24,
                    consolidacion: 12,
                    comunicaciones: 7,
                    logistica: 9,
                    jesusPlace: 4,
                    datafono: 8,
                    coffee: 2,
                    ministerial: 2,
                    alabanza: 8,
                    vip: 1,
                    iglekids: 55,
                },
                totalServidores: 132,
                totalPersonasIglesia: 708,
            },
            vehiculos: {
                carros: 104,
                motos: 35,
                bicicletas: 2,
                total: 141,
            },
            ofrendas: {
                canastas: 8,
                sobresOfrendas: {
                    inicial: 987,
                    recibidos: 320,
                    total: 1307,
                    entregados: 218,
                    final: 1089,
                },
                sobresProtemplo: {
                    inicial: 0,
                    recibidos: 0,
                    total: 0,
                    entregados: 0,
                    final: 0,
                },
                sobresIglekids: {
                    inicial: 26,
                    recibidos: 1,
                    total: 27,
                    entregados: 1,
                    final: 26,
                },
            },
        }),
    },
});

const fechaFormateada = computed(() => {
    const fecha = new Date(props.servicio.fecha);
    return fecha.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
});

const exportarPDF = () => {
    window.open(route('informe-final.pdf'), '_blank');
};

const exportarExcel = () => {
    window.location.href = route('informe-final.excel');
};
</script>

<template>
    <AppLayout title="Informe Final">
        <div class="space-y-3">
            <!-- Header -->
            <Card variant="accent" class="border-l-4 border-l-blue-600">
                <CardContent class="p-4">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex-1">
                            <div class="mb-3 inline-flex items-center rounded-full bg-blue-100 px-3 py-1">
                                <svg class="mr-2 h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-xs font-semibold text-blue-600">Informe de Servicio Grupo 3</span>
                            </div>
                            <div class="mt-2 grid grid-cols-2 gap-x-6 gap-y-2 sm:grid-cols-4">
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Fecha</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ fechaFormateada }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">N° Servicio</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ servicio.numero_servicio }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Sede</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ servicio.sede }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Horario</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ servicio.dia_semana }} {{ servicio.hora }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 sm:flex-row">
                            <SecondaryButton @click="exportarExcel" class="justify-center gap-2 shadow-md hover:shadow-lg">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="font-semibold">Exportar Excel</span>
                            </SecondaryButton>
                            <PrimaryButton @click="exportarPDF" class="justify-center gap-2 shadow-md hover:shadow-lg">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <span class="font-semibold">Exportar PDF</span>
                            </PrimaryButton>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- 1. Asistencia Personas -->
            <Card>
                <CardHeader compact>
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500 text-sm font-bold text-white">
                            1
                        </div>
                        <CardTitle size="base">Asistencia Personas</CardTitle>
                    </div>
                </CardHeader>
                <CardContent compact>
                    <div class="space-y-4">
                        <!-- Asistencia en Auditorio -->
                        <div>
                            <h3 class="mb-3 text-sm font-semibold text-gray-900">Asistencia en Auditorio</h3>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                <div class="rounded-lg border border-gray-200 bg-white p-3">
                                    <p class="text-xs text-gray-500">En Sillas</p>
                                    <p class="mt-1 text-xl font-bold text-gray-900">{{ data.asistencia.enSillas }}</p>
                                </div>
                                <div class="rounded-lg border border-gray-200 bg-white p-3">
                                    <p class="text-xs text-gray-500">En Gradas</p>
                                    <p class="mt-1 text-xl font-bold text-gray-900">{{ data.asistencia.enGradas }}</p>
                                </div>
                                <div class="rounded-lg border border-gray-200 bg-white p-3">
                                    <p class="text-xs text-gray-500">Niños Auditorio</p>
                                    <p class="mt-1 text-xl font-bold text-gray-900">{{ data.asistencia.ninosAuditorio }}</p>
                                </div>
                                <div class="rounded-lg border border-gray-200 bg-white p-3">
                                    <p class="text-xs text-gray-500">Niños Iglekids</p>
                                    <p class="mt-1 text-xl font-bold text-gray-900">{{ data.asistencia.ninosIglekids }}</p>
                                </div>
                            </div>
                            <div class="mt-3 rounded-lg bg-blue-50 p-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-gray-900">Total Auditorio</span>
                                    <span class="text-2xl font-bold text-blue-600">{{ data.asistencia.totalAuditorio }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Área de Servidores -->
                        <div class="border-t border-gray-200 pt-4">
                            <h3 class="mb-3 text-sm font-semibold text-gray-900">Área de Servidores</h3>
                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
                                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2">
                                    <span class="text-xs text-gray-600">Servidores</span>
                                    <span class="font-semibold text-gray-900">{{ data.asistencia.servidores.servidores }}</span>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2">
                                    <span class="text-xs text-gray-600">Consolidación</span>
                                    <span class="font-semibold text-gray-900">{{ data.asistencia.servidores.consolidacion }}</span>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2">
                                    <span class="text-xs text-gray-600">Comunicaciones</span>
                                    <span class="font-semibold text-gray-900">{{ data.asistencia.servidores.comunicaciones }}</span>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2">
                                    <span class="text-xs text-gray-600">Logística</span>
                                    <span class="font-semibold text-gray-900">{{ data.asistencia.servidores.logistica }}</span>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2">
                                    <span class="text-xs text-gray-600">Jesus Place</span>
                                    <span class="font-semibold text-gray-900">{{ data.asistencia.servidores.jesusPlace }}</span>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2">
                                    <span class="text-xs text-gray-600">Datáfono</span>
                                    <span class="font-semibold text-gray-900">{{ data.asistencia.servidores.datafono }}</span>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2">
                                    <span class="text-xs text-gray-600">Coffee</span>
                                    <span class="font-semibold text-gray-900">{{ data.asistencia.servidores.coffee }}</span>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2">
                                    <span class="text-xs text-gray-600">Ministerial</span>
                                    <span class="font-semibold text-gray-900">{{ data.asistencia.servidores.ministerial }}</span>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2">
                                    <span class="text-xs text-gray-600">Alabanza</span>
                                    <span class="font-semibold text-gray-900">{{ data.asistencia.servidores.alabanza }}</span>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2">
                                    <span class="text-xs text-gray-600">VIP</span>
                                    <span class="font-semibold text-gray-900">{{ data.asistencia.servidores.vip }}</span>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2">
                                    <span class="text-xs text-gray-600">Iglekids</span>
                                    <span class="font-semibold text-gray-900">{{ data.asistencia.servidores.iglekids }}</span>
                                </div>
                            </div>
                            <div class="mt-3 rounded-lg bg-blue-50 p-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-gray-900">Total Área Servidores</span>
                                    <span class="text-2xl font-bold text-blue-600">{{ data.asistencia.totalServidores }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Total General -->
                        <div class="rounded-lg border-2 border-blue-500 bg-gradient-to-r from-blue-50 to-blue-100 p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-gray-900">Total Personas Iglesia</span>
                                <span class="text-3xl font-bold text-blue-600">{{ data.asistencia.totalPersonasIglesia }}</span>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- 2. Vehículos -->
            <Card>
                <CardHeader compact>
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500 text-sm font-bold text-white">
                            2
                        </div>
                        <CardTitle size="base">Vehículos</CardTitle>
                    </div>
                </CardHeader>
                <CardContent compact>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div class="rounded-lg border border-gray-200 bg-white p-3 text-center">
                            <p class="text-xs text-gray-500">Carros</p>
                            <p class="mt-1 text-2xl font-bold text-gray-900">{{ data.vehiculos.carros }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-3 text-center">
                            <p class="text-xs text-gray-500">Motos</p>
                            <p class="mt-1 text-2xl font-bold text-gray-900">{{ data.vehiculos.motos }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-3 text-center">
                            <p class="text-xs text-gray-500">Bicicletas</p>
                            <p class="mt-1 text-2xl font-bold text-gray-900">{{ data.vehiculos.bicicletas }}</p>
                        </div>
                        <div class="rounded-lg bg-blue-50 p-3 text-center">
                            <p class="text-xs font-semibold text-gray-700">Total Vehículos</p>
                            <p class="mt-1 text-2xl font-bold text-blue-600">{{ data.vehiculos.total }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- 3. Ofrendas -->
            <Card>
                <CardHeader compact>
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500 text-sm font-bold text-white">
                            3
                        </div>
                        <CardTitle size="base">Ofrendas</CardTitle>
                    </div>
                </CardHeader>
                <CardContent compact>
                    <div class="space-y-4">
                        <!-- Canastas -->
                        <div class="rounded-lg border border-gray-200 bg-white p-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-gray-900">Canastas Entregadas</span>
                                <span class="text-xl font-bold text-gray-900">{{ data.ofrendas.canastas }}</span>
                            </div>
                        </div>

                        <!-- Inventario de Sobres -->
                        <div class="grid grid-cols-1 gap-3 lg:grid-cols-3">
                            <!-- Sobres Ofrendas -->
                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                <h4 class="mb-3 text-sm font-semibold text-gray-900">Sobres Ofrendas</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Inicial</span>
                                        <span class="font-semibold">{{ data.ofrendas.sobresOfrendas.inicial }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Recibidos</span>
                                        <span class="font-semibold">{{ data.ofrendas.sobresOfrendas.recibidos }}</span>
                                    </div>
                                    <div class="flex justify-between border-t border-gray-200 pt-2">
                                        <span class="font-semibold text-gray-700">Total</span>
                                        <span class="font-bold text-gray-900">{{ data.ofrendas.sobresOfrendas.total }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Entregados</span>
                                        <span class="font-semibold">{{ data.ofrendas.sobresOfrendas.entregados }}</span>
                                    </div>
                                    <div class="flex justify-between rounded-lg bg-blue-50 p-2">
                                        <span class="font-semibold text-gray-900">Final</span>
                                        <span class="text-lg font-bold text-blue-600">{{ data.ofrendas.sobresOfrendas.final }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Sobres Protemplo -->
                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                <h4 class="mb-3 text-sm font-semibold text-gray-900">Sobres Protemplo</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Inicial</span>
                                        <span class="font-semibold">{{ data.ofrendas.sobresProtemplo.inicial }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Recibidos</span>
                                        <span class="font-semibold">{{ data.ofrendas.sobresProtemplo.recibidos }}</span>
                                    </div>
                                    <div class="flex justify-between border-t border-gray-200 pt-2">
                                        <span class="font-semibold text-gray-700">Total</span>
                                        <span class="font-bold text-gray-900">{{ data.ofrendas.sobresProtemplo.total }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Entregados</span>
                                        <span class="font-semibold">{{ data.ofrendas.sobresProtemplo.entregados }}</span>
                                    </div>
                                    <div class="flex justify-between rounded-lg bg-blue-50 p-2">
                                        <span class="font-semibold text-gray-900">Final</span>
                                        <span class="text-lg font-bold text-blue-600">{{ data.ofrendas.sobresProtemplo.final }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Sobres Iglekids -->
                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                <h4 class="mb-3 text-sm font-semibold text-gray-900">Sobres Iglekids</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Inicial</span>
                                        <span class="font-semibold">{{ data.ofrendas.sobresIglekids.inicial }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Recibidos</span>
                                        <span class="font-semibold">{{ data.ofrendas.sobresIglekids.recibidos }}</span>
                                    </div>
                                    <div class="flex justify-between border-t border-gray-200 pt-2">
                                        <span class="font-semibold text-gray-700">Total</span>
                                        <span class="font-bold text-gray-900">{{ data.ofrendas.sobresIglekids.total }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Entregados</span>
                                        <span class="font-semibold">{{ data.ofrendas.sobresIglekids.entregados }}</span>
                                    </div>
                                    <div class="flex justify-between rounded-lg bg-blue-50 p-2">
                                        <span class="font-semibold text-gray-900">Final</span>
                                        <span class="text-lg font-bold text-blue-600">{{ data.ofrendas.sobresIglekids.final }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
