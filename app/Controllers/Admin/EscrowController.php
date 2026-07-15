<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EscrowModel;
use App\Models\NotifikasiModel;
use App\Models\PesananModel;
use App\Models\ProdukModel;
use App\Models\ReturModel;

class EscrowController extends BaseController
{
    protected EscrowModel $escrowModel;
    protected ReturModel $returModel;

    public function __construct()
    {
        $this->escrowModel = new EscrowModel();
        $this->returModel  = new ReturModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        $escrow = $db->table('escrow')
            ->select('escrow.*, pesanan.id_pedagang, produk.nama_produk,
                pedagang.nama_lengkap AS nama_pedagang, petani.nama_lengkap AS nama_petani')
            ->join('pesanan', 'pesanan.id_pesanan = escrow.id_pesanan')
            ->join('produk', 'produk.id_produk = pesanan.id_produk')
            ->join('users AS pedagang', 'pedagang.id_user = pesanan.id_pedagang')
            ->join('users AS petani', 'petani.id_user = produk.id_petani')
            ->orderBy('escrow.tanggal_ditahan', 'DESC')
            ->get()->getResultArray();

        // Retur yang ditolak petani = butuh mediasi admin
        $sengketa = $this->returModel->where('status', 'ditolak')->findAll();
        $sengketaDetail = array_map(fn ($r) => $this->returModel->getDetail($r['id_retur']), $sengketa);

        return view('admin/escrow/index', [
            'pageTitle'    => 'Monitoring Escrow',
            'pageSubtitle' => 'Pantau dana tertahan & mediasi sengketa retur',
            'escrow'       => $escrow,
            'sengketa'     => $sengketaDetail,
        ]);
    }

    /**
     * Mediasi: admin memutuskan dana dikembalikan ke pedagang (retur menang)
     */
    public function mediasiSetujui(int $idRetur)
    {
        $retur = $this->returModel->getDetail($idRetur);
        if (! $retur || $retur['status'] !== 'ditolak') {
            return redirect()->to('/admin/escrow')->with('error', 'Data retur tidak valid.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $this->returModel->update($idRetur, [
                'status'          => 'disetujui',
                'tanggal_selesai' => date('Y-m-d H:i:s'),
            ]);

            $pesananModel = new PesananModel();
            $pesananModel->update($retur['id_pesanan'], ['status_escrow' => 'dikembalikan']);

            $escrow = $this->escrowModel->getByPesanan($retur['id_pesanan']);
            if ($escrow) {
                $this->escrowModel->kembalikanDana($escrow['id_escrow']);
            }

            (new ProdukModel())->tambahStok($retur['id_produk'], $retur['jumlah_kg']);

            (new NotifikasiModel())->kirim($retur['id_pedagang'], 'Mediasi Retur: Anda Menang', 'Admin memutuskan retur untuk "' . $retur['nama_produk'] . '" disetujui. Dana dikembalikan.');
            (new NotifikasiModel())->kirim($retur['id_petani'], 'Mediasi Retur Selesai', 'Admin memutuskan retur untuk "' . $retur['nama_produk'] . '" disetujui setelah peninjauan.');

            $db->transComplete();
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi database gagal.');
            }
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal memproses mediasi: ' . $e->getMessage());
        }

        return redirect()->to('/admin/escrow')->with('success', 'Mediasi selesai: dana dikembalikan ke pedagang.');
    }

    /**
     * Mediasi: admin memutuskan dana tetap dilepas ke petani (retur kalah)
     */
    public function mediasiTolak(int $idRetur)
    {
        $retur = $this->returModel->getDetail($idRetur);
        if (! $retur || $retur['status'] !== 'ditolak') {
            return redirect()->to('/admin/escrow')->with('error', 'Data retur tidak valid.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $pesananModel = new PesananModel();
            $pesananModel->update($retur['id_pesanan'], [
                'status_escrow'     => 'dilepas',
                'status_pengiriman' => 'Selesai',
            ]);

            $escrow = $this->escrowModel->getByPesanan($retur['id_pesanan']);
            if ($escrow) {
                $this->escrowModel->lepasDana($escrow['id_escrow']);
            }

            (new NotifikasiModel())->kirim($retur['id_petani'], 'Mediasi Retur: Anda Menang', 'Admin memutuskan dana untuk "' . $retur['nama_produk'] . '" tetap dilepas ke Anda.');
            (new NotifikasiModel())->kirim($retur['id_pedagang'], 'Mediasi Retur Selesai', 'Admin memutuskan pengajuan retur Anda untuk "' . $retur['nama_produk'] . '" tidak dapat diproses.');

            $db->transComplete();
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi database gagal.');
            }
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal memproses mediasi: ' . $e->getMessage());
        }

        return redirect()->to('/admin/escrow')->with('success', 'Mediasi selesai: dana dilepas ke petani.');
    }
}