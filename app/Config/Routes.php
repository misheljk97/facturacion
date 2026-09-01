<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */


// Rutas Públicas (Login)
$routes->get('login', 'AuthController::index');
$routes->post('login/authenticate', 'AuthController::authenticate');
$routes->get('logout', 'AuthController::logout');

// Rutas Protegidas (Requieren autenticación)
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Home::index');
    $routes->get('facturacion', 'Home::index');
    // Registra aquí los demás módulos protegidos...



    $routes->group('', ['filter' => 'auth'], function($routes) {
        $routes->get('/', 'Home::index');
        $routes->get('facturacion', 'Home::index');
    
        // Módulo Categorías
        $routes->get('categorias', 'CategoriasController::index');
        $routes->get('categorias/listar', 'CategoriasController::listar');
        $routes->post('categorias/guardar', 'CategoriasController::guardar');
        $routes->get('categorias/obtener/(:num)', 'CategoriasController::obtener/$1');
        $routes->get('categorias/eliminar/(:num)', 'CategoriasController::eliminar/$1');
    });
});
