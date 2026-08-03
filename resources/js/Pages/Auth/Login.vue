<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Masuk" />

        <h1 class="font-display text-xl font-semibold text-slate-800">Selamat Datang</h1>
        <p class="mt-1 text-sm text-slate-500">Masuk ke akun E-Perpus kamu</p>

        <div v-if="status" class="mt-4 rounded-lg bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="mt-6">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="nama@smkkosgorobogor.sch.id"
                />
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                <InputError class="mt-1" :message="form.errors.password" />
            </div>

            <div class="mt-4 flex items-center justify-between">
                <label class="flex items-center gap-2">
                    <Checkbox v-model:checked="form.remember" />
                    <span class="text-sm text-slate-500">Ingat saya</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-kosgoro-navy hover:underline"
                >
                    Lupa password?
                </Link>
            </div>

            <PrimaryButton class="mt-6 w-full justify-center" :disabled="form.processing">
                Masuk
            </PrimaryButton>

            <p class="mt-6 text-center text-sm text-slate-500">
                Belum punya akun?
                <Link :href="route('register')" class="font-medium text-kosgoro-navy hover:underline">
                    Daftar sebagai siswa
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>