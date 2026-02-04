import { ref, reactive } from 'vue';

export function useSillasPersonas(initial = {}) {
    const data = reactive({
        totalSillas: initial.totalSillas || 0,
        sillasVacias: initial.sillasVacias || 0,
        totalPersonas: initial.totalPersonas || 0,
        totalNinos: initial.totalNinos || 0,
    });

    const update = (key, value) => {
        data[key] = value;
    };

    return {
        data,
        update,
    };
}
