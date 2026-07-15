<?php
/**
 * @var array $user
 * @var array $statistik
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-2xl">
    <div class="card">
        <div class="flex items-center gap-3 mb-5">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary-100 text-primary-700 font-bold text-xl uppercase">
                <?= esc(mb_substr($user['nama_lengkap'], 0, 1)) ?>
            </div>
            <div>
                <p class="font-semibold text-gray-800 text-lg"><?= esc($user['nama_lengkap']) ?></p>
                <p class="text-sm text-gray-500 capitalize"><?= esc($user['role']) ?> &middot; <?= esc($user['email']) ?></p>
            </div>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-5">
            <div><dt class="text-gray-500">No. HP</dt><dd class="font-medium text-gray-800"><?= esc($user['no_hp']) ?></dd></div>
            <div><dt class="text-gray-500">Bergabung</dt><dd class="font-medium text-gray-800"><?= date('d M Y', strtotime($user['created_at'])) ?></dd></div>
            <div class="sm:col-span-2"><dt class="text-gray-500">Alamat</dt><dd class="font-medium text-gray-800"><?= esc($user['alamat']) ?></dd></div>
        </dl>

        <div class="border-t border-gray-100 pt-4 grid grid-cols-2 sm:grid-cols-3 gap-4">
            <?php if ($user['role'] === 'petani'): ?>
                <div><p class="text-xs text-gray-500">Produk</p><p class="font-semibold text-gray-800"><?= $statistik['jumlah_produk'] ?></p></div>
                <div><p class="text-xs text-gray-500">Pendapatan</p><p class="font-semibold text-gray-800"><?= format_rupiah($statistik['total_pendapatan']) ?></p></div>
                <div><p class="text-xs text-gray-500">Rating</p><p class="font-semibold text-gray-800"><?= $statistik['rata_rating'] ?: '-' ?> ★</p></div>
            <?php else: ?>
                <div><p class="text-xs text-gray-500">Total Belanja</p><p class="font-semibold text-gray-800"><?= format_rupiah($statistik['total_belanja']) ?></p></div>
                <div><p class="text-xs text-gray-500">Jumlah Transaksi</p><p class="font-semibold text-gray-800"><?= $statistik['jumlah_transaksi'] ?></p></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>