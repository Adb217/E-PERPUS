<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    LayoutDashboard, BookOpen, ArrowLeftRight, History,
    Award, Settings, LogOut, Menu, Users, ClipboardList, FileBarChart,
} from 'lucide-vue-next';
import { AlertTriangle } from 'lucide-vue-next';

const page = usePage();
const user = computed(() => page.props.auth.user);
const roleName = computed(() => user.value?.roles?.[0]?.name ?? 'user');

const sidebarOpen = ref(false);

const baseMenu = [
    { name: 'Dashboard', href: route('dashboard'), icon: LayoutDashboard },
    { name: 'Katalog Buku', href: route('catalog.index'), icon: BookOpen },
    { name: 'Riwayat Saya', href: route('my-borrowings.index'), icon: History },
    { name: 'Rewards', href: route('rewards.index'), icon: Award },
];

const adminMenu = [
    { name: 'Sirkulasi', href: route('sirkulasi.index'), icon: ClipboardList },
];

const superadminMenu = [
    { name: 'Manajemen User', href: route('users.index'), icon: Users },
    { name: 'Validasi Laporan', href: route('condition-reports.index'), icon: AlertTriangle },
    { name: 'Laporan', href: route('reports.index'), icon: FileBarChart },
];

const menuItems = computed(() => {
    if (roleName.value === 'superadmin') return [...baseMenu, ...adminMenu, ...superadminMenu];
    if (roleName.value === 'admin') return [...baseMenu, ...adminMenu];
    return baseMenu;
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 font-sans">
        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-40 w-64 transform bg-kosgoro-navy transition-transform lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex h-16 items-center gap-3 border-b border-white/10 px-6">
                <div class="flex h-9 w-9 items-center justify-center rounded-md bg-kosgoro-gold font-display text-lg font-semibold text-kosgoro-navy-dark">
                    K
                </div>
                <div class="leading-tight">
                    <p class="font-display text-base font-semibold text-white">E-Perpus</p>
                    <p class="text-xs text-white/60">SMK Kosgoro Bogor</p>
                </div>
            </div>

            <nav class="mt-4 flex flex-col gap-1 px-3">
                <Link
                    v-for="item in menuItems"
                    :key="item.name"
                    :href="item.href"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/70 transition hover:bg-white/10 hover:text-white"
                    :class="{ 'bg-white/10 text-white': route().current().startsWith(item.href) }"
                >
                    <component :is="item.icon" class="h-[18px] w-[18px]" />
                    {{ item.name }}
                </Link>
            </nav>

            <div class="absolute bottom-0 w-full border-t border-white/10 p-3">
                <Link
                    :href="route('profile.edit')"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/70 transition hover:bg-white/10 hover:text-white"
                >
                    <Settings class="h-[18px] w-[18px]" />
                    Pengaturan Akun
                </Link>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/70 transition hover:bg-white/10 hover:text-white"
                >
                    <LogOut class="h-[18px] w-[18px]" />
                    Keluar
                </Link>
            </div>
        </aside>

        <!-- Overlay mobile -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-30 bg-black/40 lg:hidden"
            @click="sidebarOpen = false"
        />

        <!-- Main content -->
        <div class="lg:pl-64">
            <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 sm:px-6">
                <button class="lg:hidden" @click="sidebarOpen = true">
                    <Menu class="h-6 w-6 text-slate-500" />
                </button>

                <div v-if="$slots.header" class="hidden lg:block">
                    <slot name="header" />
                </div>

                <div class="ml-auto flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-sm font-medium text-slate-800">{{ user.name }}</p>
                        <p class="text-xs capitalize text-slate-400">{{ roleName }}</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-kosgoro-navy-light font-display font-semibold text-kosgoro-navy">
                        {{ user.name.charAt(0) }}
                    </div>
                </div>
            </header>

            <div class="p-4 sm:p-6 lg:hidden">
                <slot name="header" />
            </div>

            <main class="p-4 sm:p-6">
                <slot />
            </main>
        </div>
    </div>
</template>