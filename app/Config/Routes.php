<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// 1. Rutas Públicas (Login)
$routes->get('login', 'AuthController::index');
$routes->post('login/authenticate', 'AuthController::authenticate');
$routes->get('logout', 'AuthController::logout');

// 2. Rutas Protegidas de Vistas HTML (Solo requieren Login)
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Home::index');
    
    // Vistas principales de módulos
    $routes->get('categorias', 'CategoriasController::index');
    $routes->get('marcas', 'MarcasController::index');
    $routes->get('clientes', 'ClientesController::index');
    $routes->get('proveedores', 'ProveedoresController::index');
    $routes->get('usuarios', 'UsuariosController::index');
    $routes->get('productos', 'ProductosController::index');
});

// 3. Módulo de Facturación
$routes->group('facturas', ['filter' => 'auth'], function($routes) {
    $routes->get('nueva', 'FacturasController::index');
    $routes->get('buscarClientes', 'FacturasController::buscarClientes');
    $routes->get('buscarProductos', 'FacturasController::buscarProductos');
    $routes->post('guardar', 'FacturasController::guardar');
});

// 4. Endpoints AJAX / API (Requieren Login Y Petición AJAX)
$routes->group('categorias', ['filter' => ['auth', 'ajax']], function($routes) {
    $routes->get('listar', 'CategoriasController::listar');
    $routes->post('guardar', 'CategoriasController::guardar');
    $routes->get('obtener/(:num)', 'CategoriasController::obtener/$1');
    $routes->get('eliminar/(:num)', 'CategoriasController::eliminar/$1');
});

$routes->group('marcas', ['filter' => ['auth', 'ajax']], function($routes) {
    $routes->get('listar', 'MarcasController::listar');
    $routes->post('guardar', 'MarcasController::guardar');
    $routes->get('obtener/(:num)', 'MarcasController::obtener/$1');
    $routes->get('eliminar/(:num)', 'MarcasController::eliminar/$1');
});

$routes->group('clientes', ['filter' => ['auth', 'ajax']], function($routes) {
    $routes->get('listar', 'ClientesController::listar');
    $routes->post('guardar', 'ClientesController::guardar');
    $routes->get('obtener/(:num)', 'ClientesController::obtener/$1');
    $routes->get('eliminar/(:num)', 'ClientesController::eliminar/$1');
});

$routes->group('proveedores', ['filter' => ['auth', 'ajax']], function($routes) {
    $routes->get('listar', 'ProveedoresController::listar');
    $routes->post('guardar', 'ProveedoresController::guardar');
    $routes->get('obtener/(:num)', 'ProveedoresController::obtener/$1');
    $routes->get('eliminar/(:num)', 'ProveedoresController::eliminar/$1');
});

$routes->group('usuarios', ['filter' => ['auth', 'ajax']], function($routes) {
    $routes->get('listar', 'UsuariosController::listar');
    $routes->post('guardar', 'UsuariosController::guardar');
    $routes->get('obtener/(:num)', 'UsuariosController::obtener/$1');
    $routes->get('eliminar/(:num)', 'UsuariosController::eliminar/$1');
});

$routes->group('productos', ['filter' => ['auth', 'ajax']], function($routes) {
    $routes->get('listar', 'ProductosController::listar');
    $routes->post('guardar', 'ProductosController::guardar');
    $routes->get('obtener/(:num)', 'ProductosController::obtener/$1');
    $routes->get('eliminar/(:num)', 'ProductosController::eliminar/$1');
});

// 3. Módulo de Facturación
$routes->group('facturas', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'FacturasController::index');       // <- Agrega esta línea
    $routes->get('nueva', 'FacturasController::index');
    $routes->get('buscarClientes', 'FacturasController::buscarClientes');
    $routes->get('buscarProductos', 'FacturasController::buscarProductos');
    $routes->post('guardar', 'FacturasController::guardar');
});

// 3. Módulo de Facturación
$routes->group('facturas', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'FacturasController::index');
    $routes->get('nueva', 'FacturasController::index');
    $routes->get('buscarClientes', 'FacturasController::buscarClientes');
    $routes->get('buscarProductos', 'FacturasController::buscarProductos');
    $routes->post('guardar', 'FacturasController::guardar');
});

// Alias para evitar el error 404 si entras por /facturacion
$routes->get('facturacion', 'FacturasController::index', ['filter' => 'auth']);

// 3. Módulo de Facturación
$routes->group('facturas', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'FacturasController::index');
    $routes->get('nueva', 'FacturasController::index');
    $routes->get('historial', 'FacturasController::historial'); // <- Agrega esta línea
    $routes->get('buscarClientes', 'FacturasController::buscarClientes');
    $routes->get('buscarProductos', 'FacturasController::buscarProductos');
    $routes->post('guardar', 'FacturasController::guardar');
});

$routes->get('home/getDataGraficos', 'Home::getDataGraficos');

$routes->get('compras', 'ComprasController::index');
$routes->post('compras/guardar', 'ComprasController::guardar');

$routes->get('compras/historial', 'ComprasController::historial');
$routes->get('facturas/imprimir/(:num)', 'FacturasController::imprimir/$1');