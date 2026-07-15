<?php
/**
 * @var string $title
 * @var int $totalBelanja
 * @var int $produkTersedia
 * @var int $negoAktif
 * @var int $pesananAktif
 * @var array $riwayatTerbaru
 * @var array $negoTerbaru
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Halo, <?= esc(logged_user()['nama_lengkap']) ?> 👋</h1>
    <p class="text-gray-500 mt-1">Berikut ringkasan aktivitas belanja Anda di GetHarvest.</p>
</div>

<!-- Kartu Statistik -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="card p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
            <?= heroicon('credit-card', 'w-6 h-6') ?>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Belanja</p>
            <p class="text-xl font-bold text-gray-800"><?= format_rupiah($totalBelanja) ?></p>
        </div>
    </div>

    <div class="card p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-gold/10 flex items-center justify-center text-gold">
            <?= heroicon('box', 'w-6 h-6') ?>
        </div>
        <div>
            <p class="text-sm text-gray-500">Produk Tersedia</p>
            <p class="text-xl font-bold text-gray-800"><?= number_format($produkTersedia) ?></p>
        </div>
    </div>

    <div class="card p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
            <?= heroicon('chat', 'w-6 h-6') ?>
        </div>
        <div>
            <p class="text-sm text-gray-500">Nego Aktif</p>
            <p class="text-xl font-bold text-gray-800"><?= number_format($negoAktif) ?></p>
        </div>
    </div>

    <div class="card p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500">
            <?= heroicon('clipboard', 'w-6 h-6') ?>
        </div>
        <div>
            <p class="text-sm text-gray-500">Pesanan Berjalan</p>
            <p class="text-xl font-bold text-gray-800"><?= number_format($pesananAktif) ?></p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <a href="<?= site_url('pedagang/katalog') ?>" class="card p-5 hover:shadow-card-hover transition flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-primary text-white flex items-center justify-center">
            <?= heroicon('shopping-bag', 'w-5 h-5') ?>
        </div>
        <div>
            <p class="font-semibold text-gray-800">Jelajahi Katalog</p>
            <p class="text-sm text-gray-500">Cari produk segar dari petani</p>
        </div>
    </a>

    <a href="<?= site_url('pedagang/pesanan') ?>" class="card p-5 hover:shadow-card-hover transition flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-gold text-white flex items-center justify-center">
            <?= heroicon('clipboard', 'w-5 h-5') ?>
        </div>
        <div>
            <p class="font-semibold text-gray-800">Riwayat Belanja</p>
            <p class="text-sm text-gray-500">Lacak status pesanan Anda</p>
        </div>
    </a>

    <a href="<?= site_url('pedagang/nego') ?>" class="card p-5 hover:shadow-card-hover transition flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-blue-600 text-white flex items-center justify-center">
            <?= heroicon('chat', 'w-5 h-5') ?>
        </div>
        <div>
            <p class="font-semibold text-gray-800">Negosiasi Saya</p>
            <p class="text-sm text-gray-500">Pantau tawaran harga</p>
        </div>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Aktivitas Pesanan Terbaru -->
    <div class="lg:col-span-2 card p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-800">Aktivitas Pesanan Terbaru</h2>
            <a href="<?= site_url('pedagang/pesanan') ?>" class="text-sm text-primary hover:underline">Lihat semua</a>
        </div>

        <?php if (empty($riwayatTerbaru)): ?>
            <div class="text-center py-10 text-gray-400">
                <?= heroicon('clipboard', 'w-10 h-10 mx-auto mb-2') ?>
                <p>Belum ada pesanan. Yuk mulai belanja di katalog!</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2 pr-3">Produk</th>
                            <th class="py-2 pr-3">Petani</th>
                            <th class="py-2 pr-3">Jumlah</th>
                            <th class="py-2 pr-3">Total</th>
                            <th class="py-2 pr-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($riwayatTerbaru as $row): ?>
                            <tr class="border-b last:border-0">
                                <td class="py-3 pr-3 flex items-center gap-2">
                                    <img src="<?= base_url('assets/uploads/produk/' . $row['gambar_produk']) ?>"
                                         class="w-9 h-9 rounded-lg object-cover" alt="">
                                    <span class="font-medium text-gray-800"><?= esc($row['nama_produk']) ?></span>
                                </td>
                                <td class="py-3 pr-3 text-gray-600"><?= esc($row['nama_petani']) ?></td>
                                <td class="py-3 pr-3 text-gray-600"><?= number_format($row['jumlah_kg']) ?> kg</td>
                                <td class="py-3 pr-3 font-medium text-gray-800"><?= format_rupiah($row['total_harga']) ?></td>
                                <td class="py-3 pr-3">
                                    <span class="badge <?= badge_status($row['status_pengiriman']) ?>"><?= esc($row['status_pengiriman']) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Nego Berjalan -->
    <div class="card p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-800">Nego Berjalan</h2>
            <a href="<?= site_url('pedagang/nego') ?>" class="text-sm text-primary hover:underline">Semua</a>
        </div>

        <?php if (empty($negoTerbaru)): ?>
            <div class="text-center py-10 text-gray-400">
                <?= heroicon('chat', 'w-10 h-10 mx-auto mb-2') ?>
                <p class="text-sm">Belum ada negosiasi aktif.</p>
            </div>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($negoTerbaru as $n): ?>
                    <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:bg-gray-50">
                        <img src="<?= base_url('assets/uploads/produk/' . $n['gambar_produk']) ?>"
                             class="w-10 h-10 rounded-lg object-cover" alt="">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate"><?= esc($n['nama_produk']) ?></p>
                            <p class="text-xs text-gray-500">Tawaran: <?= format_rupiah($n['harga_tawaran']) ?>/kg</p>
                        </div>
                        <span class="badge <?= badge_status($n['status_nego']) ?> text-xs"><?= esc($n['status_nego']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>