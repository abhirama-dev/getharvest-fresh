<?php
$user  = logged_user();
$notif = recent_notifications($user['id_user'] ?? 0);
$unread = unread_notif_count($user['id_user'] ?? 0);
?>
<header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-gray-200 bg-white/90 backdrop-blur px-4 lg:px-6">
    <div class="flex items-center gap-3">
        <button @click="mobileOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700">
            <?= heroicon('menu', 'w-6 h-6') ?>
        </button>
        <div>
            <h1 class="text-lg font-semibold text-gray-800"><?= esc($pageTitle ?? 'Dashboard') ?></h1>
            <?php if (! empty($pageSubtitle)): ?>
                <p class="text-xs text-gray-500"><?= esc($pageSubtitle) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="flex items-center gap-2 sm:gap-4">
        <!-- Notifikasi -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="relative rounded-full p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                <?= heroicon('bell', 'w-6 h-6') ?>
                <?php if ($unread > 0): ?>
                    <span class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                        <?= $unread > 9 ? '9+' : $unread ?>
                    </span>
                <?php endif; ?>
            </button>

            <div x-show="open" @click.outside="open = false" x-transition
                 class="absolute right-0 mt-2 w-80 rounded-xl border border-gray-100 bg-white shadow-card-hover overflow-hidden"
                 style="display: none;">
                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                    <p class="text-sm font-semibold text-gray-800">Notifikasi</p>
                    <?php if ($unread > 0): ?>
                        <form action="<?= base_url('notifikasi/baca-semua') ?>" method="post">
                            <?= csrf_field() ?>
                            <button class="text-xs font-medium text-primary-700 hover:underline">Tandai semua dibaca</button>
                        </form>
                    <?php endif; ?>
                </div>
                <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                    <?php if (empty($notif)): ?>
                        <p class="px-4 py-6 text-center text-sm text-gray-400">Belum ada notifikasi</p>
                    <?php else: ?>
                        <?php foreach ($notif as $n): ?>
                            <div class="flex gap-3 px-4 py-3 hover:bg-gray-50 <?= ! $n['is_read'] ? 'bg-primary-50/40' : '' ?>">
                                <div class="mt-1 h-2 w-2 flex-shrink-0 rounded-full <?= ! $n['is_read'] ? 'bg-primary-600' : 'bg-transparent' ?>"></div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800"><?= esc($n['judul']) ?></p>
                                    <p class="text-xs text-gray-500 line-clamp-2"><?= esc($n['pesan']) ?></p>
                                    <p class="mt-1 text-[11px] text-gray-400"><?= time_ago($n['created_at']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Avatar -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-gray-100">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 text-primary-700 font-semibold uppercase text-sm">
                    <?= esc(mb_substr($user['nama_lengkap'] ?? 'U', 0, 1)) ?>
                </div>
                <?= heroicon('chevron-down', 'w-4 h-4 text-gray-400 hidden sm:block') ?>
            </button>
            <div x-show="open" @click.outside="open = false" x-transition
                 class="absolute right-0 mt-2 w-48 rounded-xl border border-gray-100 bg-white shadow-card-hover py-1"
                 style="display: none;">
                <p class="px-4 py-2 text-xs text-gray-400 truncate"><?= esc($user['email'] ?? '') ?></p>
                <a href="<?= base_url(($user['role'] ?? '') . '/profil') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profil Saya</a>
                <a href="<?= base_url('logout') ?>" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Keluar</a>
            </div>
        </div>
    </div>
</header>