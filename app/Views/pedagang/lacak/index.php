<?php
/**
 * @var array|null $hasil
 * @var bool $notFound
 * @var string|null $resiDicari
 * @var array $sedangDikirim
 */

$tahapan = ['Dibayar', 'Dikemas', 'Dikirim', 'Selesai'];
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card mb-6">
    <?= form_open('pedagang/lacak', ['method' => 'get', 'class' => 'flex gap-2']) ?>
        <input type="text" name="resi" value="<?= esc($resiDicari) ?>" placeholder="Masukkan nomor resi..."
               class="input-field flex-1">
        <button type="submit" class="btn-primary">
            <?= heroicon('search', 'w-4 h-4') ?> Lacak
        </button>
    <?= form_close() ?>
</div>

<?php if ($notFound): ?>
    <div class="card border-red-200 bg-red-50/50 text-center py-8 mb-6">
        <p class="text-sm text-red-600">Nomor resi "<?= esc($resiDicari) ?>" tidak ditemukan pada pesanan Anda.</p>
    </div>
<?php endif; ?>

<?php if ($hasil): ?>
    <div class="card mb-6">
        <div class="flex items-center gap-3 mb-5">
            <img src="<?= base_url('assets/uploads/produk/' . $hasil['gambar_produk']) ?>"
                 class="h-14 w-14 rounded-lg object-cover border border-gray-100" alt="">
            <div>
                <p class="font-semibold text-gray-800"><?= esc($hasil['nama_produk']) ?></p>
                <p class="text-sm text-gray-500">Petani: <?= esc($hasil['nama_petani']) ?> &middot; <?= esc($hasil['hp_petani']) ?></p>
                <p class="text-xs text-gray-400 mt-1">No. Resi: <span class="font-mono font-semibold text-gray-700"><?= esc($hasil['nomor_resi']) ?></span></p>
            </div>
        </div>

        <!-- Timeline status -->
        <div class="flex items-center justify-between relative">
            <?php
            $currentIndex = array_search($hasil['status_pengiriman'], $tahapan);
            if ($hasil['status_pengiriman'] === 'Retur') $currentIndex = -1;
            ?>
            <?php foreach ($tahapan as $i => $t): ?>
                <div class="flex-1 flex flex-col items-center relative z-10">
                    <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold
                        <?= $i <= $currentIndex ? 'bg-primary-700 text-white' : 'bg-gray-200 text-gray-400' ?>">
                        <?= $i <= $currentIndex ? '✓' : $i + 1 ?>
                    </div>
                    <p class="mt-2 text-xs text-center <?= $i <= $currentIndex ? 'text-gray-800 font-medium' : 'text-gray-400' ?>"><?= $t ?></p>
                </div>
                <?php if ($i < count($tahapan) - 1): ?>
                    <div class="flex-1 h-0.5 -mt-6 <?= $i < $currentIndex ? 'bg-primary-700' : 'bg-gray-200' ?>"></div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <?php if ($hasil['status_pengiriman'] === 'Retur'): ?>
            <div class="mt-4 rounded-lg bg-red-50 border border-red-200 p-3 text-center">
                <span class="badge-danger">Retur</span>
                <p class="text-xs text-red-600 mt-1">Pesanan ini sedang dalam proses retur.</p>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="card">
    <h3 class="text-sm font-semibold text-gray-800 mb-4">Pesanan Sedang Dikemas/Dikirim</h3>
    <?php if (empty($sedangDikirim)): ?>
        <p class="text-xs text-gray-400 text-center py-6">Tidak ada pesanan yang sedang dalam proses pengiriman.</p>
    <?php else: ?>
        <div class="space-y-2">
            <?php foreach ($sedangDikirim as $s): ?>
                <div class="flex items-center gap-3 rounded-lg border border-gray-100 px-3 py-2">
                    <img src="<?= base_url('assets/uploads/produk/' . $s['gambar_produk']) ?>"
                         class="h-10 w-10 rounded-lg object-cover border border-gray-100" alt="">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800"><?= esc($s['nama_produk']) ?></p>
                        <p class="text-xs text-gray-400">
                            <?= $s['nomor_resi'] ? 'Resi: ' . esc($s['nomor_resi']) : 'Menunggu resi dari petani' ?>
                        </p>
                    </div>
                    <span class="<?= badge_status($s['status_pengiriman']) ?>"><?= esc($s['status_pengiriman']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>