<?php
namespace App\Controllers;

class AuthController extends BaseController
{
    public function index()
    {
        // Si ya está autenticado, redirigir al módulo principal
        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url('facturacion'));
        }
        return view('auth/login');
    }

    public function authenticate()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Validacion estática temporal
        if ($username === 'admin' && $password === 'admin') {
            session()->set([
                'username'   => 'admin',
                'name'       => 'Usuario Administrador',
                'isLoggedIn' => true
            ]);

            return redirect()->to(base_url('facturacion'));
        }

        return redirect()->back()->with('error', 'Usuario o contraseña incorrectos.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'));
    }
}
