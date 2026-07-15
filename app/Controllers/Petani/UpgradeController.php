<?php

namespace App\Controllers\Petani;

use App\Controllers\BaseController;
use App\Models\NotifikasiModel;
use App\Models\PermintaanUpgradeModel;
use App\Models\RekeningAdminModel;
use App\Models\SubscriptionModel;

class UpgradeController extends BaseController
{
    protected PermintaanUpgradeModel $upgradeModel;
    protected RekeningAdminModel $rekeningAdminModel;
    protected SubscriptionModel $subscriptionModel;

    public function __construct()
    {
        $this->upgradeModel       = new PermintaanUpgradeModel();
        $this->rekeningAdminModel = new RekeningAdminModel();
        $this->subscriptionModel  = new SubscriptionModel();
    }

    public function index()
    {
        $idUser = session()->get('id_user');

        $isPremium      = (bool) session()->get('is_premium');
        $subscriptionAktif = $isPremium ? $this->subscriptionModel->getAktif($idUser) : null;
        $rekeningAdmin  = $this->rekeningAdminModel->findAll();
        $riwayat        = $this->upgradeModel->getByUser($idUser);

        $adaPending = ! empty(array_filter($riwayat, fn ($r) => $r['status'] === 'Menunggu'));

        return view('petani/upgrade/index', [
            'pageTitle'         => 'Upgrade Premium',
            'pageSubtitle'      => 'Buka fitur Kalkulator Laba dengan berlangganan Premium',
            'isPremium'         => $isPremium,
            'subscriptionAktif' => $subscriptionAktif,
            'rekeningAdmin'     => $rekeningAdmin,
            'riwayat'           => $riwayat,
            'adaPending'        => $adaPending,
        ]);
    }

    public function store()
    {
        $idUser = session()->get('id_user');

        $rules = [
            'tipe'        => 'required|in_list[bulanan,tahunan]',
            'bukti_bayar' => 'uploaded[bukti_bayar]|is_image[bukti_bayar]|max_size[bukti_bayar,2048]',
        ];
        $messages = [
            'bukti_bayar' => [
                'uploaded' => 'Bukti pembayaran wajib diunggah.',
                'is_image' => 'File harus berupa gambar (jpg/png).',
                'max_size' => 'Ukuran file maksimal 2MB.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $file     = $this->request->getFile('bukti_bayar');
        $namaFile = $file->getRandomName();
        $file->move(FCPATH . 'assets/uploads/bukti_bayar', $namaFile);

        $this->upgradeModel->insert([
            'id_user'     => $idUser,
            'tipe'        => $this->request->getPost('tipe'),
            'bukti_bayar' => $namaFile,
            'status'      => 'Menunggu',
        ]);

        // Notifikasi ke semua admin
        $adminModel = new \App\Models\UserModel();
        $notifModel = new NotifikasiModel();
        foreach ($adminModel->where('role', 'admin')->findAll() as $admin) {
            $notifModel->kirim(
                $admin['id_user'],
                'Pengajuan Upgrade Premium',
                session()->get('nama_lengkap') . ' mengajukan upgrade premium paket ' . $this->request->getPost('tipe') . '.'
            );
        }

        return redirect()->to('/petani/upgrade')->with('success', 'Pengajuan upgrade berhasil dikirim, menunggu verifikasi Admin.');
    }
}