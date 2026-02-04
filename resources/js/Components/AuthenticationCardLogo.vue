<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link :href="'/'">
        <img
            src="/logo-app.webp"
            alt="Logo"
            class="h-16 w-auto object-contain"
        />
    </Link>
</template>
