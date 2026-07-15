<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div x-data="{
        namaProduk: '<?= old('nama_produk') ?>',
        kategori: '<?= old('kategori') ?>',
        hargaPerKg: '<?= old('harga_per_kg') ?>',
        stokKg: '<?= old('stok_kg') ?>',
        statusPanen: '<?= old('status_panen', 'Siap Jual') ?>',
        grade: '<?= old('grade', 'Biasa') ?>',
        gambarPreview: null,
        sertifikatName: null,
        submitting: false,

        get formValid() {
            return this.namaProduk.length >= 3 &&
                   this.kategori.length > 0 &&
                   this.hargaPerKg > 0 &&
                   this.stokKg > 0 &&
                   (this.statusPanen !== 'Pre-Order' || this.tanggalPanenFilled);
        },
        tanggalPanenFilled: <?= old('status_panen') === 'Pre-Order' ? 'true' : 'false' ?>,

        previewGambar(e) {
            const file = e.target.files[0];
            if (file) this.gambarPreview = URL.createObjectURL(file);
        },
        onSertifikat(e) {
            const file = e.target.files[0];
            this.sertifikatName = file ? file.name : null;
        }
     }" class="max-w-3xl">

    <div class="card">
        <?= form_open_multipart('petani/produk/simpan', ['@submit' => 'submitting = true']) ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
                <label class="label-field">Nama Produk</label>
                <input type="text" name="nama_produk" x-model="namaProduk" required
                       class="input-field" placeholder="Contoh: Cabai Merah Keriting">
            </div>

            <div>
                <label class="label-field">Kategori</label>
                <select name="kategori" x-model="kategori" required class="input-field">
                    <option value="">Pilih kategori</option>
                    <option value="Sayuran">Sayuran</option>
                    <option value="Buah">Buah</option>
                    <option value="Padi & Beras">Padi &amp; Beras</option>
                    <option value="Palawija">Palawija</option>
                    <option value="Rempah">Rempah</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div>
                <label class="label-field">Grade Kualitas</label>
                <select name="grade" x-model="grade" required class="input-field">
                    <option value="Biasa">Biasa</option>
                    <option value="A">Grade A</option>
                    <option value="B">Grade B</option>
                    <option value="C">Grade C</option>
                    <option value="Organik">Organik</option>
                </select>
            </div>

            <div>
                <label class="label-field">Harga per Kg (Rp)</label>
                <input type="number" name="harga_per_kg" x-model="hargaPerKg" min="1" required
                       class="input-field" placeholder="15000">
            </div>

            <div>
                <label class="label-field">Stok (Kg)</label>
                <input type="number" name="stok_kg" x-model="stokKg" min="1" required
                       class="input-field" placeholder="500">
            </div>

            <div class="sm:col-span-2">
                <label class="label-field">Status Panen</label>
                <div class="flex gap-3">
                    <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2.5 cursor-pointer flex-1"
                           :class="statusPanen === 'Siap Jual' ? 'border-primary-500 bg-primary-50' : ''">
                        <input type="radio" name="status_panen" value="Siap Jual" x-model="statusPanen" class="text-primary-700">
                        <span class="text-sm">Siap Jual (stok tersedia sekarang)</span>
                    </label>
                    <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2.5 cursor-pointer flex-1"
                           :class="statusPanen === 'Pre-Order' ? 'border-primary-500 bg-primary-50' : ''">
                        <input type="radio" name="status_panen" value="Pre-Order" x-model="statusPanen" class="text-primary-700">
                        <span class="text-sm">Pre-Order (belum panen)</span>
                    </label>
                </div>
            </div>

            <div class="sm:col-span-2" x-show="statusPanen === 'Pre-Order'" x-cloak
                 @change="tanggalPanenFilled = $event.target.value.length > 0">
                <label class="label-field">Estimasi Tanggal Panen</label>
                <input type="date" name="tanggal_estimasi_panen" value="<?= old('tanggal_estimasi_panen') ?>"
                       class="input-field" min="<?= date('Y-m-d') ?>"
                       @input="tanggalPanenFilled = $event.target.value.length > 0">
            </div>

            <div>
                <label class="label-field">Gambar Produk <span class="text-red-500">*</span></label>
                <input type="file" name="gambar_produk" accept="image/*" required @change="previewGambar($event)"
                       class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-700 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-800">
                <p class="mt-1 text-xs text-gray-400">Format JPG/PNG, maksimal 2MB.</p>
                <template x-if="gambarPreview">
                    <img :src="gambarPreview" class="mt-3 h-40 w-full rounded-lg object-cover border border-gray-200">
                </template>
            </div>

            <div>
                <label class="label-field">Sertifikat (opsional)</label>
                <input type="file" name="sertifikat" accept="image/*,.pdf" @change="onSertifikat($event)"
                       class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-700 hover:file:bg-gray-200">
                <p class="mt-1 text-xs text-gray-400">Sertifikat organik/mutu bila ada (JPG/PNG/PDF).</p>
                <p x-show="sertifikatName" x-text="sertifikatName" class="mt-2 text-xs text-primary-700 font-medium"></p>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="<?= base_url('petani/produk') ?>" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary" :disabled="!formValid || submitting">
                <span x-show="!submitting">Simpan Produk</span>
                <span x-show="submitting" x-cloak>Menyimpan...</span>
            </button>
        </div>

        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>