<script setup>
import { useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Save, Plus, Trash2 } from 'lucide-vue-next';

const props = defineProps({ book: Object, categories: Array });

const form = useForm({
    category_id: props.book.category_id,
    title: props.book.title,
    author: props.book.author,
    publisher: props.book.publisher,
    publish_year: props.book.publish_year,
    isbn: props.book.isbn,
    synopsis: props.book.synopsis,
    page_count: props.book.page_count,
    shelf_location: props.book.shelf_location,
    language: props.book.language,
});

function submit() {
    form.put(route('books.update', props.book.id));
}

function addCopy() {
    router.post(route('books.add-copy', props.book.id), {}, { preserveScroll: true });
}

function updateCondition(copy, condition) {
    router.put(route('copies.update', copy.id), { condition }, { preserveScroll: true });
}

function deleteCopy(copy) {
    if (confirm(`Hapus eksemplar ${copy.copy_code}?`)) {
        router.delete(route('copies.destroy', copy.id), { preserveScroll: true });
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-semibold text-slate-800">Edit Buku</h2>
        </template>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <form @submit.prevent="submit" class="rounded-xl border border-slate-200 bg-white p-6 lg:col-span-2">
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Kategori</label>
                    <select v-model="form.category_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy">
                        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                </div>

                <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Judul Buku</label>
                        <input v-model="form.title" type="text" class="w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Penulis</label>
                        <input v-model="form.author" type="text" class="w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Penerbit</label>
                        <input v-model="form.publisher" type="text" class="w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Tahun Terbit</label>
                        <input v-model="form.publish_year" type="number" class="w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Jumlah Halaman</label>
                        <input v-model="form.page_count" type="number" class="w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Lokasi Rak</label>
                        <input v-model="form.shelf_location" type="text" class="w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
                    </div>
                </div>

                <div class="mb-6">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Sinopsis</label>
                    <textarea v-model="form.synopsis" rows="3" class="w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy"></textarea>
                </div>

                <button type="submit" :disabled="form.processing" class="flex items-center gap-2 rounded-lg bg-kosgoro-navy px-5 py-2.5 text-sm font-medium text-white hover:bg-kosgoro-navy-dark disabled:opacity-50">
                    <Save class="h-4 w-4" />
                    Simpan Perubahan
                </button>
            </form>

            <!-- Kelola Eksemplar -->
            <div class="rounded-xl border border-slate-200 bg-white">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <h3 class="font-display text-base font-semibold text-slate-800">Eksemplar</h3>
                    <button @click="addCopy" class="flex items-center gap-1 rounded-lg bg-kosgoro-navy-light px-2.5 py-1 text-xs font-medium text-kosgoro-navy hover:bg-kosgoro-navy hover:text-white">
                        <Plus class="h-3.5 w-3.5" /> Tambah
                    </button>
                </div>
                <div class="max-h-[420px] divide-y divide-slate-50 overflow-y-auto">
                    <div v-for="copy in book.copies" :key="copy.id" class="flex items-center justify-between gap-2 px-5 py-3">
                        <div>
                            <p class="text-sm font-medium text-slate-700">{{ copy.copy_code }}</p>
                            <select
                                :value="copy.condition"
                                @change="updateCondition(copy, $event.target.value)"
                                class="mt-1 rounded-md border-slate-200 py-0.5 text-xs focus:border-kosgoro-navy focus:ring-kosgoro-navy"
                            >
                                <option value="baik">Baik</option>
                                <option value="rusak">Rusak</option>
                                <option value="hilang">Hilang</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <span
                                class="rounded-full px-2 py-0.5 text-xs"
                                :class="copy.status === 'tersedia' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'"
                            >
                                {{ copy.status }}
                            </span>
                            <button @click="deleteCopy(copy)" class="text-slate-300 hover:text-red-500">
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>