<?php

namespace App\Controllers\Pedagang;

use App\Controllers\BaseController;
use App\Models\NotifikasiModel;
use App\Models\PesananModel;
use App\Models\ReturModel;

class ReturController extends BaseController
{
    protected ReturModel $returModel;
    protected PesananModel $pesananModel;

    public function __construct()
    {
        $this->returModel   = new ReturModel();
        $this->pesananModel = new PesananModel();
    }

    public function index()
    {
        $idPedagang = session()->get('id_user');
        $retur      = $this->returModel->getByPedagang($idPedagang);

        return view('pedagang/retur/index', [
            'pageTitle'    => 'Riwayat Retur',
            'pageSubtitle' => 'Pantau status pengajuan retur Anda',
            'retur'        => $retur,
        ]);
    }

    public function ajukan(int $idPesanan)
    {
        $pesanan = $this->cekAksesPesanan($idPesanan);
        if ($pesanan instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $pesanan;
        }

        return view('pedagang/retur/ajukan', [
            'pageTitle'    => 'Ajukan Retur',
            'pageSubtitle' => 'Laporkan ketidaksesuaian pesanan Anda',
            'pesanan'      => $pesanan,
        ]);
    }

    public function store(int $idPesanan)
    {
        $pesanan = $this->cekAksesPesanan($idPesanan);
        if ($pesanan instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $pesanan;
        }

        $rules = [
            'alasan'     => 'required|min_length[10]',
            'foto_bukti' => 'uploaded[foto_bukti]|is_image[foto_bukti]|max_size[foto_bukti,2048]',
        ];
        $messages = [
            'foto_bukti' => [
                'uploaded' => 'Foto bukti wajib diunggah sebagai bukti ketidaksesuaian.',
                'is_image' => 'File harus berupa gambar (jpg/png).',
                'max_size' => 'Ukuran foto maksimal 2MB.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $file     = $this->request->getFile('foto_bukti');
            $namaFile = $file->getRandomName();
            $file->move(FCPATH . 'assets/uploads/retur', $namaFile);

            $this->returModel->insert([
                'id_pesanan' => $idPesanan,
                'alasan'     => $this->request->getPost('alasan'),
                'foto_bukti' => $namaFile,
                'status'     => 'menunggu',
            ]);

            $this->pesananModel->update($idPesanan, ['status_pengiriman' => 'Retur']);

            $notifModel = new NotifikasiModel();
            $notifModel->kirim(
                $pesanan['id_petani'],
                'Pengajuan Retur Baru',
                'Pedagang mengajukan retur untuk pesanan "' . $pesanan['nama_produk'] . '". Mohon segera ditinjau.'
            );

            $db->transComplete();
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi database gagal.');
            }
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal mengajukan retur: ' . $e->getMessage());
        }

        return redirect()->to('/pedagang/retur')->with('success', 'Pengajuan retur berhasil dikirim, menunggu tanggapan petani.');
    }

    private function cekAksesPesanan(int $idPesanan)
    {
        $idPedagang = session()->get('id_user');
        $pesanan    = $this->pesananModel->getDetail($idPesanan);

        if (! $pesanan || $pesanan['id_pedagang'] != $idPedagang) {
            return redirect()->to('/pedagang/pesanan')->with('error', 'Pesanan tidak ditemukan.');
        }

        if ($pesanan['status_pengiriman'] !== 'Dikirim') {
            return redirect()->to('/pedagang/pesanan')->with('error', 'Retur hanya bisa diajukan saat pesanan berstatus "Dikirim".');
        }

        if ($this->returModel->sudahRetur($idPesanan)) {
            return redirect()->to('/pedagang/retur')->with('error', 'Anda sudah pernah mengajukan retur untuk pesanan ini.');
        }

        return $pesanan;
    }
}