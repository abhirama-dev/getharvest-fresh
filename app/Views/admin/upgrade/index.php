<?php
/**
 * @var array $permintaan
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php if (empty($permintaan)): ?>
    <div class="card flex flex-col items-center justify-center py-16 text-center">
        <?= heroicon('star', 'w-12 h-12 text-gray-300') ?>
        <p class="mt-3 text-sm font-medium text-gray-500">Tidak ada pengajuan upgrade yang menunggu</p>
    </div>
<?php else: ?>
    <div class="space-y-3">
        <?php foreach ($permintaan as $p): ?>
            <div class="card flex flex-col sm:flex-row sm:items-center gap-4" x-data="{ showTolak: false, showBukti: false }">
                <div class="flex-1">
                    <p class="font-semibold text-gray-800"><?= esc($p['nama_lengkap']) ?></p>
                    <p class="text-sm text-gray-500"><?= esc($p['email']) ?></p>
                    <p class="text-xs text-gray-400 mt-1">Paket <span class="capitalize font-medium"><?= esc($p['tipe']) ?></span> &middot; Diajukan <?= time_ago($p['tanggal_permintaan']) ?></p>
                </div>

                <?php if ($p['bukti_bayar']): ?>
                    <button @click="showBukti = true" class="btn-secondary text-xs px-3 py-2">Lihat Bukti Bayar</button>
                <?php endif; ?>

                <div class="flex gap-2">
                    <?= form_open('admin/upgrade/setujui/' . $p['id_permintaan']) ?>
                        <button type="submit" class="btn-primary text-xs px-3 py-2">Setujui</button>
                    <?= form_close() ?>
                    <button @click="showTolak = true" class="btn-secondary text-xs px-3 py-2 text-red-600">Tolak</button>
                </div>

                <!-- Modal Bukti -->
                <div x-show="showBukti" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4">
                    <div @click.outside="showBukti = false" class="w-full max-w-lg rounded-xl bg-white p-5 shadow-2xl">
                        <img src="<?= base_url('assets/uploads/bukti_bayar/' . $p['bukti_bayar']) ?>" class="w-full rounded-lg" alt="">
                        <button @click="showBukti = false" class="btn-secondary w-full mt-3 text-sm">Tutup</button>
                    </div>
                </div>

                <!-- Modal Tolak -->
                <div x-show="showTolak" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4">
                    <div @click.outside="showTolak = false" class="w-full max-w-sm rounded-xl bg-white p-5 shadow-2xl">
                        <p class="text-sm font-semibold text-gray-800 mb-3">Tolak Pengajuan</p>
                        <?= form_open('admin/upgrade/tolak/' . $p['id_permintaan']) ?>
                            <textarea name="alasan_tolak" required rows="3" class="input-field" placeholder="Alasan penolakan..."></textarea>
                            <div class="mt-3 flex justify-end gap-2">
                                <button type="button" @click="showTolak = false" class="btn-secondary text-xs px-3 py-1.5">Batal</button>
                                <button type="submit" class="btn-danger text-xs px-3 py-1.5">Tolak</button>
                            </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>