<?php
/**
 * @var array $pedagang
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php if (empty($pedagang)): ?>
    <div class="card flex flex-col items-center justify-center py-16 text-center">
        <?= heroicon('check-circle', 'w-12 h-12 text-gray-300') ?>
        <p class="mt-3 text-sm font-medium text-gray-500">Tidak ada pendaftaran pedagang yang menunggu</p>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($pedagang as $p): ?>
            <div class="card">
                <img src="<?= base_url('assets/uploads/toko/' . $p['foto_toko']) ?>"
                     class="h-32 w-full rounded-lg object-cover border border-gray-100 mb-3" alt="">
                <p class="font-semibold text-gray-800"><?= esc($p['nama_lengkap']) ?></p>
                <p class="text-sm text-gray-500"><?= esc($p['alamat_toko']) ?></p>
                <p class="text-xs text-gray-400 mt-1">Daftar <?= time_ago($p['created_at']) ?></p>
                <a href="<?= base_url('admin/verifikasi/detail/' . $p['id_user']) ?>" class="btn-primary w-full mt-4 text-sm">
                    Tinjau Detail
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>