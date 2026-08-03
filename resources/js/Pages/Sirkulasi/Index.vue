<script setup>
import { ref, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { UserSearch, ScanLine, ArrowUpFromLine, ArrowDownToLine, AlertTriangle } from 'lucide-vue-next';

defineProps({ activeBorrowings: Array });
const page = usePage();

// ---- Check-out ----
const siswaQuery = ref('');
const siswaResults = ref([]);
const selectedSiswa = ref(null);

const copyQuery = ref('');
const copyResults = ref([]);
const selectedCopy = ref(null);

const checkoutForm = useForm({ user_id: null, book_copy_id: null });

watch(siswaQuery, async (val) => {
    if (!val) { siswaResults.value = []; return; }
    const { data } = await axios.get(route('sirkulasi.search-siswa'), { params: { q: val } });
    siswaResults.value = data;
});

watch(copyQuery, async (val) => {
    if (!val) { copyResults.value = []; return; }
    const { data } = await axios.get(route('sirkulasi.search-copy'), { params: { q: val } });
    copyResults.value = data;
});

function pickSiswa(siswa) {
    selectedSiswa.value = siswa;
    checkoutForm.user_id = siswa.id;
    siswaQuery.value = siswa.name;
    siswaResults.value = [];
}

function pickCopy(copy) {
    selectedCopy.value = copy;
    checkoutForm.book_copy_id = copy.id;
    copyQuery.value = `${copy.book.title} (${copy.copy_code})`;
    copyResults.value = [];
}

function submitCheckout() {
    checkoutForm.post(route('sirkulasi.checkout'), {
        preserveScroll: true,
        onSuccess: () => {
            checkoutForm.reset();
            selectedSiswa.value = null;
            selectedCopy.value = null;
            siswaQuery.value = '';
            copyQuery.value = '';
        },
    });
}

// ---- Check-in ----
const checkinForm = useForm({ copy_code: '' });

function submitCheckin() {
    checkinForm.post(route('sirkulasi.checkin'), {
        preserveScroll: true,
        onSuccess: () => checkinForm.reset(),
    });
}

// ---- Lapor Kondisi ----
const reportCopyQuery = ref('');
const reportCopyResults = ref([]);
const reportForm = useForm({ book_copy_id: null, condition: 'rusak', note: '' });

watch(reportCopyQuery, async (val) => {
    if (!val) { reportCopyResults.value = []; return; }
    const { data } = await axios.get(route('sirkulasi.search-copy'), { params: { q: val } });
    reportCopyResults.value = data;
});

function pickReportCopy(copy) {
    reportForm.book_copy_id = copy.id;
    reportCopyQuery.value = `${copy.book.title} (${copy.copy_code})`;
    reportCopyResults.value = [];
}

function submitReport() {
    reportForm.post(route('condition-reports.store'), {
        preserveScroll: true,
        onSuccess: () => {
            reportForm.reset();
            reportCopyQuery.value = '';
        },
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-semibold text-slate-800">Sirkulasi</h2>
        </template>

        <div v-if="page.props.flash?.success" class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ page.props.flash.success }}
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <!-- Check-out -->
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="mb-4 flex items-center gap-2 text-kosgoro-navy">
                    <ArrowUpFromLine class="h-5 w-5" />
                    <h3 class="font-display text-base font-semibold">Book Check-out</h3>
                </div>

                <label class="mb-1 block text-xs font-medium text-slate-500">Nama / Email Siswa</label>
                <div class="relative mb-3">
                    <div class="relative">
                        <UserSearch class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <input v-model="siswaQuery" type="text" placeholder="Cari siswa..."
                            class="w-full rounded-lg border-slate-300 py-2 pl-10 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
                    </div>
                    <div v-if="siswaResults.length" class="absolute z-10 mt-1 w-full rounded-lg border border-slate-200 bg-white shadow-lg">
                        <button v-for="s in siswaResults" :key="s.id" @click="pickSiswa(s)"
                            class="block w-full px-4 py-2 text-left text-sm hover:bg-slate-50">
                            {{ s.name }} <span class="text-xs text-slate-400">{{ s.email }}</span>
                        </button>
                    </div>
                </div>
                <p v-if="checkoutForm.errors.user_id" class="mb-2 text-xs text-red-600">{{ checkoutForm.errors.user_id }}</p>

                <label class="mb-1 block text-xs font-medium text-slate-500">Judul / Kode Eksemplar</label>
                <div class="relative mb-4">
                    <div class="relative">
                        <ScanLine class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <input v-model="copyQuery" type="text" placeholder="Cari buku / scan kode..."
                            class="w-full rounded-lg border-slate-300 py-2 pl-10 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
                    </div>
                    <div v-if="copyResults.length" class="absolute z-10 mt-1 w-full rounded-lg border border-slate-200 bg-white shadow-lg">
                        <button v-for="c in copyResults" :key="c.id" @click="pickCopy(c)"
                            class="block w-full px-4 py-2 text-left text-sm hover:bg-slate-50">
                            {{ c.book.title }} <span class="text-xs text-slate-400">{{ c.copy_code }}</span>
                        </button>
                    </div>
                </div>
                <p v-if="checkoutForm.errors.book_copy_id" class="mb-2 text-xs text-red-600">{{ checkoutForm.errors.book_copy_id }}</p>

                <button @click="submitCheckout" :disabled="checkoutForm.processing || !checkoutForm.user_id || !checkoutForm.book_copy_id"
                    class="w-full rounded-lg bg-kosgoro-navy py-2.5 text-sm font-medium text-white transition hover:bg-kosgoro-navy-dark disabled:opacity-40">
                    Catat Peminjaman
                </button>
            </div>

            <!-- Check-in -->
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="mb-4 flex items-center gap-2 text-kosgoro-gold">
                    <ArrowDownToLine class="h-5 w-5" />
                    <h3 class="font-display text-base font-semibold">Book Check-in</h3>
                </div>

                <label class="mb-1 block text-xs font-medium text-slate-500">Kode Eksemplar</label>
                <input v-model="checkinForm.copy_code" type="text" placeholder="Scan atau ketik kode..."
                    class="mb-2 w-full rounded-lg border-slate-300 py-2 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
                <p v-if="checkinForm.errors.copy_code" class="mb-2 text-xs text-red-600">{{ checkinForm.errors.copy_code }}</p>

                <button @click="submitCheckin" :disabled="checkinForm.processing || !checkinForm.copy_code"
                    class="w-full rounded-lg bg-kosgoro-gold py-2.5 text-sm font-medium text-white transition hover:opacity-90 disabled:opacity-40">
                    Catat Pengembalian
                </button>
            </div>
        </div>

        <!-- Lapor Kondisi -->
        <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-5">
            <div class="mb-4 flex items-center gap-2 text-amber-700">
                <AlertTriangle class="h-5 w-5" />
                <h3 class="font-display text-base font-semibold">Lapor Buku Rusak / Hilang</h3>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="relative sm:col-span-2">
                    <input v-model="reportCopyQuery" type="text" placeholder="Cari judul / kode eksemplar..."
                        class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy" />
                    <div v-if="reportCopyResults.length" class="absolute z-10 mt-1 w-full rounded-lg border border-slate-200 bg-white shadow-lg">
                        <button v-for="c in reportCopyResults" :key="c.id" @click="pickReportCopy(c)"
                            class="block w-full px-4 py-2 text-left text-sm hover:bg-slate-50">
                            {{ c.book.title }} <span class="text-xs text-slate-400">{{ c.copy_code }}</span>
                        </button>
                    </div>
                </div>
                <select v-model="reportForm.condition" class="rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy">
                    <option value="rusak">Rusak</option>
                    <option value="hilang">Hilang</option>
                </select>
            </div>
            <textarea v-model="reportForm.note" rows="2" placeholder="Catatan (opsional)..."
                class="mt-3 w-full rounded-lg border-slate-300 text-sm focus:border-kosgoro-navy focus:ring-kosgoro-navy"></textarea>
            <p v-if="reportForm.errors.book_copy_id" class="mt-1 text-xs text-red-600">{{ reportForm.errors.book_copy_id }}</p>

            <button @click="submitReport" :disabled="reportForm.processing || !reportForm.book_copy_id"
                class="mt-3 rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700 disabled:opacity-40">
                Kirim Laporan
            </button>
        </div>

        <!-- Daftar peminjaman aktif -->
        <div class="mt-6 rounded-xl border border-slate-200 bg-white">
    <div class="border-b border-slate-100 px-5 py-4">
        <h3 class="font-display text-base font-semibold text-slate-800">Peminjaman Aktif</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 text-left text-xs uppercase text-slate-400">
                        <th class="px-5 py-2">Siswa</th>
                        <th class="px-5 py-2">Buku</th>
                        <th class="px-5 py-2">Jatuh Tempo</th>
                        <th class="px-5 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="b in activeBorrowings" :key="b.id" class="border-b border-slate-50">
                        <td class="px-5 py-3">{{ b.user.name }}</td>
                        <td class="px-5 py-3">{{ b.book_copy.book.title }} <span class="text-xs text-slate-400">({{ b.book_copy.copy_code }})</span></td>
                        <td class="px-5 py-3">{{ b.due_date }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs text-amber-700">{{ b.status }}</span>
                        </td>
                    </tr>
                    <tr v-if="!activeBorrowings.length">
                        <td colspan="4" class="px-5 py-6 text-center text-slate-400">Belum ada peminjaman aktif</td>
                    </tr>
                </tbody>
           </table>
    </div>
</div>
    </AuthenticatedLayout>
</template>