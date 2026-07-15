<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'Dashboard') ?> — GetHarvest</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <?= csrf_meta() ?>
    <?= $this->renderSection('styles') ?>
</head>
<body class="font-sans bg-gray-50 text-gray-800 antialiased" x-data="{ mobileOpen: false }">

    <?= $this->include('components/sidebar') ?>

    <div class="transition-all duration-300 lg:pl-64" :class="{'lg:!pl-20': localStorage.getItem('gh_sidebar_collapsed') === 'true'}">
        <?= $this->include('components/navbar') ?>

        <main class="p-4 lg:p-6">
            <?= $this->include('components/alerts') ?>
            <?= $this->renderSection('content') ?>
        </main>

        <footer class="border-t border-gray-200 bg-white px-4 lg:px-6 py-4 mt-6">
            <p class="text-center text-xs text-gray-400">
                &copy; <?= date('Y') ?> GetHarvest — Menghubungkan Petani dengan Pedagang secara langsung dan transparan.
            </p>
        </footer>
    </div>

    <script src="<?= base_url('assets/js/alpine.min.js') ?>" defer></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>