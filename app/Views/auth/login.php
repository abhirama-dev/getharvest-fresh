<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div x-data="{
        email: '<?= old('email') ?>',
        password: '',
        showPassword: false,
        submitting: false,
        get emailValid() { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email); },
        get formValid() { return this.emailValid && this.password.length >= 6; }
     }">
    <h2 class="text-xl font-bold text-gray-800 mb-1">Masuk ke Akun Anda</h2>
    <p class="text-sm text-gray-500 mb-6">Kelola produk, pesanan, dan negosiasi dari satu tempat.</p>

    <?= form_open('login', ['@submit' => 'submitting = true']) ?>
        <div class="space-y-4">
            <div>
                <label class="label-field">Email</label>
                <input type="email" name="email" x-model="email" required
                       class="input-field" placeholder="nama@email.com" autocomplete="email">
                <p x-show="email.length > 0 && !emailValid" x-cloak class="mt-1 text-xs text-red-600">
                    Format email tidak valid.
                </p>
            </div>

            <div>
                <label class="label-field">Password</label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password" required
                           class="input-field pr-10" placeholder="Minimal 6 karakter" autocomplete="current-password">
                    <button type="button" @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                        <span x-show="!showPassword"><?= heroicon('eye', 'w-5 h-5') ?></span>
                        <span x-show="showPassword" x-cloak><?= heroicon('eye-slash', 'w-5 h-5') ?></span>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-primary w-full" :disabled="!formValid || submitting">
                <span x-show="!submitting">Masuk</span>
                <span x-show="submitting" x-cloak>Memproses...</span>
            </button>
        </div>
    <?= form_close() ?>

    <p class="mt-6 text-center text-sm text-gray-500">
        Belum punya akun?
        <a href="<?= base_url('register') ?>" class="font-semibold text-primary-700 hover:underline">Daftar sekarang</a>
    </p>
</div>
<?= $this->endSection() ?>