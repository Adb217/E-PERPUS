<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen font-sans">
        <!-- Panel kiri: branding, disembunyiin di mobile -->
        <div class="relative hidden w-1/2 flex-col justify-between overflow-hidden bg-kosgoro-navy p-12 text-white lg:flex">
            <!-- Motif garis vertikal ala punggung buku di rak -->
            <div class="pointer-events-none absolute inset-0 flex opacity-[0.07]">
                <div v-for="i in 24" :key="i" class="h-full flex-1 border-r border-white" />
            </div>

            <div class="relative flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-md bg-kosgoro-gold font-display text-lg font-semibold text-kosgoro-navy-dark">
                    K
                </div>
                <div class="leading-tight">
                    <p class="font-display text-base font-semibold">E-Perpus</p>
                    <p class="text-xs text-white/60">SMK Kosgoro Kota Bogor</p>
                </div>
            </div>

            <div class="relative">
                <p class="font-display text-3xl font-medium leading-snug">
                    "Membaca satu buku hari ini,<br />membuka satu dunia esok hari."
                </p>
                <p class="mt-4 text-sm text-white/60">
                    Kelola koleksi, pinjam buku, dan kumpulkan reward dari satu tempat.
                </p>
            </div>

            <p class="relative text-xs text-white/40">&copy; {{ new Date().getFullYear() }} SMK Kosgoro Kota Bogor</p>
        </div>

        <!-- Panel kanan: form -->
        <div class="flex w-full flex-col items-center justify-center bg-slate-50 px-6 py-12 lg:w-1/2">
            <div class="mb-8 flex items-center gap-3 lg:hidden">
                <div class="flex h-10 w-10 items-center justify-center rounded-md bg-kosgoro-navy font-display text-lg font-semibold text-white">
                    K
                </div>
                <div class="leading-tight">
                    <p class="font-display text-base font-semibold text-slate-800">E-Perpus</p>
                    <p class="text-xs text-slate-400">SMK Kosgoro Kota Bogor</p>
                </div>
            </div>

         <div class="w-full max-w-sm rounded-xl border border-slate-200 bg-white p-6 sm:p-8">
                <slot />
            </div>
        </div>
    </div>
</template>