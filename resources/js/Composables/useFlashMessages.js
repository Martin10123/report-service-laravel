import { watch, nextTick } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

export function useFlashMessages() {
    const page = usePage();
    const toast = useToast();

    // Watch cada propiedad individualmente
    watch(
        () => page.props.flash?.success,
        (message) => {
            if (message) {
                nextTick(() => {
                    toast.add({
                        severity: 'success',
                        summary: 'Éxito',
                        detail: message,
                        life: 4000
                    });
                });
            }
        },
        { immediate: true }
    );

    watch(
        () => page.props.flash?.error,
        (message) => {
            if (message) {
                nextTick(() => {
                    toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: message,
                        life: 5000
                    });
                });
            }
        },
        { immediate: true }
    );

    watch(
        () => page.props.flash?.warning,
        (message) => {
            if (message) {
                nextTick(() => {
                    toast.add({
                        severity: 'warn',
                        summary: 'Atención',
                        detail: message,
                        life: 5000
                    });
                });
            }
        },
        { immediate: true }
    );
}