<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardTitle from '@/Components/CardTitle.vue';
import CardContent from '@/Components/CardContent.vue';

const temaActual = ref('light');

const temas = [
    { id: 'light', name: 'Claro', icon: '☀️' },
    { id: 'dark', name: 'Oscuro', icon: '🌙' },
    { id: 'auto', name: 'Automático', icon: '🌗' },
];

const cambiarTema = (tema) => {
    temaActual.value = tema;
    // Aquí se implementará la lógica real cuando haya backend
};
</script>

<template>
    <AppLayout title="Configuraciones">
        <div class="space-y-4">
            <!-- Header -->
            <div class="flex flex-col gap-2">
                <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">
                    Configuraciones
                </h1>
                <p class="text-sm text-gray-600">
                    Administra tu cuenta y preferencias de la aplicación.
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Cuenta y Perfil -->
                <Link
                    :href="route('profile.show')"
                    class="group block rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition hover:border-blue-300 hover:shadow-md"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600 transition group-hover:bg-blue-600 group-hover:text-white">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="font-semibold text-gray-900">
                                Perfil y Cuenta
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Información personal, contraseña y seguridad
                            </p>
                        </div>
                        <svg class="size-5 shrink-0 text-gray-400 transition group-hover:translate-x-1 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </Link>

                <!-- Roles y Permisos -->
                <Link
                    :href="route('roles-permisos.index')"
                    class="group block rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition hover:border-purple-300 hover:shadow-md"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-purple-100 text-purple-600 transition group-hover:bg-purple-600 group-hover:text-white">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="font-semibold text-gray-900">
                                Roles y Permisos
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Gestionar usuarios, roles y accesos
                            </p>
                        </div>
                        <svg class="size-5 shrink-0 text-gray-400 transition group-hover:translate-x-1 group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </Link>

                <!-- Auditorías -->
                <Link
                    :href="route('auditorias.index')"
                    class="group block rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition hover:border-indigo-300 hover:shadow-md"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 transition group-hover:bg-indigo-600 group-hover:text-white">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="font-semibold text-gray-900">
                                Auditorías
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Historial de actividades del sistema
                            </p>
                        </div>
                        <svg class="size-5 shrink-0 text-gray-400 transition group-hover:translate-x-1 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </Link>
            </div>

            <!-- Apariencia -->
            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2">
                        <div class="flex size-8 items-center justify-center rounded-lg bg-amber-100">
                            <span class="text-lg">🎨</span>
                        </div>
                        <CardTitle size="sm">Apariencia</CardTitle>
                    </div>
                </CardHeader>
                <CardContent>
                    <p class="mb-4 text-sm text-gray-600">Personaliza el tema visual de la aplicación</p>
                    <div class="grid grid-cols-3 gap-3">
                        <button
                            v-for="tema in temas"
                            :key="tema.id"
                            type="button"
                            @click="cambiarTema(tema.id)"
                            :class="[
                                'flex flex-col items-center gap-2 rounded-lg border-2 p-3 transition',
                                temaActual === tema.id
                                    ? 'border-blue-500 bg-blue-50'
                                    : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'
                            ]"
                        >
                            <span class="text-2xl">{{ tema.icon }}</span>
                            <span class="text-sm font-medium text-gray-900">{{ tema.name }}</span>
                            <svg
                                v-if="temaActual === tema.id"
                                class="size-4 text-blue-600"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </CardContent>
            </Card>

            <!-- Sedes -->
            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2">
                        <div class="flex size-8 items-center justify-center rounded-lg bg-orange-100">
                            <svg class="size-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <CardTitle size="sm">Gestión de Sedes</CardTitle>
                    </div>
                </CardHeader>
                <CardContent>
                    <p class="mb-3 text-sm text-gray-600">Administrar sedes y configuraciones específicas</p>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                    >
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Configurar sedes
                    </button>
                </CardContent>
            </Card>

            <!-- Datos y Exportación -->
            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2">
                        <div class="flex size-8 items-center justify-center rounded-lg bg-indigo-100">
                            <svg class="size-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </div>
                        <CardTitle size="sm">Datos y Exportación</CardTitle>
                    </div>
                </CardHeader>
                <CardContent>
                    <p class="mb-3 text-sm text-gray-600">Exportar reportes históricos y gestionar respaldos</p>
                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                        >
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Descargar datos
                        </button>
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                        >
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                            </svg>
                            Respaldo automático
                        </button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
