<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import SidebarLink from '@/Components/SidebarLink.vue';

defineProps({
    title: String,
});

const page = usePage();

// Obtener datos del backend
const isSuperUser = computed(() => page.props.auth?.is_super_user ?? true);
const sedes = computed(() => page.props.sedes || []);
const sedeActual = computed(() => page.props.sedeActual || null);
const opcionesDisponiblesBackend = computed(() => page.props.opcionesDisponibles || []);

// Servicio actual desde props (servicioActual o servicio)
// Solo usar servicio si estamos en una página que lo requiere y no se ha solicitado limpiar
const servicioActual = computed(() => {
    // Si servicioActual existe en las props globales, usarlo (viene de la sesión)
    const servicio = page.props.servicioActual;
    if (!servicio) return null;

    return {
        id: servicio.id,
        sede: servicio.sede?.nombre ?? servicio.sede,
        sede_id: servicio.sede?.id ?? servicio.sede_id,
        numero_servicio: servicio.numero_servicio,
        fecha: servicio.fecha,
        dia_semana: servicio.dia_semana,
        hora: servicio.hora,
    };
});

// Verificar si una opción está disponible
// Ahora usa la configuración que viene del backend
const opcionDisponible = (nombreRuta) => {
    // Si no hay opciones del backend, mostrar todo (fallback)
    if (!opcionesDisponiblesBackend.value || opcionesDisponiblesBackend.value.length === 0) {
        return true;
    }
    return opcionesDisponiblesBackend.value.includes(nombreRuta);
};

const formatearFechaCorta = (fecha) => {
    if (!fecha) return '';

    let fechaLimpia = fecha;
    if (typeof fecha === 'string' && fecha.includes('T')) {
        fechaLimpia = fecha.split('T')[0];
    }

    const [year, month, day] = fechaLimpia.split('-').map(Number);
    const d = new Date(year, month - 1, day);
    return d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit' });
};

const formatearHoraCorta = (hora) => {
    if (!hora) return '';
    if (hora.includes('T')) {
        const d = new Date(hora);
        return d.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', hour12: false });
    }
    if (hora.includes(' ')) {
        const partes = hora.split(' ');
        const soloHora = partes[1] || partes[0];
        return soloHora.substring(0, 5);
    }
    return hora.substring(0, 5);
};

const sidebarOpen = ref(false);
const showingUserDropdown = ref(false);
const showingSedeDropdown = ref(false);
const showingServicioDropdown = ref(false);

const closeSidebar = () => { sidebarOpen.value = false; };

const selectSede = (sede) => {
    showingSedeDropdown.value = false;
    // Cambiar la sede en el backend y recargar las opciones
    router.get(route('sede.switch', sede.slug), {}, { preserveState: false });
};

const logout = () => {
    router.post(route('logout'));
};

const rutasConServicio = new Set([
    'primer-conteo',
    'conteo-a1',
    'conteo-a2',
    'conteo-a3',
    'conteo-a4',
    'conteo-sobres',
    'informe-final',
]);

// Rutas placeholder; cuando existan, usar route('servicios.index'), etc.
const r = (name, params = {}) => {
    if (name === 'servicios') {
        // Si hay servicio seleccionado, ir al detalle; sino, a la lista
        const servicioId = servicioActual.value?.id;
        return servicioId ? route('servicios.show', servicioId) : route('servicios.index');
    }
    if (name === 'configuraciones') return route('configuraciones.index');
    if (name === 'auditorias') return route('auditorias.index');
    if (name === 'profile') return route('profile.show');

    const servicioId = servicioActual.value?.id;
    const finalParams = rutasConServicio.has(name) && servicioId
        ? { servicio_id: servicioId, ...params }
        : params;

    if (name === 'primer-conteo') return route('primer-conteo', finalParams);
    if (name === 'conteo-a1') return route('conteo-a1', finalParams);
    if (name === 'conteo-a2') return route('conteo-a2', finalParams);
    if (name === 'conteo-a3') return route('conteo-a3', finalParams);
    if (name === 'conteo-a4') return route('conteo-a4', finalParams);
    if (name === 'conteo-sobres') return route('conteo-sobres', finalParams);
    if (name === 'informe-final') return route('informe-final', finalParams);

    return route('servicios.index');
};
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <Head :title="title" />
        <Banner />

        <!-- Navbar -->
        <header class="sticky top-0 z-30 flex h-14 items-center border-b border-gray-200 bg-white px-4 shadow-sm sm:px-6">
            <button
                type="button"
                class="mr-3 rounded-md p-2 text-gray-500 hover:bg-gray-100 lg:hidden"
                aria-label="Abrir menú"
                @click="sidebarOpen = true"
            >
                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <Link :href="r('servicios')" class="flex shrink-0 items-center gap-2">
                <ApplicationMark class="h-8 w-auto" />
            </Link>

            <div class="ml-4 hidden lg:block">
                <span class="text-sm font-medium text-gray-500">Reportes de Servicio</span>
            </div>

            <div class="ml-auto flex items-center gap-2">
                <!-- Selector de sede (solo super usuario) -->
                <div v-if="isSuperUser" class="relative">
                    <button
                        type="button"
                        class="flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                        @click="showingSedeDropdown = !showingSedeDropdown; showingUserDropdown = false"
                    >
                        <span class="hidden sm:inline">Ver sede:</span>
                        <span class="text-primary-600">{{ sedeActual?.nombre || 'Seleccionar' }}</span>
                        <svg class="size-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div
                        v-show="showingSedeDropdown"
                        class="absolute right-0 top-full z-50 mt-1 w-52 rounded-lg border border-gray-200 bg-white py-1 shadow-lg"
                    >
                        <button
                            v-for="sede in sedes"
                            :key="sede.id"
                            type="button"
                            class="flex w-full items-center justify-between px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50"
                            @click="selectSede(sede)"
                        >
                            {{ sede.nombre }}
                            <svg
                                v-if="sedeActual && sede.id === sedeActual.id"
                                class="size-4 text-primary-600"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Usuario -->
                <div class="relative">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button
                                type="button"
                                class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100"
                            >
                                <span class="hidden max-w-[120px] truncate sm:inline">{{ $page.props.auth?.user?.name ?? 'Usuario' }}</span>
                                <svg class="size-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </button>
                        </template>
                        <template #content>
                            <div class="block px-4 py-2 text-xs text-gray-400">Cuenta</div>
                            <DropdownLink :href="route('profile.show')">Perfil</DropdownLink>
                            <div class="border-t border-gray-200" />
                            <form @submit.prevent="logout">
                                <DropdownLink as="button">Cerrar sesión</DropdownLink>
                            </form>
                        </template>
                    </Dropdown>
                </div>
            </div>
        </header>

        <!-- Banner de servicio actual - Compacto y discreto -->
        <div v-if="servicioActual" class="sticky top-14 z-20 border-b border-blue-200 bg-blue-50 px-4 py-1.5 sm:px-6">
            <div class="flex items-center justify-between gap-2">
                <div class="flex min-w-0 flex-1 items-center gap-2">
                    <svg class="size-3.5 shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <div class="flex min-w-0 flex-1 flex-wrap items-center gap-x-2 gap-y-0.5 text-xs">
                        <span class="font-semibold text-blue-900">{{ servicioActual.sede }}</span>
                        <span class="rounded bg-blue-600 px-1.5 py-0.5 font-bold text-white">N° {{ servicioActual.numero_servicio }}</span>
                        <span class="hidden text-blue-600 sm:inline">•</span>
                        <span class="text-blue-700">{{ formatearFechaCorta(servicioActual.fecha) }} • {{ formatearHoraCorta(servicioActual.hora) }}</span>
                    </div>
                </div>
                <div class="relative flex items-center gap-1">
                    <Link
                        :href="route('servicios.show', servicioActual.id)"
                        class="shrink-0 text-xs font-medium text-blue-600 transition hover:text-blue-700 hover:underline"
                    >
                        Ver
                    </Link>
                    <span class="text-blue-400">•</span>
                    <button
                        type="button"
                        class="flex shrink-0 items-center gap-1 text-xs font-medium text-blue-600 transition hover:text-blue-700"
                        @click="showingServicioDropdown = !showingServicioDropdown"
                    >
                        Cambiar
                        <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div
                        v-show="showingServicioDropdown"
                        class="absolute right-0 top-full z-50 mt-2 w-48 rounded-lg border border-gray-200 bg-white py-1 shadow-lg"
                    >
                        <Link
                            :href="route('servicios.index')"
                            class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50"
                            @click="showingServicioDropdown = false"
                        >
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            Ver todos los servicios
                        </Link>
                        <Link
                            :href="route('servicios.index', { clear_servicio: 1 })"
                            class="flex w-full items-center gap-2 border-t border-gray-100 px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50"
                            @click="showingServicioDropdown = false"
                        >
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Deseleccionar servicio
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex">
            <!-- Sidebar -->
            <aside
                :class="[
                    'fixed inset-y-0 left-0 z-40 w-64 border-r border-gray-200 bg-white transition-transform duration-200 ease-out',
                    'lg:sticky lg:inset-auto lg:h-screen lg:z-0',
                    servicioActual ? 'pt-[6rem] lg:pt-0 lg:top-[6rem]' : 'pt-14 lg:pt-0 lg:top-14',
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
                ]"
            >
                <div class="flex h-full flex-col lg:min-h-0">
                    <nav class="flex-1 space-y-0.5 overflow-y-auto px-3 py-4" aria-label="Menú principal">
                        <SidebarLink :href="r('servicios')" :active="route().current('servicios.index') || route().current('servicios.show')" @click="closeSidebar">
                            <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Servicios
                        </SidebarLink>
                        <SidebarLink v-if="opcionDisponible('primer-conteo')" :href="r('primer-conteo')" :active="route().current('primer-conteo')" @click="closeSidebar">
                            <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            Primer Conteo
                        </SidebarLink>
                        <div v-if="opcionDisponible('conteo-a1') || opcionDisponible('conteo-a2') || opcionDisponible('conteo-a3') || opcionDisponible('conteo-a4')" class="my-2 border-t border-gray-100" />
                        <SidebarLink v-if="opcionDisponible('conteo-a1')" :href="r('conteo-a1')" :active="route().current('conteo-a1')" @click="closeSidebar">
                            <span class="flex size-5 shrink-0 items-center justify-center rounded bg-gray-100 text-xs font-semibold text-gray-600">A1</span>
                            Conteo A1
                        </SidebarLink>
                        <SidebarLink v-if="opcionDisponible('conteo-a2')" :href="r('conteo-a2')" :active="route().current('conteo-a2')" @click="closeSidebar">
                            <span class="flex size-5 shrink-0 items-center justify-center rounded bg-gray-100 text-xs font-semibold text-gray-600">A2</span>
                            Conteo A2
                        </SidebarLink>
                        <SidebarLink v-if="opcionDisponible('conteo-a3')" :href="r('conteo-a3')" :active="route().current('conteo-a3')" @click="closeSidebar">
                            <span class="flex size-5 shrink-0 items-center justify-center rounded bg-gray-100 text-xs font-semibold text-gray-600">A3</span>
                            Conteo A3
                        </SidebarLink>
                        <SidebarLink v-if="opcionDisponible('conteo-a4')" :href="r('conteo-a4')" :active="route().current('conteo-a4')" @click="closeSidebar">
                            <span class="flex size-5 shrink-0 items-center justify-center rounded bg-gray-100 text-xs font-semibold text-gray-600">A4</span>
                            Conteo A4
                        </SidebarLink>
                        <div v-if="opcionDisponible('conteo-sobres')" class="my-2 border-t border-gray-100" />
                        <SidebarLink v-if="opcionDisponible('conteo-sobres')" :href="r('conteo-sobres')" :active="route().current('conteo-sobres')" @click="closeSidebar">
                            <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8 4-8-4m16 0l-8 4 8 4m0-8l-8 4-8-4" />
                            </svg>
                            Conteo Sobres
                        </SidebarLink>
                        <SidebarLink :href="r('informe-final')" :active="route().current('informe-final')" @click="closeSidebar">
                            <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.5a2 2 0 012 2v5.5a2 2 0 01-2 2H9z" />
                            </svg>
                            Informe Final
                        </SidebarLink>
                        <div class="my-2 border-t border-gray-100" />
                        <SidebarLink :href="r('configuraciones')" :active="route().current('configuraciones.index')" @click="closeSidebar">
                            <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Configuraciones
                        </SidebarLink>
                        <SidebarLink :href="r('auditorias')" :active="route().current('auditorias.index')" @click="closeSidebar">
                            <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Auditorías
                        </SidebarLink>
                        <SidebarLink :href="r('profile')" :active="route().current('profile.show')" @click="closeSidebar">
                            <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Perfil
                        </SidebarLink>
                    </nav>
                </div>
            </aside>

            <!-- Overlay móvil -->
            <div
                v-show="sidebarOpen"
                class="fixed inset-0 z-20 bg-black/20 lg:hidden"
                aria-hidden="true"
                @click="closeSidebar"
            />

            <!-- Contenido principal -->
            <main class="min-w-0 flex-1">
                <div v-if="$slots.header" class="border-b border-gray-200 bg-white px-4 py-4 shadow-sm sm:px-6">
                    <slot name="header" />
                </div>
                <div class="p-4 sm:p-6">
                    <slot />
                </div>
            </main>
        </div>

        <!-- Cerrar dropdowns al hacer clic fuera -->
        <div
            v-show="showingSedeDropdown"
            class="fixed inset-0 z-10"
            aria-hidden="true"
            @click="showingSedeDropdown = false"
        />
        <div
            v-show="showingServicioDropdown"
            class="fixed inset-0 z-10"
            aria-hidden="true"
            @click="showingServicioDropdown = false"
        />
    </div>
</template>
