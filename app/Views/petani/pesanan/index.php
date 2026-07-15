<?php
/**
 * @var string $title
 * @var array $pesanan
 * @var string|null $status
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div x-data="{ kirimId: null }">
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Pesanan Masuk</h1>
    <p class="text-gray-500 mb-6">Kelola pesanan yang sudah dibayar oleh pedagang.</p>

    <div class="flex flex-wrap gap-2 mb-6">
        <?php
        $tabs = ['' => 'Semua', 'Dibayar' => 'Dibayar', 'Dikemas' => 'Dikemas', 'Dikirim' => 'Dikirim', 'Selesai' => 'Selesai', 'Retur' => 'Retur'];
        ?>
        <?php foreach ($tabs as $val => $label): ?>
            <a href="<?= site_url('petani/pesanan') . ($val ? '?status=' . $val : '') ?>"
               class="px-4 py-1.5 rounded-full text-sm <?= $status === $val || (empty($status) && $val === '') ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($pesanan)): ?>
        <div class="card p-12 text-center text-gray-400">
            <?= heroicon('clipboard', 'w-12 h-12 mx-auto mb-3') ?>
            <p>Belum ada pesanan masuk pada status ini.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($pesanan as $p): ?>
                <div class="card p-4 flex flex-col sm:flex-row sm:items-center gap-4">
                    <img src="<?= base_url('assets/uploads/produk/' . $p['gambar_produk']) ?>"
                         class="w-16 h-16 rounded-xl object-cover" alt="">

                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-800 truncate"><?= esc($p['nama_produk']) ?></p>
                        <p class="text-sm text-gray-500">dari <?= esc($p['nama_pedagang']) ?> &middot; <?= number_format($p['jumlah_kg']) ?> kg</p>
                        <p class="text-xs text-gray-400">#<?= $p['id_pesanan'] ?> &middot; <?= time_ago($p['tanggal_pesan']) ?></p>
                    </div>

                    <div class="text-right">
                        <p class="font-bold text-primary mb-1"><?= format_rupiah($p['total_harga']) ?></p>
                        <span class="badge <?= badge_status($p['status_pengiriman']) ?>"><?= esc($p['status_pengiriman']) ?></span>
                    </div>

                    <div class="flex sm:flex-col gap-2">
                        <?php if ($p['status_pengiriman'] === 'Dibayar'): ?>
                            <form action="<?= site_url('petani/pesanan/kemas/' . $p['id_pesanan']) ?>" method="post">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-primary text-sm py-2 justify-center w-full">Proses & Kemas</button>
                            </form>
                        <?php elseif ($p['status_pengiriman'] === 'Dikemas'): ?>
                            <button type="button" @click="kirimId = <?= $p['id_pesanan'] ?>" class="btn-gold text-sm py-2 justify-center">
                                Input Resi & Kirim
                            </button>
                        <?php elseif ($p['status_pengiriman'] === 'Dikirim'): ?>
                            <p class="text-xs text-gray-500 sm:text-right">Resi: <?= esc($p['nomor_resi']) ?></p>
                            <p class="text-xs text-gray-400">Menunggu konfirmasi pembeli</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Modal Input Resi -->
    <div x-show="kirimId !== null" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
        <div class="absolute inset-0 bg-black/40" @click="kirimId = null"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
            <h3 class="font-bold text-gray-800 mb-3">Input Nomor Resi</h3>
            <form :action="`<?= site_url('petani/pesanan/kirim') ?>/${kirimId}`" method="post">
                <?= csrf_field() ?>
                <label class="label-field">Nomor Resi</label>
                <input type="text" name="nomor_resi" class="input-field mb-4" required placeholder="Contoh: JNE1234567890">
                <div class="flex gap-3">
                    <button type="button" @click="kirimId = null" class="btn-secondary flex-1 justify-center">Batal</button>
                    <button type="submit" class="btn-primary flex-1 justify-center">Kirim Pesanan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>