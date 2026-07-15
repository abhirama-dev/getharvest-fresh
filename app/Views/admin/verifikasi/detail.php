<?php
/**
 * @var array $pedagang
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-2xl" x-data="{ showTolak: false }">
    <div class="card">
        <img src="<?= base_url('assets/uploads/toko/' . $pedagang['foto_toko']) ?>"
             class="h-56 w-full rounded-lg object-cover border border-gray-100 mb-5" alt="">

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Nama Lengkap</dt>
                <dd class="font-medium text-gray-800"><?= esc($pedagang['nama_lengkap']) ?></dd>
            </div>
            <div>
                <dt class="text-gray-500">Email</dt>
                <dd class="font-medium text-gray-800"><?= esc($pedagang['email']) ?></dd>
            </div>
            <div>
                <dt class="text-gray-500">No. HP</dt>
                <dd class="font-medium text-gray-800"><?= esc($pedagang['no_hp']) ?></dd>
            </div>
            <div>
                <dt class="text-gray-500">Koordinat Toko</dt>
                <dd class="font-medium text-gray-800">
                    <a href="https://maps.google.com/?q=<?= esc($pedagang['koordinat']) ?>" target="_blank" class="text-primary-700 hover:underline">
                        <?= esc($pedagang['koordinat']) ?>
                    </a>
                </dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-gray-500">Alamat Toko</dt>
                <dd class="font-medium text-gray-800"><?= esc($pedagang['alamat_toko']) ?></dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-gray-500">Alamat Domisili</dt>
                <dd class="font-medium text-gray-800"><?= esc($pedagang['alamat']) ?></dd>
            </div>
        </dl>

        <div class="mt-6 flex justify-end gap-3">
            <button @click="showTolak = true" class="btn-secondary text-red-600">Tolak</button>
            <?= form_open('admin/verifikasi/setujui/' . $pedagang['id_user']) ?>
                <button type="submit" class="btn-primary">Setujui Pedagang</button>
            <?= form_close() ?>
        </div>
    </div>

    <div x-show="showTolak" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4">
        <div @click.outside="showTolak = false" class="w-full max-w-sm rounded-xl bg-white p-5 shadow-2xl">
            <p class="text-sm font-semibold text-gray-800 mb-3">Tolak Pendaftaran</p>
            <?= form_open('admin/verifikasi/tolak/' . $pedagang['id_user']) ?>
                <textarea name="alasan_tolak" required rows="3" class="input-field" placeholder="Alasan penolakan..."></textarea>
                <div class="mt-3 flex justify-end gap-2">
                    <button type="button" @click="showTolak = false" class="btn-secondary text-xs px-3 py-1.5">Batal</button>
                    <button type="submit" class="btn-danger text-xs px-3 py-1.5">Tolak Pendaftaran</button>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>