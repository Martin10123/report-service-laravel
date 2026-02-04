<script setup>
defineProps({
    class: {
        type: String,
        default: 'h-12 w-auto object-contain',
    },
});
</script>

<template>
    <img
        src="/logo-app.webp"
        alt="Logo"
        :class="$props.class"
    />
</template>
