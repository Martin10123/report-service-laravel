<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import InlineMessage from 'primevue/inlinemessage';
import AuthLayout from '@/Components/Layout/AuthLayout.vue';
import AuthCard from '@/Components/Layout/AuthCard.vue';
import loginImg from '@/images/login-img.svg';

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Restablecer contraseña" />

    <AuthLayout
        :image-src="loginImg"
        image-alt="Restablecer contraseña"
        :image-on-left="false"
    >
        <AuthCard>
            <div class="space-y-6">
                <div class="space-y-2 text-center lg:text-left">
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                        Nueva contraseña
                    </h1>
                    <p class="text-base text-gray-600">
                        Elige una contraseña segura para tu cuenta.
                    </p>
                </div>

                <form class="space-y-5" @submit.prevent="submit">
                    <div class="space-y-2">
                        <label
                            for="email"
                            class="text-sm font-medium text-gray-700"
                        >
                            Correo electrónico
                        </label>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-envelope" />
                            </InputIcon>
                            <InputText
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="w-full"
                                fluid
                                :disabled="form.processing"
                                :invalid="!!form.errors.email"
                                autocomplete="username"
                            />
                        </IconField>
                        <InlineMessage
                            v-if="form.errors.email"
                            severity="error"
                        >
                            {{ form.errors.email }}
                        </InlineMessage>
                    </div>

                    <div class="space-y-2">
                        <label
                            for="password"
                            class="text-sm font-medium text-gray-700"
                        >
                            Contraseña
                        </label>
                        <Password
                            id="password"
                            v-model="form.password"
                            placeholder="••••••••"
                            :feedback="false"
                            toggle-mask
                            fluid
                            :disabled="form.processing"
                            :invalid="!!form.errors.password"
                            input-class="w-full"
                            autocomplete="new-password"
                        />
                        <InlineMessage
                            v-if="form.errors.password"
                            severity="error"
                        >
                            {{ form.errors.password }}
                        </InlineMessage>
                    </div>

                    <div class="space-y-2">
                        <label
                            for="password_confirmation"
                            class="text-sm font-medium text-gray-700"
                        >
                            Confirmar contraseña
                        </label>
                        <Password
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            placeholder="••••••••"
                            :feedback="false"
                            toggle-mask
                            fluid
                            :disabled="form.processing"
                            :invalid="!!form.errors.password_confirmation"
                            input-class="w-full"
                            autocomplete="new-password"
                        />
                        <InlineMessage
                            v-if="form.errors.password_confirmation"
                            severity="error"
                        >
                            {{ form.errors.password_confirmation }}
                        </InlineMessage>
                    </div>

                    <Button
                        type="submit"
                        :label="form.processing ? 'Restableciendo...' : 'Restablecer contraseña'"
                        :loading="form.processing"
                        class="w-full"
                        size="large"
                        :disabled="form.processing"
                    />
                </form>

                <p class="text-center text-sm text-gray-600">
                    <Link
                        :href="route('login')"
                        class="cursor-pointer font-semibold text-primary-600 underline-offset-2 transition-colors hover:underline hover:text-primary-700"
                    >
                        Volver a iniciar sesión
                    </Link>
                </p>
            </div>
        </AuthCard>
    </AuthLayout>
</template>
