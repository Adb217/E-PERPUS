<script setup>
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Trophy, Medal, History } from 'lucide-vue-next';

const props = defineProps({
    badges: Array,
    userPoints: Number,
    leaderboard: Array,
    pointLogs: Array,
});

const page = usePage();
const currentUserId = page.props.auth.user.id;

const rankMedalColor = (index) => {
    if (index === 0) return 'text-yellow-500';
    if (index === 1) return 'text-slate-400';
    if (index === 2) return 'text-amber-700';
    return 'text-slate-300';
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-semibold text-slate-800">Rewards</h2>
        </template>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <!-- Badges -->
            <div class="rounded-xl border border-slate-200 bg-white lg:col-span-2">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="font-display text-base font-semibold text-slate-800">Koleksi Badge</h3>
                    <p class="text-xs text-slate-400">Kamu punya {{ userPoints }} poin</p>
                </div>
                <div class="grid grid-cols-1 gap-3 p-5 sm:grid-cols-3">
                    <div
                        v-for="badge in badges"
                        :key="badge.id"
                        class="flex flex-col items-center gap-2 rounded-xl border p-4 text-center"
                        :class="userPoints >= badge.min_points
                            ? 'border-kosgoro-gold bg-kosgoro-gold-light'
                            : 'border-slate-100 bg-slate-50 opacity-50'"
                    >
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full"
                            :class="userPoints >= badge.min_points ? 'bg-kosgoro-gold' : 'bg-slate-200'"
                        >
                            <Trophy class="h-6 w-6 text-white" />
                        </div>
                        <p class="text-sm font-semibold text-slate-800">{{ badge.name }}</p>
                        <p class="text-xs text-slate-400">Min. {{ badge.min_points }} poin</p>
                    </div>
                </div>
            </div>

            <!-- Leaderboard -->
            <div class="rounded-xl border border-slate-200 bg-white">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="flex items-center gap-2 font-display text-base font-semibold text-slate-800">
                        <Medal class="h-4 w-4 text-kosgoro-gold" />
                        Papan Peringkat
                    </h3>
                </div>
                <div class="divide-y divide-slate-50">
                    <div
                        v-for="(u, index) in leaderboard"
                        :key="u.id"
                        class="flex items-center gap-3 px-5 py-3"
                        :class="{ 'bg-kosgoro-navy-light': u.id === currentUserId }"
                    >
                        <span class="w-5 text-center text-sm font-bold" :class="rankMedalColor(index)">
                            {{ index + 1 }}
                        </span>
                        <span class="flex-1 text-sm font-medium text-slate-700">
                            {{ u.name }} <span v-if="u.id === currentUserId" class="text-xs text-kosgoro-navy">(Kamu)</span>
                        </span>
                        <span class="text-sm font-semibold text-slate-800">{{ u.points }}</span>
                    </div>
                    <p v-if="!leaderboard.length" class="px-5 py-6 text-center text-sm text-slate-400">Belum ada data</p>
                </div>
            </div>
        </div>

        <!-- Riwayat poin -->
        <div class="mt-4 rounded-xl border border-slate-200 bg-white">
            <div class="flex items-center gap-2 border-b border-slate-100 px-5 py-4">
                <History class="h-4 w-4 text-slate-400" />
                <h3 class="font-display text-base font-semibold text-slate-800">Riwayat Poin</h3>
            </div>
            <div class="divide-y divide-slate-50">
                <div v-for="log in pointLogs" :key="log.id" class="flex items-center justify-between px-5 py-3">
                    <div>
                        <p class="text-sm text-slate-700">{{ log.reason }}</p>
                        <p v-if="log.borrowing" class="text-xs text-slate-400">{{ log.borrowing.book_copy.book.title }}</p>
                    </div>
                    <span class="text-sm font-semibold text-emerald-600">+{{ log.points }}</span>
                </div>
                <p v-if="!pointLogs.length" class="px-5 py-6 text-center text-sm text-slate-400">Belum ada riwayat poin</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>