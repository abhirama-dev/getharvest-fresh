<?php
/**
 * @var bool $isPremium
 * @var array|null $subscriptionAktif
 * @var array $rekeningAdmin
 * @var array $riwayat
 * @var bool $adaPending
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php if ($isPremium): ?>
    <div class="card border-primary-200 bg-primary-50/50 flex items-center gap-4 mb-6">
        <span class="rounded-lg bg-primary-700 p-3 text-white"><?= heroicon('star', 'w-6 h-6') ?></span>
        <div>
            <p class="font-semibold text-gray-800">Akun Anda sudah Premium 🎉</p>
            <?php if ($subscriptionAktif): ?>
                <p class="text-sm text-gray-500">
                    Paket <?= esc($subscriptionAktif['tipe']) ?>, aktif hingga
                    <?= date('d M Y', strtotime($subscriptionAktif['tanggal_akhir'])) ?>.
                </p>
            <?php endif; ?>
        </div>
        <a href="<?= base_url('petani/kalkulator') ?>" class="btn-primary ml-auto">Buka Kalkulator Laba</a>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Paket -->
    <div class="lg:col-span-2 space-y-6">
        <?php if (! $isPremium): ?>
            <?php if ($adaPending): ?>
                <div class="card border-amber-200 bg-amber-50/50">
                    <p class="text-sm text-amber-700">
                        Anda memiliki pengajuan upgrade yang sedang menunggu verifikasi Admin. Silakan tunggu konfirmasi sebelum mengajukan ulang.
                    </p>
                </div>
            <?php else: ?>
                <div x-data="{ tipe: 'bulanan', showForm: false, fotoPreview: null, submitting: false }">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <label class="card cursor-pointer transition" :class="tipe === 'bulanan' ? 'ring-2 ring-primary-600' : ''">
                            <input type="radio" x-model="tipe" value="bulanan" class="hidden">
                            <p class="text-sm text-gray-500">Paket Bulanan</p>
                            <p class="mt-1 text-2xl font-bold text-gray-800"><?= format_rupiah(25000) ?><span class="text-sm font-normal text-gray-400">/bulan</span></p>
                            <p class="mt-2 text-xs text-gray-400">Cocok untuk mencoba fitur premium</p>
                        </label>
                        <label class="card cursor-pointer transition relative" :class="tipe === 'tahunan' ? 'ring-2 ring-primary-600' : ''">
                            <span class="absolute -top-2 right-3 badge-warning !bg-gold-100 !text-gold-600">Hemat 20%</span>
                            <input type="radio" x-model="tipe" value="tahunan" class="hidden">
                            <p class="text-sm text-gray-500">Paket Tahunan</p>
                            <p class="mt-1 text-2xl font-bold text-gray-800"><?= format_rupiah(240000) ?><span class="text-sm font-normal text-gray-400">/tahun</span></p>
                            <p class="mt-2 text-xs text-gray-400">Paling hemat untuk penggunaan jangka panjang</p>
                        </label>
                    </div>

                    <div class="card">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Transfer ke Rekening Platform</h3>
                        <?php if (empty($rekeningAdmin)): ?>
                            <p class="text-sm text-gray-400">Rekening platform belum tersedia, hubungi Admin.</p>
                        <?php else: ?>
                            <div class="space-y-2 mb-4">
                                <?php foreach ($rekeningAdmin as $ra): ?>
                                    <div class="flex items-center gap-3 rounded-lg border border-gray-100 px-3 py-2">
                                        <span class="rounded-lg bg-gray-100 p-2 text-gray-600"><?= heroicon('credit-card', 'w-4 h-4') ?></span>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800"><?= esc($ra['nama_bank']) ?></p>
                                            <p class="text-xs text-gray-500"><?= esc($ra['nomor_rekening']) ?> a.n. <?= esc($ra['atas_nama']) ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?= form_open_multipart('petani/upgrade/simpan', ['@submit' => 'submitting = true']) ?>
                            <input type="hidden" name="tipe" x-model="tipe">
                            <label class="label-field">Upload Bukti Transfer</label>
                            <input type="file" name="bukti_bayar" accept="image/*" required
                                   @change="fotoPreview = URL.createObjectURL($event.target.files[0])"
                                   class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-700 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-800">
                            <template x-if="fotoPreview">
                                <img :src="fotoPreview" class="mt-3 h-40 w-full rounded-lg object-cover border border-gray-200">
                            </template>
                            <button type="submit" class="btn-primary w-full mt-4" :disabled="submitting">
                                <span x-show="!submitting">Ajukan Upgrade</span>
                                <span x-show="submitting" x-cloak>Mengirim...</span>
                            </button>
                        <?= form_close() ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Riwayat -->
    <div class="card h-fit">
        <h3 class="text-sm font-semibold text-gray-800 mb-3">Riwayat Pengajuan</h3>
        <?php if (empty($riwayat)): ?>
            <p class="text-xs text-gray-400 text-center py-6">Belum ada pengajuan upgrade.</p>
        <?php else: ?>
            <div class="space-y-2">
                <?php foreach ($riwayat as $r): ?>
                    <div class="rounded-lg border border-gray-100 px-3 py-2">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-800 capitalize"><?= esc($r['tipe']) ?></p>
                            <span class="<?= badge_status($r['status']) ?>"><?= esc($r['status']) ?></span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1"><?= time_ago($r['tanggal_permintaan']) ?></p>
                        <?php if ($r['status'] === 'Ditolak' && $r['alasan_tolak']): ?>
                            <p class="text-xs text-red-600 mt-1">Alasan: <?= esc($r['alasan_tolak']) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>