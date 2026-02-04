<script setup>
defineProps({
    compact: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div :class="compact ? 'p-3 pt-0' : 'px-6 pb-6'">
        <slot />
    </div>
</template>
