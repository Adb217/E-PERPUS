<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <header>
        <h2 class="font-display text-base font-semibold text-slate-800">Ganti Password</h2>
        <p class="mt-1 text-sm text-slate-500">Pastikan akun kamu pakai password yang kuat dan gampang diingat.</p>
    </header>

    <form @submit.prevent="updatePassword" class="mt-4 space-y-4">
        <div>
            <InputLabel for="current_password" value="Password Saat Ini" />
            <TextInput
                id="current_password"
                ref="currentPasswordInput"
                v-model="form.current_password"
                type="password"
                autocomplete="current-password"
            />
            <InputError :message="form.errors.current_password" class="mt-1" />
        </div>

        <div>
            <InputLabel for="password" value="Password Baru" />
            <TextInput
                id="password"
                ref="passwordInput"
                v-model="form.password"
                type="password"
                autocomplete="new-password"
            />
            <InputError :message="form.errors.password" class="mt-1" />
        </div>

        <div>
            <InputLabel for="password_confirmation" value="Konfirmasi Password Baru" />
            <TextInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                autocomplete="new-password"
            />
            <InputError :message="form.errors.password_confirmation" class="mt-1" />
        </div>

        <div class="flex items-center gap-4">
            <PrimaryButton :disabled="form.processing">Simpan Password</PrimaryButton>
            <p v-if="form.recentlySuccessful" class="text-sm text-emerald-600">Tersimpan.</p>
        </div>
    </form>
</template>