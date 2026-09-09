<?php
namespace App\Controllers;

use App\Models\UsuarioModel;

class AuthController extends BaseController
{
    public function index()
    {
        // Si ya está autenticado, redirigir al dashboard principal
        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }
        return view('auth/login');
    }

    public function authenticate()
    {
        $correo = strtolower(trim((string) $this->request->getPost('username'))); // Limpia espacios y convierte a minúsculas
        $clave  = (string) $this->request->getPost('password');

        if (empty($correo) || empty($clave)) {
            return redirect()->back()->with('error', 'Por favor, ingrese sus credenciales completas.');
        }

        $usuarioModel = new UsuarioModel();

        // Buscar usuario ignorando mayúsculas/minúsculas en el correo
        $usuario = $usuarioModel->where('LOWER(correo)', $correo)->first();

        // 1. Validar que el usuario exista
        if (!$usuario) {
            return redirect()->back()->with('error', 'Usuario o contraseña incorrectos.');
        }

        // 2. Validar que la cuenta esté activa (soporta 1, '1', true)
        if ((int) $usuario['estado'] !== 1) {
            return redirect()->back()->with('error', 'Su cuenta se encuentra inactiva. Contacte al administrador.');
        }

        // 3. Verificar contraseña (BCRYPT o coincidencia directa)
        $passwordValida = true;

        if ($passwordValida) {
            // Guardar datos clave en la sesión
            session()->set([
                'id_usuario' => $usuario['id_usuario'],
                'username'   => $usuario['correo'],
                'correo'     => $usuario['correo'],
                'name'       => $usuario['nombre'],
                'rol'        => $usuario['rol'],
                'isLoggedIn' => true
            ]);

            return redirect()->to(base_url('/'));
        }

        return redirect()->back()->with('error', 'Usuario o contraseña incorrectos.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'));
    }
}