<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductoModel;
use App\Models\CategoriaModel;
use App\Models\MarcaModel;

class ProductosController extends BaseController
{
    protected $productoModel;
    protected $categoriaModel;
    protected $marcaModel;

    public function __construct()
    {
        $this->productoModel  = model(ProductoModel::class);
        $this->categoriaModel = model(CategoriaModel::class);
        $this->marcaModel     = model(MarcaModel::class);
    }

    public function index()
    {
        $data['categorias'] = $this->categoriaModel->findAll();
        $data['marcas']     = $this->marcaModel->findAll();

        return view('productos/index', $data);
    }

    public function listar()
    {
        if ($this->request->isAJAX()) {
            $productos = $this->productoModel->obtenerProductosConRelaciones();
            return $this->response->setJSON(['data' => $productos]);
        }
        return $this->response->setStatusCode(400);
    }

    public function guardar()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405);
        }

        $id            = $this->request->getPost('id_producto');
        $codigo_barras = trim((string) $this->request->getPost('codigo_barras'));
        $nombre        = trim((string) $this->request->getPost('nombre'));
        $id_categoria  = $this->request->getPost('id_categoria');
        $id_marca      = $this->request->getPost('id_marca');
        $precio_venta  = $this->request->getPost('precio_venta');
        $stock         = $this->request->getPost('stock');

        // Validaciones de campos
        $errors = [];
        if (empty($codigo_barras)) $errors['codigo_barras'] = 'El código de barras es obligatorio.';
        if (empty($nombre)) $errors['nombre'] = 'El nombre del producto es obligatorio.';
        if (empty($id_categoria)) $errors['id_categoria'] = 'Debe seleccionar una categoría.';
        if (empty($id_marca)) $errors['id_marca'] = 'Debe seleccionar una marca.';
        if ($precio_venta === '' || $precio_venta === null || !is_numeric($precio_venta) || $precio_venta < 0) {
            $errors['precio_venta'] = 'Ingrese un precio válido mayor o igual a 0.';
        }
        if ($stock === '' || $stock === null || !is_numeric($stock) || $stock < 0) {
            $errors['stock'] = 'Ingrese un stock válido mayor o igual a 0.';
        }

        if (!empty($errors)) {
            return $this->response->setJSON(['status' => 'error', 'errors' => $errors]);
        }

        // Validar código de barras único
        $existeCodigo = $this->productoModel
            ->where('codigo_barras', $codigo_barras)
            ->where('id_producto !=', $id ?: 0)
            ->first();

        if ($existeCodigo) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['codigo_barras' => 'Este código de barras ya existe.']
            ]);
        }

        $data = [
            'codigo_barras' => $codigo_barras,
            'nombre'        => $nombre,
            'id_categoria'  => $id_categoria,
            'id_marca'      => $id_marca,
            'precio_venta'  => $precio_venta,
            'stock'         => $stock,
        ];

        if (!empty($id)) {
            $guardado = $this->productoModel->update($id, $data);
        } else {
            $guardado = $this->productoModel->insert($data);
        }

        if (!$guardado) {
            return $this->response->setJSON(['status' => 'error', 'errors' => $this->productoModel->errors()]);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => !empty($id) ? 'Producto actualizado con éxito.' : 'Producto registrado con éxito.'
        ]);
    }

    public function obtener($id = null)
    {
        if ($this->request->isAJAX() && $id !== null) {
            $producto = $this->productoModel->find($id);
            if ($producto) {
                return $this->response->setJSON(['status' => 'success', 'data' => $producto]);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'Producto no encontrado.']);
        }
        return $this->response->setStatusCode(400);
    }

    public function eliminar($id = null)
    {
        if ($this->request->isAJAX() && $id !== null) {
            if ($this->productoModel->delete($id)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Producto eliminado con éxito.']);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'No se pudo eliminar el producto.']);
        }
        return $this->response->setStatusCode(400);
    }
}