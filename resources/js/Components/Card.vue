<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'default', // default, primary, accent
    },
});

const classes = computed(() => {
    const base = 'flex flex-col gap-2 rounded-xl border shadow-sm';
    const variants = {
        default: 'bg-white text-gray-900',
        primary: 'border-blue-100 bg-blue-50/50',
        accent: 'border-blue-200 bg-blue-50',
    };
    return `${base} ${variants[props.variant] || variants.default}`;
});
</script>

<template>
    <div :class="classes">
        <slot />
    </div>
</template>
