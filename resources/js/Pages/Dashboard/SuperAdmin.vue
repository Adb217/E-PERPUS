<script setup>
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { BookOpen, ArrowLeftRight, Users, ScanLine } from 'lucide-vue-next';

defineProps({ stats: Object });
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-semibold text-slate-800">Dashboard Superadmin</h2>
        </template>

        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center gap-2 text-slate-400">
                    <BookOpen class="h-4 w-4" />
                    <span class="text-xs font-medium uppercase">Total Buku</span>
                </div>
                <p class="mt-2 font-display text-2xl font-semibold text-slate-800">{{ stats.total_books }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center gap-2 text-slate-400">
                    <ArrowLeftRight class="h-4 w-4" />
                    <span class="text-xs font-medium uppercase">Peminjaman Aktif</span>
                </div>
                <p class="mt-2 font-display text-2xl font-semibold text-slate-800">{{ stats.active_borrowings }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center gap-2 text-slate-400">
                    <Users class="h-4 w-4" />
                    <span class="text-xs font-medium uppercase">Total Siswa</span>
                </div>
                <p class="mt-2 font-display text-2xl font-semibold text-slate-800">{{ stats.total_users }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-kosgoro-navy p-6 text-white">
                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-white/10">
                    <BookOpen class="h-5 w-5" />
                </div>
                <h3 class="mt-3 font-display text-base font-semibold">Kelola Buku</h3>
                <p class="mt-1 text-sm text-white/70">Tambah, edit, atau hapus data buku & eksemplar.</p>
                <Link :href="route('catalog.index')" class="mt-4 inline-block rounded-lg bg-kosgoro-gold px-4 py-2 text-sm font-medium">
                    Buka Katalog
                </Link>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-kosgoro-navy-light">
                    <ScanLine class="h-5 w-5 text-kosgoro-navy" />
                </div>
                <h3 class="mt-3 font-display text-base font-semibold text-slate-800">Meja Sirkulasi</h3>
                <p class="mt-1 text-sm text-slate-500">Pantau atau bantu proses peminjaman langsung.</p>
                <Link :href="route('sirkulasi.index')" class="mt-4 inline-block rounded-lg bg-kosgoro-navy px-4 py-2 text-sm font-medium text-white">
                    Buka Sirkulasi
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>