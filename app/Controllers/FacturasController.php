<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\VentaModel;
use App\Models\DetalleVentaModel;
use App\Models\ProductoModel;
use App\Models\ClienteModel;

class FacturasController extends BaseController
{
    public function index()
    {
        return view('facturacion/index');
    }

    // Muestra la vista del Historial de Facturas
    public function historial()
    {
        $ventaModel = new VentaModel();

        // Consulta usando las tablas en singular ('venta' y 'cliente') y ordenando por 'fecha'
        $data['ventas'] = $ventaModel->select('venta.*, cliente.nombre as cliente')
                                     ->join('cliente', 'cliente.id_cliente = venta.id_cliente')
                                     ->orderBy('venta.fecha', 'DESC')
                                     ->findAll();

        return view('facturacion/historial', $data);
    }

    // Buscar clientes por coincidencia de nombre o identificación
    public function buscarClientes()
    {
        $term = $this->request->getGet('q');
        $clienteModel = new ClienteModel();

        $clientes = $clienteModel->like('nombre', $term)
                                 ->orLike('identificacion', $term)
                                 ->findAll(10);

        return $this->response->setJSON($clientes);
    }

    // Buscar productos por coincidencia de nombre o código de barras
    public function buscarProductos()
    {
        $term = $this->request->getGet('q');
        $productoModel = new ProductoModel();

        $productos = $productoModel->like('nombre', $term)
                                   ->orLike('codigo_barras', $term)
                                   ->where('stock >', 0)
                                   ->findAll(10);

        return $this->response->setJSON($productos);
    }

    // Procesa y guarda la factura
    public function guardar()
    {
        $json = $this->request->getJSON();

        if (!$json || empty($json->id_cliente) || empty($json->detalles)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Datos incompletos para procesar la factura.'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $productoModel = new ProductoModel();
            $ventaModel = new VentaModel();
            $detalleModel = new DetalleVentaModel();

            // 1. Validar disponibilidad de stock antes de registrar
            foreach ($json->detalles as $item) {
                $producto = $productoModel->find($item->id_producto);
                if (!$producto) {
                    throw new \Exception("El producto con ID {$item->id_producto} no existe.");
                }
                if ($producto['stock'] < $item->cantidad) {
                    throw new \Exception("Stock insuficiente para '{$producto['nombre']}'. Disponible: {$producto['stock']}.");
                }
            }

            // 2. Insertar cabecera de la factura (venta)
            $idUsuario = session()->get('id_usuario') ?? 1; // ID fallback si aplica
            $idVenta = $ventaModel->insert([
                'id_cliente' => $json->id_cliente,
                'id_usuario' => $idUsuario,
                'total'      => $json->total,
                'fecha'      => date('Y-m-d H:i:s')
            ]);

            // 3. Insertar detalle y actualizar stock
            foreach ($json->detalles as $item) {
                $detalleModel->insert([
                    'id_venta'        => $idVenta,
                    'id_producto'     => $item->id_producto,
                    'cantidad'        => $item->cantidad,
                    'precio_unitario' => $item->precio_unitario,
                    'subtotal'        => $item->subtotal
                ]);

                // Disminuir stock
                $productoModel->where('id_producto', $item->id_producto)
                              ->set('stock', 'stock - ' . (int)$item->cantidad, false)
                              ->update();
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al procesar la factura en base de datos.'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Factura registrada con éxito.',
                'id_venta' => $idVenta
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}