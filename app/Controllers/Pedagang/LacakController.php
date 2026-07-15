<?php

namespace App\Controllers\Pedagang;

use App\Controllers\BaseController;
use App\Models\PesananModel;

class LacakController extends BaseController
{
    protected PesananModel $pesananModel;

    public function __construct()
    {
        $this->pesananModel = new PesananModel();
    }

    public function index()
    {
        $idPedagang = session()->get('id_user');

        $resi   = $this->request->getGet('resi');
        $hasil  = null;
        $notFound = false;

        if ($resi) {
            $hasil = $this->pesananModel->cariByResi(trim($resi), $idPedagang);
            $notFound = ! $hasil;
        }

        $sedangDikirim = $this->pesananModel->getSedangDikirim($idPedagang);

        return view('pedagang/lacak/index', [
            'pageTitle'      => 'Lacak Pengiriman',
            'pageSubtitle'   => 'Pantau status pengiriman pesanan Anda',
            'hasil'          => $hasil,
            'notFound'       => $notFound,
            'resiDicari'     => $resi,
            'sedangDikirim'  => $sedangDikirim,
        ]);
    }
}