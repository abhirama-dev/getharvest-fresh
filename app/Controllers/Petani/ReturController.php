<?php

namespace App\Controllers\Petani;

use App\Controllers\BaseController;
use App\Models\EscrowModel;
use App\Models\NotifikasiModel;
use App\Models\PesananModel;
use App\Models\ProdukModel;
use App\Models\ReturModel;

class ReturController extends BaseController
{
    protected ReturModel $returModel;
    protected PesananModel $pesananModel;
    protected EscrowModel $escrowModel;
    protected ProdukModel $produkModel;

    public function __construct()
    {
        $this->returModel   = new ReturModel();
        $this->pesananModel = new PesananModel();
        $this->escrowModel  = new EscrowModel();
        $this->produkModel  = new ProdukModel();
    }

    public function index()
    {
        $idPetani = session()->get('id_user');
        $retur    = $this->returModel->getByPetani($idPetani);

        return view('petani/retur/index', [
            'pageTitle'    => 'Pengajuan Retur',
            'pageSubtitle' => 'Tinjau permintaan retur dari pedagang',
            'retur'        => $retur,
        ]);
    }

    public function setujui(int $idRetur)
    {
        $retur = $this->cekAksesRetur($idRetur);
        if ($retur instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $retur;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $this->returModel->update($idRetur, [
                'status'          => 'disetujui',
                'tanggal_selesai' => date('Y-m-d H:i:s'),
            ]);

            $this->pesananModel->update($retur['id_pesanan'], [
                'status_escrow' => 'dikembalikan',
            ]);

            $escrow = $this->escrowModel->getByPesanan($retur['id_pesanan']);
            if ($escrow) {
                $this->escrowModel->kembalikanDana($escrow['id_escrow']);
            }

            $this->produkModel->tambahStok($retur['id_produk'], $retur['jumlah_kg']);

            $notifModel = new NotifikasiModel();
            $notifModel->kirim(
                $retur['id_pedagang'],
                'Retur Disetujui',
                'Petani menyetujui retur untuk "' . $retur['nama_produk'] . '". Dana akan dikembalikan ke rekening Anda.'
            );

            $db->transComplete();
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi database gagal.');
            }
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal menyetujui retur: ' . $e->getMessage());
        }

        return redirect()->to('/petani/retur')->with('success', 'Retur disetujui, stok dan dana telah disesuaikan.');
    }

    public function tolak(int $idRetur)
    {
        $retur = $this->cekAksesRetur($idRetur);
        if ($retur instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $retur;
        }

        $alasanTolak = $this->request->getPost('alasan_tolak');
        if (empty($alasanTolak)) {
            return redirect()->back()->with('error', 'Alasan penolakan wajib diisi.');
        }

        $this->returModel->update($idRetur, [
            'status'          => 'ditolak',
            'tanggal_selesai' => date('Y-m-d H:i:s'),
        ]);

        $notifModel = new NotifikasiModel();
        $notifModel->kirim(
            $retur['id_pedagang'],
            'Retur Ditolak',
            'Petani menolak retur untuk "' . $retur['nama_produk'] . '". Alasan: ' . $alasanTolak . '. Anda dapat mengajukan mediasi ke Admin.'
        );

        return redirect()->to('/petani/retur')->with('success', 'Retur ditolak. Pedagang telah diberi notifikasi.');
    }

    private function cekAksesRetur(int $idRetur)
    {
        $idPetani = session()->get('id_user');
        $retur    = $this->returModel->getDetail($idRetur);

        if (! $retur || $retur['id_petani'] != $idPetani) {
            return redirect()->to('/petani/retur')->with('error', 'Data retur tidak ditemukan.');
        }

        if ($retur['status'] !== 'menunggu') {
            return redirect()->to('/petani/retur')->with('error', 'Retur ini sudah diproses sebelumnya.');
        }

        return $retur;
    }
}