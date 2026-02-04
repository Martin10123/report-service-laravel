<script setup>
import Card from '@/Components/Card.vue';
import CardContent from '@/Components/CardContent.vue';

defineProps({
    totalPersonas: {
        type: Number,
        default: 0,
    },
    totalServidores: {
        type: Number,
        default: 0,
    },
    areaName: {
        type: String,
        default: 'A1',
    },
});
</script>

<template>
    <Card variant="accent">
        <CardContent compact class="pb-3 pt-3">
            <div class="mb-2 border-b border-gray-200 pb-2">
                <h3 class="text-sm font-semibold text-gray-700">Área {{ areaName }}</h3>
            </div>
            <div class="grid grid-cols-2 gap-4 text-center">
                <div>
                    <p class="text-xl font-bold text-gray-900">{{ totalPersonas }}</p>
                    <p class="text-xs text-gray-600">Total Personas</p>
                </div>
                <div>
                    <p class="text-xl font-bold text-blue-600">{{ totalServidores }}</p>
                    <p class="text-xs text-gray-600">Servidores</p>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
