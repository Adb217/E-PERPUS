<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { FileDown, TrendingUp, Users, AlertTriangle, Award } from 'lucide-vue-next';

const props = defineProps({
    summary: Object,
    popularBooks: Array,
    activeReaders: Array,
    categoryBreakdown: Array,
    selectedMonth: String,
});

const month = ref(props.selectedMonth);

function applyFilter() {
    router.get(route('reports.index'), { month: month.value }, { preserveState: true });
}

function exportCsv() {
    window.location.href = route('reports.export', { month: month.value });
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-semibold text-slate-800">Laporan</h2>
        </template>

        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center">
            <input
                v-model="month"
                type="month"
                @change="applyFilter"
                class="rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy"
            />
            <button
                @click="exportCsv"
                class="flex items-center gap-1.5 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50"
            >
                <FileDown class="h-4 w-4" /> Export CSV
            </button>
        </div>

        <!-- Summary cards -->
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center gap-2 text-slate-400">
                    <TrendingUp class="h-4 w-4" />
                    <span class="text-xs font-medium uppercase">Peminjaman Bulan Ini</span>
                </div>
                <p class="mt-2 font-display text-2xl font-semibold text-slate-800">{{ summary.total_borrowings_this_month }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center gap-2 text-emerald-500">
                    <Users class="h-4 w-4" />
                    <span class="text-xs font-medium uppercase">Kembali Tepat Waktu</span>
                </div>
                <p class="mt-2 font-display text-2xl font-semibold text-slate-800">{{ summary.total_returned_on_time }}</p>
            </div>
            <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                <div class="flex items-center gap-2 text-red-500">
                    <AlertTriangle class="h-4 w-4" />
                    <span class="text-xs font-medium uppercase">Total Terlambat</span>
                </div>
                <p class="mt-2 font-display text-2xl font-semibold text-red-600">{{ summary.total_overdue }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center gap-2 text-kosgoro-gold">
                    <Award class="h-4 w-4" />
                    <span class="text-xs font-medium uppercase">Total Poin Terbit</span>
                </div>
                <p class="mt-2 font-display text-2xl font-semibold text-slate-800">{{ summary.total_points_issued }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <!-- Buku terpopuler -->
            <div class="rounded-xl border border-slate-200 bg-white lg:col-span-2">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="font-display text-base font-semibold text-slate-800">Buku Terpopuler</h3>
                </div>
                <div class="divide-y divide-slate-50">
                    <div v-for="(book, i) in popularBooks" :key="book.id" class="flex items-center gap-3 px-5 py-3">
                        <span class="w-5 text-center text-sm font-bold text-slate-300">{{ i + 1 }}</span>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-700">{{ book.title }}</p>
                            <p class="text-xs text-slate-400">{{ book.author }}</p>
                        </div>
                        <span class="text-sm font-semibold text-kosgoro-navy">{{ book.borrow_count }}x</span>
                    </div>
                    <p v-if="!popularBooks.length" class="px-5 py-8 text-center text-sm text-slate-400">Belum ada data</p>
                </div>
            </div>

            <!-- Pembaca teraktif -->
            <div class="rounded-xl border border-slate-200 bg-white">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="font-display text-base font-semibold text-slate-800">Pembaca Teraktif</h3>
                </div>
                <div class="divide-y divide-slate-50">
                    <div v-for="reader in activeReaders" :key="reader.name" class="flex items-center justify-between px-5 py-3">
                        <span class="text-sm text-slate-700">{{ reader.name }}</span>
                        <span class="text-sm font-semibold text-kosgoro-gold">{{ reader.points }} pts</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribusi kategori -->
        <div class="mt-4 rounded-xl border border-slate-200 bg-white">
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="font-display text-base font-semibold text-slate-800">Distribusi Buku per Kategori</h3>
            </div>
            <div class="p-5">
                <div v-for="cat in categoryBreakdown" :key="cat.name" class="mb-3 last:mb-0">
                    <div class="mb-1 flex justify-between text-xs text-slate-500">
                        <span>{{ cat.name }}</span>
                        <span>{{ cat.total_books }} buku</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                        <div
                            class="h-full rounded-full bg-kosgoro-navy"
                            :style="{ width: (cat.total_books / categoryBreakdown[0].total_books * 100) + '%' }"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>