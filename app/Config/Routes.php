<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Landing / root
$routes->get('/', 'Home::index');

// Auth (tamu)
$routes->group('', ['filter' => 'guest'], function ($routes) {
    $routes->get('login', 'Auth\AuthController::login');
    $routes->post('login', 'Auth\AuthController::attemptLogin');
    $routes->get('register', 'Auth\AuthController::register');
    $routes->post('register', 'Auth\AuthController::attemptRegister');
});
$routes->get('logout', 'Auth\AuthController::logout');

// Notifikasi (semua role login)
$routes->post('notifikasi/baca-semua', 'NotifikasiController::bacaSemua', ['filter' => 'auth']);

// ================= PETANI =================
$routes->group('petani', ['filter' => ['auth', 'role:petani']], function ($routes) {
    $routes->get('dashboard', 'Petani\DashboardController::index');

    $routes->get('produk', 'Petani\ProdukController::index');
    $routes->get('produk/tambah', 'Petani\ProdukController::create');
    $routes->post('produk/simpan', 'Petani\ProdukController::store');
    $routes->get('produk/hapus/(:num)', 'Petani\ProdukController::delete/$1');

    // BARU — Tahap 6: Pesanan Masuk
    $routes->get('pesanan', 'Petani\PesananController::index');
    $routes->post('pesanan/kemas/(:num)', 'Petani\PesananController::kemas/$1');
    $routes->post('pesanan/kirim/(:num)', 'Petani\PesananController::kirim/$1');

    // BARU — Tahap 7: Negosiasi
    $routes->get('nego', 'Petani\NegoController::index');
    $routes->post('nego/respon/(:num)', 'Petani\NegoController::respon/$1');

    $routes->get('retur', 'Petani\ReturController::index');
    $routes->get('escrow', 'Petani\EscrowController::index');
    $routes->get('rekening', 'Petani\RekeningController::index');
    $routes->get('upgrade', 'Petani\UpgradeController::index');
    $routes->get('profil', 'Petani\ProfilController::index');

    // retur & rating
    $routes->post('retur/setujui/(:num)', 'Petani\ReturController::setujui/$1');
    $routes->post('retur/tolak/(:num)', 'Petani\ReturController::tolak/$1');

    // grup 'petani', ['filter' => ['auth', 'role:petani']]
    $routes->get('rekening', 'Petani\RekeningController::index');
    $routes->post('rekening/simpan', 'Petani\RekeningController::store');
    $routes->post('rekening/verifikasi/(:num)', 'Petani\RekeningController::verifikasi/$1');
    $routes->post('rekening/hapus/(:num)', 'Petani\RekeningController::hapus/$1');

    // grup 'petani', ['filter' => ['auth', 'role:petani']]
    $routes->post('upgrade/simpan', 'Petani\UpgradeController::store');
    $routes->post('profil/update', 'Petani\ProfilController::update');
    $routes->post('profil/password', 'Petani\ProfilController::updatePassword');

    // grup 'pedagang', ['filter' => ['auth', 'role:pedagang']]
    $routes->post('profil/update', 'Pedagang\ProfilController::update');
    $routes->post('profil/password', 'Pedagang\ProfilController::updatePassword');
});
$routes->group('petani', ['filter' => ['auth', 'role:petani', 'premium']], function ($routes) {
    $routes->get('kalkulator', 'Petani\KalkulatorController::index');
    $routes->post('kalkulator/pengeluaran', 'Petani\KalkulatorController::simpanPengeluaran');
    $routes->post('kalkulator/pengeluaran/hapus/(:num)', 'Petani\KalkulatorController::hapusPengeluaran/$1');
});

// ================= PEDAGANG =================
$routes->group('pedagang', ['filter' => ['auth', 'role:pedagang']], function ($routes) {
    $routes->get('dashboard', 'Pedagang\DashboardController::index');

    $routes->get('katalog', 'Pedagang\KatalogController::index');
    $routes->get('katalog/(:num)', 'Pedagang\KatalogController::show/$1');

    // BARU — Tahap 6: Pembelian & Escrow
    $routes->post('pembelian/store', 'Pedagang\PembelianController::store');
    $routes->get('pembelian/bayar/(:num)', 'Pedagang\PembelianController::bayar/$1');
    $routes->post('pembelian/bayar/(:num)', 'Pedagang\PembelianController::uploadBukti/$1');

    $routes->get('pesanan', 'Pedagang\PesananController::index');
    $routes->post('pesanan/konfirmasi/(:num)', 'Pedagang\PesananController::konfirmasiTerima/$1');

    // BARU — Tahap 7: Negosiasi
    $routes->get('nego', 'Pedagang\NegoController::index');
    $routes->post('nego/ajukan', 'Pedagang\NegoController::ajukan');
    $routes->post('nego/respon/(:num)', 'Pedagang\NegoController::respon/$1');

    $routes->get('lacak', 'Pedagang\LacakController::index');
    $routes->get('rekening', 'Pedagang\RekeningController::index');
    $routes->get('profil', 'Pedagang\ProfilController::index');
    // Retur & Rating
    $routes->get('retur', 'Pedagang\ReturController::index');
    $routes->get('retur/ajukan/(:num)', 'Pedagang\ReturController::ajukan/$1');
    $routes->post('retur/simpan/(:num)', 'Pedagang\ReturController::store/$1');
    $routes->get('rating/(:num)', 'Pedagang\RatingController::form/$1');
    $routes->post('rating/simpan/(:num)', 'Pedagang\RatingController::store/$1');
    $routes->get('rekening', 'Pedagang\RekeningController::index');
    $routes->post('rekening/simpan', 'Pedagang\RekeningController::store');
    $routes->post('rekening/verifikasi/(:num)', 'Pedagang\RekeningController::verifikasi/$1');
    $routes->post('rekening/hapus/(:num)', 'Pedagang\RekeningController::hapus/$1');
    $routes->post('profil/update', 'Pedagang\ProfilController::update');
    $routes->post('profil/password', 'Pedagang\ProfilController::updatePassword');
    $routes->get('lacak', 'Pedagang\LacakController::index');
});

// ================= ADMIN =================
$routes->group('admin', ['filter' => ['auth', 'role:admin']], function ($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');

    $routes->get('verifikasi', 'Admin\VerifikasiController::index');
    $routes->get('verifikasi/detail/(:num)', 'Admin\VerifikasiController::detail/$1');
    $routes->post('verifikasi/setujui/(:num)', 'Admin\VerifikasiController::setujui/$1');
    $routes->post('verifikasi/tolak/(:num)', 'Admin\VerifikasiController::tolak/$1');

    $routes->get('users', 'Admin\UserController::index');
    $routes->get('users/detail/(:num)', 'Admin\UserController::detail/$1');

    $routes->get('upgrade', 'Admin\UpgradeController::index');
    $routes->post('upgrade/setujui/(:num)', 'Admin\UpgradeController::setujui/$1');
    $routes->post('upgrade/tolak/(:num)', 'Admin\UpgradeController::tolak/$1');

    $routes->get('rekening', 'Admin\RekeningController::index');
    $routes->post('rekening/verifikasi/(:num)', 'Admin\RekeningController::verifikasi/$1');
    $routes->post('rekening/tolak/(:num)', 'Admin\RekeningController::tolak/$1');

    $routes->get('escrow', 'Admin\EscrowController::index');
    $routes->post('escrow/mediasi/setujui/(:num)', 'Admin\EscrowController::mediasiSetujui/$1');
    $routes->post('escrow/mediasi/tolak/(:num)', 'Admin\EscrowController::mediasiTolak/$1');

    $routes->get('transaksi', 'Admin\TransaksiController::index');

    $routes->get('rekening-admin', 'Admin\RekeningAdminController::index');
    $routes->post('rekening-admin/simpan', 'Admin\RekeningAdminController::store');
    $routes->post('rekening-admin/hapus/(:num)', 'Admin\RekeningAdminController::delete/$1');

    $routes->get('profil', 'Admin\ProfilController::index');
    $routes->post('profil/update', 'Admin\ProfilController::update');
    $routes->post('profil/password', 'Admin\ProfilController::updatePassword');
});
