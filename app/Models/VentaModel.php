<?php

namespace App\Models;

use CodeIgniter\Model;

class VentaModel extends Model
{
    protected $table            = 'venta';
    protected $primaryKey       = 'id_venta';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Incluimos created_at por si lo manejas manualmente o con timestamps
    protected $allowedFields    = ['fecha', 'id_cliente', 'id_usuario', 'total', 'created_at'];

    // Si tu tabla usa la columna created_at automáticamente con CodeIgniter, activa esta línea:
    // protected $useTimestamps = true;
}