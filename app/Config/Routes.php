<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/averias', 'Averias::index');
$routes->get('/averias/registrar', 'Averias::registrar');
$routes->post('/averias/guardar', 'Averias::guardar');
