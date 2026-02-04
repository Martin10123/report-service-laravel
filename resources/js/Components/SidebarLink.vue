<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: { type: String, default: '#' },
    active: Boolean,
});

const classes = computed(() =>
    props.active
        ? 'flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-primary-600 bg-primary-50 border-l-2 border-primary-600'
        : 'flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 border-l-2 border-transparent'
);
</script>

<template>
    <Link :href="href" :class="classes" class="rounded-r-md transition-colors" @click="$emit('click')">
        <slot />
    </Link>
</template>
