<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardTitle from '@/Components/CardTitle.vue';
import CardContent from '@/Components/CardContent.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});
</script>

<template>
    <AppLayout title="Perfil">
        <div class="space-y-4">
            <!-- Header -->
            <div class="flex items-center gap-3">
                <Link
                    :href="route('configuraciones.index')"
                    class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                    aria-label="Volver a configuraciones"
                >
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">
                        Perfil
                    </h1>
                    <p class="mt-0.5 text-sm text-gray-600">
                        Información de tu cuenta, seguridad y sesiones.
                    </p>
                </div>
            </div>

            <!-- Información del Perfil -->
            <div v-if="$page.props.jetstream.canUpdateProfileInformation">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <div class="flex size-8 items-center justify-center rounded-lg bg-blue-100">
                                <svg class="size-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <CardTitle size="sm">Información del Perfil</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <UpdateProfileInformationForm :user="$page.props.auth.user" />
                    </CardContent>
                </Card>
            </div>

            <!-- Actualizar Contraseña -->
            <div v-if="$page.props.jetstream.canUpdatePassword">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <div class="flex size-8 items-center justify-center rounded-lg bg-green-100">
                                <svg class="size-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <CardTitle size="sm">Actualizar Contraseña</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <UpdatePasswordForm />
                    </CardContent>
                </Card>
            </div>

            <!-- Autenticación de Dos Factores -->
            <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <div class="flex size-8 items-center justify-center rounded-lg bg-purple-100">
                                <svg class="size-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <CardTitle size="sm">Autenticación de Dos Factores</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <TwoFactorAuthenticationForm
                            :requires-confirmation="confirmsTwoFactorAuthentication"
                        />
                    </CardContent>
                </Card>
            </div>

            <!-- Sesiones del Navegador -->
            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2">
                        <div class="flex size-8 items-center justify-center rounded-lg bg-orange-100">
                            <svg class="size-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <CardTitle size="sm">Sesiones del Navegador</CardTitle>
                    </div>
                </CardHeader>
                <CardContent>
                    <LogoutOtherBrowserSessionsForm :sessions="sessions" />
                </CardContent>
            </Card>

            <!-- Eliminar Cuenta -->
            <div v-if="$page.props.jetstream.hasAccountDeletionFeatures">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <div class="flex size-8 items-center justify-center rounded-lg bg-red-100">
                                <svg class="size-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <CardTitle size="sm">Eliminar Cuenta</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <DeleteUserForm />
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
