<?php

namespace App\Controllers;

use App\Models\NotifikasiModel;

class NotifikasiController extends BaseController
{
    public function bacaSemua()
    {
        $idUser = session()->get('id_user');
        (new NotifikasiModel())->markAllRead($idUser);

        return redirect()->back();
    }
}