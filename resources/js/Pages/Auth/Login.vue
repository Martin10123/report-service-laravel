<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Checkbox from 'primevue/checkbox';
import Button from 'primevue/button';
import Message from 'primevue/message';
import InlineMessage from 'primevue/inlinemessage';
import AuthLayout from '@/Components/Layout/AuthLayout.vue';
import AuthCard from '@/Components/Layout/AuthCard.vue';
import loginImg from '@/images/login-img.svg';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Iniciar sesión" />

    <AuthLayout
        :image-src="loginImg"
        image-alt="Iniciar sesión"
        :image-on-left="false"
    >
        <AuthCard>
            <div class="space-y-6">
                <div class="space-y-2 text-center lg:text-left">
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                        Bienvenido de nuevo
                    </h1>
                    <p class="text-base text-gray-600">
                        Ingresa tus credenciales para continuar
                    </p>
                </div>

                <Message v-if="status" severity="success" :closable="false">
                    {{ status }}
                </Message>

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
                                placeholder="tu@ejemplo.com"
                                class="w-full"
                                fluid
                                :disabled="form.processing"
                                :invalid="!!form.errors.email"
                                autocomplete="email"
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
                        <div class="flex items-center justify-between">
                            <label
                                for="password"
                                class="text-sm font-medium text-gray-700"
                            >
                                Contraseña
                            </label>
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="cursor-pointer text-sm font-medium text-primary-600 underline-offset-2 transition-colors hover:underline hover:text-primary-700"
                            >
                                ¿Olvidaste tu contraseña?
                            </Link>
                        </div>
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
                            autocomplete="current-password"
                        />
                        <InlineMessage
                            v-if="form.errors.password"
                            severity="error"
                        >
                            {{ form.errors.password }}
                        </InlineMessage>
                    </div>

                    <div class="flex items-center gap-2">
                        <Checkbox
                            v-model="form.remember"
                            input-id="remember"
                            :binary="true"
                            :disabled="form.processing"
                        />
                        <label for="remember" class="cursor-pointer text-sm text-gray-600">
                            Recordarme
                        </label>
                    </div>

                    <Button
                        type="submit"
                        :label="form.processing ? 'Iniciando sesión...' : 'Iniciar sesión'"
                        :loading="form.processing"
                        class="w-full"
                        size="large"
                        :disabled="form.processing"
                    />
                </form>

                <p class="text-center text-sm text-gray-600">
                    ¿No tienes cuenta?
                    <Link
                        :href="route('register')"
                        class="cursor-pointer font-semibold text-primary-600 underline-offset-2 transition-colors hover:underline hover:text-primary-700"
                    >
                        Regístrate gratis
                    </Link>
                </p>
            </div>
        </AuthCard>
    </AuthLayout>
</template>
