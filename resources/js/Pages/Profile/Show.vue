<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
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
        <template #header>
            <div class="flex items-center gap-3">
                <Link
                    :href="route('configuraciones.index')"
                    class="rounded-md p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                    aria-label="Volver a configuraciones"
                >
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div>
                    <h1 class="text-xl font-semibold text-gray-800">
                        Perfil
                    </h1>
                    <p class="mt-0.5 text-sm text-gray-500">
                        Información de tu cuenta, seguridad y sesiones.
                    </p>
                </div>
            </div>
        </template>

        <div class="mx-auto max-w-3xl space-y-8">
            <div v-if="$page.props.jetstream.canUpdateProfileInformation">
                <UpdateProfileInformationForm :user="$page.props.auth.user" />
            </div>

            <template v-if="$page.props.jetstream.canUpdateProfileInformation && ($page.props.jetstream.canUpdatePassword || $page.props.jetstream.canManageTwoFactorAuthentication)">
                <SectionBorder />
            </template>

            <div v-if="$page.props.jetstream.canUpdatePassword">
                <UpdatePasswordForm />
            </div>

            <template v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
                <SectionBorder />
                <TwoFactorAuthenticationForm
                    :requires-confirmation="confirmsTwoFactorAuthentication"
                />
            </template>

            <SectionBorder />
            <LogoutOtherBrowserSessionsForm :sessions="sessions" />

            <template v-if="$page.props.jetstream.hasAccountDeletionFeatures">
                <SectionBorder />
                <DeleteUserForm />
            </template>
        </div>
    </AppLayout>
</template>
