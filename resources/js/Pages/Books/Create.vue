<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { BookPlus } from 'lucide-vue-next';

defineProps({ categories: Array });

const useNewCategory = ref(false);

const form = useForm({
    category_id: '',
    new_category: '',
    title: '',
    author: '',
    publisher: '',
    publish_year: '',
    isbn: '',
    synopsis: '',
    page_count: '',
    shelf_location: '',
    language: 'Indonesia',
    copies_count: 1,
});

function submit() {
    form.post(route('books.store'));
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-semibold text-slate-800">Tambah Buku Baru</h2>
        </template>

        <form @submit.prevent="submit" class="max-w-2xl rounded-xl border border-slate-200 bg-white p-6">
            <div class="mb-4">
                <label class="mb-1 block text-sm font-medium text-slate-700">Kategori</label>
                <div v-if="!useNewCategory" class="flex gap-2">
                    <select v-model="form.category_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy">
                        <option value="">Pilih kategori</option>
                        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                    <button type="button" @click="useNewCategory = true" class="shrink-0 rounded-lg border border-slate-300 px-3 text-sm text-slate-600 hover:bg-slate-50">
                        + Baru
                    </button>
                </div>
                <div v-else class="flex gap-2">
                    <input v-model="form.new_category" type="text" placeholder="Nama kategori baru" class="w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
                    <button type="button" @click="useNewCategory = false; form.new_category = ''" class="shrink-0 rounded-lg border border-slate-300 px-3 text-sm text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                </div>
                <p v-if="form.errors.category_id" class="mt-1 text-xs text-red-600">{{ form.errors.category_id }}</p>
            </div>

            <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Judul Buku</label>
                    <input v-model="form.title" type="text" class="w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
                    <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Penulis</label>
                    <input v-model="form.author" type="text" class="w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
                    <p v-if="form.errors.author" class="mt-1 text-xs text-red-600">{{ form.errors.author }}</p>
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
                    <label class="mb-1 block text-sm font-medium text-slate-700">ISBN (opsional)</label>
                    <input v-model="form.isbn" type="text" class="w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Jumlah Halaman</label>
                    <input v-model="form.page_count" type="number" class="w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Lokasi Rak</label>
                    <input v-model="form.shelf_location" type="text" placeholder="Rak A-1" class="w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Bahasa</label>
                    <input v-model="form.language" type="text" class="w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
                </div>
            </div>

            <div class="mb-4">
                <label class="mb-1 block text-sm font-medium text-slate-700">Sinopsis (opsional)</label>
                <textarea v-model="form.synopsis" rows="3" class="w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy"></textarea>
            </div>

            <div class="mb-6">
                <label class="mb-1 block text-sm font-medium text-slate-700">Jumlah Eksemplar</label>
                <input v-model="form.copies_count" type="number" min="1" class="w-full max-w-[160px] rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
                <p class="mt-1 text-xs text-slate-400">Sistem otomatis bikin kode eksemplar buat tiap kopi.</p>
                <p v-if="form.errors.copies_count" class="mt-1 text-xs text-red-600">{{ form.errors.copies_count }}</p>
            </div>

            <button type="submit" :disabled="form.processing" class="flex items-center gap-2 rounded-lg bg-kosgoro-navy px-5 py-2.5 text-sm font-medium text-white hover:bg-kosgoro-navy-dark disabled:opacity-50">
                <BookPlus class="h-4 w-4" />
                Simpan Buku
            </button>
        </form>
    </AuthenticatedLayout>
</template>