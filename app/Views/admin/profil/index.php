<?php
/**
 * @var array $user
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-2xl space-y-6">
    <div class="card">
        <div class="flex items-center gap-4 mb-5">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary-100 text-primary-700 font-bold text-2xl uppercase">
                <?= esc(mb_substr($user['nama_lengkap'], 0, 1)) ?>
            </div>
            <div>
                <p class="font-semibold text-gray-800 text-lg"><?= esc($user['nama_lengkap']) ?></p>
                <p class="text-sm text-gray-500"><?= esc($user['email']) ?> &middot; <span class="capitalize">Administrator</span></p>
            </div>
        </div>

        <?= form_open('admin/profil/update') ?>
            <div class="space-y-4">
                <div>
                    <label class="label-field">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="<?= esc($user['nama_lengkap']) ?>" required class="input-field">
                </div>
                <div>
                    <label class="label-field">Alamat</label>
                    <textarea name="alamat" required rows="2" class="input-field"><?= esc($user['alamat']) ?></textarea>
                </div>
                <div>
                    <label class="label-field">No. HP</label>
                    <input type="text" name="no_hp" value="<?= esc($user['no_hp']) ?>" required class="input-field">
                </div>
                <div>
                    <label class="label-field">Email</label>
                    <input type="email" value="<?= esc($user['email']) ?>" disabled class="input-field bg-gray-50 text-gray-400">
                    <p class="mt-1 text-xs text-gray-400">Email tidak dapat diubah.</p>
                </div>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        <?= form_close() ?>
    </div>

    <div class="card">
        <h3 class="text-sm font-semibold text-gray-800 mb-4">Ubah Password</h3>
        <?= form_open('admin/profil/password') ?>
            <div class="space-y-4">
                <input type="password" name="password_lama" required class="input-field" placeholder="Password lama">
                <input type="password" name="password_baru" required class="input-field" placeholder="Password baru (min. 6 karakter)">
                <input type="password" name="konfirmasi" required class="input-field" placeholder="Konfirmasi password baru">
                <button type="submit" class="btn-secondary">Ubah Password</button>
            </div>
        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>