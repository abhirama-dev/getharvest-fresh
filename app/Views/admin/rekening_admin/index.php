<?php
/**
 * @var array $rekening
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-3">
        <?php foreach ($rekening as $r): ?>
            <div class="card flex items-center justify-between" x-data="{ confirmDelete: false }">
                <div class="flex items-center gap-3">
                    <span class="rounded-lg bg-primary-50 p-2.5 text-primary-700"><?= heroicon('credit-card', 'w-5 h-5') ?></span>
                    <div>
                        <p class="font-semibold text-gray-800"><?= esc($r['nama_bank'] ?: strtoupper($r['tipe'])) ?></p>
                        <p class="text-sm text-gray-500"><?= esc($r['nomor_rekening']) ?> a.n. <?= esc($r['atas_nama']) ?></p>
                    </div>
                </div>
                <button @click="confirmDelete = true" class="rounded-lg p-2 text-red-600 hover:bg-red-50">
                    <?= heroicon('trash', 'w-4 h-4') ?>
                </button>
                <div x-show="confirmDelete" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4">
                    <div @click.outside="confirmDelete = false" class="w-full max-w-sm rounded-xl bg-white p-5 shadow-2xl">
                        <p class="text-sm font-semibold text-gray-800">Hapus rekening ini?</p>
                        <div class="mt-4 flex justify-end gap-2">
                            <button @click="confirmDelete = false" class="btn-secondary text-xs px-3 py-1.5">Batal</button>
                            <?= form_open('admin/rekening-admin/hapus/' . $r['id_rekening']) ?>
                                <button type="submit" class="btn-danger text-xs px-3 py-1.5">Hapus</button>
                            <?= form_close() ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($rekening)): ?>
            <div class="card text-center py-10 text-sm text-gray-400">Belum ada rekening platform terdaftar.</div>
        <?php endif; ?>
    </div>

    <div class="card h-fit">
        <h3 class="text-base font-semibold text-gray-800 mb-4">Tambah Rekening</h3>
        <?= form_open('admin/rekening-admin/simpan') ?>
            <div class="space-y-3">
                <select name="tipe" required class="input-field">
                    <option value="bank">Bank</option>
                    <option value="e_wallet">E-Wallet</option>
                </select>
                <input type="text" name="nama_bank" placeholder="Nama Bank/E-Wallet (mis. BCA, OVO)" class="input-field">
                <input type="text" name="nomor_rekening" required placeholder="Nomor Rekening" class="input-field">
                <input type="text" name="atas_nama" required placeholder="Atas Nama" class="input-field">
                <button type="submit" class="btn-primary w-full">Simpan</button>
            </div>
        <?= form_close() ?>
    </div>
</div>
<?= $this->endSection() ?>