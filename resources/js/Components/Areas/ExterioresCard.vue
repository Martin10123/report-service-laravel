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
    fields: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Card>
        <CardHeader compact>
            <CardTitle size="sm">{{ title }}</CardTitle>
        </CardHeader>
        <CardContent compact>
            <div class="grid grid-cols-2 gap-2">
                <NumberInput
                    v-for="field in fields"
                    :key="field.label"
                    :label="field.label"
                    :model-value="field.value"
                    @update:model-value="field.onChange"
                />
            </div>
        </CardContent>
    </Card>
</template>
