<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;

class UsuariosController extends BaseController
{
    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = model(UsuarioModel::class);
    }

    public function index()
    {
        return view('usuarios/index');
    }

    public function listar()
    {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['data' => $this->usuarioModel->findAll()]);
        }
        return $this->response->setStatusCode(400);
    }

    public function guardar()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405);
        }

        $id     = $this->request->getPost('id_usuario');
        $nombre = trim((string) $this->request->getPost('nombre'));
        $correo = trim((string) $this->request->getPost('correo'));
        $clave  = (string) $this->request->getPost('clave');
        $rol    = $this->request->getPost('rol');
        $estado = $this->request->getPost('estado') !== null ? (int) $this->request->getPost('estado') : 1;

        // 1. Validaciones de campos primordiales
        if (empty($nombre)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['nombre' => 'El nombre del usuario es obligatorio.']
            ]);
        }

        if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['correo' => 'Ingrese un correo electrónico válido.']
            ]);
        }

        if (empty($id) && empty($clave)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['clave' => 'La contraseña es obligatoria para nuevos usuarios.']
            ]);
        }

        if (!empty($clave) && strlen($clave) < 6) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['clave' => 'La contraseña debe tener al menos 6 caracteres.']
            ]);
        }

        if (empty($rol) || !in_array($rol, ['administrador', 'encargado'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['rol' => 'Seleccione un rol válido.']
            ]);
        }

        // 2. Validar correo único
        $existe = $this->usuarioModel
            ->where('correo', $correo)
            ->where('id_usuario !=', $id ?: 0)
            ->first();

        if ($existe) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['correo' => 'Este correo electrónico ya está registrado.']
            ]);
        }

        // 3. Preparar datos para inserción / edición
        $data = [
            'nombre' => $nombre,
            'correo' => $correo,
            'rol'    => $rol,
            'estado' => $estado,
        ];

        if (!empty($clave)) {
            $data['clave'] = password_hash($clave, PASSWORD_BCRYPT);
        }

        if (!empty($id)) {
            $guardado = $this->usuarioModel->update($id, $data);
        } else {
            $guardado = $this->usuarioModel->insert($data);
        }

        if (!$guardado) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->usuarioModel->errors()
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => !empty($id) ? 'Usuario actualizado con éxito.' : 'Usuario registrado con éxito.'
        ]);
    }

    public function obtener($id = null)
    {
        if ($this->request->isAJAX() && $id !== null) {
            $usuario = $this->usuarioModel->find($id);
            if ($usuario) {
                unset($usuario['clave']); // No enviar el hash de la contraseña a la vista
                return $this->response->setJSON(['status' => 'success', 'data' => $usuario]);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'Usuario no encontrado.']);
        }
        return $this->response->setStatusCode(400);
    }

    public function eliminar($id = null)
    {
        if ($this->request->isAJAX() && $id !== null) {
            if ($this->usuarioModel->delete($id)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Usuario eliminado con éxito.']);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'No se pudo eliminar el usuario.']);
        }
        return $this->response->setStatusCode(400);
    }
}