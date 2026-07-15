<?php
/**
 * @var string $title
 * @var array $daftarNego
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div x-data="{ balasId: null }">
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Negosiasi Saya</h1>
    <p class="text-gray-500 mb-6">Pantau tawaran harga yang sedang berjalan.</p>

    <?php if (empty($daftarNego)): ?>
        <div class="card p-12 text-center text-gray-400">
            <?= heroicon('chat', 'w-12 h-12 mx-auto mb-3') ?>
            <p>Belum ada negosiasi. Ajukan tawaran dari halaman detail produk.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($daftarNego as $n): ?>
                <div class="card p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <img src="<?= base_url('assets/uploads/produk/' . $n['gambar_produk']) ?>" class="w-12 h-12 rounded-xl object-cover" alt="">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800"><?= esc($n['nama_produk']) ?></p>
                            <p class="text-xs text-gray-500">Harga awal <?= format_rupiah($n['harga_per_kg']) ?>/kg</p>
                        </div>
                        <span class="badge <?= badge_status($n['status_terkini']) ?>"><?= esc($n['status_terkini']) ?></span>
                    </div>

                    <!-- Timeline riwayat tawar-menawar -->
                    <div class="space-y-2 mb-4 pl-2 border-l-2 border-gray-100">
                        <?php foreach ($n['riwayat'] as $r): ?>
                            <div class="pl-3 -ml-[9px] relative">
                                <span class="absolute -left-[5px] top-1.5 w-2 h-2 rounded-full bg-primary"></span>
                                <p class="text-sm text-gray-700">
                                    <?= format_rupiah($r['harga_tawaran']) ?>/kg &middot; <?= number_format($r['jumlah_kebutuhan_kg']) ?> kg
                                    <span class="text-xs text-gray-400">(<?= time_ago($r['tanggal_nego']) ?>)</span>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($n['status_terkini'] === 'Menunggu' && $n['pihak_selanjutnya'] === 'pedagang'): ?>
                        <div class="flex flex-wrap gap-2">
                            <form action="<?= site_url('pedagang/nego/respon/' . $n['id_nego_aktif']) ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="aksi" value="terima">
                                <button type="submit" class="btn-primary text-sm py-2">Terima Tawaran Petani</button>
                            </form>
                            <button type="button" @click="balasId = <?= $n['id_nego_aktif'] ?>" class="btn-gold text-sm py-2">Nego Balik</button>
                            <form action="<?= site_url('pedagang/nego/respon/' . $n['id_nego_aktif']) ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="aksi" value="tolak">
                                <button type="submit" class="btn-danger text-sm py-2">Tolak</button>
                            </form>
                        </div>
                    <?php elseif ($n['status_terkini'] === 'Menunggu'): ?>
                        <p class="text-xs text-gray-400">Menunggu respon petani...</p>
                    <?php elseif ($n['status_terkini'] === 'Diterima'): ?>
                        <p class="text-xs text-primary">Disetujui — lanjutkan ke Riwayat Belanja untuk membayar.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Modal Nego Balik -->
    <div x-show="balasId !== null" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
        <div class="absolute inset-0 bg-black/40" @click="balasId = null"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
            <h3 class="font-bold text-gray-800 mb-3">Ajukan Harga Baru</h3>
            <form :action="`<?= site_url('pedagang/nego/respon') ?>/${balasId}`" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="aksi" value="balas">
                <label class="label-field">Harga Tawaran Baru (per Kg)</label>
                <input type="number" name="harga_baru" min="1" class="input-field mb-4" required>
                <div class="flex gap-3">
                    <button type="button" @click="balasId = null" class="btn-secondary flex-1 justify-center">Batal</button>
                    <button type="submit" class="btn-gold flex-1 justify-center">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>