<?php
namespace App\Models;

use CodeIgniter\Model;

class ProveedorModel extends Model
{
    protected $table            = 'proveedor';
    protected $primaryKey       = 'id_proveedor';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['identificacion', 'nombre', 'telefono'];

    protected $validationRules = [
        'identificacion' => 'required|min_length[10]|max_length[20]',
        'nombre'         => 'required|min_length[3]|max_length[100]',
        'telefono'       => 'permit_empty|max_length[20]',
    ];

    protected $validationMessages = [
        'identificacion' => [
            'required'   => 'La identificación o RUC es obligatoria.',
            'min_length' => 'La identificación debe tener al menos 10 caracteres.',
            'max_length' => 'La identificación no puede superar los 20 caracteres.',
        ],
        'nombre' => [
            'required'   => 'El nombre o razón social del proveedor es obligatorio.',
            'min_length' => 'El nombre debe tener al menos 3 caracteres.',
            'max_length' => 'El nombre no puede superar los 100 caracteres.',
        ],
    ];
}