<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoriaModel;

class CategoriasController extends BaseController
{
    protected $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = model(CategoriaModel::class);
    }

    public function index()
    {
        return view('categorias/index');
    }

    public function listar()
    {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['data' => $this->categoriaModel->findAll()]);
        }
        return $this->response->setStatusCode(400);
    }

    public function guardar()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405);
        }

        $id = $this->request->getPost('id_categoria');
        $nombre = trim((string) $this->request->getPost('nombre'));

        // Validación de campo requerido
        if (empty($nombre)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['nombre' => 'El nombre de la categoría es obligatorio.']
            ]);
        }

        // Validar si ya existe otro registro con el mismo nombre
        $existe = $this->categoriaModel
            ->where('nombre', $nombre)
            ->where('id_categoria !=', $id ?: 0)
            ->first();

        if ($existe) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['nombre' => 'Esta categoría ya se encuentra registrada.']
            ]);
        }

        // Operación de Guardado o Edición
        if (!empty($id)) {
            $guardado = $this->categoriaModel->update($id, ['nombre' => $nombre]);
        } else {
            $guardado = $this->categoriaModel->insert(['nombre' => $nombre]);
        }

        if (!$guardado) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->categoriaModel->errors()
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => !empty($id) ? 'Categoría actualizada con éxito.' : 'Categoría registrada con éxito.'
        ]);
    }

    public function obtener($id = null)
    {
        if ($this->request->isAJAX() && $id !== null) {
            $categoria = $this->categoriaModel->find($id);
            if ($categoria) {
                return $this->response->setJSON(['status' => 'success', 'data' => $categoria]);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'Categoría no encontrada.']);
        }
        return $this->response->setStatusCode(400);
    }

    public function eliminar($id = null)
    {
        if ($this->request->isAJAX() && $id !== null) {
            if ($this->categoriaModel->delete($id)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Categoría eliminada con éxito.']);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'No se pudo eliminar la categoría.']);
        }
        return $this->response->setStatusCode(400);
    }
}