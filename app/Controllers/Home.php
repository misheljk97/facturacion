<?php

namespace App\Controllers;

use App\Models\VentaModel;
use App\Models\ClienteModel;
use App\Models\ProductoModel;
use CodeIgniter\Controller;

class Home extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // 1. Tarjetas KPI
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        $data['ventas_hoy'] = $db->table('venta')->where("DATE(fecha)", $today)->countAllResults();
        
        $queryIngresos = $db->table('venta')
            ->selectSum('total')
            ->where("DATE_FORMAT(fecha, '%Y-%m')", $thisMonth)
            ->get()->getRow();
        $data['ingresos_mes'] = $queryIngresos->total ?? 0;

        $data['total_clientes'] = $db->table('cliente')->countAllResults();
        $data['stock_alerta'] = $db->table('producto')->where('stock <=', 5)->countAllResults();

        // 2. Productos más vendidos
        $data['top_productos'] = $db->table('detalle_venta dv')
            ->select('p.nombre, SUM(dv.cantidad) as unidades, SUM(dv.subtotal) as ingresos')
            ->join('producto p', 'p.id_producto = dv.id_producto')
            ->groupBy('dv.id_producto')
            ->orderBy('unidades', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        // 3. Alertas de Inventario
        $data['alertas_stock'] = $db->table('producto')
            ->where('stock <=', 5)
            ->limit(5)
            ->get()->getResultArray();

        return view('dashboard', $data);
    }

    // Endpoint AJAX para gráficos
    public function getDataGraficos()
    {
        $db = \Config\Database::connect();

        // Datos últimos 7 días
        $fechas = [];
        $ventas7Dias = [];
        $ingresos7Dias = [];

        for ($i = 6; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-$i days"));
            $fechas[] = date('d/m', strtotime($fecha));

            $res = $db->table('venta')
                ->select('COUNT(id_venta) as total_ventas, COALESCE(SUM(total), 0) as total_ingresos')
                ->where("DATE(fecha)", $fecha)
                ->get()->getRow();

            $ventas7Dias[] = (int)$res->total_ventas;
            $ingresos7Dias[] = (float)$res->total_ingresos;
        }

        return $this->response->setJSON([
            'fechas' => $fechas,
            'ventas' => $ventas7Dias,
            'ingresos' => $ingresos7Dias
        ]);
    }
}