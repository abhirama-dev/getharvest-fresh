<?php
/**
 * @var int    $totalPendapatan
 * @var int    $totalModal
 * @var int    $jumlahProduk
 * @var int    $totalStok
 * @var array  $pesananTerbaru
 * @var int    $jumlahNegoAktif
 * @var int    $pesananMenunggu
 */
?>

<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Statistik -->
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="card">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">Total Pendapatan</p>
            <span class="rounded-lg bg-primary-50 p-2 text-primary-700"><?= heroicon('banknotes', 'w-5 h-5') ?></span>
        </div>
        <p class="mt-2 text-2xl font-bold text-gray-800"><?= format_rupiah($totalPendapatan) ?></p>
        <p class="mt-1 text-xs text-gray-400">Dari pesanan berstatus Selesai</p>
    </div>

    <div class="card">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">Total Modal</p>
            <span class="rounded-lg bg-red-50 p-2 text-red-600"><?= heroicon('chart-bar', 'w-5 h-5') ?></span>
        </div>
        <p class="mt-2 text-2xl font-bold text-gray-800"><?= format_rupiah($totalModal) ?></p>
        <p class="mt-1 text-xs text-gray-400">Total pengeluaran tercatat</p>
    </div>

    <div class="card">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">Produk Aktif</p>
            <span class="rounded-lg bg-blue-50 p-2 text-blue-600"><?= heroicon('box', 'w-5 h-5') ?></span>
        </div>
        <p class="mt-2 text-2xl font-bold text-gray-800"><?= $jumlahProduk ?></p>
        <p class="mt-1 text-xs text-gray-400"><?= number_format($totalStok) ?> kg total stok</p>
    </div>

    <div class="card">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">Nego Aktif</p>
            <span class="rounded-lg bg-gold-100 p-2 text-gold-600"><?= heroicon('chat', 'w-5 h-5') ?></span>
        </div>
        <p class="mt-2 text-2xl font-bold text-gray-800"><?= $jumlahNegoAktif ?></p>
        <p class="mt-1 text-xs text-gray-400"><?= $pesananMenunggu ?> pesanan menunggu diproses</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <a href="<?= base_url('petani/produk/tambah') ?>" class="card flex items-center gap-3 hover:shadow-card-hover transition">
        <span class="rounded-lg bg-primary-700 p-2.5 text-white"><?= heroicon('plus', 'w-5 h-5') ?></span>
        <div>
            <p class="text-sm font-semibold text-gray-800">Tambah Produk</p>
            <p class="text-xs text-gray-500">Upload hasil panen baru</p>
        </div>
    </a>
    <a href="<?= base_url('petani/pesanan') ?>" class="card flex items-center gap-3 hover:shadow-card-hover transition">
        <span class="rounded-lg bg-blue-600 p-2.5 text-white"><?= heroicon('clipboard', 'w-5 h-5') ?></span>
        <div>
            <p class="text-sm font-semibold text-gray-800">Pesanan Masuk</p>
            <p class="text-xs text-gray-500">Kelola pesanan dari pedagang</p>
        </div>
    </a>
    <a href="<?= base_url('petani/nego') ?>" class="card flex items-center gap-3 hover:shadow-card-hover transition">
        <span class="rounded-lg bg-gold-500 p-2.5 text-white"><?= heroicon('chat', 'w-5 h-5') ?></span>
        <div>
            <p class="text-sm font-semibold text-gray-800">Negosiasi</p>
            <p class="text-xs text-gray-500">Tanggapi tawaran pedagang</p>
        </div>
    </a>
</div>

<!-- Aktivitas Terbaru -->
<div class="card mt-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-base font-semibold text-gray-800">Pesanan Terbaru</h3>
        <a href="<?= base_url('petani/pesanan') ?>" class="text-sm font-medium text-primary-700 hover:underline">Lihat semua</a>
    </div>

    <?php if (empty($pesananTerbaru)): ?>
        <div class="flex flex-col items-center justify-center py-10 text-center">
            <?= heroicon('clipboard', 'w-10 h-10 text-gray-300') ?>
            <p class="mt-2 text-sm text-gray-400">Belum ada pesanan masuk.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-left text-gray-500">
                        <th class="pb-2 font-medium">Produk</th>
                        <th class="pb-2 font-medium">Pedagang</th>
                        <th class="pb-2 font-medium">Jumlah</th>
                        <th class="pb-2 font-medium">Total</th>
                        <th class="pb-2 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($pesananTerbaru as $p): ?>
                        <tr>
                            <td class="py-3 flex items-center gap-2">
                                <img src="<?= base_url('assets/uploads/produk/' . $p['gambar_produk']) ?>"
                                     class="h-9 w-9 rounded-lg object-cover border border-gray-100" alt="">
                                <span class="font-medium text-gray-700"><?= esc($p['nama_produk']) ?></span>
                            </td>
                            <td class="py-3 text-gray-600"><?= esc($p['nama_pedagang']) ?></td>
                            <td class="py-3 text-gray-600"><?= $p['jumlah_kg'] ?> kg</td>
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