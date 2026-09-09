<?php

namespace App\Controllers;

use App\Models\CompraModel;
use App\Models\DetalleCompraModel;
use App\Models\ProductoModel;
use App\Models\ProveedorModel;

class ComprasController extends BaseController
{
    public function index()
    {
        $productoModel = new ProductoModel();
        $proveedorModel = new ProveedorModel();

        $data['productos']   = $productoModel->findAll();
        $data['proveedores'] = $proveedorModel->findAll();

        return view('compras/index', $data);
    }

    // Método para buscar proveedores vía AJAX en el input con autocompletado
    public function buscarProveedores()
    {
        $term = $this->request->getGet('q');
        $proveedorModel = new ProveedorModel();

        $proveedores = $proveedorModel->like('nombre', $term)
                                      ->orLike('identificacion', $term)
                                      ->findAll(10);

        return $this->response->setJSON($proveedores);
    }

    public function historial()
    {
        $db = \Config\Database::connect();

        $data['compras'] = $db->table('compra c')
            ->select('c.id_compra, c.fecha, c.total, p.nombre as proveedor, u.nombre as usuario')
            ->join('proveedor p', 'p.id_proveedor = c.id_proveedor')
            ->join('usuario u', 'u.id_usuario = c.id_usuario')
            ->orderBy('c.fecha', 'DESC')
            ->get()->getResultArray();

        return view('compras/historial', $data);
    }

    public function guardar()
    {
        $request = $this->request;
        $db = \Config\Database::connect();

        $id_proveedor = $request->getPost('id_proveedor');
        $productos    = $request->getPost('productos'); // Array de productos
        $total_compra = $request->getPost('total_compra');
        $id_usuario   = session()->get('id_usuario') ?? 1;

        if (empty($id_proveedor) || empty($productos)) {
            return redirect()->back()->with('error', 'Debe seleccionar un proveedor y al menos un producto.');
        }

        // Iniciar Transacción
        $db->transBegin();

        try {
            // 1. Insertar Cabecera de Compra
            $compraModel = new CompraModel();
            $id_compra = $compraModel->insert([
                'id_proveedor' => $id_proveedor,
                'id_usuario'   => $id_usuario,
                'total'        => $total_compra,
                'fecha'        => date('Y-m-d H:i:s')
            ]);

            $detalleModel  = new DetalleCompraModel();
            $productoModel = new ProductoModel();

            // 2. Insertar Detalles e Incrementar Stock
            foreach ($productos as $item) {
                // Guardar en detalle_compra
                $detalleModel->insert([
                    'id_compra'      => $id_compra,
                    'id_producto'    => $item['id_producto'],
                    'cantidad'       => $item['cantidad'],
                    'costo_unitario' => $item['costo_unitario'],
                    'subtotal'       => $item['subtotal']
                ]);

                // INCREMENTO DE STOCK AUTOMÁTICO
                $db->table('producto')
                   ->where('id_producto', $item['id_producto'])
                   ->set('stock', 'stock + ' . (int)$item['cantidad'], false)
                   ->update();
            }

            // Confirmar transacción si todo salió bien
            if ($db->transStatus() === false) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Ocurrió un error al procesar el ingreso de mercadería.');
            } else {
                $db->transCommit();
                return redirect()->to(base_url('compras/historial'))->with('success', '¡Ingreso de mercadería registrado e inventario actualizado con éxito!');
            }

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}