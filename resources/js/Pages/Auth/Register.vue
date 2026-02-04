<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Checkbox from 'primevue/checkbox';
import Button from 'primevue/button';
import InlineMessage from 'primevue/inlinemessage';
import AuthLayout from '@/Components/Layout/AuthLayout.vue';
import AuthCard from '@/Components/Layout/AuthCard.vue';
import registerImg from '@/images/register-img.svg';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Registro" />

    <AuthLayout
        :image-src="registerImg"
        image-alt="Registro"
        :image-on-left="true"
    >
        <AuthCard>
            <div class="space-y-6">
                <div class="space-y-2 text-center lg:text-left">
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                        Crear cuenta
                    </h1>
                    <p class="text-base text-gray-600">
                        Completa el formulario para registrarte
                    </p>
                </div>

                <form class="space-y-5" @submit.prevent="submit">
                    <div class="space-y-2">
                        <label
                            for="name"
                            class="text-sm font-medium text-gray-700"
                        >
                            Nombre
                        </label>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-user" />
                            </InputIcon>
                            <InputText
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Tu nombre"
                                class="w-full"
                                fluid
                                :disabled="form.processing"
                                :invalid="!!form.errors.name"
                                autocomplete="name"
                            />
                        </IconField>
                        <InlineMessage
                            v-if="form.errors.name"
                            severity="error"
                        >
                            {{ form.errors.name }}
                        </InlineMessage>
                    </div>

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

                    <div
                        v-if="$page.props.jetstream?.hasTermsAndPrivacyPolicyFeature"
                        class="flex items-start gap-3"
                    >
                        <Checkbox
                            v-model="form.terms"
                            input-id="terms"
                            :binary="true"
                            :disabled="form.processing"
                            :invalid="!!form.errors.terms"
                        />
                        <label for="terms" class="cursor-pointer text-sm text-gray-600">
                            Acepto los
                            <a
                                :href="route('terms.show')"
                                target="_blank"
                                class="font-medium text-primary-600 underline-offset-2 hover:underline"
                            >
                                Términos de Servicio
                            </a>
                            y la
                            <a
                                :href="route('policy.show')"
                                target="_blank"
                                class="font-medium text-primary-600 underline-offset-2 hover:underline"
                            >
                                Política de Privacidad
                            </a>
                        </label>
                    </div>
                    <InlineMessage
                        v-if="form.errors.terms"
                        severity="error"
                    >
                        {{ form.errors.terms }}
                    </InlineMessage>

                    <Button
                        type="submit"
                        :label="form.processing ? 'Creando cuenta...' : 'Registrarse'"
                        :loading="form.processing"
                        class="w-full"
                        size="large"
                        :disabled="form.processing"
                    />
                </form>

                <p class="text-center text-sm text-gray-600">
                    ¿Ya tienes cuenta?
                    <Link
                        :href="route('login')"
                        class="cursor-pointer font-semibold text-primary-600 underline-offset-2 transition-colors hover:underline hover:text-primary-700"
                    >
                        Inicia sesión
                    </Link>
                </p>
            </div>
        </AuthCard>
    </AuthLayout>
</template>
