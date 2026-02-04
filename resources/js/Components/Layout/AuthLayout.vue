<script setup>
defineProps({
    imageSrc: {
        type: String,
        required: true,
    },
    imageAlt: {
        type: String,
        default: '',
    },
    /** true = imagen a la izquierda (ej. Register), false = imagen a la derecha (ej. Login) */
    imageOnLeft: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div class="min-h-screen w-full bg-surface grid grid-cols-1 lg:grid-cols-2 place-items-center p-4 sm:p-6 lg:p-8">
        <!-- Col formulario -->
        <main
            :class="[
                'w-full max-w-xl flex flex-col items-center justify-center lg:pr-8 xl:pr-16',
                imageOnLeft ? 'lg:items-start lg:pl-8 xl:pl-16 order-1 lg:order-2' : 'lg:items-end',
            ]"
        >
            <slot />
        </main>

        <!-- Col imagen: solo desktop -->
        <aside
            :class="[
                'hidden lg:flex flex-1 items-center justify-center w-full h-full',
                imageOnLeft ? 'pr-8 xl:pr-16 order-2 lg:order-1' : 'pl-8 xl:pl-16',
            ]"
        >
            <div class="relative w-full max-w-lg">
                <img
                    :src="imageSrc"
                    :alt="imageAlt"
                    class="relative z-10 w-full h-auto object-contain drop-shadow-2xl transition-transform duration-700"
                />
                <div
                    :class="[
                        'absolute inset-0 z-0 rounded-full bg-gradient-to-br from-primary-400/20 via-primary-500/10 to-primary-400/20 blur-3xl animate-pulse',
                        imageOnLeft ? '-left-10' : '-right-10',
                    ]"
                    aria-hidden
                />
            </div>
        </aside>
    </div>
</template>
