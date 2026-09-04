<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProveedorModel;

class ProveedoresController extends BaseController
{
    protected $proveedorModel;

    public function __construct()
    {
        $this->proveedorModel = model(ProveedorModel::class);
    }

    public function index()
    {
        return view('proveedores/index');
    }

    public function listar()
    {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['data' => $this->proveedorModel->findAll()]);
        }
        return $this->response->setStatusCode(400);
    }

    public function guardar()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405);
        }

        $id             = $this->request->getPost('id_proveedor');
        $identificacion = trim((string) $this->request->getPost('identificacion'));
        $nombre         = trim((string) $this->request->getPost('nombre'));
        $telefono       = trim((string) $this->request->getPost('telefono'));

        // 1. Validación de campos obligatorios
        if (empty($identificacion)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['identificacion' => 'La identificación es obligatoria.']
            ]);
        }

        if (empty($nombre)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['nombre' => 'El nombre o razón social es obligatorio.']
            ]);
        }

     // 2. Validación de Cédula/RUC Ecuatoriano
     if (ctype_digit($identificacion) && (strlen($identificacion) === 10 || strlen($identificacion) === 13)) {
        if (!$this->validarCedulaORucEcuador($identificacion)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['identificacion' => 'El número de cédula o RUC ingresado no es válido.']
            ]);
        }
    }
        // 3. Validar si ya existe otro proveedor con la misma identificación
        $existe = $this->proveedorModel
            ->where('identificacion', $identificacion)
            ->where('id_proveedor !=', $id ?: 0)
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
        ];

        if (!empty($id)) {
            $guardado = $this->proveedorModel->update($id, $data);
        } else {
            $guardado = $this->proveedorModel->insert($data);
        }

        if (!$guardado) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->proveedorModel->errors()
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => !empty($id) ? 'Proveedor actualizado con éxito.' : 'Proveedor registrado con éxito.'
        ]);
    }

    public function obtener($id = null)
    {
        if ($this->request->isAJAX() && $id !== null) {
            $proveedor = $this->proveedorModel->find($id);
            if ($proveedor) {
                return $this->response->setJSON(['status' => 'success', 'data' => $proveedor]);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'Proveedor no encontrado.']);
        }
        return $this->response->setStatusCode(400);
    }

    public function eliminar($id = null)
    {
        if ($this->request->isAJAX() && $id !== null) {
            if ($this->proveedorModel->delete($id)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Proveedor eliminado con éxito.']);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'No se pudo eliminar el proveedor.']);
        }
        return $this->response->setStatusCode(400);
    }

    /**
     * Algoritmo Módulo 10 para validación de Cédulas y RUC Persona Natural de Ecuador
     */
    private function validarCedulaORucEcuador(string $identificacion): bool
    {
        if (!ctype_digit($identificacion)) {
            return false;
        }

        // Extraer los 10 primeros dígitos si es RUC (13 dígitos)
        $cedula = (strlen($identificacion) === 13) ? substr($identificacion, 0, 10) : $identificacion;

        if (strlen($cedula) !== 10) {
            return false;
        }

        // Validación si es RUC: los últimos 3 dígitos deben terminar en 001, 002, etc.
        if (strlen($identificacion) === 13 && substr($identificacion, 10, 3) === '000') {
            return false;
        }

        $provincia = (int) substr($cedula, 0, 2);
        if (($provincia < 1 || $provincia > 24) && $provincia !== 30) {
            return false;
        }

        $tercerDigito = (int) $cedula[2];
        if ($tercerDigito >= 6) {
            return false; // Aplica para persona natural
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