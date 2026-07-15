<?php
/**
 * @var string $judul
 * @var string $pesan
 */
?>
<div style="font-family: Inter, Arial, sans-serif; max-width: 480px; margin: auto;">
    <div style="background:#2e7d32; padding:20px; border-radius:12px 12px 0 0;">
        <h2 style="color:#fff; margin:0;">GetHarvest</h2>
    </div>
    <div style="border:1px solid #eee; border-top:none; padding:24px; border-radius:0 0 12px 12px;">
        <h3 style="color:#2e7d32; margin-top:0;"><?= esc($judul) ?></h3>
        <p style="color:#444; line-height:1.6;"><?= esc($pesan) ?></p>
        <p style="color:#999; font-size:12px; margin-top:24px;">Email ini dikirim otomatis oleh sistem GetHarvest.</p>
    </div>
</div>