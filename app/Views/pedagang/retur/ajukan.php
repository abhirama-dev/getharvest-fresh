<?php
/**
 * @var array $pesanan
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-2xl" x-data="{ alasan: '', fotoPreview: null, submitting: false }">
    <div class="card mb-4 flex items-center gap-3">
        <img src="<?= base_url('assets/uploads/produk/' . $pesanan['gambar_produk']) ?>"
             class="h-14 w-14 rounded-lg object-cover border border-gray-100" alt="">
        <div>
            <p class="font-semibold text-gray-800"><?= esc($pesanan['nama_produk']) ?></p>
            <p class="text-sm text-gray-500"><?= $pesanan['jumlah_kg'] ?> kg &middot; <?= format_rupiah($pesanan['total_harga']) ?></p>
        </div>
    </div>

    <div class="card">
        <?= form_open_multipart('pedagang/retur/simpan/' . $pesanan['id_pesanan'], ['@submit' => 'submitting = true']) ?>
            <div class="space-y-4">
                <div>
                    <label class="label-field">Alasan Retur</label>
                    <textarea name="alasan" x-model="alasan" required rows="4" class="input-field"
                              placeholder="Jelaskan ketidaksesuaian barang yang Anda terima (min. 10 karakter)"></textarea>
                </div>

                <div>
                    <label class="label-field">Foto Bukti</label>
                    <input type="file" name="foto_bukti" accept="image/*" required
                           @change="fotoPreview = URL.createObjectURL($event.target.files[0])"
                           class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-700 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-800">
                    <template x-if="fotoPreview">
                        <img :src="fotoPreview" class="mt-3 h-48 w-full rounded-lg object-cover border border-gray-200">
                    </template>
                </div>

                <div class="rounded-lg bg-amber-50 border border-amber-200 p-3 text-xs text-amber-700">
                    Dana pembayaran Anda akan tetap tertahan di escrow sampai petani menanggapi pengajuan ini.
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="<?= base_url('pedagang/pesanan') ?>" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-danger" :disabled="alasan.length < 10 || submitting">
                    <span x-show="!submitting">Ajukan Retur</span>
                    <span x-show="submitting" x-cloak>Mengirim...</span>
                </button>
            </div>
        <?= form_close() ?>
    </div>
</div>
<?= $this->endSection() ?>