<?php
/**
 * @var int $totalPetani
 * @var int $totalPedagang
 * @var int $jumlahPending
 * @var int $totalTransaksi
 * @var int $totalEscrowDitahan
 * @var int $jumlahRekeningPending
 * @var int $jumlahUpgradePending
 * @var array $pesananTerbaru
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="card">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">Total Petani</p>
            <span class="rounded-lg bg-primary-50 p-2 text-primary-700"><?= heroicon('users', 'w-5 h-5') ?></span>
        </div>
        <p class="mt-2 text-2xl font-bold text-gray-800"><?= number_format($totalPetani) ?></p>
    </div>
    <div class="card">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">Pedagang Aktif</p>
            <span class="rounded-lg bg-blue-50 p-2 text-blue-600"><?= heroicon('shopping-bag', 'w-5 h-5') ?></span>
        </div>
        <p class="mt-2 text-2xl font-bold text-gray-800"><?= number_format($totalPedagang) ?></p>
    </div>
    <div class="card">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">Total Transaksi Selesai</p>
            <span class="rounded-lg bg-gold-100 p-2 text-gold-600"><?= heroicon('chart-bar', 'w-5 h-5') ?></span>
        </div>
        <p class="mt-2 text-2xl font-bold text-gray-800"><?= format_rupiah($totalTransaksi) ?></p>
    </div>
    <div class="card">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">Dana Escrow Tertahan</p>
            <span class="rounded-lg bg-red-50 p-2 text-red-600"><?= heroicon('banknotes', 'w-5 h-5') ?></span>
        </div>
        <p class="mt-2 text-2xl font-bold text-gray-800"><?= format_rupiah($totalEscrowDitahan) ?></p>
    </div>
</div>

<!-- Perlu Tindakan -->
<div class="mt-6">
    <h3 class="text-base font-semibold text-gray-800 mb-3">Perlu Tindakan Anda</h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <a href="<?= base_url('admin/verifikasi') ?>" class="card flex items-center gap-3 hover:shadow-card-hover transition <?= $jumlahPending > 0 ? 'border-amber-300 bg-amber-50/40' : '' ?>">
            <span class="rounded-lg bg-amber-500 p-2.5 text-white"><?= heroicon('user-plus', 'w-5 h-5') ?></span>
            <div>
                <p class="text-sm font-semibold text-gray-800"><?= $jumlahPending ?> Verifikasi Pedagang</p>
                <p class="text-xs text-gray-500">Menunggu persetujuan Anda</p>
            </div>
        </a>
        <a href="<?= base_url('admin/rekening') ?>" class="card flex items-center gap-3 hover:shadow-card-hover transition <?= $jumlahRekeningPending > 0 ? 'border-amber-300 bg-amber-50/40' : '' ?>">
            <span class="rounded-lg bg-blue-600 p-2.5 text-white"><?= heroicon('credit-card', 'w-5 h-5') ?></span>
            <div>
                <p class="text-sm font-semibold text-gray-800"><?= $jumlahRekeningPending ?> Validasi Rekening</p>
                <p class="text-xs text-gray-500">Rekening menunggu verifikasi</p>
            </div>
        </a>
        <a href="<?= base_url('admin/upgrade') ?>" class="card flex items-center gap-3 hover:shadow-card-hover transition <?= $jumlahUpgradePending > 0 ? 'border-amber-300 bg-amber-50/40' : '' ?>">
            <span class="rounded-lg bg-gold-500 p-2.5 text-white"><?= heroicon('star', 'w-5 h-5') ?></span>
            <div>
                <p class="text-sm font-semibold text-gray-800"><?= $jumlahUpgradePending ?> Upgrade Premium</p>
                <p class="text-xs text-gray-500">Bukti bayar menunggu verifikasi</p>
            </div>
        </a>
    </div>
</div>

<!-- Transaksi Terbaru -->
<div class="card mt-6">
    <h3 class="text-base font-semibold text-gray-800 mb-4">Transaksi Terbaru</h3>
    <?php if (empty($pesananTerbaru)): ?>
        <p class="text-sm text-gray-400 text-center py-6">Belum ada transaksi.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-left text-gray-500">
                        <th class="pb-2 font-medium">Produk</th>
                        <th class="pb-2 font-medium">Pedagang</th>
                        <th class="pb-2 font-medium">Total</th>
                        <th class="pb-2 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($pesananTerbaru as $p): ?>
                        <tr>
                            <td class="py-3 text-gray-700"><?= esc($p['nama_produk']) ?></td>
                            <td class="py-3 text-gray-600"><?= esc($p['nama_pedagang']) ?></td>
                            <td class="py-3 font-medium text-gray-800"><?= format_rupiah($p['total_harga']) ?></td>
                            <td class="py-3"><span class="<?= badge_status($p['status_pengiriman']) ?>"><?= esc($p['status_pengiriman']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>