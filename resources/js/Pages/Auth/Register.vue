<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Components/Layout/AuthLayout.vue';
import AuthCard from '@/Components/Layout/AuthCard.vue';
import registerImg from '@/images/register-img.svg';

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

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
                        <div class="relative group">
                            <i
                                class="pi pi-user absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400 transition-colors group-focus-within:text-blue-500"
                            />
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Tu nombre"
                                class="h-11 w-full rounded-xl border border-gray-300 bg-white pl-10 pr-4 text-gray-900 placeholder:text-gray-400 transition-all hover:border-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30 disabled:opacity-70"
                                autocomplete="name"
                                :disabled="form.processing"
                            />
                        </div>
                        <p v-if="form.errors.name" class="text-sm text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>

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
                                autocomplete="username"
                                :disabled="form.processing"
                            />
                        </div>
                        <p v-if="form.errors.email" class="text-sm text-red-600">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <label
                            for="password"
                            class="text-sm font-medium text-gray-700"
                        >
                            Contraseña
                        </label>
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
                                autocomplete="new-password"
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

                    <div class="space-y-2">
                        <label
                            for="password_confirmation"
                            class="text-sm font-medium text-gray-700"
                        >
                            Confirmar contraseña
                        </label>
                        <div class="relative group">
                            <i
                                class="pi pi-lock absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400 transition-colors group-focus-within:text-blue-500"
                            />
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                :type="showPasswordConfirmation ? 'text' : 'password'"
                                placeholder="••••••••"
                                class="h-11 w-full rounded-xl border border-gray-300 bg-white pl-10 pr-10 text-gray-900 placeholder:text-gray-400 transition-all hover:border-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30 disabled:opacity-70"
                                autocomplete="new-password"
                                :disabled="form.processing"
                            />
                            <button
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400 transition-colors hover:text-gray-600"
                                :aria-label="
                                    showPasswordConfirmation
                                        ? 'Ocultar contraseña'
                                        : 'Mostrar contraseña'
                                "
                                @click="
                                    showPasswordConfirmation =
                                        !showPasswordConfirmation
                                "
                            >
                                <i
                                    :class="[
                                        'pi h-5 w-5',
                                        showPasswordConfirmation
                                            ? 'pi-eye-slash'
                                            : 'pi-eye',
                                    ]"
                                />
                            </button>
                        </div>
                        <p
                            v-if="form.errors.password_confirmation"
                            class="text-sm text-red-600"
                        >
                            {{ form.errors.password_confirmation }}
                        </p>
                    </div>

                    <div
                        v-if="$page.props.jetstream?.hasTermsAndPrivacyPolicyFeature"
                        class="block"
                    >
                        <label class="flex cursor-pointer items-start gap-3">
                            <input
                                v-model="form.terms"
                                type="checkbox"
                                name="terms"
                                required
                                class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                            />
                            <span class="text-sm text-gray-600">
                                Acepto los
                                <a
                                    :href="route('terms.show')"
                                    target="_blank"
                                    class="font-medium text-blue-600 underline-offset-2 hover:underline"
                                >
                                    Términos de Servicio
                                </a>
                                y la
                                <a
                                    :href="route('policy.show')"
                                    target="_blank"
                                    class="font-medium text-blue-600 underline-offset-2 hover:underline"
                                >
                                    Política de Privacidad
                                </a>
                            </span>
                        </label>
                        <p v-if="form.errors.terms" class="mt-2 text-sm text-red-600">
                            {{ form.errors.terms }}
                        </p>
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
                        <span v-if="form.processing">Creando cuenta...</span>
                        <span v-else>Registrarse</span>
                    </button>
                </form>

                <p class="text-center text-sm text-gray-600">
                    ¿Ya tienes cuenta?
                    <Link
                        :href="route('login')"
                        class="cursor-pointer font-semibold text-blue-600 underline-offset-2 transition-colors hover:underline hover:text-blue-700"
                    >
                        Inicia sesión
                    </Link>
                </p>
            </div>
        </AuthCard>
    </AuthLayout>
</template>
