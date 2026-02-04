<script setup>
defineProps({
    compact: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div :class="compact ? 'p-3 pb-1' : 'p-6 pb-2'">
        <slot />
    </div>
</template>
