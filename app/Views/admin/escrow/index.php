<?php
/**
 * @var array $escrow
 * @var array $sengketa
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php if (! empty($sengketa)): ?>
<div class="card mb-6 border-red-200 bg-red-50/40">
    <h3 class="text-base font-semibold text-red-700 mb-3 flex items-center gap-2">
        <?= heroicon('x-circle', 'w-5 h-5') ?> Sengketa Retur Butuh Mediasi
    </h3>
    <div class="space-y-3">
        <?php foreach ($sengketa as $s): ?>
            <div class="rounded-lg bg-white border border-gray-100 p-4 flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex-1">
                    <p class="font-medium text-gray-800"><?= esc($s['nama_produk']) ?></p>
                    <p class="text-xs text-gray-500">Petani menolak retur pedagang. Alasan pedagang: "<?= esc($s['alasan']) ?>"</p>
                </div>
                <div class="flex gap-2">
                    <?= form_open('admin/escrow/mediasi/setujui/' . $s['id_retur']) ?>
                        <button type="submit" class="btn-secondary text-xs px-3 py-1.5">Menangkan Pedagang</button>
                    <?= form_close() ?>
                    <?= form_open('admin/escrow/mediasi/tolak/' . $s['id_retur']) ?>
                        <button type="submit" class="btn-primary text-xs px-3 py-1.5">Menangkan Petani</button>
                    <?= form_close() ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="card overflow-x-auto p-0">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50 text-left text-gray-500">
                <th class="px-5 py-3 font-medium">Produk</th>
                <th class="px-5 py-3 font-medium">Pedagang</th>
                <th class="px-5 py-3 font-medium">Petani</th>
                <th class="px-5 py-3 font-medium">Jumlah</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Ditahan Sejak</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach ($escrow as $e): ?>
                <tr>
                    <td class="px-5 py-3 text-gray-700"><?= esc($e['nama_produk']) ?></td>
                    <td class="px-5 py-3 text-gray-600"><?= esc($e['nama_pedagang']) ?></td>
                    <td class="px-5 py-3 text-gray-600"><?= esc($e['nama_petani']) ?></td>
                    <td class="px-5 py-3 font-medium text-gray-800"><?= format_rupiah($e['jumlah_escrow']) ?></td>
                    <td class="px-5 py-3"><span class="<?= badge_status($e['status']) ?>"><?= esc($e['status']) ?></span></td>
                    <td class="px-5 py-3 text-gray-500"><?= time_ago($e['tanggal_ditahan']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>