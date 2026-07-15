<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div x-data="{
        role: '<?= old('role', 'petani') ?>',
        namaLengkap: '<?= old('nama_lengkap') ?>',
        email: '<?= old('email') ?>',
        password: '',
        confirmPassword: '',
        noHp: '<?= old('no_hp') ?>',
        koordinat: '<?= old('koordinat') ?>',
        fotoPreview: null,
        gettingLocation: false,
        submitting: false,

        get passwordMatch() { return this.password.length > 0 && this.password === this.confirmPassword; },
        get formValidBase() {
            return this.namaLengkap.length >= 3 &&
                   /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email) &&
                   this.password.length >= 6 &&
                   this.passwordMatch &&
                   this.noHp.length >= 9;
        },

        previewFoto(event) {
            const file = event.target.files[0];
            if (file) {
                this.fotoPreview = URL.createObjectURL(file);
            }
        },

        ambilLokasi() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung geolokasi. Silakan isi koordinat secara manual.');
                return;
            }
            this.gettingLocation = true;
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.koordinat = pos.coords.latitude.toFixed(6) + ',' + pos.coords.longitude.toFixed(6);
                    this.gettingLocation = false;
                },
                () => {
                    alert('Gagal mengambil lokasi. Pastikan izin lokasi diaktifkan.');
                    this.gettingLocation = false;
                }
            );
        }
     }">
    <h2 class="text-xl font-bold text-gray-800 mb-1">Buat Akun Baru</h2>
    <p class="text-sm text-gray-500 mb-5">Bergabunglah sebagai Petani atau Pedagang.</p>

    <!-- Tab Role -->
    <div class="mb-6 grid grid-cols-2 gap-2 rounded-lg bg-gray-100 p-1">
        <button type="button" @click="role = 'petani'"
                :class="role === 'petani' ? 'bg-white shadow text-primary-700' : 'text-gray-500'"
                class="rounded-md py-2 text-sm font-semibold transition">Saya Petani</button>
        <button type="button" @click="role = 'pedagang'"
                :class="role === 'pedagang' ? 'bg-white shadow text-primary-700' : 'text-gray-500'"
                class="rounded-md py-2 text-sm font-semibold transition">Saya Pedagang</button>
    </div>

    <?= form_open_multipart('register') ?>
        <input type="hidden" name="role" x-model="role">

        <div class="space-y-4">
            <div>
                <label class="label-field">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" x-model="namaLengkap" required class="input-field" placeholder="Nama sesuai KTP">
            </div>

            <div>
                <label class="label-field">Alamat</label>
                <textarea name="alamat" required rows="2" class="input-field" placeholder="Alamat domisili lengkap"><?= old('alamat') ?></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label-field">Email</label>
                    <input type="email" name="email" x-model="email" required class="input-field" placeholder="nama@email.com">
                </div>
                <div>
                    <label class="label-field">No. HP / WhatsApp</label>
                    <input type="text" name="no_hp" x-model="noHp" required class="input-field" placeholder="08xxxxxxxxxx">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label-field">Password</label>
                    <input type="password" name="password" x-model="password" required class="input-field" placeholder="Minimal 6 karakter">
                </div>
                <div>
                    <label class="label-field">Konfirmasi Password</label>
                    <input type="password" name="confirm_password" x-model="confirmPassword" required class="input-field" placeholder="Ulangi password">
                    <p x-show="confirmPassword.length > 0 && !passwordMatch" x-cloak class="mt-1 text-xs text-red-600">
                        Password tidak sama.
                    </p>
                </div>
            </div>

            <!-- Field khusus Pedagang -->
            <template x-if="role === 'pedagang'">
                <div class="space-y-4 rounded-lg border border-gold-200 bg-gold-50/50 p-4">
                    <p class="flex items-center gap-2 text-sm font-semibold text-gold-600">
                        <?= heroicon('shield-check', 'w-4 h-4') ?> Verifikasi khusus akun Pedagang
                    </p>

                    <div>
                        <label class="label-field">Alamat Toko / Gudang</label>
                        <input type="text" name="alamat_toko" required class="input-field" placeholder="Alamat toko/tempat usaha">
                    </div>

                    <div>
                        <label class="label-field">Koordinat Lokasi</label>
                        <div class="flex gap-2">
                            <input type="text" name="koordinat" x-model="koordinat" required
                                   class="input-field" placeholder="-6.200000,106.816666" readonly>
                            <button type="button" @click="ambilLokasi()" class="btn-secondary whitespace-nowrap">
                                <span x-show="!gettingLocation">Ambil Lokasi</span>
                                <span x-show="gettingLocation" x-cloak>Mencari...</span>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Klik "Ambil Lokasi" saat berada di lokasi toko Anda.</p>
                    </div>

                    <div>
                        <label class="label-field">Foto Toko</label>
                        <input type="file" name="foto_toko" accept="image/*" @change="previewFoto($event)"
                               class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-700 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-800">
                        <template x-if="fotoPreview">
                            <img :src="fotoPreview" class="mt-3 h-32 w-full rounded-lg object-cover border border-gray-200">
                        </template>
                    </div>

                    <p class="text-xs text-gray-500">
                        Akun pedagang wajib diverifikasi oleh Admin sebelum dapat digunakan untuk login.
                    </p>
                </div>
            </template>

            <button type="submit" class="btn-primary w-full"
                    :disabled="!formValidBase || (role === 'pedagang' && !koordinat) || submitting">
                <span x-show="!submitting">Daftar Sekarang</span>
                <span x-show="submitting" x-cloak>Memproses...</span>
            </button>
        </div>
    <?= form_close() ?>

    <p class="mt-6 text-center text-sm text-gray-500">
        Sudah punya akun?
        <a href="<?= base_url('login') ?>" class="font-semibold text-primary-700 hover:underline">Masuk di sini</a>
    </p>
</div>
<?= $this->endSection() ?>