<script setup>
import Card from '@/Components/Card.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardTitle from '@/Components/CardTitle.vue';
import CardContent from '@/Components/CardContent.vue';
import NumberInput from '@/Components/NumberInput.vue';

defineProps({
    vehiculos: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['update']);

const updateVehiculo = (key, value) => {
    emit('update', key, value);
};
</script>

<template>
    <Card>
        <CardHeader compact>
            <CardTitle size="sm">Vehículos</CardTitle>
        </CardHeader>
        <CardContent compact>
            <div class="space-y-2">
                <NumberInput
                    label="Carros"
                    :model-value="vehiculos.carros"
                    @update:model-value="updateVehiculo('carros', $event)"
                />
                <NumberInput
                    label="Motos"
                    :model-value="vehiculos.motos"
                    @update:model-value="updateVehiculo('motos', $event)"
                />
                <NumberInput
                    label="Bicicletas"
                    :model-value="vehiculos.bicicletas"
                    @update:model-value="updateVehiculo('bicicletas', $event)"
                />
                <div class="rounded-lg bg-blue-50 px-3 py-2">
                    <span class="text-xs text-gray-600">Total Vehículos</span>
                    <p class="text-lg font-bold text-gray-900">
                        {{ vehiculos.carros + vehiculos.motos + vehiculos.bicicletas }}
                    </p>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
