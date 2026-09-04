<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MarcaModel;

class MarcasController extends BaseController
{
    protected $marcaModel;

    public function __construct()
    {
        $this->marcaModel = model(MarcaModel::class);
    }

    public function index()
    {
        return view('marcas/index');
    }

    public function listar()
    {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['data' => $this->marcaModel->findAll()]);
        }
        return $this->response->setStatusCode(400);
    }

    public function guardar()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405);
        }

        $id = $this->request->getPost('id_marca');
        $nombre = trim((string) $this->request->getPost('nombre'));

        // Validación de campo obligatorio
        if (empty($nombre)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['nombre' => 'El nombre de la marca es obligatorio.']
            ]);
        }

        // Validar si ya existe otro registro con el mismo nombre
        $existe = $this->marcaModel
            ->where('nombre', $nombre)
            ->where('id_marca !=', $id ?: 0)
            ->first();

        if ($existe) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['nombre' => 'Esta marca ya se encuentra registrada.']
            ]);
        }

        // Operación de Guardado o Edición
        if (!empty($id)) {
            $guardado = $this->marcaModel->update($id, ['nombre' => $nombre]);
        } else {
            $guardado = $this->marcaModel->insert(['nombre' => $nombre]);
        }

        if (!$guardado) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->marcaModel->errors()
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => !empty($id) ? 'Marca actualizada con éxito.' : 'Marca registrada con éxito.'
        ]);
    }

    public function obtener($id = null)
    {
        if ($this->request->isAJAX() && $id !== null) {
            $marca = $this->marcaModel->find($id);
            if ($marca) {
                return $this->response->setJSON(['status' => 'success', 'data' => $marca]);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'Marca no encontrada.']);
        }
        return $this->response->setStatusCode(400);
    }

    public function eliminar($id = null)
    {
        if ($this->request->isAJAX() && $id !== null) {
            if ($this->marcaModel->delete($id)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Marca eliminada con éxito.']);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'No se pudo eliminar la marca.']);
        }
        return $this->response->setStatusCode(400);
    }
}