<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Message from 'primevue/message';
import InlineMessage from 'primevue/inlinemessage';
import AuthLayout from '@/Components/Layout/AuthLayout.vue';
import AuthCard from '@/Components/Layout/AuthCard.vue';
import loginImg from '@/images/login-img.svg';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Recuperar contraseña" />

    <AuthLayout
        :image-src="loginImg"
        image-alt="Recuperar contraseña"
        :image-on-left="false"
    >
        <AuthCard>
            <div class="space-y-6">
                <div class="space-y-2 text-center lg:text-left">
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                        Recuperar contraseña
                    </h1>
                    <p class="text-base text-gray-600">
                        Indica tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
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

                    <Button
                        type="submit"
                        :label="form.processing ? 'Enviando enlace...' : 'Enviar enlace de recuperación'"
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
