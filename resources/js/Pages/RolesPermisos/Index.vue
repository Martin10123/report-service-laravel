<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardTitle from '@/Components/CardTitle.vue';
import CardContent from '@/Components/CardContent.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';

// Props
defineProps({
    roles: {
        type: Array,
        default: () => [
            { id: 1, name: 'Administrador', description: 'Acceso total al sistema', users_count: 2, color: 'red' },
            { id: 2, name: 'Coordinador', description: 'Gestiona servicios y reportes', users_count: 5, color: 'blue' },
            { id: 3, name: 'Contador', description: 'Realiza conteos en servicios', users_count: 15, color: 'green' },
            { id: 4, name: 'Visualizador', description: 'Solo consulta reportes', users_count: 8, color: 'gray' },
        ]
    },
    users: {
        type: Array,
        default: () => [
            { id: 1, name: 'Juan Pérez', email: 'juan@example.com', role: 'Administrador', role_color: 'red' },
            { id: 2, name: 'María García', email: 'maria@example.com', role: 'Coordinador', role_color: 'blue' },
            { id: 3, name: 'Carlos López', email: 'carlos@example.com', role: 'Contador', role_color: 'green' },
            { id: 4, name: 'Ana Martínez', email: 'ana@example.com', role: 'Contador', role_color: 'green' },
            { id: 5, name: 'Luis Rodríguez', email: 'luis@example.com', role: 'Visualizador', role_color: 'gray' },
        ]
    }
});

const mostrarModalRol = ref(false);
const mostrarModalUsuario = ref(false);
const rolEditando = ref(null);
const usuarioEditando = ref(null);

const formRol = useForm({
    name: '',
    description: '',
    permisos: []
});

const formUsuario = useForm({
    name: '',
    email: '',
    role_id: null,
    password: ''
});

const permisos = [
    { id: 'servicios.view', name: 'Ver servicios', category: 'Servicios' },
    { id: 'servicios.create', name: 'Crear servicios', category: 'Servicios' },
    { id: 'servicios.edit', name: 'Editar servicios', category: 'Servicios' },
    { id: 'servicios.delete', name: 'Eliminar servicios', category: 'Servicios' },
    { id: 'conteos.view', name: 'Ver conteos', category: 'Conteos' },
    { id: 'conteos.create', name: 'Crear conteos', category: 'Conteos' },
    { id: 'conteos.edit', name: 'Editar conteos', category: 'Conteos' },
    { id: 'conteos.delete', name: 'Eliminar conteos', category: 'Conteos' },
    { id: 'reportes.view', name: 'Ver reportes', category: 'Reportes' },
    { id: 'reportes.export', name: 'Exportar reportes', category: 'Reportes' },
    { id: 'usuarios.view', name: 'Ver usuarios', category: 'Usuarios' },
    { id: 'usuarios.manage', name: 'Gestionar usuarios', category: 'Usuarios' },
    { id: 'roles.manage', name: 'Gestionar roles', category: 'Administración' },
    { id: 'settings.manage', name: 'Configuraciones', category: 'Administración' },
];

const abrirModalNuevoRol = () => {
    rolEditando.value = null;
    formRol.reset();
    mostrarModalRol.value = true;
};

const abrirModalEditarRol = (rol) => {
    rolEditando.value = rol;
    formRol.name = rol.name;
    formRol.description = rol.description;
    mostrarModalRol.value = true;
};

const abrirModalNuevoUsuario = () => {
    usuarioEditando.value = null;
    formUsuario.reset();
    mostrarModalUsuario.value = true;
};

const abrirModalEditarUsuario = (usuario) => {
    usuarioEditando.value = usuario;
    formUsuario.name = usuario.name;
    formUsuario.email = usuario.email;
    formUsuario.role_id = usuario.role_id;
    mostrarModalUsuario.value = true;
};

const guardarRol = () => {
    // Aquí se implementará la lógica para guardar
    console.log('Guardando rol:', formRol.data());
    mostrarModalRol.value = false;
};

const guardarUsuario = () => {
    // Aquí se implementará la lógica para guardar
    console.log('Guardando usuario:', formUsuario.data());
    mostrarModalUsuario.value = false;
};

const eliminarRol = (rol) => {
    if (confirm(`¿Estás seguro de eliminar el rol "${rol.name}"?`)) {
        // Aquí se implementará la lógica para eliminar
        console.log('Eliminando rol:', rol);
    }
};

const eliminarUsuario = (usuario) => {
    if (confirm(`¿Estás seguro de eliminar al usuario "${usuario.name}"?`)) {
        // Aquí se implementará la lógica para eliminar
        console.log('Eliminando usuario:', usuario);
    }
};

const colorClasses = {
    red: 'bg-red-100 text-red-800',
    blue: 'bg-blue-100 text-blue-800',
    green: 'bg-green-100 text-green-800',
    gray: 'bg-gray-100 text-gray-800',
    purple: 'bg-purple-100 text-purple-800',
};
</script>

<template>
    <AppLayout title="Roles y Permisos">
        <div class="space-y-4">
            <!-- Header -->
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
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
                            Roles y Permisos
                        </h1>
                        <p class="mt-0.5 text-sm text-gray-600">
                            Gestiona roles, permisos y usuarios del sistema
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-6">
                    <button class="border-b-2 border-purple-500 px-1 py-3 text-sm font-medium text-purple-600">
                        Roles
                    </button>
                    <button class="border-b-2 border-transparent px-1 py-3 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                        Usuarios
                    </button>
                </nav>
            </div>

            <!-- Roles Section -->
            <div>
                <div class="mb-4 flex items-center justify-between">
                    <p class="text-sm text-gray-600">{{ roles.length }} roles configurados</p>
                    <PrimaryButton @click="abrirModalNuevoRol">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nuevo Rol
                    </PrimaryButton>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <Card v-for="rol in roles" :key="rol.id">
                        <CardContent class="p-5">
                            <div class="mb-3 flex items-start justify-between">
                                <div class="flex-1">
                                    <span :class="['inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium', colorClasses[rol.color]]">
                                        {{ rol.name }}
                                    </span>
                                    <p class="mt-2 text-sm text-gray-600">{{ rol.description }}</p>
                                </div>
                                <div class="flex gap-1">
                                    <button
                                        @click="abrirModalEditarRol(rol)"
                                        class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                                        title="Editar rol"
                                    >
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button
                                        @click="eliminarRol(rol)"
                                        class="rounded p-1 text-gray-400 hover:bg-red-100 hover:text-red-600"
                                        title="Eliminar rol"
                                    >
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span>{{ rol.users_count }} usuario{{ rol.users_count !== 1 ? 's' : '' }}</span>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Usuarios Section -->
            <div class="mt-8">
                <div class="mb-4 flex items-center justify-between">
                    <p class="text-sm text-gray-600">{{ users.length }} usuarios registrados</p>
                    <PrimaryButton @click="abrirModalNuevoUsuario">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nuevo Usuario
                    </PrimaryButton>
                </div>

                <Card>
                    <CardContent class="p-0">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Usuario
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Correo
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Rol
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-for="usuario in users" :key="usuario.id" class="hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-purple-100 text-purple-600">
                                                    <span class="text-sm font-medium">{{ usuario.name.charAt(0) }}</span>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-gray-900">{{ usuario.name }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            {{ usuario.email }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span :class="['inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium', colorClasses[usuario.role_color]]">
                                                {{ usuario.role }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                            <div class="flex justify-end gap-2">
                                                <button
                                                    @click="abrirModalEditarUsuario(usuario)"
                                                    class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                                                    title="Editar usuario"
                                                >
                                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <button
                                                    @click="eliminarUsuario(usuario)"
                                                    class="rounded p-1.5 text-gray-400 hover:bg-red-100 hover:text-red-600"
                                                    title="Eliminar usuario"
                                                >
                                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Modal Rol -->
        <Modal :show="mostrarModalRol" @close="mostrarModalRol = false" max-width="2xl">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ rolEditando ? 'Editar Rol' : 'Nuevo Rol' }}
                </h2>
                <form @submit.prevent="guardarRol" class="mt-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre del rol</label>
                        <input
                            v-model="formRol.name"
                            type="text"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                            required
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Descripción</label>
                        <textarea
                            v-model="formRol.description"
                            rows="2"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                        ></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Permisos</label>
                        <div class="space-y-3 max-h-64 overflow-y-auto rounded-lg border border-gray-200 p-3">
                            <div v-for="(grupo, category) in Object.groupBy(permisos, p => p.category)" :key="category">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">{{ category }}</p>
                                <div class="space-y-2 ml-2">
                                    <label v-for="permiso in grupo" :key="permiso.id" class="flex items-center gap-2">
                                        <input
                                            type="checkbox"
                                            :value="permiso.id"
                                            v-model="formRol.permisos"
                                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                                        />
                                        <span class="text-sm text-gray-700">{{ permiso.name }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <SecondaryButton type="button" @click="mostrarModalRol = false">
                            Cancelar
                        </SecondaryButton>
                        <PrimaryButton type="submit">
                            {{ rolEditando ? 'Actualizar' : 'Crear' }} Rol
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Modal Usuario -->
        <Modal :show="mostrarModalUsuario" @close="mostrarModalUsuario = false">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ usuarioEditando ? 'Editar Usuario' : 'Nuevo Usuario' }}
                </h2>
                <form @submit.prevent="guardarUsuario" class="mt-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre completo</label>
                        <input
                            v-model="formUsuario.name"
                            type="text"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                            required
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                        <input
                            v-model="formUsuario.email"
                            type="email"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                            required
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Rol</label>
                        <select
                            v-model="formUsuario.role_id"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                            required
                        >
                            <option :value="null">Seleccionar rol...</option>
                            <option v-for="rol in roles" :key="rol.id" :value="rol.id">
                                {{ rol.name }}
                            </option>
                        </select>
                    </div>
                    <div v-if="!usuarioEditando">
                        <label class="block text-sm font-medium text-gray-700">Contraseña</label>
                        <input
                            v-model="formUsuario.password"
                            type="password"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                            required
                        />
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <SecondaryButton type="button" @click="mostrarModalUsuario = false">
                            Cancelar
                        </SecondaryButton>
                        <PrimaryButton type="submit">
                            {{ usuarioEditando ? 'Actualizar' : 'Crear' }} Usuario
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>
