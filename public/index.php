<?php 

require_once __DIR__ . '/../includes/app.php';

require_once __DIR__ . '/../Router.php';

use MVC\Router;

$router = new Router();

// Ruta de inicio
$router->get('/', [Controllers\HomeController::class, 'inicio']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
