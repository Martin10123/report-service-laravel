<script setup>
import Card from '@/Components/Card.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardTitle from '@/Components/CardTitle.vue';
import CardContent from '@/Components/CardContent.vue';
import NumberInput from '@/Components/NumberInput.vue';

defineProps({
    title: {
        type: String,
        required: true,
    },
    data: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['update']);

const update = (key, value) => {
    emit('update', key, value);
};
</script>

<template>
    <Card>
        <CardHeader compact>
            <CardTitle size="sm">{{ title }}</CardTitle>
        </CardHeader>
        <CardContent compact>
            <div class="space-y-2">
                <NumberInput
                    label="Inicial"
                    :model-value="data.inicial"
                    @update:model-value="update('inicial', $event)"
                />
                <NumberInput
                    label="Ingresados"
                    :model-value="data.recibidos"
                    @update:model-value="update('recibidos', $event)"
                />
                <div class="rounded-lg bg-gray-50 px-3 py-2">
                    <span class="text-xs text-gray-600">Total</span>
                    <p class="text-lg font-bold text-gray-900">
                        {{ data.inicial + data.recibidos }}
                    </p>
                </div>
                <NumberInput
                    label="Entregados"
                    :model-value="data.entregados"
                    :max="data.inicial + data.recibidos"
                    @update:model-value="update('entregados', $event)"
                />
                <div class="rounded-lg bg-blue-50 px-3 py-2">
                    <span class="text-xs text-gray-600">Final</span>
                    <p class="text-lg font-bold text-blue-600">
                        {{ data.inicial + data.recibidos - data.entregados }}
                    </p>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
