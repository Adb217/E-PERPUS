<script setup>
import { ref, watch } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BookCard from '@/Components/BookCard.vue';
import { Search, BookOpen, Plus, ChevronLeft, ChevronRight } from 'lucide-vue-next';

const page = usePage();
const isSuperadmin = () => page.props.auth.user?.roles?.some(r => r.name === 'superadmin');

const props = defineProps({
    mode: String,
    books: Object,
    categorySections: Array,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters?.search ?? '');
const selectedCategory = ref(props.filters?.category ?? '');

let searchTimeout = null;
watch([search, selectedCategory], () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('catalog.index'), {
            search: search.value || undefined,
            category: selectedCategory.value || undefined,
        }, { preserveState: true, replace: true });
    }, 350);
});

function scrollSlider(id, direction) {
    const el = document.getElementById(id);
    if (el) el.scrollBy({ left: direction * 600, behavior: 'smooth' });
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-semibold text-slate-800">Katalog Buku</h2>
        </template>

        <!-- Search & Filter -->
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari judul atau penulis..."
                    class="w-full rounded-lg border-slate-300 py-2.5 pl-10 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy"
                />
            </div>

            <select
                v-model="selectedCategory"
                class="rounded-lg border-slate-300 py-2.5 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy sm:w-56"
            >
                <option value="">Semua Kategori</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.slug">
                    {{ cat.name }}
                </option>
            </select>

            <Link
                v-if="isSuperadmin()"
                :href="route('books.create')"
                class="flex shrink-0 items-center justify-center gap-1.5 rounded-lg bg-kosgoro-navy px-4 py-2.5 text-sm font-medium text-white hover:bg-kosgoro-navy-dark"
            >
                <Plus class="h-4 w-4" /> Tambah Buku
            </Link>
        </div>

        <!-- MODE BROWSE: slider per kategori -->
        <div v-if="mode === 'browse'" class="flex flex-col gap-8">
            <div v-for="section in categorySections" :key="section.id">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="font-display text-lg font-semibold text-slate-800">{{ section.name }}</h3>
                    <div class="flex items-center gap-2">
                        <button
                            v-if="section.books.length > 2"
                            @click="scrollSlider(`slider-${section.id}`, -1)"
                            class="rounded-full border border-slate-200 p-1.5 text-slate-500 hover:bg-slate-50"
                        >
                            <ChevronLeft class="h-4 w-4" />
                        </button>
                        <button
                            v-if="section.books.length > 2"
                            @click="scrollSlider(`slider-${section.id}`, 1)"
                            class="rounded-full border border-slate-200 p-1.5 text-slate-500 hover:bg-slate-50"
                        >
                            <ChevronRight class="h-4 w-4" />
                        </button>
                        <button
                            @click="selectedCategory = section.slug"
                            class="ml-1 text-xs font-medium text-kosgoro-navy hover:underline"
                        >
                            Lihat Semua
                        </button>
                    </div>
                </div>

                <div
                    :id="`slider-${section.id}`"
                    class="flex snap-x gap-4 overflow-x-auto pb-2 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                >
                    <div
                        v-for="book in section.books"
                        :key="book.id"
                        class="w-56 shrink-0 snap-start sm:w-64"
                    >
                        <BookCard :book="book" />
                    </div>
                </div>
            </div>

            <div v-if="!categorySections.length" class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 py-16 text-center">
                <BookOpen class="h-10 w-10 text-slate-300" />
                <p class="mt-3 text-sm font-medium text-slate-500">Belum ada buku di katalog</p>
            </div>
        </div>

        <!-- MODE SEARCH: grid biasa -->
        <div v-else>
            <div v-if="books.data.length" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <BookCard v-for="book in books.data" :key="book.id" :book="book" />
            </div>

            <div v-else class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 py-16 text-center">
                <BookOpen class="h-10 w-10 text-slate-300" />
                <p class="mt-3 text-sm font-medium text-slate-500">Buku tidak ditemukan</p>
                <p class="text-xs text-slate-400">Coba kata kunci atau kategori lain</p>
            </div>

            <div v-if="books.links.length > 3" class="mt-6 flex flex-wrap gap-1">
                <template v-for="link in books.links" :key="link.label">
                    <button
                        v-if="link.url"
                        @click="router.get(link.url, {}, { preserveState: true })"
                        class="rounded-md px-3 py-1.5 text-sm"
                        :class="link.active ? 'bg-kosgoro-navy text-white' : 'text-slate-500 hover:bg-slate-100'"
                        v-html="link.label"
                    />
                    <span v-else class="rounded-md px-3 py-1.5 text-sm text-slate-300" v-html="link.label" />
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>