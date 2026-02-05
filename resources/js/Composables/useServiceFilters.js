import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { formatearFecha } from '@/Utils/dateHelpers';

export function useServiceFilters(props) {
    const sedeFiltro = ref(props.filters.sede || null);
    const estadoFiltro = ref(props.filters.estado || null);
    const busqueda = ref('');

    // Filtro local de búsqueda usando computed (sin peticiones HTTP)
    const serviciosFiltrados = computed(() => {
        if (!busqueda.value.trim()) {
            return props.servicios.data || [];
        }

        const termino = busqueda.value.toLowerCase().trim();
        
        return (props.servicios.data || []).filter(servicio => {
            const nombreSede = obtenerNombreSede(servicio.sede).toLowerCase();
            const numeroServicio = String(servicio.numero_servicio);
            const fecha = formatearFecha(servicio.fecha).toLowerCase();
            
            return nombreSede.includes(termino) ||
                   numeroServicio.includes(termino) ||
                   fecha.includes(termino);
        });
    });

    // Solo aplicar filtros de sede y estado (peticiones HTTP)
    const aplicarFiltros = () => {
        router.get(route('servicios.index'), {
            sede: sedeFiltro.value,
            estado: estadoFiltro.value,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const limpiarFiltros = () => {
        sedeFiltro.value = null;
        estadoFiltro.value = null;
        busqueda.value = '';
        router.get(route('servicios.index'), {}, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const obtenerNombreSede = (sede) => {
        if (typeof sede === 'object' && sede !== null) {
            return sede.nombre;
        }
        return sede;
    };

    return {
        sedeFiltro,
        estadoFiltro,
        busqueda,
        serviciosFiltrados,
        aplicarFiltros,
        limpiarFiltros,
    };
}
