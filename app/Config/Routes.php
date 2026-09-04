<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// 1. Rutas Públicas (Login)
$routes->get('login', 'AuthController::index');
$routes->post('login/authenticate', 'AuthController::authenticate');
$routes->get('logout', 'AuthController::logout');

// 2. Rutas Protegidas que devuelven Vistas HTML (Solo requieren Login)
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Home::index');
    $routes->get('facturacion', 'Home::index');
    
    // Vista principal del módulo categorías
    $routes->get('categorias', 'CategoriasController::index');
});

// 3. Rutas de Endpoints / Datos (Requieren Login Y Petición AJAX)
$routes->group('categorias', ['filter' => ['auth', 'ajax']], function($routes) {
    $routes->get('listar', 'CategoriasController::listar');
    $routes->post('guardar', 'CategoriasController::guardar');
    $routes->get('obtener/(:num)', 'CategoriasController::obtener/$1');
    $routes->get('eliminar/(:num)', 'CategoriasController::eliminar/$1');
});  

    // Vista principal del módulo marcas (dentro del grupo con filtro 'auth')
$routes->group('', ['filter' => 'auth'], function($routes) {
    // ... rutas existentes
    $routes->get('marcas', 'MarcasController::index');
});

// Endpoints AJAX (dentro del grupo con filtros 'auth' y 'ajax')
$routes->group('marcas', ['filter' => ['auth', 'ajax']], function($routes) {
    $routes->get('listar', 'MarcasController::listar');
    $routes->post('guardar', 'MarcasController::guardar');
    $routes->get('obtener/(:num)', 'MarcasController::obtener/$1');
    $routes->get('eliminar/(:num)', 'MarcasController::eliminar/$1');

});

// Vista principal del módulo clientes (dentro del grupo con filtro 'auth')
$routes->group('', ['filter' => 'auth'], function($routes) {
    // ... rutas existentes
    $routes->get('clientes', 'ClientesController::index');
});

// Endpoints AJAX (dentro del grupo con filtros 'auth' y 'ajax')
$routes->group('clientes', ['filter' => ['auth', 'ajax']], function($routes) {
    $routes->get('listar', 'ClientesController::listar');
    $routes->post('guardar', 'ClientesController::guardar');
    $routes->get('obtener/(:num)', 'ClientesController::obtener/$1');
    $routes->get('eliminar/(:num)', 'ClientesController::eliminar/$1');
});
// Vista principal del módulo proveedores (dentro del grupo con filtro 'auth')
$routes->group('', ['filter' => 'auth'], function($routes) {
    // ... rutas existentes
    $routes->get('proveedores', 'ProveedoresController::index');
});

// Endpoints AJAX (dentro del grupo con filtros 'auth' y 'ajax')
$routes->group('proveedores', ['filter' => ['auth', 'ajax']], function($routes) {
    $routes->get('listar', 'ProveedoresController::listar');
    $routes->post('guardar', 'ProveedoresController::guardar');
    $routes->get('obtener/(:num)', 'ProveedoresController::obtener/$1');
    $routes->get('eliminar/(:num)', 'ProveedoresController::eliminar/$1');
});