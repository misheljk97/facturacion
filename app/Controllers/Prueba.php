<?php

namespace App\Controllers;

class Prueba extends BaseController
{
    public function index(): string
{
    //echo "hola"
    $dato["nombre"]="Mishel";
    $dato["direccion"]="ibarra";
    return view('prueba/datos');
    
}
}
