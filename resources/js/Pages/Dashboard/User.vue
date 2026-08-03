<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Award, BookOpen, ArrowLeftRight, Trophy } from 'lucide-vue-next';

const props = defineProps({
    stats: Object,
    recentBorrowings: Array,
    badges: Array,
    currentBadge: Object,
    nextBadge: Object,
});

const progressPercent = props.nextBadge
    ? Math.min(100, Math.round((props.stats.points / props.nextBadge.min_points) * 100))
    : 100;
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-semibold text-slate-800">Dashboard Siswa</h2>
        </template>

        <!-- Progress reward -->
        <div class="mb-6 rounded-xl border border-slate-200 bg-white p-6">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-kosgoro-gold-light px-2.5 py-1 text-xs font-medium text-kosgoro-gold">
                        <Trophy class="h-3.5 w-3.5" />
                        {{ currentBadge?.name ?? 'Belum ada badge' }}
                    </span>
                    <h3 class="mt-2 font-display text-lg font-semibold text-slate-800">
                        {{ stats.points }} Poin Terkumpul
                    </h3>
                    <p v-if="nextBadge" class="mt-1 text-sm text-slate-500">
                        {{ nextBadge.min_points - stats.points }} poin lagi menuju badge "{{ nextBadge.name }}"
                    </p>
                    <p v-else class="mt-1 text-sm text-slate-500">Kamu udah dapet semua badge yang ada 🎉</p>
                </div>
                <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-kosgoro-navy-light">
                    <Award class="h-9 w-9 text-kosgoro-navy" />
                </div>
            </div>

            <div v-if="nextBadge" class="mt-4">
                <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full rounded-full bg-kosgoro-gold transition-all" :style="{ width: progressPercent + '%' }" />
                </div>
            </div>
        </div>

        <!-- Stat cards -->
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center gap-2 text-slate-400">
                    <ArrowLeftRight class="h-4 w-4" />
                    <span class="text-xs font-medium uppercase">Sedang Dipinjam</span>
                </div>
                <p class="mt-2 font-display text-2xl font-semibold text-slate-800">{{ stats.active_borrowings }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center gap-2 text-slate-400">
                    <BookOpen class="h-4 w-4" />
                    <span class="text-xs font-medium uppercase">Buku Selesai Dibaca</span>
                </div>
                <p class="mt-2 font-display text-2xl font-semibold text-slate-800">{{ stats.total_books_read }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center gap-2 text-slate-400">
                    <Trophy class="h-4 w-4" />
                    <span class="text-xs font-medium uppercase">Total Badge Tersedia</span>
                </div>
                <p class="mt-2 font-display text-2xl font-semibold text-slate-800">{{ badges.length }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <!-- Riwayat -->
            <div class="rounded-xl border border-slate-200 bg-white lg:col-span-2">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="font-display text-base font-semibold text-slate-800">Riwayat Peminjaman</h3>
                </div>
                <div v-if="recentBorrowings.length" class="divide-y divide-slate-50">
                    <div v-for="b in recentBorrowings" :key="b.id" class="flex items-center justify-between px-5 py-3">
                        <div>
                            <p class="text-sm font-medium text-slate-700">{{ b.book_copy.book.title }}</p>
                            <p class="text-xs text-slate-400">Dipinjam {{ b.borrowed_at }}</p>
                        </div>
                        <span
                            class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                            :class="{
                                'bg-amber-100 text-amber-700': b.status === 'dipinjam',
                                'bg-emerald-100 text-emerald-700': b.status === 'dikembalikan',
                                'bg-red-100 text-red-700': b.status === 'terlambat',
                            }"
                        >
                            {{ b.status }}
                        </span>
                    </div>
                </div>
                <p v-else class="px-5 py-8 text-center text-sm text-slate-400">Belum ada riwayat peminjaman</p>
            </div>

            <!-- Daftar badge -->
            <div class="rounded-xl border border-slate-200 bg-white">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="font-display text-base font-semibold text-slate-800">Semua Badge</h3>
                </div>
                <div class="divide-y divide-slate-50">
                    <div
                        v-for="badge in badges"
                        :key="badge.id"
                        class="flex items-center gap-3 px-5 py-3"
                        :class="stats.points >= badge.min_points ? 'opacity-100' : 'opacity-40'"
                    >
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-kosgoro-gold-light">
                            <Trophy class="h-4 w-4 text-kosgoro-gold" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-700">{{ badge.name }}</p>
                            <p class="text-xs text-slate-400">Min. {{ badge.min_points }} poin</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>