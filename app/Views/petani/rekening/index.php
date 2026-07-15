<?php
/**
 * @var array $rekening
 * @var array $microNominal
 * @var string $backUrl
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-3">
        <?php if (empty($rekening)): ?>
            <div class="card flex flex-col items-center justify-center py-16 text-center">
                <?= heroicon('credit-card', 'w-12 h-12 text-gray-300') ?>
                <p class="mt-3 text-sm font-medium text-gray-500">Anda belum menambahkan rekening</p>
                <p class="text-xs text-gray-400">Tambahkan rekening di panel sebelah untuk mulai bertransaksi.</p>
            </div>
        <?php else: ?>
            <?php foreach ($rekening as $r): ?>
                <div class="card" x-data="{ confirmDelete: false, nominal: '' }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="rounded-lg bg-primary-50 p-2.5 text-primary-700"><?= heroicon('credit-card', 'w-5 h-5') ?></span>
                            <div>
                                <p class="font-semibold text-gray-800"><?= esc($r['nama_bank']) ?> <span class="text-xs text-gray-400 uppercase">(<?= $r['tipe'] === 'bank' ? 'Bank' : 'E-Wallet' ?>)</span></p>
                                <p class="text-sm text-gray-500"><?= esc($r['nomor_rekening']) ?> a.n. <?= esc($r['atas_nama']) ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="<?= badge_status($r['status_validasi']) ?> capitalize"><?= esc($r['status_validasi']) ?></span>
                            <button @click="confirmDelete = true" class="text-red-500 hover:text-red-700">
                                <?= heroicon('trash', 'w-4 h-4') ?>
                            </button>
                        </div>
                    </div>

                    <?php if ($r['status_validasi'] === 'pending'): ?>
                        <div class="mt-4 rounded-lg bg-amber-50 border border-amber-200 p-4">
                            <p class="text-xs text-amber-700 mb-2">
                                <strong>Simulasi Micro-Transfer:</strong> Sistem telah "mengirim" nominal verifikasi sebesar
                                <strong>Rp<?= $microNominal[$r['id_rekening']] ?? '-' ?></strong> ke rekening ini.
                                Masukkan nominal tersebut untuk mengonfirmasi kepemilikan rekening.
                                <span class="italic">(Dalam sistem produksi nyata, nominal ini disembunyikan dan Anda perlu mengecek mutasi rekening sendiri — di sini ditampilkan langsung untuk keperluan simulasi/demo.)</span>
                            </p>
                            <?= form_open($backUrl . '/verifikasi/' . $r['id_rekening'], ['class' => 'flex gap-2']) ?>
                                <input type="number" name="nominal" required placeholder="Masukkan nominal (Rp)"
                                       class="input-field text-sm flex-1">
                                <button type="submit" class="btn-primary text-sm px-4">Konfirmasi</button>
                            <?= form_close() ?>
                        </div>
                    <?php endif; ?>

                    <div x-show="confirmDelete" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4">
                        <div @click.outside="confirmDelete = false" class="w-full max-w-sm rounded-xl bg-white p-5 shadow-2xl">
                            <p class="text-sm font-semibold text-gray-800">Hapus rekening ini?</p>
                            <p class="mt-1 text-xs text-gray-500">"<?= esc($r['nama_bank']) ?> - <?= esc($r['nomor_rekening']) ?>" akan dihapus permanen.</p>
                            <div class="mt-4 flex justify-end gap-2">
                                <button @click="confirmDelete = false" class="btn-secondary text-xs px-3 py-1.5">Batal</button>
                                <?= form_open($backUrl . '/hapus/' . $r['id_rekening']) ?>
                                    <button type="submit" class="btn-danger text-xs px-3 py-1.5">Ya, Hapus</button>
                                <?= form_close() ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Form Tambah -->
    <div class="card h-fit" x-data="{ tipe: 'bank' }">
        <h3 class="text-base font-semibold text-gray-800 mb-4">Tambah Rekening Baru</h3>
        <?= form_open($backUrl . '/simpan') ?>
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center justify-center gap-2 rounded-lg border px-3 py-2 text-sm cursor-pointer"
                           :class="tipe === 'bank' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 text-gray-500'">
                        <input type="radio" name="tipe" value="bank" x-model="tipe" class="hidden">
                        Bank
                    </label>
                    <label class="flex items-center justify-center gap-2 rounded-lg border px-3 py-2 text-sm cursor-pointer"
                           :class="tipe === 'e_wallet' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 text-gray-500'">
                        <input type="radio" name="tipe" value="e_wallet" x-model="tipe" class="hidden">
                        E-Wallet
                    </label>
                </div>

                <input type="text" name="nama_bank" required class="input-field text-sm"
                       :placeholder="tipe === 'bank' ? 'Nama Bank (mis. BCA, BRI)' : 'Nama E-Wallet (mis. OVO, Dana)'">
                <input type="text" name="nomor_rekening" required class="input-field text-sm" placeholder="Nomor Rekening / No. HP">
                <input type="text" name="atas_nama" required class="input-field text-sm" placeholder="Atas Nama">

                <button type="submit" class="btn-primary w-full text-sm">Tambah Rekening</button>
            </div>
        <?= form_close() ?>

        <p class="mt-4 text-xs text-gray-400">
            Rekening perlu diverifikasi (via simulasi micro-transfer atau oleh Admin) sebelum bisa digunakan untuk menerima/melakukan pembayaran.
        </p>
    </div>
</div>

<?= $this->endSection() ?>