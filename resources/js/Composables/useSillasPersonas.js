import { ref, reactive, watch } from 'vue';

export function useSillasPersonas(initial = {}) {
    const data = reactive({
        totalSillas: initial.totalSillas || 0,
        sillasVacias: initial.sillasVacias || 0,
        totalPersonas: initial.totalPersonas || 0,
        totalNinos: initial.totalNinos || 0,
    });

    const update = (key, value) => {
        const numValue = parseInt(value) || 0;
        
        // Validaciones
        if (key === 'sillasVacias') {
            // Las sillas vacías no pueden ser mayores que el total de sillas
            data[key] = Math.min(numValue, data.totalSillas);
        } else if (key === 'totalPersonas') {
            // El total de personas no puede ser mayor que el total de sillas
            data[key] = Math.min(numValue, data.totalSillas);
            // Si el total de personas es menor que el total de niños, ajustar niños
            if (data.totalNinos > data.totalPersonas) {
                data.totalNinos = data.totalPersonas;
            }
        } else if (key === 'totalNinos') {
            // Los niños no pueden ser más que el total de personas
            data[key] = Math.min(numValue, data.totalPersonas);
        } else if (key === 'totalSillas') {
            data[key] = numValue;
            // Ajustar valores dependientes si es necesario
            if (data.sillasVacias > numValue) {
                data.sillasVacias = numValue;
            }
            if (data.totalPersonas > numValue) {
                data.totalPersonas = numValue;
            }
        } else {
            data[key] = numValue;
        }
    };

    return {
        data,
        update,
    };
}
