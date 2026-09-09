<?php

namespace App\Models;

use CodeIgniter\Model;

class CompraModel extends Model
{
    protected $table            = 'compra';
    protected $primaryKey       = 'id_compra';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['fecha', 'id_proveedor', 'id_usuario', 'total'];
}