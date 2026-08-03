<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
});
</script>

<template>
    <Head title="Pengaturan Akun" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-semibold text-slate-800">Pengaturan Akun</h2>
        </template>

        <div class="mx-auto flex max-w-2xl flex-col gap-4">
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" />
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <UpdatePasswordForm />
            </div>

            <div class="rounded-xl border border-red-200 bg-white p-6">
                <DeleteUserForm />
            </div>
        </div>
    </AuthenticatedLayout>
</template>