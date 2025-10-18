<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Vistas
$routes->get('/averias', 'Averias::index');
$routes->get('/averias/registrar', 'Averias::registrar');
$routes->get('/averias/solucionados', 'Averias::solucionados');

// API Endpoints
$routes->post('public/api/averias/registrar', 'Averias::agregarRegistro');
$routes->get('public/api/averias/listar', 'Averias::listarAverias');
$routes->get('public/api/averias/solucionados', 'Averias::listarSolucionados');
$routes->post('public/api/averias/cambiarStatus', 'Averias::cambiarStatus');