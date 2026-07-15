<?php if (session()->getFlashdata('success')): ?>
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         x-transition
         class="mb-4 flex items-start gap-3 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
        <?= heroicon('check-circle', 'w-5 h-5 flex-shrink-0 text-green-600') ?>
        <span class="flex-1"><?= esc(session()->getFlashdata('success')) ?></span>
        <button @click="show = false" class="text-green-600 hover:text-green-800">
            <?= heroicon('x-mark', 'w-4 h-4') ?>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div x-data="{ show: true }" x-show="show" x-transition
         class="mb-4 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
        <?= heroicon('x-circle', 'w-5 h-5 flex-shrink-0 text-red-600') ?>
        <span class="flex-1"><?= esc(session()->getFlashdata('error')) ?></span>
        <button @click="show = false" class="text-red-600 hover:text-red-800">
            <?= heroicon('x-mark', 'w-4 h-4') ?>
        </button>
    </div>
<?php endif; ?>

<?php if (isset($validation) && $validation->getErrors()): ?>
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
        <p class="font-semibold mb-1">Terjadi kesalahan input:</p>
        <ul class="list-disc list-inside space-y-0.5">
            <?php foreach ($validation->getErrors() as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>