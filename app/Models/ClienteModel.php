<?php
namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table            = 'cliente';
    protected $primaryKey       = 'id_cliente';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['identificacion', 'nombre', 'telefono', 'correo'];

    protected $validationRules = [
        'identificacion' => 'required|min_length[10]|max_length[20]',
        'nombre'         => 'required|min_length[3]|max_length[100]',
        'telefono'       => 'permit_empty|max_length[20]',
        'correo'         => 'permit_empty|valid_email|max_length[100]',
    ];

    protected $validationMessages = [
        'identificacion' => [
            'required'   => 'La identificación es obligatoria.',
            'min_length' => 'La identificación debe tener al menos 10 caracteres.',
            'max_length' => 'La identificación no puede superar los 20 caracteres.',
        ],
        'nombre' => [
            'required'   => 'El nombre del cliente es obligatorio.',
            'min_length' => 'El nombre debe tener al menos 3 caracteres.',
            'max_length' => 'El nombre no puede superar los 100 caracteres.',
        ],
        'correo' => [
            'valid_email' => 'Proporcione una dirección de correo electrónico válida.',
            'max_length'  => 'El correo no puede superar los 100 caracteres.',
        ],
    ];
}