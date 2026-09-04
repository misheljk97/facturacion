<?php
namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuario';
    protected $primaryKey       = 'id_usuario';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nombre', 'correo', 'clave', 'rol', 'estado'];

    protected $validationRules = [
        'nombre' => 'required|min_length[3]|max_length[100]',
        'correo' => 'required|valid_email|max_length[100]',
        'rol'    => 'required|in_list[administrador,encargado]',
    ];

    protected $validationMessages = [
        'nombre' => [
            'required'   => 'El nombre del usuario es obligatorio.',
            'min_length' => 'El nombre debe tener al menos 3 caracteres.',
            'max_length' => 'El nombre no puede superar los 100 caracteres.',
        ],
        'correo' => [
            'required'    => 'El correo electrónico es obligatorio.',
            'valid_email' => 'Proporcione una dirección de correo válida.',
            'max_length'  => 'El correo no puede superar los 100 caracteres.',
        ],
        'rol' => [
            'required' => 'El rol del usuario es obligatorio.',
            'in_list'  => 'El rol seleccionado no es válido.',
        ],
    ];
}