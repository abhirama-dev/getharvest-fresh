<?php
/**
 * @var array $produk
 */
?>

<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="flex items-center justify-between mb-4">
    <p class="text-sm text-gray-500"><?= count($produk) ?> produk di etalase Anda</p>
    <a href="<?= base_url('petani/produk/tambah') ?>" class="btn-primary">
        <?= heroicon('plus', 'w-4 h-4') ?> Tambah Produk
    </a>
</div>

<?php if (empty($produk)): ?>
    <div class="card flex flex-col items-center justify-center py-16 text-center">
        <?= heroicon('box', 'w-12 h-12 text-gray-300') ?>
        <p class="mt-3 text-sm font-medium text-gray-500">Etalase Anda masih kosong</p>
        <p class="text-xs text-gray-400 mb-4">Mulai jual hasil panen Anda ke ribuan pedagang.</p>
        <a href="<?= base_url('petani/produk/tambah') ?>" class="btn-primary">Tambah Produk Pertama</a>
    </div>
<?php else: ?>
    <div class="card overflow-x-auto p-0">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50 text-left text-gray-500">
                    <th class="px-5 py-3 font-medium">Produk</th>
                    <th class="px-5 py-3 font-medium">Kategori</th>
                    <th class="px-5 py-3 font-medium">Grade</th>
                    <th class="px-5 py-3 font-medium">Harga/Kg</th>
                    <th class="px-5 py-3 font-medium">Stok</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($produk as $p): ?>
                    <tr>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <img src="<?= base_url('assets/uploads/produk/' . $p['gambar_produk']) ?>"
                                     class="h-10 w-10 rounded-lg object-cover border border-gray-100" alt="<?= esc($p['nama_produk']) ?>">
                                <span class="font-medium text-gray-800"><?= esc($p['nama_produk']) ?></span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-600"><?= esc($p['kategori'] ?: '-') ?></td>
                        <td class="px-5 py-3">
                            <span class="badge <?= $p['grade'] === 'Organik' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' ?>">
                                <?= esc($p['grade']) ?>
                            </span>
                        </td>
                        <td class="px-5 py-3 font-medium text-gray-800"><?= format_rupiah($p['harga_per_kg']) ?></td>
                        <td class="px-5 py-3">
                            <span class="<?= $p['stok_kg'] < 10 ? 'text-red-600 font-semibold' : 'text-gray-600' ?>">
                                <?= number_format($p['stok_kg']) ?> kg
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="<?= $p['status_panen'] === 'Siap Jual' ? 'badge-success' : 'badge-info' ?>">
                                <?= esc($p['status_panen']) ?>
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-2"
                                 x-data="{ confirmDelete: false }">
                                <button @click="confirmDelete = true" class="rounded-lg p-2 text-red-600 hover:bg-red-50" title="Hapus">
                                    <?= heroicon('trash', 'w-4 h-4') ?>
                                </button>

                                <div x-show="confirmDelete" x-cloak
                                     class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4">
                                    <div @click.outside="confirmDelete = false" class="w-full max-w-sm rounded-xl bg-white p-5 shadow-2xl">
                                        <p class="text-sm font-semibold text-gray-800">Hapus produk ini?</p>
                                        <p class="mt-1 text-xs text-gray-500">
                                            "<?= esc($p['nama_produk']) ?>" akan dihapus permanen dari etalase Anda.
                                        </p>
                                        <div class="mt-4 flex justify-end gap-2">
                                            <button @click="confirmDelete = false" class="btn-secondary text-xs px-3 py-1.5">Batal</button>
                                            <?= form_open('petani/produk/hapus/' . $p['id_produk']) ?>
                                                <button type="submit" class="btn-danger text-xs px-3 py-1.5">Ya, Hapus</button>
                                            <?= form_close() ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>