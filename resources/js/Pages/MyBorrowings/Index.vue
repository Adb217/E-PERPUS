<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { BookOpen, Calendar, MapPin } from 'lucide-vue-next';

const props = defineProps({ borrowings: Object, filters: Object });

const selectedStatus = ref(props.filters.status ?? '');

watch(selectedStatus, (val) => {
    router.get(route('my-borrowings.index'), {
        status: val || undefined,
    }, { preserveState: true, replace: true });
});

const statusLabel = {
    dipinjam: 'Sedang Dipinjam',
    dikembalikan: 'Sudah Dikembalikan',
    terlambat: 'Terlambat',
};

const statusColor = {
    dipinjam: 'bg-amber-100 text-amber-700',
    dikembalikan: 'bg-emerald-100 text-emerald-700',
    terlambat: 'bg-red-100 text-red-700',
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-semibold text-slate-800">Peminjaman Saya</h2>
        </template>

        <div class="mb-6">
            <select v-model="selectedStatus" class="rounded-lg border-slate-300 py-2.5 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy sm:w-56">
                <option value="">Semua Status</option>
                <option value="dipinjam">Sedang Dipinjam</option>
                <option value="dikembalikan">Sudah Dikembalikan</option>
                <option value="terlambat">Terlambat</option>
            </select>
        </div>

        <div v-if="borrowings.data.length" class="flex flex-col gap-3">
            <div
                v-for="b in borrowings.data"
                :key="b.id"
                class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-start gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-kosgoro-navy-light">
                        <BookOpen class="h-5 w-5 text-kosgoro-navy" />
                    </div>
                    <div>
                        <span class="text-xs font-medium uppercase tracking-wide text-kosgoro-gold">
                            {{ b.book_copy.book.category.name }}
                        </span>
                        <h3 class="font-display text-sm font-semibold text-slate-800">{{ b.book_copy.book.title }}</h3>
                        <p class="text-xs text-slate-400">{{ b.book_copy.book.author }} &middot; {{ b.book_copy.copy_code }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 sm:justify-end">
                    <div class="flex items-center gap-1.5">
                        <Calendar class="h-3.5 w-3.5" />
                        Pinjam: {{ b.borrowed_at }}
                    </div>
                    <div class="flex items-center gap-1.5">
                        <Calendar class="h-3.5 w-3.5" />
                        Tempo: {{ b.due_date }}
                    </div>
                    <div v-if="b.points_earned > 0" class="font-medium text-emerald-600">
                        +{{ b.points_earned }} poin
                    </div>
                    <span class="rounded-full px-2.5 py-1 font-medium" :class="statusColor[b.status]">
                        {{ statusLabel[b.status] }}
                    </span>
                </div>
            </div>
        </div>

        <div v-else class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 py-16 text-center">
            <BookOpen class="h-10 w-10 text-slate-300" />
            <p class="mt-3 text-sm font-medium text-slate-500">Belum ada riwayat peminjaman</p>
            <p class="text-xs text-slate-400">Yuk pinjam buku pertamamu di perpustakaan!</p>
        </div>

        <div v-if="borrowings.links.length > 3" class="mt-6 flex flex-wrap gap-1">
            <template v-for="link in borrowings.links" :key="link.label">
                <button v-if="link.url" @click="router.get(link.url, {}, { preserveState: true })"
                    class="rounded-md px-3 py-1.5 text-sm"
                    :class="link.active ? 'bg-kosgoro-navy text-white' : 'text-slate-500 hover:bg-slate-100'"
                    v-html="link.label" />
                <span v-else class="rounded-md px-3 py-1.5 text-sm text-slate-300" v-html="link.label" />
            </template>
        </div>
    </AuthenticatedLayout>
</template>