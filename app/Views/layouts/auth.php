<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'Masuk') ?> — GetHarvest</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <?= csrf_meta() ?>
</head>
<body class="font-sans antialiased">
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-primary-900 px-4 py-10">
        <!-- Background pertanian -->
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1500937386664-56d1dfef3854?auto=format&fit=crop&w=1600&q=80"
                 alt="Latar Pertanian" class="h-full w-full object-cover opacity-40">
            <div class="absolute inset-0 bg-gradient-to-br from-primary-900/90 via-primary-800/80 to-primary-700/70"></div>
        </div>

        <div class="relative z-10 w-full max-w-md">
            <div class="mb-6 flex flex-col items-center text-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-primary-700 font-extrabold text-2xl shadow-lg">G</div>
                <h1 class="mt-3 text-2xl font-bold text-white">GetHarvest</h1>
                <p class="text-sm text-primary-100">Pasar transparan petani &amp; pedagang</p>
            </div>

            <div class="rounded-2xl border border-white/20 bg-white/95 p-6 sm:p-8 shadow-2xl backdrop-blur">
                <?= $this->include('components/alerts') ?>
                <?= $this->renderSection('content') ?>
            </div>

            <p class="mt-6 text-center text-xs text-primary-100">
                &copy; <?= date('Y') ?> GetHarvest. Memutus rantai tengkulak, memakmurkan petani.
            </p>
        </div>
    </div>

    <script src="<?= base_url('assets/js/alpine.min.js') ?>" defer></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>