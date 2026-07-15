<?php

namespace App\Controllers\Petani;

use App\Controllers\BaseController;
use App\Models\EscrowModel;

class EscrowController extends BaseController
{
    public function index()
    {
        $idPetani    = session()->get('id_user');
        $escrowModel = new EscrowModel();

        $escrow = $escrowModel->getByPetani($idPetani);

        $totalDitahan = array_sum(array_map(
            fn ($e) => $e['status'] === 'ditahan' ? $e['jumlah_escrow'] : 0,
            $escrow
        ));
        $totalDilepas = array_sum(array_map(
            fn ($e) => $e['status'] === 'dilepas' ? $e['jumlah_escrow'] : 0,
            $escrow
        ));

        return view('petani/escrow/index', [
            'pageTitle'    => 'Escrow Saya',
            'pageSubtitle' => 'Pantau dana yang tertahan dan sudah dilepas ke Anda',
            'escrow'       => $escrow,
            'totalDitahan' => $totalDitahan,
            'totalDilepas' => $totalDilepas,
        ]);
    }
}