<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NotifikasiModel;
use App\Models\PermintaanUpgradeModel;
use App\Models\SubscriptionModel;
use App\Models\UserModel;

class UpgradeController extends BaseController
{
    protected PermintaanUpgradeModel $upgradeModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->upgradeModel = new PermintaanUpgradeModel();
        $this->userModel    = new UserModel();
    }

    public function index()
    {
        $pending = $this->upgradeModel->getPending();

        return view('admin/upgrade/index', [
            'pageTitle'    => 'Verifikasi Upgrade Premium',
            'pageSubtitle' => 'Tinjau bukti pembayaran upgrade akun petani',
            'permintaan'   => $pending,
        ]);
    }

    public function setujui(int $id)
    {
        $permintaan = $this->cekAkses($id);
        if ($permintaan instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $permintaan;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $this->upgradeModel->update($id, ['status' => 'Disetujui']);
            $this->userModel->setPremium($permintaan['id_user'], true);
            (new SubscriptionModel())->buatSubscription($permintaan['id_user'], $permintaan['tipe']);

            (new NotifikasiModel())->kirim(
                $permintaan['id_user'],
                'Upgrade Premium Disetujui',
                'Selamat! Akun Anda kini berstatus Premium (' . $permintaan['tipe'] . '). Nikmati fitur Kalkulator Laba.'
            );

            $db->transComplete();
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi database gagal.');
            }
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal menyetujui upgrade: ' . $e->getMessage());
        }

        return redirect()->to('/admin/upgrade')->with('success', 'Upgrade premium untuk "' . $permintaan['nama_lengkap'] . '" disetujui.');
    }

    public function tolak(int $id)
    {
        $permintaan = $this->cekAkses($id);
        if ($permintaan instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $permintaan;
        }

        $alasan = $this->request->getPost('alasan_tolak');
        if (empty($alasan)) {
            return redirect()->back()->with('error', 'Alasan penolakan wajib diisi.');
        }

        $this->upgradeModel->update($id, [
            'status'       => 'Ditolak',
            'alasan_tolak' => $alasan,
        ]);

        (new NotifikasiModel())->kirim(
            $permintaan['id_user'],
            'Upgrade Premium Ditolak',
            'Pengajuan upgrade premium Anda ditolak. Alasan: ' . $alasan
        );

        return redirect()->to('/admin/upgrade')->with('success', 'Pengajuan upgrade ditolak.');
    }

    private function cekAkses(int $id)
    {
        $permintaan = $this->upgradeModel->find($id);
        if (! $permintaan || $permintaan['status'] !== 'Menunggu') {
            return redirect()->to('/admin/upgrade')->with('error', 'Data tidak valid atau sudah diproses.');
        }
        $permintaan['nama_lengkap'] = $this->userModel->find($permintaan['id_user'])['nama_lengkap'];
        return $permintaan;
    }
}