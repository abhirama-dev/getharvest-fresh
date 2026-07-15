<?php
/**
 * @var array $rekening
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php if (empty($rekening)): ?>
    <div class="card flex flex-col items-center justify-center py-16 text-center">
        <?= heroicon('credit-card', 'w-12 h-12 text-gray-300') ?>
        <p class="mt-3 text-sm font-medium text-gray-500">Tidak ada rekening yang menunggu validasi</p>
    </div>
<?php else: ?>
    <div class="card overflow-x-auto p-0">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50 text-left text-gray-500">
                    <th class="px-5 py-3 font-medium">Pemilik</th>
                    <th class="px-5 py-3 font-medium">Role</th>
                    <th class="px-5 py-3 font-medium">Tipe</th>
                    <th class="px-5 py-3 font-medium">No. Rekening</th>
                    <th class="px-5 py-3 font-medium">Atas Nama</th>
                    <th class="px-5 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($rekening as $r): ?>
                    <tr>
                        <td class="px-5 py-3 font-medium text-gray-800"><?= esc($r['nama_lengkap']) ?></td>
                        <td class="px-5 py-3 capitalize text-gray-600"><?= esc($r['role']) ?></td>
                        <td class="px-5 py-3 text-gray-600"><?= esc($r['nama_bank'] ?: strtoupper($r['tipe'])) ?></td>
                        <td class="px-5 py-3 text-gray-600"><?= esc($r['nomor_rekening']) ?></td>
                        <td class="px-5 py-3 text-gray-600"><?= esc($r['atas_nama']) ?></td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <?= form_open('admin/rekening/verifikasi/' . $r['id_rekening']) ?>
                                    <button type="submit" class="text-xs font-medium text-primary-700 hover:underline">Verifikasi</button>
                                <?= form_close() ?>
                                <?= form_open('admin/rekening/tolak/' . $r['id_rekening']) ?>
                                    <button type="submit" class="text-xs font-medium text-red-600 hover:underline">Tolak</button>
                                <?= form_close() ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>