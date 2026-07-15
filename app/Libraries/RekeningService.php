<?php

namespace App\Libraries;

use App\Models\RekeningModel;

class RekeningService
{
    protected RekeningModel $rekeningModel;

    public function __construct()
    {
        $this->rekeningModel = new RekeningModel();
    }

    public function tambah(int $idUser, array $data): int|false
    {
        return $this->rekeningModel->insert([
            'id_user'        => $idUser,
            'tipe'           => $data['tipe'],
            'nama_bank'      => $data['nama_bank'],
            'nomor_rekening' => $data['nomor_rekening'],
            'atas_nama'      => $data['atas_nama'],
            'status_validasi' => 'pending',
        ]);
    }

    /**
     * Simulasi micro-transfer: generate nominal acak 100-999,
     * disimpan di session (karena skema tabel tidak punya kolom khusus untuk ini).
     */
    public function generateMicroTransfer(int $idRekening): int
    {
        $nominal = random_int(100, 999);
        session()->set('micro_transfer_' . $idRekening, $nominal);
        return $nominal;
    }

    public function getMicroTransfer(int $idRekening): ?int
    {
        return session()->get('micro_transfer_' . $idRekening);
    }

    public function konfirmasiMicroTransfer(int $idRekening, int $idUser, int $inputNominal): bool
    {
        $rekening = $this->rekeningModel->find($idRekening);
        if (! $rekening || $rekening['id_user'] != $idUser || $rekening['status_validasi'] !== 'pending') {
            return false;
        }

        $expected = $this->getMicroTransfer($idRekening);
        if ($expected === null || $expected !== $inputNominal) {
            return false;
        }

        $this->rekeningModel->update($idRekening, ['status_validasi' => 'verified']);
        session()->remove('micro_transfer_' . $idRekening);

        return true;
    }

    public function hapus(int $idRekening, int $idUser): bool
    {
        $rekening = $this->rekeningModel->find($idRekening);
        if (! $rekening || $rekening['id_user'] != $idUser) {
            return false;
        }

        return (bool) $this->rekeningModel->delete($idRekening);
    }
}