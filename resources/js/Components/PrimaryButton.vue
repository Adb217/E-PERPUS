<script setup>
defineProps({ type: { type: String, default: 'submit' } });
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center gap-2 rounded-lg bg-kosgoro-navy px-5 py-2.5 text-sm font-medium text-white transition hover:bg-kosgoro-navy-dark focus:outline-none focus:ring-2 focus:ring-kosgoro-navy focus:ring-offset-2 disabled:opacity-50"
    >
        <slot />
    </button>
</template>