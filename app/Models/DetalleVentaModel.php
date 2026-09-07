<?php

namespace App\Models;

use CodeIgniter\Model;

class DetalleVentaModel extends Model
{
    protected $table            = 'detalle_venta';
    protected $primaryKey       = 'id_detalle_venta';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_venta', 'id_producto', 'cantidad', 'precio_unitario', 'subtotal'];
}