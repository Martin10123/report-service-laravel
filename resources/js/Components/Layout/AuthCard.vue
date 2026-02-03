<script setup>
defineProps({
    class: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div
        :class="[
            'w-full rounded-3xl border border-gray-200/60 bg-white/80 backdrop-blur-xl p-8 sm:p-10 shadow-2xl shadow-gray-900/10 ring-1 ring-gray-900/5',
            $props.class,
        ]"
    >
        <slot />
    </div>
</template>
