<?php
/**
 * @var array $retur
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php if (empty($retur)): ?>
    <div class="card flex flex-col items-center justify-center py-16 text-center">
        <?= heroicon('arrow-path', 'w-12 h-12 text-gray-300') ?>
        <p class="mt-3 text-sm font-medium text-gray-500">Belum ada pengajuan retur masuk</p>
    </div>
<?php else: ?>
    <div class="space-y-3">
        <?php foreach ($retur as $r): ?>
            <div class="card" x-data="{ showTolak: false }">
                <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                    <img src="<?= base_url('assets/uploads/produk/' . $r['gambar_produk'] ?? '') ?>"
                         class="h-14 w-14 rounded-lg object-cover border border-gray-100" alt="">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <p class="font-semibold text-gray-800"><?= esc($r['nama_produk']) ?></p>
                            <span class="<?= badge_status($r['status']) ?> capitalize"><?= esc($r['status']) ?></span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Pedagang: <?= esc($r['nama_pedagang']) ?></p>
                        <p class="text-sm text-gray-500 mt-1">"<?= esc($r['alasan']) ?>"</p>
                        <a href="<?= base_url('assets/uploads/retur/' . $r['foto_bukti']) ?>" target="_blank"
                           class="mt-2 inline-block text-xs font-medium text-primary-700 hover:underline">Lihat foto bukti</a>
                    </div>

                    <?php if ($r['status'] === 'menunggu'): ?>
                        <div class="flex flex-col gap-2 sm:w-40">
                            <?= form_open('petani/retur/setujui/' . $r['id_retur']) ?>
                                <button type="submit" class="btn-primary w-full text-xs py-2">Setujui</button>
                            <?= form_close() ?>
                            <button @click="showTolak = true" class="btn-secondary w-full text-xs py-2 text-red-600">Tolak</button>
                        </div>
                    <?php endif; ?>
                </div>

                <div x-show="showTolak" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4">
                    <div @click.outside="showTolak = false" class="w-full max-w-sm rounded-xl bg-white p-5 shadow-2xl">
                        <p class="text-sm font-semibold text-gray-800 mb-3">Tolak Pengajuan Retur</p>
                        <?= form_open('petani/retur/tolak/' . $r['id_retur']) ?>
                            <textarea name="alasan_tolak" required rows="3" class="input-field" placeholder="Alasan penolakan..."></textarea>
                            <div class="mt-3 flex justify-end gap-2">
                                <button type="button" @click="showTolak = false" class="btn-secondary text-xs px-3 py-1.5">Batal</button>
                                <button type="submit" class="btn-danger text-xs px-3 py-1.5">Tolak Retur</button>
                            </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>