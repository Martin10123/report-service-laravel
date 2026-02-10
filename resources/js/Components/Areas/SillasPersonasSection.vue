<script setup>
import Card from '@/Components/Card.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardTitle from '@/Components/CardTitle.vue';
import CardContent from '@/Components/CardContent.vue';
import NumberInput from '@/Components/NumberInput.vue';

defineProps({
    data: {
        type: Object,
        required: true,
    },
    ninosLabel: {
        type: String,
        default: 'Niños',
    },
    readonlyTotalPersonas: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update']);

const update = (key, value) => {
    emit('update', key, value);
};
</script>

<template>
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        <Card>
            <CardHeader compact>
                <CardTitle size="sm">Sillas</CardTitle>
            </CardHeader>
            <CardContent compact>
                <div class="space-y-2">
                    <NumberInput
                        label="Total sillas"
                        :model-value="data.totalSillas"
                        @update:model-value="update('totalSillas', $event)"
                    />
                    <NumberInput
                        label="Vacías"
                        :model-value="data.sillasVacias"
                        :max="data.totalSillas"
                        @update:model-value="update('sillasVacias', $event)"
                    />
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader compact>
                <CardTitle size="sm">Personas</CardTitle>
            </CardHeader>
            <CardContent compact>
                <div class="space-y-2">
                    <NumberInput
                        label="Total personas"
                        :model-value="data.totalPersonas"
                        :max="data.totalSillas"
                        :readonly="readonlyTotalPersonas"
                        @update:model-value="update('totalPersonas', $event)"
                    />
                    <NumberInput
                        :label="ninosLabel"
                        :model-value="data.totalNinos"
                        :max="data.totalPersonas"
                        :error="data.totalNinos > data.totalPersonas ? `No puede haber más de ${data.totalPersonas} niños` : ''"
                        @update:model-value="update('totalNinos', $event)"
                    />
                </div>
            </CardContent>
        </Card>
    </div>
</template>
