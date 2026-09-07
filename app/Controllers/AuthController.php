<?php
namespace App\Controllers;

use App\Models\UsuarioModel;

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
        $correo = trim((string) $this->request->getPost('username')); // Acepta el correo en el campo de entrada
        $clave  = (string) $this->request->getPost('password');

        if (empty($correo) || empty($clave)) {
            return redirect()->back()->with('error', 'Por favor, ingrese sus credenciales completeas.');
        }

        $usuarioModel = model(UsuarioModel::class);

        // Buscar usuario por correo electrónico
        $usuario = $usuarioModel->where('correo', $correo)->first();

        // 1. Validar que el usuario exista
        if (!$usuario) {
            return redirect()->back()->with('error', 'Usuario o contraseña incorrectos.');
        }

        // 2. Validar que la cuenta esté activa (estado = 1)
        if ((int) $usuario['estado'] !== 1) {
            return redirect()->back()->with('error', 'Su cuenta se encuentra inactiva. Contacte al administrador.');
        }

        // 3. Verificar contraseña (soporta hash BCRYPT del módulo de usuarios y fallback plano temporal)
        $passwordValida = password_verify($clave, $usuario['clave']) || $clave === $usuario['clave'];

        if ($passwordValida) {
            // Guardar datos del usuario autenticado en la sesión
            session()->set([
                'id_usuario' => $usuario['id_usuario'],
                'username'   => $usuario['correo'],
                'name'       => $usuario['nombre'],
                'rol'        => $usuario['rol'],
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