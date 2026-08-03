<script setup>
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { BookOpen, MapPin, FileText, Calendar, User, ArrowLeft } from 'lucide-vue-next';

defineProps({ book: Object });
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <Link :href="route('catalog.index')" class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-kosgoro-navy">
                <ArrowLeft class="h-4 w-4" /> Kembali ke Katalog
            </Link>
        </template>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="flex h-64 items-center justify-center rounded-xl bg-kosgoro-navy-light lg:col-span-1">
                <BookOpen class="h-16 w-16 text-kosgoro-navy/40" />
            </div>

            <div class="lg:col-span-2">
                <span class="text-xs font-medium uppercase tracking-wide text-kosgoro-gold">{{ book.category.name }}</span>
                <h1 class="mt-1 font-display text-2xl font-semibold text-slate-800">{{ book.title }}</h1>
                <p class="mt-1 text-slate-500">{{ book.author }}</p>

                <div class="mt-4 flex flex-wrap gap-4 text-sm text-slate-500">
                    <div v-if="book.shelf_location" class="flex items-center gap-1.5">
                        <MapPin class="h-4 w-4" /> {{ book.shelf_location }}
                    </div>
                    <div v-if="book.page_count" class="flex items-center gap-1.5">
                        <FileText class="h-4 w-4" /> {{ book.page_count }} halaman
                    </div>
                    <div v-if="book.publish_year" class="flex items-center gap-1.5">
                        <Calendar class="h-4 w-4" /> {{ book.publish_year }}
                    </div>
                    <div v-if="book.publisher" class="flex items-center gap-1.5">
                        <User class="h-4 w-4" /> {{ book.publisher }}
                    </div>
                </div>

                <p v-if="book.synopsis" class="mt-4 text-sm leading-relaxed text-slate-600">{{ book.synopsis }}</p>
                <p v-else class="mt-4 text-sm italic text-slate-400">Belum ada sinopsis untuk buku ini.</p>

                <div class="mt-6 rounded-xl border border-slate-200 p-4">
                    <p class="mb-3 text-sm font-medium text-slate-700">Status Eksemplar</p>
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="copy in book.copies"
                            :key="copy.id"
                            class="rounded-lg px-2.5 py-1 text-xs font-medium"
                            :class="copy.status === 'tersedia' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'"
                        >
                            {{ copy.copy_code }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>