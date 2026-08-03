<script setup>
import { ref, watch } from 'vue';
import { router, Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Search, UserPlus, KeyRound, Trash2, Users } from 'lucide-vue-next';

const props = defineProps({ users: Object, filters: Object });
const page = usePage();

const search = ref(props.filters.search ?? '');
const selectedRole = ref(props.filters.role ?? '');

let searchTimeout = null;
watch([search, selectedRole], () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('users.index'), {
            search: search.value || undefined,
            role: selectedRole.value || undefined,
        }, { preserveState: true, replace: true });
    }, 350);
});

function roleName(user) {
    return user.roles?.[0]?.name ?? '-';
}

function changeRole(user, role) {
    router.put(route('users.update-role', user.id), { role }, { preserveScroll: true });
}

function resetPassword(user) {
    if (confirm(`Reset password untuk ${user.name}?`)) {
        router.post(route('users.reset-password', user.id), {}, { preserveScroll: true });
    }
}

function deleteUser(user) {
    if (confirm(`Hapus akun ${user.name}? Aksi ini tidak bisa dibatalkan.`)) {
        router.delete(route('users.destroy', user.id), { preserveScroll: true });
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-semibold text-slate-800">Manajemen User</h2>
        </template>

        <div v-if="page.props.flash?.success" class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ page.props.flash.success }}
        </div>
        <div v-if="page.props.errors?.user || page.props.errors?.role" class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ page.props.errors.user || page.props.errors.role }}
        </div>

        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input v-model="search" type="text" placeholder="Cari nama atau email..."
                    class="w-full rounded-lg border-slate-300 py-2.5 pl-10 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
            </div>

            <select v-model="selectedRole" class="rounded-lg border-slate-300 py-2.5 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy sm:w-48">
                <option value="">Semua Role</option>
                <option value="superadmin">Superadmin</option>
                <option value="admin">Admin (OSIS)</option>
                <option value="user">Siswa</option>
            </select>

            <Link :href="route('users.create')"
                class="flex shrink-0 items-center justify-center gap-1.5 rounded-lg bg-kosgoro-navy px-4 py-2.5 text-sm font-medium text-white hover:bg-kosgoro-navy-dark">
                <UserPlus class="h-4 w-4" /> Tambah Akun
            </Link>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase text-slate-400">
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Role</th>
                        <th class="px-5 py-3">Poin</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="u in users.data" :key="u.id" class="border-b border-slate-50">
                        <td class="px-5 py-3 font-medium text-slate-700">{{ u.name }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ u.email }}</td>
                        <td class="px-5 py-3">
                            <select
                                :value="roleName(u)"
                                @change="changeRole(u, $event.target.value)"
                                :disabled="roleName(u) === 'superadmin'"
                                class="rounded-md border-slate-200 py-1 text-xs focus:border-kosgoro-navy focus:ring-kosgoro-navy disabled:bg-slate-50 disabled:text-slate-400"
                            >
                                <option v-if="roleName(u) === 'superadmin'" value="superadmin">Superadmin</option>
                                <option value="admin">Admin (OSIS)</option>
                                <option value="user">Siswa</option>
                            </select>
                        </td>
                        <td class="px-5 py-3 text-slate-500">{{ u.points }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="resetPassword(u)" title="Reset Password" class="text-slate-400 hover:text-kosgoro-navy">
                                    <KeyRound class="h-4 w-4" />
                                </button>
                                <button
                                    v-if="roleName(u) !== 'superadmin'"
                                    @click="deleteUser(u)"
                                    title="Hapus Akun"
                                    class="text-slate-400 hover:text-red-500"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!users.data.length">
                        <td colspan="5" class="px-5 py-10 text-center text-slate-400">
                            <Users class="mx-auto mb-2 h-8 w-8 text-slate-300" />
                            Tidak ada user ditemukan
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        </div>

        <div v-if="users.links.length > 3" class="mt-4 flex flex-wrap gap-1">
            <template v-for="link in users.links" :key="link.label">
                <button v-if="link.url" @click="router.get(link.url, {}, { preserveState: true })"
                    class="rounded-md px-3 py-1.5 text-sm"
                    :class="link.active ? 'bg-kosgoro-navy text-white' : 'text-slate-500 hover:bg-slate-100'"
                    v-html="link.label" />
                <span v-else class="rounded-md px-3 py-1.5 text-sm text-slate-300" v-html="link.label" />
            </template>
        </div>
    </AuthenticatedLayout>
</template>