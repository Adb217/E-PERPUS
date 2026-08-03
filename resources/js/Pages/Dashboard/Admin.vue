<script setup>
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ArrowLeftRight, AlertCircle, ScanLine } from 'lucide-vue-next';

defineProps({ stats: Object });
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-semibold text-slate-800">Dashboard Admin (OSIS)</h2>
        </template>

        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="rounded-xl border border-slate-200 bg-white p-5">
        <div class="flex items-center gap-2 text-slate-400">
            <ArrowLeftRight class="h-4 w-4" />
            <span class="text-xs font-medium uppercase">Peminjaman Aktif</span>
        </div>
        <p class="mt-2 font-display text-2xl font-semibold text-slate-800">{{ stats.active_borrowings }}</p>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white p-5">
        <div class="flex items-center gap-2 text-red-400">
            <AlertCircle class="h-4 w-4" />
            <span class="text-xs font-medium uppercase">Jatuh Tempo Hari Ini</span>
        </div>
        <p class="mt-2 font-display text-2xl font-semibold text-slate-800">{{ stats.due_today }}</p>
    </div>
    <div class="rounded-xl border border-red-200 bg-red-50 p-5">
        <div class="flex items-center gap-2 text-red-500">
            <AlertCircle class="h-4 w-4" />
            <span class="text-xs font-medium uppercase">Terlambat</span>
        </div>
        <p class="mt-2 font-display text-2xl font-semibold text-red-600">{{ stats.overdue }}</p>
    </div>
</div>

        <div class="rounded-xl border border-slate-200 bg-kosgoro-navy p-6 text-white">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white/10">
                    <ScanLine class="h-6 w-6" />
                </div>
                <div>
                    <h3 class="font-display text-base font-semibold">Meja Sirkulasi</h3>
                    <p class="mt-0.5 text-sm text-white/70">Proses peminjaman & pengembalian buku siswa di sini.</p>
                </div>
                <Link :href="route('sirkulasi.index')" class="ml-auto shrink-0 rounded-lg bg-kosgoro-gold px-4 py-2 text-sm font-medium text-white">
                    Buka Sirkulasi
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>