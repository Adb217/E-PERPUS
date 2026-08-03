<script setup>
const checked = defineModel({ type: [Array, Boolean], default: false });

defineProps({ value: { default: null } });
</script>

<template>
    <input
        v-model="checked"
        type="checkbox"
        :value="value"
        class="rounded border-slate-300 text-kosgoro-navy shadow-sm focus:ring-kosgoro-navy"
    />
</template>