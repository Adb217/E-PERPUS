<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { UserPlus } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'user',
});

function submit() {
    form.post(route('users.store'));
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-semibold text-slate-800">Tambah Akun Baru</h2>
        </template>

        <form @submit.prevent="submit" class="max-w-lg rounded-xl border border-slate-200 bg-white p-6">
            <div class="mb-4">
                <label class="mb-1 block text-sm font-medium text-slate-700">Nama Lengkap</label>
                <input v-model="form.name" type="text" class="w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
                <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
            </div>

            <div class="mb-4">
                <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                <input v-model="form.email" type="email" class="w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
                <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
            </div>

            <div class="mb-4">
                <label class="mb-1 block text-sm font-medium text-slate-700">Password</label>
                <input v-model="form.password" type="password" class="w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
                <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
            </div>

            <div class="mb-6">
                <label class="mb-1 block text-sm font-medium text-slate-700">Role</label>
                <select v-model="form.role" class="w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy">
                    <option value="user">Siswa</option>
                    <option value="admin">Admin (OSIS)</option>
                </select>
            </div>

            <button type="submit" :disabled="form.processing" class="flex items-center gap-2 rounded-lg bg-kosgoro-navy px-5 py-2.5 text-sm font-medium text-white hover:bg-kosgoro-navy-dark disabled:opacity-50">
                <UserPlus class="h-4 w-4" />
                Buat Akun
            </button>
        </form>
    </AuthenticatedLayout>
</template>