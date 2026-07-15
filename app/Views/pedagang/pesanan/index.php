<?php

/**
 * @var string $title
 * @var array $pesanan
 * @var string|null $status
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div x-data="{ konfirmasiId: null }">
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Riwayat Belanja</h1>
    <p class="text-gray-500 mb-6">Pantau status semua pesanan Anda.</p>

    <!-- Filter Status -->
    <div class="flex flex-wrap gap-2 mb-6">
        <?php
        $tabs = ['' => 'Semua', 'Menunggu' => 'Menunggu', 'Dibayar' => 'Dibayar', 'Dikemas' => 'Dikemas', 'Dikirim' => 'Dikirim', 'Selesai' => 'Selesai', 'Retur' => 'Retur'];
        ?>
        <?php foreach ($tabs as $val => $label): ?>
            <a href="<?= site_url('pedagang/pesanan') . ($val ? '?status=' . $val : '') ?>"
                class="px-4 py-1.5 rounded-full text-sm <?= $status === $val || (empty($status) && $val === '') ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($pesanan)): ?>
        <div class="card p-12 text-center text-gray-400">
            <?= heroicon('clipboard', 'w-12 h-12 mx-auto mb-3') ?>
            <p>Belum ada pesanan pada status ini.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($pesanan as $p): ?>
                <div class="card p-4 flex flex-col sm:flex-row sm:items-center gap-4">
                    <img src="<?= base_url('assets/uploads/produk/' . $p['gambar_produk']) ?>"
                        class="w-16 h-16 rounded-xl object-cover" alt="">

                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-800 truncate"><?= esc($p['nama_produk']) ?></p>
                        <p class="text-sm text-gray-500">dari <?= esc($p['nama_petani']) ?> &middot; <?= number_format($p['jumlah_kg']) ?> kg</p>
                        <p class="text-xs text-gray-400">#<?= $p['id_pesanan'] ?> &middot; <?= time_ago($p['tanggal_pesan']) ?></p>
                    </div>

                    <div class="text-right">
                        <p class="font-bold text-primary mb-1"><?= format_rupiah($p['total_harga']) ?></p>
                        <span class="badge <?= badge_status($p['status_pengiriman']) ?>"><?= esc($p['status_pengiriman']) ?></span>
                    </div>

                    <div class="flex sm:flex-col gap-2">
                        <?php if ($p['status_pengiriman'] === 'Menunggu'): ?>
                            <a href="<?= site_url('pedagang/pembelian/bayar/' . $p['id_pesanan']) ?>" class="btn-primary text-sm py-2 justify-center">
                                Lanjut Bayar
                            </a>
                        <?php elseif ($p['status_pengiriman'] === 'Dikirim'): ?>
                            <?php if (!empty($p['nomor_resi'])): ?>
                                <p class="text-xs text-gray-500 sm:text-right">Resi: <?= esc($p['nomor_resi']) ?></p>
                            <?php endif; ?>
                            <button type="button" @click="konfirmasiId = <?= $p['id_pesanan'] ?>" class="btn-primary text-sm py-2 justify-center">
                                Konfirmasi Terima
                            </button>
                            <a href="<?= base_url('pedagang/retur/ajukan/' . $p['id_pesanan']) ?>"
                                class="text-xs font-medium text-red-600 hover:underline text-center">Ajukan Retur</a>
                        <?php elseif ($p['status_pengiriman'] === 'Selesai'): ?>
                            <a href="<?= site_url('pedagang/rating/' . $p['id_pesanan']) ?>" class="btn-secondary text-sm py-2 justify-center">
                                Beri Rating
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Modal Konfirmasi Terima -->
    <template x-for="p in [1]" :key="p">
        <div x-show="konfirmasiId !== null" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
            <div class="absolute inset-0 bg-black/40" @click="konfirmasiId = null"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
                <?= heroicon('box', 'w-10 h-10 mx-auto text-primary mb-3') ?>
                <h3 class="font-bold text-gray-800 mb-1">Konfirmasi Barang Diterima?</h3>
                <p class="text-sm text-gray-500 mb-5">Dana akan diteruskan ke petani setelah Anda konfirmasi. Pastikan barang sudah sesuai.</p>
                <div class="flex gap-3">
                    <button type="button" @click="konfirmasiId = null" class="btn-secondary flex-1 justify-center">Batal</button>
                    <form :action="`<?= site_url('pedagang/pesanan/konfirmasi') ?>/${konfirmasiId}`" method="post" class="flex-1">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-primary w-full justify-center">Ya, Terima</button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>

<?= $this->endSection() ?>