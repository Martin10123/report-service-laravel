import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { formatearFechaParaInput, formatearHoraParaInput } from '@/Utils/dateHelpers';

export function useServiceForm() {
    const mostrarFormulario = ref(false);
    const mostrarModalEliminar = ref(false);
    const servicioEditar = ref(null);
    const servicioEliminar = ref(null);

    const form = useForm({
        sede_id: null,
        fecha: null,
        numero_servicio: null,
        hora: null,
        observaciones: '',
    });

    const crearServicio = () => {
        const url = servicioEditar.value 
            ? route('servicios.update', servicioEditar.value.id)
            : route('servicios.store');
        
        const method = servicioEditar.value ? 'put' : 'post';
        
        form[method](url, {
            preserveScroll: true,
            onSuccess: () => {
                mostrarFormulario.value = false;
                form.reset();
                servicioEditar.value = null;
            },
        });
    };

    const abrirEditar = (servicio) => {
        servicioEditar.value = servicio;
        form.sede_id = servicio.sede_id;
        form.fecha = formatearFechaParaInput(servicio.fecha);
        form.numero_servicio = servicio.numero_servicio;
        form.hora = formatearHoraParaInput(servicio.hora);
        form.observaciones = servicio.observaciones || '';
        mostrarFormulario.value = true;
    };

    const abrirNuevo = () => {
        servicioEditar.value = null;
        form.reset();
        mostrarFormulario.value = true;
    };

    const confirmarEliminar = (servicio) => {
        servicioEliminar.value = servicio;
        mostrarModalEliminar.value = true;
    };

    const eliminarServicio = () => {
        if (!servicioEliminar.value) return;
        
        router.delete(route('servicios.destroy', servicioEliminar.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                mostrarModalEliminar.value = false;
                servicioEliminar.value = null;
            },
            onError: () => {
                console.error('Error al eliminar el servicio');
            }
        });
    };

    const cerrarModal = () => {
        mostrarFormulario.value = false;
        servicioEditar.value = null;
        form.reset();
    };

    const obtenerNombreSede = (sede) => {
        if (typeof sede === 'object' && sede !== null) {
            return sede.nombre;
        }
        return sede;
    };

    return {
        form,
        mostrarFormulario,
        mostrarModalEliminar,
        servicioEditar,
        servicioEliminar,
        crearServicio,
        abrirEditar,
        abrirNuevo,
        confirmarEliminar,
        eliminarServicio,
        cerrarModal,
        obtenerNombreSede,
    };
}
