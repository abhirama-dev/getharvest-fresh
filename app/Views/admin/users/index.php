<?php
/**
 * @var array $users
 * @var string $filterRole
 * @var string $filterQ
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card mb-4">
    <?= form_open('admin/users', ['method' => 'get', 'class' => 'flex flex-col sm:flex-row gap-3']) ?>
        <input type="text" name="q" value="<?= esc($filterQ) ?>" placeholder="Cari nama atau email..." class="input-field flex-1">
        <select name="role" class="input-field sm:w-48">
            <option value="">Semua Role</option>
            <option value="petani" <?= $filterRole === 'petani' ? 'selected' : '' ?>>Petani</option>
            <option value="pedagang" <?= $filterRole === 'pedagang' ? 'selected' : '' ?>>Pedagang</option>
        </select>
        <button type="submit" class="btn-primary sm:w-32">Cari</button>
    <?= form_close() ?>
</div>

<div class="card overflow-x-auto p-0">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50 text-left text-gray-500">
                <th class="px-5 py-3 font-medium">Nama</th>
                <th class="px-5 py-3 font-medium">Email</th>
                <th class="px-5 py-3 font-medium">Role</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Bergabung</th>
                <th class="px-5 py-3 font-medium text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach ($users as $u): ?>
                <tr>
                    <td class="px-5 py-3 font-medium text-gray-800">
                        <?= esc($u['nama_lengkap']) ?>
                        <?php if ($u['is_premium']): ?>
                            <span class="badge-warning !bg-gold-100 !text-gold-600 ml-1">Premium</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3 text-gray-600"><?= esc($u['email']) ?></td>
                    <td class="px-5 py-3 capitalize text-gray-600"><?= esc($u['role']) ?></td>
                    <td class="px-5 py-3">
                        <?php if ($u['role'] === 'pedagang'): ?>
                            <span class="<?= badge_status($u['status_verifikasi']) ?>"><?= esc($u['status_verifikasi']) ?></span>
                        <?php else: ?>
                            <span class="badge-success">aktif</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3 text-gray-500"><?= time_ago($u['created_at']) ?></td>
                    <td class="px-5 py-3 text-right">
                        <a href="<?= base_url('admin/users/detail/' . $u['id_user']) ?>" class="text-xs font-medium text-primary-700 hover:underline">Lihat Detail</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>