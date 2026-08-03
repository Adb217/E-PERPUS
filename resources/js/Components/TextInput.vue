<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({ type: String, required: true });

defineProps({
    type: { type: String, default: 'text' },
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        v-model="model"
        :type="type"
        class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy"
        ref="input"
    />
</template>