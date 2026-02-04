<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

<template>
    <AppLayout title="Configuraciones">
        <template #header>
            <h1 class="text-xl font-semibold text-gray-800">
                Configuraciones
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Administra tu cuenta y preferencias de la aplicación.
            </p>
        </template>

        <div class="mx-auto max-w-2xl space-y-4">
            <Link
                :href="route('profile.show')"
                class="flex items-start gap-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition hover:border-gray-300 hover:shadow"
            >
                <span class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary-600">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </span>
                <div class="min-w-0 flex-1">
                    <h2 class="font-medium text-gray-900">
                        Perfil
                    </h2>
                    <p class="mt-0.5 text-sm text-gray-500">
                        Nombre, correo electrónico y foto de perfil. Contraseña, sesiones y eliminación de cuenta.
                    </p>
                </div>
                <svg class="size-5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </Link>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-start gap-4">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <h2 class="font-medium text-gray-900">
                            Preferencias de la aplicación
                        </h2>
                        <p class="mt-0.5 text-sm text-gray-500">
                            Opciones generales (idioma, notificaciones, etc.) estarán disponibles próximamente.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
