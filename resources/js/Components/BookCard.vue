<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, MapPin, FileText, Pencil } from 'lucide-vue-next';

defineProps({ book: Object });

const page = usePage();
const isSuperadmin = () => page.props.auth.user?.roles?.some(r => r.name === 'superadmin');
</script>

<template>
    <Link
        :href="route('books.show', book.id)"
        class="group block overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:shadow-md"
    >
        <div class="relative flex h-36 items-center justify-center bg-kosgoro-navy-light">
            <BookOpen class="h-10 w-10 text-kosgoro-navy/40" />

            <Link
                v-if="isSuperadmin()"
                :href="route('books.edit', book.id)"
                @click.stop
                class="absolute left-2 top-2 flex h-7 w-7 items-center justify-center rounded-full bg-white/90 text-slate-600 hover:bg-white"
            >
                <Pencil class="h-3.5 w-3.5" />
            </Link>

            <span
                class="absolute right-2 top-2 rounded-full px-2 py-0.5 text-xs font-medium"
                :class="book.available_copies_count > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'"
            >
                {{ book.available_copies_count > 0 ? 'Tersedia' : 'Dipinjam Semua' }}
            </span>
        </div>

        <div class="p-4">
            <span class="text-xs font-medium uppercase tracking-wide text-kosgoro-gold">
                {{ book.category.name }}
            </span>
            <h3 class="mt-1 font-display text-base font-semibold leading-snug text-slate-800">
                {{ book.title }}
            </h3>
            <p class="mt-0.5 text-sm text-slate-500">{{ book.author }}</p>

            <div class="mt-3 flex flex-col gap-1.5 border-t border-slate-100 pt-3 text-xs text-slate-500">
                <div v-if="book.shelf_location" class="flex items-center gap-1.5">
                    <MapPin class="h-3.5 w-3.5" /> {{ book.shelf_location }}
                </div>
                <div v-if="book.page_count" class="flex items-center gap-1.5">
                    <FileText class="h-3.5 w-3.5" /> {{ book.page_count }} halaman
                </div>
                <div class="flex items-center gap-1.5 font-medium text-slate-700">
                    {{ book.available_copies_count }} / {{ book.total_copies_count }} eksemplar
                </div>
            </div>
        </div>
    </Link>
</template>