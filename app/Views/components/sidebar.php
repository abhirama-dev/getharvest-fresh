<?php
$user    = logged_user();
$role    = $user['role'] ?? 'petani';
$isPremium = (bool) ($user['is_premium'] ?? false);
$current = current_url(true)->getPath();

function isActive(string $needle, string $current): string
{
    return str_contains($current, $needle) ? 'active' : '';
}

$menus = [
    'petani' => [
        ['label' => 'Dashboard',        'url' => '/petani/dashboard',    'icon' => 'home',        'key' => 'petani/dashboard'],
        ['label' => 'Etalase Produk',   'url' => '/petani/produk',       'icon' => 'box',         'key' => 'petani/produk'],
        ['label' => 'Pesanan Masuk',    'url' => '/petani/pesanan',      'icon' => 'clipboard',   'key' => 'petani/pesanan'],
        ['label' => 'Negosiasi',        'url' => '/petani/nego',         'icon' => 'chat',        'key' => 'petani/nego'],
        ['label' => 'Retur',            'url' => '/petani/retur',        'icon' => 'arrow-path',  'key' => 'petani/retur'],
        ['label' => 'Kalkulator Laba',  'url' => '/petani/kalkulator',   'icon' => 'calculator',  'key' => 'petani/kalkulator', 'premium' => true],
        ['label' => 'Upgrade Premium',  'url' => '/petani/upgrade',      'icon' => 'star',        'key' => 'petani/upgrade'],
        ['label' => 'Escrow Saya',      'url' => '/petani/escrow',       'icon' => 'banknotes',   'key' => 'petani/escrow'],
        ['label' => 'Kelola Rekening',  'url' => '/petani/rekening',     'icon' => 'credit-card', 'key' => 'petani/rekening'],
    ],
    'pedagang' => [
        ['label' => 'Dashboard',        'url' => '/pedagang/dashboard',  'icon' => 'home',         'key' => 'pedagang/dashboard'],
        ['label' => 'Katalog Produk',   'url' => '/pedagang/katalog',    'icon' => 'shopping-bag', 'key' => 'pedagang/katalog'],
        ['label' => 'Negosiasi',        'url' => '/pedagang/nego',       'icon' => 'chat',         'key' => 'pedagang/nego'],
        ['label' => 'Riwayat Belanja',  'url' => '/pedagang/pesanan',    'icon' => 'clipboard',    'key' => 'pedagang/pesanan'],
        ['label' => 'Lacak Pengiriman', 'url' => '/pedagang/lacak',      'icon' => 'truck',        'key' => 'pedagang/lacak'],
        ['label' => 'Kelola Rekening',  'url' => '/pedagang/rekening',   'icon' => 'credit-card',  'key' => 'pedagang/rekening'],
    ],
    'admin' => [
        ['label' => 'Dashboard',           'url' => '/admin/dashboard',      'icon' => 'home',         'key' => 'admin/dashboard'],
        ['label' => 'Verifikasi Pedagang', 'url' => '/admin/verifikasi',     'icon' => 'user-plus',    'key' => 'admin/verifikasi'],
        ['label' => 'Kelola Pengguna',     'url' => '/admin/users',          'icon' => 'users',        'key' => 'admin/users'],
        ['label' => 'Upgrade Premium',     'url' => '/admin/upgrade',        'icon' => 'star',         'key' => 'admin/upgrade'],
        ['label' => 'Validasi Rekening',   'url' => '/admin/rekening',       'icon' => 'credit-card',  'key' => 'admin/rekening'],
        ['label' => 'Monitoring Escrow',   'url' => '/admin/escrow',         'icon' => 'banknotes',    'key' => 'admin/escrow'],
        ['label' => 'Data Transaksi',      'url' => '/admin/transaksi',      'icon' => 'chart-bar',    'key' => 'admin/transaksi'],
        ['label' => 'Rekening Platform',   'url' => '/admin/rekening-admin', 'icon' => 'shield-check', 'key' => 'admin/rekening-admin'],
    ],
];

$menuItems = $menus[$role] ?? [];
?>
<aside
    x-data="{ collapsed: localStorage.getItem('gh_sidebar_collapsed') === 'true' }"
    x-init="$watch('collapsed', val => localStorage.setItem('gh_sidebar_collapsed', val))"
    :class="collapsed ? 'lg:w-20' : 'lg:w-64'"
    class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-white border-r border-gray-200 transition-all duration-300 transform -translate-x-full lg:translate-x-0"
    :style="{ transform: (mobileOpen || window.innerWidth >= 1024) ? 'translateX(0)' : 'translateX(-100%)' }"
>
    <!-- Logo -->
    <div class="flex h-16 items-center justify-between border-b border-gray-100 px-4">
        <a href="/" class="flex items-center gap-2 overflow-hidden">
            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-primary-700 text-white font-bold text-lg">G</div>
            <span x-show="!collapsed" x-transition class="text-lg font-bold text-gray-800 whitespace-nowrap">GetHarvest</span>
        </a>
        <button @click="collapsed = !collapsed" class="hidden lg:flex text-gray-400 hover:text-gray-600">
            <span :class="collapsed ? 'rotate-180' : ''" class="transition-transform">
                <?= heroicon('chevron-left', 'w-5 h-5') ?>
            </span>
        </button>
        <button @click="mobileOpen = false" class="lg:hidden text-gray-400 hover:text-gray-600">
            <?= heroicon('x-mark', 'w-6 h-6') ?>
        </button>
    </div>

    <!-- User info -->
    <div class="flex items-center gap-3 border-b border-gray-100 px-4 py-4">
        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-primary-100 text-primary-700 font-semibold uppercase">
            <?= esc(mb_substr($user['nama_lengkap'] ?? 'U', 0, 1)) ?>
        </div>
        <div x-show="!collapsed" x-transition class="overflow-hidden">
            <p class="truncate text-sm font-semibold text-gray-800"><?= esc($user['nama_lengkap'] ?? '-') ?></p>
            <p class="flex items-center gap-1 text-xs text-gray-500 capitalize">
                <?= esc($role) ?>
                <?php if ($isPremium): ?>
                    <span class="badge-warning !bg-gold-100 !text-gold-600">Premium</span>
                <?php endif; ?>
            </p>
        </div>
    </div>

    <!-- Menu -->
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        <?php foreach ($menuItems as $item): ?>
            <?php $locked = (! empty($item['premium']) && ! $isPremium); ?>
            <a href="<?= $locked ? '/' . $role . '/upgrade' : base_url($item['url']) ?>"
               class="sidebar-link <?= isActive($item['key'], $current) ?> <?= $locked ? 'opacity-60' : '' ?>"
               title="<?= esc($item['label']) ?>">
                <?= heroicon($item['icon'], 'w-5 h-5 flex-shrink-0') ?>
                <span x-show="!collapsed" x-transition class="whitespace-nowrap"><?= esc($item['label']) ?></span>
                <?php if ($locked): ?>
                    <span x-show="!collapsed" class="ml-auto text-gold-500">
                        <?= heroicon('star', 'w-4 h-4') ?>
                    </span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Logout -->
    <div class="border-t border-gray-100 p-3">
        <a href="<?= base_url('logout') ?>" class="sidebar-link hover:bg-red-50 hover:text-red-600" title="Keluar">
            <?= heroicon('logout', 'w-5 h-5 flex-shrink-0') ?>
            <span x-show="!collapsed" x-transition>Keluar</span>
        </a>
    </div>
</aside>

<!-- Overlay mobile -->
<div x-show="mobileOpen" x-transition.opacity @click="mobileOpen = false"
     class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden"></div>