<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            $role = session()->get('role');
            return redirect()->to('/' . $role . '/dashboard');
        }

        return redirect()->to('/login');
    }
}