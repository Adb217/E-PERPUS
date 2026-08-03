<script setup>
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { AlertTriangle, Check, X } from 'lucide-vue-next';

defineProps({ reports: Object });

function approve(report) {
    if (confirm(`Setujui laporan ${report.condition} untuk ${report.book_copy.copy_code}?`)) {
        router.post(route('condition-reports.approve', report.id), {}, { preserveScroll: true });
    }
}

function reject(report) {
    const note = prompt('Alasan penolakan (opsional):');
    router.post(route('condition-reports.reject', report.id), { review_note: note }, { preserveScroll: true });
}

const statusColor = {
    pending: 'bg-amber-100 text-amber-700',
    approved: 'bg-emerald-100 text-emerald-700',
    rejected: 'bg-red-100 text-red-700',
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-semibold text-slate-800">Validasi Laporan Kondisi</h2>
        </template>

       <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase text-slate-400">
                        <th class="px-5 py-3">Buku</th>
                        <th class="px-5 py-3">Dilaporkan Oleh</th>
                        <th class="px-5 py-3">Kondisi</th>
                        <th class="px-5 py-3">Catatan</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="r in reports.data" :key="r.id" class="border-b border-slate-50">
                        <td class="px-5 py-3">
                            <p class="font-medium text-slate-700">{{ r.book_copy.book.title }}</p>
                            <p class="text-xs text-slate-400">{{ r.book_copy.copy_code }}</p>
                        </td>
                        <td class="px-5 py-3 text-slate-500">{{ r.reporter.name }}</td>
                        <td class="px-5 py-3 capitalize text-slate-700">{{ r.condition }}</td>
                        <td class="px-5 py-3 text-slate-400">{{ r.note || '-' }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-medium" :class="statusColor[r.status]">
                                {{ r.status }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <div v-if="r.status === 'pending'" class="flex justify-end gap-2">
                                <button @click="approve(r)" class="rounded-lg bg-emerald-100 p-1.5 text-emerald-700 hover:bg-emerald-200">
                                    <Check class="h-4 w-4" />
                                </button>
                                <button @click="reject(r)" class="rounded-lg bg-red-100 p-1.5 text-red-700 hover:bg-red-200">
                                    <X class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!reports.data.length">
                        <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                            <AlertTriangle class="mx-auto mb-2 h-8 w-8 text-slate-300" />
                            Tidak ada laporan
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        </div>
    </AuthenticatedLayout>
</template>