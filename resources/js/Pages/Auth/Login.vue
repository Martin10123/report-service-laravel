<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Components/Layout/AuthLayout.vue';
import AuthCard from '@/Components/Layout/AuthCard.vue';
import loginImg from '@/images/login-img.svg';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const showPassword = ref(false);
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
                    <h1 class="!text-3xl !m-0 font-bold tracking-tight text-gray-900">
                        Bienvenido de nuevo
                    </h1>
                    <p class="text-base text-gray-600">
                        Ingresa tus credenciales para continuar
                    </p>
                </div>

                <div v-if="status" class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ status }}
                </div>

                <form class="space-y-5" @submit.prevent="submit">
                    <div class="space-y-2">
                        <label
                            for="email"
                            class="text-sm font-medium text-gray-700"
                        >
                            Correo electrónico
                        </label>
                        <div class="relative group">
                            <i
                                class="pi pi-envelope absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400 transition-colors group-focus-within:text-blue-500"
                            />
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                placeholder="tu@ejemplo.com"
                                class="h-11 w-full rounded-xl border border-gray-300 bg-white pl-10 pr-4 text-gray-900 placeholder:text-gray-400 transition-all hover:border-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30 disabled:opacity-70"
                                autocomplete="email"
                                :disabled="form.processing"
                            />
                        </div>
                        <p v-if="form.errors.email" class="text-sm text-red-600">
                            {{ form.errors.email }}
                        </p>
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
                                class="cursor-pointer text-sm font-medium text-blue-600 underline-offset-2 transition-colors hover:underline hover:text-blue-700"
                            >
                                ¿Olvidaste tu contraseña?
                            </Link>
                        </div>
                        <div class="relative group">
                            <i
                                class="pi pi-lock absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400 transition-colors group-focus-within:text-blue-500"
                            />
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="••••••••"
                                class="h-11 w-full rounded-xl border border-gray-300 bg-white pl-10 pr-10 text-gray-900 placeholder:text-gray-400 transition-all hover:border-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30 disabled:opacity-70"
                                autocomplete="current-password"
                                :disabled="form.processing"
                            />
                            <button
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400 transition-colors hover:text-gray-600"
                                :aria-label="
                                    showPassword
                                        ? 'Ocultar contraseña'
                                        : 'Mostrar contraseña'
                                "
                                @click="showPassword = !showPassword"
                            >
                                <i
                                    :class="[
                                        'pi h-5 w-5',
                                        showPassword ? 'pi-eye-slash' : 'pi-eye',
                                    ]"
                                />
                            </button>
                        </div>
                        <p
                            v-if="form.errors.password"
                            class="text-sm text-red-600"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div class="block">
                        <label class="flex cursor-pointer items-center">
                            <input
                                v-model="form.remember"
                                type="checkbox"
                                name="remember"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                            />
                            <span class="ms-2 text-sm text-gray-600">
                                Recordarme
                            </span>
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="flex h-12 w-full cursor-pointer items-center justify-center gap-2 rounded-xl bg-blue-600 font-semibold text-white shadow-lg shadow-blue-500/30 transition-all hover:scale-[1.02] hover:bg-blue-700 hover:shadow-xl hover:shadow-blue-500/40 active:scale-[0.98] disabled:opacity-70 disabled:hover:scale-100"
                        :disabled="form.processing"
                    >
                        <i
                            v-if="form.processing"
                            class="pi pi-spin pi-spinner h-5 w-5"
                        />
                        <span v-if="form.processing">Iniciando sesión...</span>
                        <span v-else>Iniciar sesión</span>
                    </button>
                </form>

                <p class="text-center text-sm text-gray-600">
                    ¿No tienes cuenta?
                    <Link
                        :href="route('register')"
                        class="cursor-pointer font-semibold text-blue-600 underline-offset-2 transition-colors hover:underline hover:text-blue-700"
                    >
                        Regístrate gratis
                    </Link>
                </p>
            </div>
        </AuthCard>
    </AuthLayout>
</template>
