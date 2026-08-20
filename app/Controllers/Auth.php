<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        return view('auth/login');
    }

    public function login()
    {
        $model = new UserModel();

        $username = trim($this->request->getPost('username'));
        $password = trim($this->request->getPost('password'));

        $user = $model->where('username', $username)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Username tidak ditemukan');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Password salah');
        }

        session()->set([
            'id_user'  => $user['id_user'],
            'nama'     => $user['nama'],
            'username' => $user['username'],
            'login'    => true
        ]);

        return redirect()->to('/analisis-data');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}