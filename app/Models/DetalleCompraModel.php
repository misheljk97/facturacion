<?php

namespace App\Models;

use CodeIgniter\Model;

class DetalleCompraModel extends Model
{
    protected $table            = 'detalle_compra';
    protected $primaryKey       = 'id_detalle_compra';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_compra', 'id_producto', 'cantidad', 'costo_unitario', 'subtotal'];
}