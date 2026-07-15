<?php
/**
 * @var string $title
 * @var array $pesanan
 * @var array $rekeningAdmin
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-2xl mx-auto" x-data="{ metode: '' }">
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Selesaikan Pembayaran</h1>
    <p class="text-gray-500 mb-6">Pesanan #<?= $pesanan['id_pesanan'] ?> — transfer ke rekening escrow GetHarvest di bawah ini.</p>

    <!-- Ringkasan Pesanan -->
    <div class="card p-5 flex items-center gap-4 mb-6">
        <img src="<?= base_url('assets/uploads/produk/' . $pesanan['gambar_produk']) ?>"
             class="w-16 h-16 rounded-xl object-cover" alt="">
        <div class="flex-1">
            <p class="font-semibold text-gray-800"><?= esc($pesanan['nama_produk']) ?></p>
            <p class="text-sm text-gray-500">dari <?= esc($pesanan['nama_petani']) ?> &middot; <?= number_format($pesanan['jumlah_kg']) ?> kg</p>
        </div>
        <p class="text-lg font-bold text-primary"><?= format_rupiah($pesanan['total_harga']) ?></p>
    </div>

    <!-- Rekening Tujuan -->
    <div class="card p-5 mb-6">
        <h2 class="font-semibold text-gray-800 mb-3">Transfer ke Salah Satu Rekening Berikut</h2>
        <div class="space-y-3">
            <?php foreach ($rekeningAdmin as $r): ?>
                <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 bg-gray-50">
                    <div>
                        <p class="text-sm text-gray-500"><?= $r['tipe'] === 'bank' ? esc($r['nama_bank']) : 'E-Wallet' ?></p>
                        <p class="font-semibold text-gray-800"><?= esc($r['nomor_rekening']) ?></p>
                        <p class="text-xs text-gray-500">a.n. <?= esc($r['atas_nama']) ?></p>
                    </div>
                    <span class="badge badge-info"><?= ucfirst($r['tipe']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="text-xs text-gray-400 mt-3">
            Dana akan ditahan sementara oleh sistem (escrow) dan baru diteruskan ke petani setelah Anda mengonfirmasi barang diterima.
        </p>
    </div>

    <!-- Form Upload Bukti -->
    <div class="card p-5">
        <h2 class="font-semibold text-gray-800 mb-3">Upload Bukti Transfer</h2>

        <?= form_open_multipart('pedagang/pembelian/bayar/' . $pesanan['id_pesanan']) ?>
        <?= csrf_field() ?>

        <label class="label-field">Metode Pembayaran</label>
        <select name="metode_pembayaran" x-model="metode" class="input-field mb-3" required>
            <option value="">-- Pilih --</option>
            <option value="transfer_bank">Transfer Bank</option>
            <option value="e_wallet">E-Wallet</option>
        </select>

        <label class="label-field">Bukti Transfer (gambar)</label>
        <input type="file" name="bukti_bayar" accept="image/*" class="input-field mb-4" required>

        <button type="submit" class="btn-primary w-full justify-center">
            <?= heroicon('clipboard', 'w-4 h-4') ?> Konfirmasi Pembayaran
        </button>
        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>