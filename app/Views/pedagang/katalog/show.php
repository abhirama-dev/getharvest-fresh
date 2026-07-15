<?php

/**
 * @var string $title
 * @var array $produk
 * @var array $rataRating
 * @var array $ulasan
 * @var array $produkLain
 * @var array $rekeningTervalidasi
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div x-data="produkDetail()" x-init="checkHash()" @hashchange.window="checkHash()">

    <nav class="text-sm text-gray-500 mb-4">
        <a href="<?= site_url('pedagang/katalog') ?>" class="hover:text-primary">Katalog</a>
        <span class="mx-1">/</span>
        <span class="text-gray-700"><?= esc($produk['nama_produk']) ?></span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">
        <!-- Gambar -->
        <div class="lg:col-span-2">
            <div class="card overflow-hidden">
                <img src="<?= base_url('assets/uploads/produk/' . $produk['gambar_produk']) ?>"
                    alt="<?= esc($produk['nama_produk']) ?>" class="w-full h-72 object-cover">
            </div>
            <?php if (!empty($produk['sertifikat'])): ?>
                <a href="<?= base_url('assets/uploads/sertifikat/' . $produk['sertifikat']) ?>" target="_blank"
                    class="mt-3 flex items-center gap-2 text-sm text-primary hover:underline">
                    <?= heroicon('clipboard', 'w-4 h-4') ?> Lihat Sertifikat
                </a>
            <?php endif; ?>
        </div>

        <!-- Info -->
        <div class="lg:col-span-3">
            <div class="flex items-center gap-2 mb-2">
                <span class="badge badge-info">Grade <?= esc($produk['grade']) ?></span>
                <span class="badge <?= $produk['status_panen'] === 'Pre-Order' ? 'badge-warning' : 'badge-success' ?>">
                    <?= esc($produk['status_panen']) ?>
                </span>
            </div>

            <h1 class="text-2xl font-bold text-gray-800 mb-1"><?= esc($produk['nama_produk']) ?></h1>

            <div class="flex items-center gap-2 mb-4">
                <?php $rating = round((float) ($rataRating['rata_rating'] ?? 0)); ?>
                <div class="flex items-center">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <svg class="w-4 h-4 <?= $i <= $rating ? 'text-gold fill-gold' : 'text-gray-200 fill-gray-200' ?>" viewBox="0 0 20 20">
                            <path d="M10 1.5l2.6 5.6 6.1.5-4.6 4.1 1.4 6-5.5-3.2-5.5 3.2 1.4-6-4.6-4.1 6.1-.5z" />
                        </svg>
                    <?php endfor; ?>
                </div>
                <span class="text-sm text-gray-500">
                    <?= $rataRating['rata_rating'] ?? '0' ?> (<?= (int) ($rataRating['jumlah_rating'] ?? 0) ?> ulasan)
                </span>
            </div>

            <p class="text-3xl font-bold text-primary mb-1"><?= format_rupiah($produk['harga_per_kg']) ?>
                <span class="text-base font-normal text-gray-400">/ kg</span>
            </p>
            <p class="text-sm <?= $produk['stok_kg'] < 10 ? 'text-red-500' : 'text-gray-500' ?> mb-5">
                Stok tersedia: <?= number_format($produk['stok_kg']) ?> kg
                <?php if ($produk['status_panen'] === 'Pre-Order' && $produk['tanggal_estimasi_panen']): ?>
                    &middot; Estimasi panen <?= date('d M Y', strtotime($produk['tanggal_estimasi_panen'])) ?>
                <?php endif; ?>
            </p>

            <!-- Info Petani -->
            <div class="card p-4 flex items-center gap-3 mb-5">
                <?php if (!empty($produk['foto_toko'])): ?>
                    <img src="<?= base_url('assets/uploads/toko/' . $produk['foto_toko']) ?>"
                        class="w-12 h-12 rounded-full object-cover" alt="">
                <?php else: ?>
                    <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-lg">
                        <?= esc(strtoupper(substr($produk['nama_petani'], 0, 1))) ?>
                    </div>
                <?php endif; ?>
                <div class="flex-1">
                    <p class="font-semibold text-gray-800"><?= esc($produk['nama_petani']) ?></p>
                    <?php if (!empty($produk['alamat_toko'])): ?>
                        <p class="text-xs text-gray-500"><?= esc($produk['alamat_toko']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="grid grid-cols-2 gap-3">
                <button type="button" @click="openBeli()" class="btn-primary justify-center py-3">
                    <?= heroicon('shopping-bag', 'w-4 h-4') ?> Beli Sekarang
                </button>
                <button type="button" @click="showNego = true" class="btn-secondary justify-center py-3">
                    <?= heroicon('chat', 'w-4 h-4') ?> Ajukan Nego
                </button>
            </div>

            <?php if (empty($rekeningTervalidasi)): ?>
                <p class="text-xs text-red-500 mt-2">
                    Petani ini belum memiliki rekening tervalidasi, pembelian belum dapat diproses.
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Ulasan -->
    <div class="card p-5 mb-8">
        <h2 class="font-semibold text-gray-800 mb-4">Ulasan Pembeli</h2>
        <?php if (empty($ulasan)): ?>
            <p class="text-sm text-gray-400">Belum ada ulasan untuk produk dari petani ini.</p>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($ulasan as $u): ?>
                    <div class="border-b last:border-0 pb-4 last:pb-0">
                        <div class="flex items-center justify-between mb-1">
                            <p class="font-medium text-gray-700 text-sm"><?= esc($u['nama_pemberi']) ?></p>
                            <span class="text-xs text-gray-400"><?= time_ago($u['tanggal']) ?></span>
                        </div>
                        <div class="flex items-center mb-1">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <svg class="w-3.5 h-3.5 <?= $i <= $u['rating'] ? 'text-gold fill-gold' : 'text-gray-200 fill-gray-200' ?>" viewBox="0 0 20 20">
                                    <path d="M10 1.5l2.6 5.6 6.1.5-4.6 4.1 1.4 6-5.5-3.2-5.5 3.2 1.4-6-4.6-4.1 6.1-.5z" />
                                </svg>
                            <?php endfor; ?>
                        </div>
                        <?php if (!empty($u['ulasan'])): ?>
                            <p class="text-sm text-gray-600"><?= esc($u['ulasan']) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Produk Lain dari Petani -->
    <?php if (!empty($produkLain)): ?>
        <div class="mb-8">
            <h2 class="font-semibold text-gray-800 mb-4">Produk Lain dari <?= esc($produk['nama_petani']) ?></h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <?php foreach ($produkLain as $pl): ?>
                    <a href="<?= site_url('pedagang/katalog/' . $pl['id_produk']) ?>" class="card overflow-hidden hover:shadow-card-hover transition">
                        <img src="<?= base_url('assets/uploads/produk/' . $pl['gambar_produk']) ?>" class="w-full h-24 object-cover" alt="">
                        <div class="p-3">
                            <p class="text-sm font-medium text-gray-800 truncate"><?= esc($pl['nama_produk']) ?></p>
                            <p class="text-sm text-primary font-semibold"><?= format_rupiah($pl['harga_per_kg']) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Modal Beli -->
    <div x-show="showBeli" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display:none">
        <div class="absolute inset-0 bg-black/40" @click="showBeli = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6" @click.outside="showBeli = false">
            <h3 class="text-lg font-bold text-gray-800 mb-1">Beli <?= esc($produk['nama_produk']) ?></h3>
            <p class="text-sm text-gray-500 mb-4">Harga <?= format_rupiah($produk['harga_per_kg']) ?> / kg</p>

            <?php if (empty($rekeningTervalidasi)): ?>
                <div class="p-4 rounded-xl bg-red-50 text-red-600 text-sm">
                    Pembelian belum dapat dilakukan karena petani belum memiliki rekening yang tervalidasi.
                </div>
            <?php else: ?>
                <?= form_open('pedagang/pembelian/store', ['id' => 'formBeli']) ?>
                <?= csrf_field() ?>
                <input type="hidden" name="id_produk" value="<?= $produk['id_produk'] ?>">

                <label class="label-field">Jumlah (Kg)</label>
                <input type="number" name="jumlah_kg" x-model.number="jumlahKg" min="1" max="<?= $produk['stok_kg'] ?>"
                    class="input-field mb-3" required>
                <p class="text-xs text-gray-400 mb-3">Maks. <?= number_format($produk['stok_kg']) ?> kg</p>

                <p class="text-xs text-gray-500 mb-3">
                    Setelah pesanan dibuat, Anda akan diarahkan untuk transfer ke rekening escrow GetHarvest.
                    Dana baru diteruskan ke petani setelah Anda konfirmasi barang diterima.
                </p>

                <div class="flex items-center justify-between p-3 rounded-xl bg-primary/5 mb-4">
                    <span class="text-sm text-gray-600">Total Pembayaran</span>
                    <span class="font-bold text-primary" x-text="formatRupiah(jumlahKg * <?= (int) $produk['harga_per_kg'] ?>)"></span>
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="showBeli = false" class="btn-secondary flex-1 justify-center">Batal</button>
                    <button type="submit" class="btn-primary flex-1 justify-center">Lanjut Pembayaran</button>
                </div>
                <?= form_close() ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Nego -->
    <div x-show="showNego" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display:none">
        <div class="absolute inset-0 bg-black/40" @click="showNego = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6" @click.outside="showNego = false">
            <h3 class="text-lg font-bold text-gray-800 mb-1">Ajukan Nego Harga</h3>
            <p class="text-sm text-gray-500 mb-4">Harga awal <?= format_rupiah($produk['harga_per_kg']) ?> / kg</p>

            <?= form_open('pedagang/nego/ajukan') ?>
            <?= csrf_field() ?>
            <input type="hidden" name="id_produk" value="<?= $produk['id_produk'] ?>">

            <label class="label-field">Jumlah Kebutuhan (Kg)</label>
            <input type="number" name="jumlah_kebutuhan_kg" min="1" max="<?= $produk['stok_kg'] ?>"
                class="input-field mb-3" required>

            <label class="label-field">Harga Tawaran (per Kg)</label>
            <input type="number" name="harga_tawaran" min="1" class="input-field mb-4" required>

            <div class="flex gap-3">
                <button type="button" @click="showNego = false" class="btn-secondary flex-1 justify-center">Batal</button>
                <button type="submit" class="btn-gold flex-1 justify-center">Kirim Tawaran</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>

</div>

<script>
    function produkDetail() {
        return {
            showBeli: false,
            showNego: false,
            jumlahKg: 1,
            openBeli() {
                this.showBeli = true;
            },
            checkHash() {
                if (window.location.hash === '#beli') this.showBeli = true;
                if (window.location.hash === '#nego') this.showNego = true;
            },
            formatRupiah(angka) {
                if (!angka || isNaN(angka)) angka = 0;
                return 'Rp' + Number(angka).toLocaleString('id-ID');
            }
        }
    }
</script>

<?= $this->endSection() ?>