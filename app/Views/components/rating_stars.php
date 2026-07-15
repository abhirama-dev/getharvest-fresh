<?php
/**
 * @var float $rata (0-5)
 * @var int   $jumlah
 */
$rata   = $rata ?? 0;
$jumlah = $jumlah ?? 0;
?>
<div class="flex items-center gap-1">
    <?php for ($i = 1; $i <= 5; $i++): ?>
        <span class="<?= $i <= round($rata) ? 'text-gold-500' : 'text-gray-200' ?>">
            <?= heroicon('star', 'w-4 h-4 fill-current') ?>
        </span>
    <?php endfor; ?>
    <span class="ml-1 text-xs text-gray-500">
        <?= $rata > 0 ? number_format($rata, 1) : '-' ?> (<?= $jumlah ?>)
    </span>
</div>