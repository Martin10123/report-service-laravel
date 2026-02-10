<script setup>
import { computed } from 'vue';

const props = defineProps({
    label: String,
    modelValue: {
        type: Number,
        default: 0,
    },
    min: {
        type: Number,
        default: 0,
    },
    max: {
        type: Number,
        default: 9999,
    },
    compact: {
        type: Boolean,
        default: false,
    },
    readonly: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);

const handleIncrement = () => {
    if (props.modelValue < props.max) {
        emit('update:modelValue', props.modelValue + 1);
    }
};

const handleDecrement = () => {
    if (props.modelValue > props.min) {
        emit('update:modelValue', props.modelValue - 1);
    }
};

const handleInput = (e) => {
    let value = parseInt(e.target.value, 10);
    
    // Si no es un número válido, usar el mínimo
    if (isNaN(value)) {
        value = props.min;
    }
    
    // Asegurar que el valor esté dentro de los límites
    value = Math.max(props.min, Math.min(props.max, value));
    
    // Actualizar el input para reflejar el valor correcto
    e.target.value = value;
    
    emit('update:modelValue', value);
};
</script>

<template>
    <div v-if="compact" class="flex items-center gap-1 rounded-md bg-gray-100 p-1">
        <button
            type="button"
            class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full border border-gray-300 bg-transparent text-gray-600 hover:bg-gray-50 disabled:opacity-50"
            :disabled="readonly || modelValue <= min"
            @click="handleDecrement"
        >
            <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
            </svg>
        </button>
        <input
            type="number"
            :value="modelValue"
            :min="min"
            :max="max"
            :readonly="readonly"
            :class="[
                'h-6 w-14 rounded border-0 text-center text-xs font-semibold [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none',
                readonly ? 'bg-gray-200 cursor-not-allowed' : 'bg-transparent focus:ring-2 focus:ring-blue-500'
            ]"
            @input="handleInput"
        />
        <button
            type="button"
            class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full border border-gray-300 bg-transparent text-gray-600 hover:bg-gray-50 disabled:opacity-50"
            :disabled="readonly || modelValue >= max"
            @click="handleIncrement"
        >
            <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        </button>
    </div>

    <div v-else class="flex flex-col gap-1.5 rounded-lg p-2">
        <span class="truncate text-xs font-medium text-gray-600">{{ label }}</span>
        <div class="flex items-center gap-1">
            <button
                type="button"
                :class="[
                    'flex h-7 w-7 shrink-0 items-center justify-center rounded-full border bg-transparent hover:bg-gray-50 disabled:opacity-50',
                    error ? 'border-red-300 text-red-600' : 'border-gray-300 text-gray-600'
                ]"
                :disabled="readonly || modelValue <= min"
                @click="handleDecrement"
            >
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                </svg>
            </button>
            <input
                type="number"
                :value="modelValue"
                :min="min"
                :max="max"
                :readonly="readonly"
                :class="[
                    'h-7 flex-1 rounded border text-center text-sm font-semibold [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none',
                    error ? 'border-red-300 bg-red-50 text-red-900 focus:border-red-500 focus:ring-2 focus:ring-red-500' : 
                    readonly ? 'border-gray-200 bg-gray-100 cursor-not-allowed text-gray-700' : 
                    'border-gray-300 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500'
                ]"
                @input="handleInput"
            />
            <button
                type="button"
                :class="[
                    'flex h-7 w-7 shrink-0 items-center justify-center rounded-full border bg-transparent hover:bg-gray-50 disabled:opacity-50',
                    error ? 'border-red-300 text-red-600' : 'border-gray-300 text-gray-600'
                ]"
                :disabled="readonly || modelValue >= max"
                @click="handleIncrement"
            >
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </button>
        </div>
        <p v-if="error" class="text-xs text-red-600 mt-0.5">{{ error }}</p>
    </div>
</template>
