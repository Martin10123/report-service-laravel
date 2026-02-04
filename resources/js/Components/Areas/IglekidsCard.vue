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
});

const emit = defineEmits(['update']);

const update = (key, value) => {
    emit('update', key, value);
};
</script>

<template>
    <Card>
        <CardHeader compact>
            <CardTitle size="sm">Iglekids</CardTitle>
        </CardHeader>
        <CardContent compact>
            <div class="space-y-2">
                <div class="rounded-lg p-2">
                    <p class="mb-1 text-xs font-medium text-gray-700">Personal</p>
                    <div class="grid grid-cols-2 gap-2">
                        <NumberInput
                            label="Coordinadoras"
                            :model-value="data.coordinadoras"
                            @update:model-value="update('coordinadoras', $event)"
                        />
                        <NumberInput
                            label="Supervisoras"
                            :model-value="data.supervisoras"
                            @update:model-value="update('supervisoras', $event)"
                        />
                        <NumberInput
                            label="Maestros"
                            :model-value="data.maestros"
                            @update:model-value="update('maestros', $event)"
                        />
                    </div>
                </div>

                <div class="rounded-lg p-2">
                    <p class="mb-1 text-xs font-medium text-gray-700">Personal de Apoyo</p>
                    <div class="grid grid-cols-2 gap-2">
                        <NumberInput
                            label="Recrearte"
                            :model-value="data.recrearte"
                            @update:model-value="update('recrearte', $event)"
                        />
                        <NumberInput
                            label="Regikids"
                            :model-value="data.regikids"
                            @update:model-value="update('regikids', $event)"
                        />
                        <NumberInput
                            label="Logikids"
                            :model-value="data.logikids"
                            @update:model-value="update('logikids', $event)"
                        />
                        <NumberInput
                            label="Salud Kids"
                            :model-value="data.saludKids"
                            @update:model-value="update('saludKids', $event)"
                        />
                        <NumberInput
                            label="Yo Soy"
                            :model-value="data.yoSoy"
                            @update:model-value="update('yoSoy', $event)"
                        />
                    </div>
                </div>

                <NumberInput
                    label="Niños"
                    :model-value="data.ninos"
                    @update:model-value="update('ninos', $event)"
                />

                <div class="grid grid-cols-2 gap-2 rounded-lg bg-blue-50 p-2">
                    <div>
                        <span class="text-xs text-gray-600">Total Apoyo</span>
                        <p class="text-base font-bold text-gray-900">
                            {{ data.recrearte + data.regikids + data.logikids + data.saludKids + data.yoSoy }}
                        </p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-600">Total Iglekids</span>
                        <p class="text-base font-bold text-blue-600">
                            {{ data.coordinadoras + data.supervisoras + data.maestros + data.recrearte + data.regikids + data.logikids + data.saludKids + data.yoSoy }}
                        </p>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
