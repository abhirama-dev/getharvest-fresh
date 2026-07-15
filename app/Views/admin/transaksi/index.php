<?php
/**
 * @var array $transaksi
 * @var int $totalOmzet
 * @var string $filterStatus
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <p class="text-sm text-gray-500">Total Omzet Platform (Selesai)</p>
        <p class="text-2xl font-bold text-primary-700"><?= format_rupiah($totalOmzet) ?></p>
    </div>
    <?= form_open('admin/transaksi', ['method' => 'get']) ?>
        <select name="status" onchange="this.form.submit()" class="input-field">
            <option value="">Semua Status</option>
            <?php foreach (['Menunggu', 'Dibayar', 'Dikemas', 'Dikirim', 'Selesai', 'Retur'] as $s): ?>
                <option value="<?= $s ?>" <?= $filterStatus === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
    <?= form_close() ?>
</div>

<div class="card overflow-x-auto p-0">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50 text-left text-gray-500">
                <th class="px-5 py-3 font-medium">Produk</th>
                <th class="px-5 py-3 font-medium">Pedagang</th>
                <th class="px-5 py-3 font-medium">Petani</th>
                <th class="px-5 py-3 font-medium">Jumlah</th>
                <th class="px-5 py-3 font-medium">Total</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Tanggal</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach ($transaksi as $t): ?>
                <tr>
                    <td class="px-5 py-3 text-gray-700"><?= esc($t['nama_produk']) ?></td>
                    <td class="px-5 py-3 text-gray-600"><?= esc($t['nama_pedagang']) ?></td>
                    <td class="px-5 py-3 text-gray-600"><?= esc($t['nama_petani']) ?></td>
                    <td class="px-5 py-3 text-gray-600"><?= $t['jumlah_kg'] ?> kg</td>
                    <td class="px-5 py-3 font-medium text-gray-800"><?= format_rupiah($t['total_harga']) ?></td>
                    <td class="px-5 py-3"><span class="<?= badge_status($t['status_pengiriman']) ?>"><?= esc($t['status_pengiriman']) ?></span></td>
                    <td class="px-5 py-3 text-gray-500"><?= date('d M Y', strtotime($t['tanggal_pesan'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>