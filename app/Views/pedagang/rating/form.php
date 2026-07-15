<?php
/**
 * @var array $pesanan
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-lg" x-data="{ rating: 0, hover: 0, ulasan: '', submitting: false }">
    <div class="card mb-4 flex items-center gap-3">
        <img src="<?= base_url('assets/uploads/produk/' . $pesanan['gambar_produk']) ?>"
             class="h-14 w-14 rounded-lg object-cover border border-gray-100" alt="">
        <div>
            <p class="font-semibold text-gray-800"><?= esc($pesanan['nama_produk']) ?></p>
            <p class="text-sm text-gray-500">Petani: <?= esc($pesanan['nama_petani']) ?></p>
        </div>
    </div>

    <div class="card">
        <?= form_open('pedagang/rating/simpan/' . $pesanan['id_pesanan'], ['@submit' => 'submitting = true']) ?>
            <label class="label-field mb-2">Bagaimana kualitas produk & pelayanan petani?</label>
            <div class="flex items-center gap-1 mb-4">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <button type="button" @click="rating = <?= $i ?>"
                            @mouseenter="hover = <?= $i ?>" @mouseleave="hover = 0"
                            :class="(hover || rating) >= <?= $i ?> ? 'text-gold-500' : 'text-gray-200'"
                            class="transition-colors">
                        <?= heroicon('star', 'w-9 h-9 fill-current') ?>
                    </button>
                <?php endfor; ?>
            </div>
            <input type="hidden" name="rating" x-model="rating">

            <label class="label-field">Ulasan (opsional)</label>
            <textarea name="ulasan" x-model="ulasan" rows="4" maxlength="1000" class="input-field"
                      placeholder="Ceritakan pengalaman Anda bertransaksi dengan petani ini..."></textarea>
            <p class="mt-1 text-xs text-gray-400 text-right" x-text="ulasan.length + '/1000'"></p>

            <div class="mt-4 flex justify-end gap-3">
                <a href="<?= base_url('pedagang/pesanan') ?>" class="btn-secondary">Lewati</a>
                <button type="submit" class="btn-primary" :disabled="rating === 0 || submitting">
                    <span x-show="!submitting">Kirim Penilaian</span>
                    <span x-show="submitting" x-cloak>Mengirim...</span>
                </button>
            </div>
        <?= form_close() ?>
    </div>
</div>
<?= $this->endSection() ?>