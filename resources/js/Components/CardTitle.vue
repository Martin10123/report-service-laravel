<script setup>
defineProps({
    size: {
        type: String,
        default: 'base', // xs, sm, base, lg
    },
});
</script>

<template>
    <div
        :class="[
            'font-semibold leading-none',
            {
                'text-xs sm:text-sm': size === 'xs',
                'text-sm': size === 'sm',
                'text-sm sm:text-base': size === 'base',
                'text-base sm:text-lg': size === 'lg',
            },
        ]"
    >
        <slot />
    </div>
</template>
