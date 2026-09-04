<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClienteModel;

class ClientesController extends BaseController
{
    protected $clienteModel;

    public function __construct()
    {
        $this->clienteModel = model(ClienteModel::class);
    }

    public function index()
    {
        return view('clientes/index');
    }

    public function listar()
    {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['data' => $this->clienteModel->findAll()]);
        }
        return $this->response->setStatusCode(400);
    }

    public function guardar()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405);
        }

        $id             = $this->request->getPost('id_cliente');
        $identificacion = trim((string) $this->request->getPost('identificacion'));
        $nombre         = trim((string) $this->request->getPost('nombre'));
        $telefono       = trim((string) $this->request->getPost('telefono'));
        $correo         = trim((string) $this->request->getPost('correo'));

        // 1. Validación de campos primordiales vacíos
        if (empty($identificacion)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['identificacion' => 'La identificación es obligatoria.']
            ]);
        }

        if (empty($nombre)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['nombre' => 'El nombre del cliente es obligatorio.']
            ]);
        }

        // 2. Validación de Cédula Ecuatoriana (Módulo 10)
        if (ctype_digit($identificacion) && strlen($identificacion) === 10) {
            if (!$this->validarCedulaEcuador($identificacion)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => ['identificacion' => 'El número de cédula ingresado no es válido.']
                ]);
            }
        }

        // 3. Validar si ya existe otro cliente con la misma identificación
        $existe = $this->clienteModel
            ->where('identificacion', $identificacion)
            ->where('id_cliente !=', $id ?: 0)
            ->first();

        if ($existe) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['identificacion' => 'Esta identificación ya se encuentra registrada.']
            ]);
        }

        // 4. Operación de Guardado o Edición
        $data = [
            'identificacion' => $identificacion,
            'nombre'         => $nombre,
            'telefono'       => $telefono ?: null,
            'correo'         => $correo ?: null,
        ];

        if (!empty($id)) {
            $guardado = $this->clienteModel->update($id, $data);
        } else {
            $guardado = $this->clienteModel->insert($data);
        }

        if (!$guardado) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->clienteModel->errors()
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => !empty($id) ? 'Cliente actualizado con éxito.' : 'Cliente registrado con éxito.'
        ]);
    }

    public function obtener($id = null)
    {
        if ($this->request->isAJAX() && $id !== null) {
            $cliente = $this->clienteModel->find($id);
            if ($cliente) {
                return $this->response->setJSON(['status' => 'success', 'data' => $cliente]);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'Cliente no encontrado.']);
        }
        return $this->response->setStatusCode(400);
    }

    public function eliminar($id = null)
    {
        if ($this->request->isAJAX() && $id !== null) {
            if ($this->clienteModel->delete($id)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Cliente eliminado con éxito.']);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'No se pudo eliminar el cliente.']);
        }
        return $this->response->setStatusCode(400);
    }

    /**
     * Algoritmo Oficial Módulo 10 para validación de Cédulas de Ecuador
     */
    private function validarCedulaEcuador(string $cedula): bool
    {
        if (strlen($cedula) !== 10 || !ctype_digit($cedula)) {
            return false;
        }

        $provincia = (int) substr($cedula, 0, 2);
        if (($provincia < 1 || $provincia > 24) && $provincia !== 30) {
            return false;
        }

        $tercerDigito = (int) $cedula[2];
        if ($tercerDigito >= 6) {
            return false;
        }

        $coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
        $suma = 0;

        for ($i = 0; $i < 9; $i++) {
            $valor = (int) $cedula[$i] * $coeficientes[$i];
            if ($valor >= 10) {
                $valor -= 9;
            }
            $suma += $valor;
        }

        $digitoVerificadorCalculado = (10 - ($suma % 10)) % 10;
        $digitoVerificadorReal = (int) $cedula[9];

        return $digitoVerificadorCalculado === $digitoVerificadorReal;
    }
}