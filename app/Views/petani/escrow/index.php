<?php
/**
 * @var array $escrow
 * @var int $totalDitahan
 * @var int $totalDilepas
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="card">
        <p class="text-sm text-gray-500">Dana Tertahan</p>
        <p class="mt-1 text-2xl font-bold text-amber-600"><?= format_rupiah($totalDitahan) ?></p>
        <p class="text-xs text-gray-400 mt-1">Menunggu konfirmasi terima dari pedagang</p>
    </div>
    <div class="card">
        <p class="text-sm text-gray-500">Dana Sudah Dilepas</p>
        <p class="mt-1 text-2xl font-bold text-primary-700"><?= format_rupiah($totalDilepas) ?></p>
        <p class="text-xs text-gray-400 mt-1">Total yang sudah masuk ke Anda</p>
    </div>
</div>

<?php if (empty($escrow)): ?>
    <div class="card flex flex-col items-center justify-center py-16 text-center">
        <?= heroicon('banknotes', 'w-12 h-12 text-gray-300') ?>
        <p class="mt-3 text-sm font-medium text-gray-500">Belum ada dana escrow tercatat</p>
    </div>
<?php else: ?>
    <div class="card overflow-x-auto p-0">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50 text-left text-gray-500">
                    <th class="px-5 py-3 font-medium">Produk</th>
                    <th class="px-5 py-3 font-medium">Pedagang</th>
                    <th class="px-5 py-3 font-medium">Jumlah</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium">Ditahan Sejak</th>
                    <th class="px-5 py-3 font-medium">Dilepas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($escrow as $e): ?>
                    <tr>
                        <td class="px-5 py-3 flex items-center gap-2">
                            <img src="<?= base_url('assets/uploads/produk/' . $e['gambar_produk']) ?>"
                                 class="h-9 w-9 rounded-lg object-cover border border-gray-100" alt="">
                            <span class="font-medium text-gray-700"><?= esc($e['nama_produk']) ?></span>
                        </td>
                        <td class="px-5 py-3 text-gray-600"><?= esc($e['nama_pedagang']) ?></td>
                        <td class="px-5 py-3 font-medium text-gray-800"><?= format_rupiah($e['jumlah_escrow']) ?></td>
                        <td class="px-5 py-3"><span class="<?= badge_status($e['status']) ?>"><?= esc($e['status']) ?></span></td>
                        <td class="px-5 py-3 text-gray-500"><?= time_ago($e['tanggal_ditahan']) ?></td>
                        <td class="px-5 py-3 text-gray-500"><?= $e['tanggal_dilepas'] ? date('d M Y', strtotime($e['tanggal_dilepas'])) : '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>