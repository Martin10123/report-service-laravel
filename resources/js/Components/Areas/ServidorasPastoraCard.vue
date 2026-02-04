<script setup>
import Card from '@/Components/Card.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardTitle from '@/Components/CardTitle.vue';
import CardContent from '@/Components/CardContent.vue';
import TextInput from '@/Components/TextInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

defineProps({
    nombres: {
        type: Array,
        default: () => [''],
    },
});

const emit = defineEmits(['update', 'add', 'remove']);

const updateNombre = (index, value) => {
    emit('update', index, value);
};

const addNombre = () => {
    emit('add');
};

const removeNombre = (index) => {
    emit('remove', index);
};
</script>

<template>
    <Card>
        <CardHeader compact>
            <CardTitle size="sm">Servidora de Pastora</CardTitle>
        </CardHeader>
        <CardContent compact>
            <div class="space-y-2">
                <div
                    v-for="(nombre, index) in nombres"
                    :key="index"
                    class="flex items-center gap-2"
                >
                    <TextInput
                        :model-value="nombre"
                        placeholder="Nombre"
                        class="w-full h-8 text-sm"
                        @input="updateNombre(index, $event.target.value)"
                    />
                    <button
                        v-if="nombres.length > 1"
                        type="button"
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md text-red-600 hover:bg-red-50"
                        @click="removeNombre(index)"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <SecondaryButton type="button" class="w-full text-xs" @click="addNombre">
                    + Agregar
                </SecondaryButton>
            </div>
        </CardContent>
    </Card>
</template>
