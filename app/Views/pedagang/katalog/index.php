<?php
/**
 * @var string $title
 * @var array $produk
 * @var array $kategoriList
 * @var string|null $keyword
 * @var string|null $kategori
 * @var string|null $grade
 * @var string|null $status
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Katalog Produk</h1>
    <p class="text-gray-500 mt-1">Temukan hasil panen segar langsung dari petani terverifikasi.</p>
</div>

<!-- Filter Bar -->
<form method="get" action="<?= site_url('pedagang/katalog') ?>" class="card p-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
        <div class="md:col-span-2">
            <input type="text" name="q" value="<?= esc($keyword) ?>" placeholder="Cari nama produk..."
                   class="input-field">
        </div>
        <div>
            <select name="kategori" class="input-field">
                <option value="">Semua Kategori</option>
                <?php foreach ($kategoriList as $k): ?>
                    <option value="<?= esc($k['kategori']) ?>" <?= $kategori === $k['kategori'] ? 'selected' : '' ?>>
                        <?= esc($k['kategori']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <select name="grade" class="input-field">
                <option value="">Semua Grade</option>
                <?php foreach (['A', 'B', 'C', 'Organik', 'Biasa'] as $g): ?>
                    <option value="<?= $g ?>" <?= $grade === $g ? 'selected' : '' ?>><?= $g ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <select name="status" class="input-field">
                <option value="">Semua Status</option>
                <option value="Siap Jual" <?= $status === 'Siap Jual' ? 'selected' : '' ?>>Siap Jual</option>
                <option value="Pre-Order" <?= $status === 'Pre-Order' ? 'selected' : '' ?>>Pre-Order</option>
            </select>
        </div>
    </div>
    <div class="flex items-center gap-3 mt-3">
        <button type="submit" class="btn-primary">
            <?= heroicon('search', 'w-4 h-4') ?> Terapkan Filter
        </button>
        <a href="<?= site_url('pedagang/katalog') ?>" class="text-sm text-gray-500 hover:text-primary">Reset</a>
    </div>
</form>

<!-- Grid Produk -->
<?php if (empty($produk)): ?>
    <div class="card p-12 text-center text-gray-400">
        <?= heroicon('box', 'w-12 h-12 mx-auto mb-3') ?>
        <p>Tidak ada produk yang cocok dengan pencarian Anda.</p>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <?php foreach ($produk as $p): ?>
            <div class="card overflow-hidden hover:shadow-card-hover transition group">
                <a href="<?= site_url('pedagang/katalog/' . $p['id_produk']) ?>" class="block relative">
                    <img src="<?= base_url('assets/uploads/produk/' . $p['gambar_produk']) ?>"
                         alt="<?= esc($p['nama_produk']) ?>"
                         class="w-full h-40 object-cover group-hover:scale-105 transition duration-300">
                    <span class="absolute top-2 left-2 badge bg-white/90 text-gray-700 shadow">
                        Grade <?= esc($p['grade']) ?>
                    </span>
                    <?php if ($p['status_panen'] === 'Pre-Order'): ?>
                        <span class="absolute top-2 right-2 badge badge-warning">Pre-Order</span>
                    <?php endif; ?>
                </a>

                <div class="p-4">
                    <a href="<?= site_url('pedagang/katalog/' . $p['id_produk']) ?>">
                        <h3 class="font-semibold text-gray-800 truncate hover:text-primary">
                            <?= esc($p['nama_produk']) ?>
                        </h3>
                    </a>
                    <p class="text-xs text-gray-500 truncate mb-2"><?= esc($p['nama_petani']) ?></p>

                    <!-- Rating -->
                    <div class="flex items-center gap-1 mb-2">
                        <?php $rating = round((float) ($p['rata_rating'] ?? 0)); ?>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <svg class="w-3.5 h-3.5 <?= $i <= $rating ? 'text-gold fill-gold' : 'text-gray-200 fill-gray-200' ?>"
                                 viewBox="0 0 20 20">
                                <path d="M10 1.5l2.6 5.6 6.1.5-4.6 4.1 1.4 6-5.5-3.2-5.5 3.2 1.4-6-4.6-4.1 6.1-.5z"/>
                            </svg>
                        <?php endfor; ?>
                        <span class="text-xs text-gray-400 ml-1">
                            (<?= (int) ($p['jumlah_rating'] ?? 0) ?>)
                        </span>
                    </div>

                    <div class="flex items-end justify-between mb-3">
                        <div>
                            <p class="text-lg font-bold text-primary"><?= format_rupiah($p['harga_per_kg']) ?></p>
                            <p class="text-xs text-gray-400">per kg</p>
                        </div>
                        <p class="text-xs <?= $p['stok_kg'] < 10 ? 'text-red-500' : 'text-gray-500' ?>">
                            Stok <?= number_format($p['stok_kg']) ?> kg
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <a href="<?= site_url('pedagang/katalog/' . $p['id_produk']) ?>#beli" class="btn-primary justify-center text-sm py-2">
                            Beli
                        </a>
                        <a href="<?= site_url('pedagang/katalog/' . $p['id_produk']) ?>#nego" class="btn-secondary justify-center text-sm py-2">
                            Nego
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>