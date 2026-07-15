<?php
/**
 * @var int   $totalModal
 * @var array $pengeluaran
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

    <!-- Kalkulator -->
    <div class="lg:col-span-3">
        <div class="card"
             x-data="{
                modal: <?= (int) $totalModal ?>,
                pakaiOtomatis: true,
                estimasiPanen: 0,
                targetUntung: 20,

                get hargaPokok() {
                    return this.estimasiPanen > 0 ? Math.round(this.modal / this.estimasiPanen) : 0;
                },
                get hargaJual() {
                    return Math.round(this.hargaPokok * (1 + (this.targetUntung / 100)));
                },
                get estimasiLaba() {
                    return Math.round(this.modal * (this.targetUntung / 100));
                },
                formatRupiah(angka) {
                    return 'Rp' + Number(angka).toLocaleString('id-ID');
                }
             }">
            <div class="flex items-center gap-2 mb-5">
                <span class="rounded-lg bg-gold-100 p-2 text-gold-600"><?= heroicon('calculator', 'w-5 h-5') ?></span>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Kalkulator Laba</h3>
                    <p class="text-xs text-gray-500">Fitur khusus Petani Premium</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="label-field mb-0">Total Modal (Rp)</label>
                        <label class="flex items-center gap-1.5 text-xs text-gray-500">
                            <input type="checkbox" x-model="pakaiOtomatis" @change="if (pakaiOtomatis) modal = <?= (int) $totalModal ?>"
                                   class="rounded text-primary-700">
                            Pakai total pengeluaran otomatis
                        </label>
                    </div>
                    <input type="number" x-model.number="modal" :readonly="pakaiOtomatis"
                           class="input-field" :class="pakaiOtomatis ? 'bg-gray-50 text-gray-500' : ''">
                    <p class="mt-1 text-xs text-gray-400">Total pengeluaran tercatat: <?= format_rupiah($totalModal) ?></p>
                </div>

                <div>
                    <label class="label-field">Estimasi Hasil Panen (Kg)</label>
                    <input type="number" x-model.number="estimasiPanen" min="0" class="input-field" placeholder="Contoh: 500">
                </div>

                <div>
                    <label class="label-field">
                        Target Keuntungan: <span class="font-semibold text-primary-700" x-text="targetUntung + '%'"></span>
                    </label>
                    <input type="range" x-model.number="targetUntung" min="5" max="100" step="5" class="w-full accent-primary-700">
                </div>
            </div>

            <!-- Hasil -->
            <div class="mt-6 rounded-xl bg-primary-50 border border-primary-100 p-5">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-xs text-gray-500">Harga Pokok / Kg</p>
                        <p class="mt-1 text-lg font-bold text-gray-800" x-text="estimasiPanen > 0 ? formatRupiah(hargaPokok) : '-'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Rekomendasi Harga Jual</p>
                        <p class="mt-1 text-lg font-bold text-primary-700" x-text="estimasiPanen > 0 ? formatRupiah(hargaJual) : '-'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Estimasi Laba</p>
                        <p class="mt-1 text-lg font-bold text-gold-600" x-text="formatRupiah(estimasiLaba)"></p>
                    </div>
                </div>
                <p x-show="estimasiPanen === 0" x-cloak class="mt-3 text-center text-xs text-amber-600">
                    Isi estimasi hasil panen untuk melihat harga pokok & rekomendasi harga jual.
                </p>
            </div>
        </div>
    </div>

    <!-- Riwayat Pengeluaran -->
    <div class="lg:col-span-2 space-y-4">
        <div class="card" x-data="{ kategori: '', keterangan: '', nominal: '', submitting: false }">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Catat Pengeluaran</h3>
            <?= form_open('petani/kalkulator/pengeluaran', ['@submit' => 'submitting = true']) ?>
                <div class="space-y-3">
                    <select name="kategori" x-model="kategori" required class="input-field text-sm">
                        <option value="">Pilih kategori</option>
                        <option value="Bibit">Bibit</option>
                        <option value="Pupuk">Pupuk</option>
                        <option value="Pestisida">Pestisida</option>
                        <option value="Tenaga Kerja">Tenaga Kerja</option>
                        <option value="Sewa Lahan">Sewa Lahan</option>
                        <option value="Transportasi">Transportasi</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    <input type="text" name="keterangan" x-model="keterangan" class="input-field text-sm" placeholder="Keterangan (opsional)">
                    <input type="number" name="nominal" x-model="nominal" min="1" required class="input-field text-sm" placeholder="Nominal (Rp)">
                    <button type="submit" class="btn-primary w-full text-sm" :disabled="!kategori || !nominal || submitting">
                        <span x-show="!submitting">Tambah Pengeluaran</span>
                        <span x-show="submitting" x-cloak>Menyimpan...</span>
                    </button>
                </div>
            <?= form_close() ?>
        </div>

        <div class="card">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Riwayat Pengeluaran</h3>
            <?php if (empty($pengeluaran)): ?>
                <p class="text-xs text-gray-400 text-center py-6">Belum ada pengeluaran tercatat.</p>
            <?php else: ?>
                <div class="space-y-2 max-h-80 overflow-y-auto">
                    <?php foreach ($pengeluaran as $p): ?>
                        <div class="flex items-center justify-between rounded-lg border border-gray-100 px-3 py-2" x-data="{ confirmDelete: false }">
                            <div>
                                <p class="text-sm font-medium text-gray-800"><?= esc($p['kategori']) ?></p>
                                <p class="text-xs text-gray-400"><?= esc($p['keterangan'] ?: '-') ?> &middot; <?= time_ago($p['tanggal']) ?></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-gray-700"><?= format_rupiah($p['nominal']) ?></span>
                                <button @click="confirmDelete = true" class="text-red-500 hover:text-red-700">
                                    <?= heroicon('trash', 'w-4 h-4') ?>
                                </button>
                            </div>
                            <div x-show="confirmDelete" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4">
                                <div @click.outside="confirmDelete = false" class="w-full max-w-sm rounded-xl bg-white p-5 shadow-2xl">
                                    <p class="text-sm font-semibold text-gray-800">Hapus catatan pengeluaran ini?</p>
                                    <div class="mt-4 flex justify-end gap-2">
                                        <button @click="confirmDelete = false" class="btn-secondary text-xs px-3 py-1.5">Batal</button>
                                        <?= form_open('petani/kalkulator/pengeluaran/hapus/' . $p['id_pengeluaran']) ?>
                                            <button type="submit" class="btn-danger text-xs px-3 py-1.5">Hapus</button>
                                        <?= form_close() ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>