<?php
/**
 * @var array $retur
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php if (empty($retur)): ?>
    <div class="card flex flex-col items-center justify-center py-16 text-center">
        <?= heroicon('arrow-path', 'w-12 h-12 text-gray-300') ?>
        <p class="mt-3 text-sm font-medium text-gray-500">Belum ada pengajuan retur</p>
    </div>
<?php else: ?>
    <div class="space-y-3">
        <?php foreach ($retur as $r): ?>
            <div class="card flex flex-col sm:flex-row sm:items-center gap-4">
                <img src="<?= base_url('assets/uploads/produk/' . $r['gambar_produk']) ?>"
                     class="h-14 w-14 rounded-lg object-cover border border-gray-100" alt="">
                <div class="flex-1">
                    <p class="font-semibold text-gray-800"><?= esc($r['nama_produk']) ?></p>
                    <p class="text-sm text-gray-500"><?= esc($r['alasan']) ?></p>
                    <p class="text-xs text-gray-400 mt-1">Diajukan <?= time_ago($r['tanggal_pengajuan']) ?></p>
                </div>
                <span class="<?= badge_status($r['status']) ?> capitalize"><?= esc($r['status']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>