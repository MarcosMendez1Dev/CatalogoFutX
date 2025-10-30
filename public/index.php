<?php 

require_once __DIR__ . '/../includes/app.php';

require_once __DIR__ . '/../Router.php';

use MVC\Router;


$router = new Router();

// Ruta de inicio
$router->get('/', [Controllers\HomeController::class, 'inicio']);
$router->get('/deportes', [Controllers\HomeController::class, 'deportes']);
$router->get('/marcas', [Controllers\HomeController::class, 'marcas']);
$router->get('/categorias', [Controllers\HomeController::class, 'categorias']);
$router->get('/sobre-nosotros', [Controllers\HomeController::class, 'sobreNosotros']);
$router->get('/productos_por_categoria', [Controllers\HomeController::class, 'productosPorCategoria']);
$router->get('/producto', [Controllers\HomeController::class, 'producto']);




//Rutas Login
$router->get('/login', [Controllers\LoginController::class, 'login']);
$router->post('/login', [Controllers\LoginController::class, 'login']);
$router->get('/logout', [Controllers\LoginController::class, 'logout']);
$router->get('/perfil', [Controllers\LoginController::class, 'perfil']);
$router->post('/perfil', [Controllers\LoginController::class, 'perfil']);
$router->get('/historial', [Controllers\LoginController::class, 'historial']);
$router->get('/olvide', [Controllers\LoginController::class, 'olvide']);
$router->post('/olvide', [Controllers\LoginController::class, 'olvide']);
$router->get('/recuperar', [Controllers\LoginController::class, 'recuperar']);
$router->post('/recuperar', [Controllers\LoginController::class, 'recuperar']);
$router->get('/crear-cuenta', [Controllers\LoginController::class, 'crear']);
$router->post('/crear-cuenta', [Controllers\LoginController::class, 'crear']);
$router->get('/mensaje', [Controllers\LoginController::class, 'mensaje']);
$router->get('/confirmar-cuenta', [Controllers\LoginController::class, 'confirmar']);

//Rutas Admin
$router->get('/admin', [Controllers\AdminController::class, 'index']);
$router->get('/admin/agregar-producto', [Controllers\AdminController::class, 'agregarProducto']);
$router->post('/admin/agregar-producto', [Controllers\AdminController::class, 'agregarProducto']);
$router->get('/admin/editar-producto', [Controllers\AdminController::class, 'editarProducto']);
$router->post('/admin/editar-producto', [Controllers\AdminController::class, 'editarProducto']);
$router->get('/admin/agregar-categoria', [Controllers\AdminController::class, 'agregarCategoria']);
$router->post('/admin/agregar-categoria', [Controllers\AdminController::class, 'agregarCategoria']);
$router->get('/admin/editar-categoria', [Controllers\AdminController::class, 'editarCategoria']);
$router->get('/admin/agregar-marca', [Controllers\AdminController::class, 'agregarMarca']);
$router->post('/admin/agregar-marca', [Controllers\AdminController::class, 'agregarMarca']);
$router->get('/admin/editar-marca', [Controllers\AdminController::class, 'editarMarca']);
$router->get('/admin/agregar-deporte', [Controllers\AdminController::class, 'agregarDeporte']);
$router->post('/admin/agregar-deporte', [Controllers\AdminController::class, 'agregarDeporte']);
$router->get('/admin/editar-deporte', [Controllers\AdminController::class, 'editarDeporte']);
$router->post('/admin/editar-deporte', [Controllers\AdminController::class, 'editarDeporte']);
$router->get('/admin/agregar-venta', [Controllers\AdminController::class, 'agregarVenta']);
$router->get('/admin/editar-venta', [Controllers\AdminController::class, 'editarVenta']);
$router->get('/admin/destacar-producto', [Controllers\AdminController::class, 'destacarProducto']);
$router->post('/admin/destacar-producto', [Controllers\AdminController::class, 'destacarProducto']);
$router->get('/admin/ordenes', [Controllers\AdminController::class, 'verOrdenes']);
$router->get('/admin/orden/{id}/aprobar', [Controllers\AdminController::class, 'aprobarOrden']);
$router->get('/admin/orden/{id}/rechazar', [Controllers\AdminController::class, 'rechazarOrden']);

// Rutas Carrito
$router->post('/carrito/agregar', [Controllers\CarritoController::class, 'agregar']);
$router->post('/carrito/actualizar', [Controllers\CarritoController::class, 'actualizar']);
$router->post('/carrito/eliminar', [Controllers\CarritoController::class, 'eliminar']);
$router->get('/carrito/ver', [Controllers\CarritoController::class, 'ver']);
$router->get('/checkout', [Controllers\CarritoController::class, 'checkout']);
$router->post('/checkout', [Controllers\CarritoController::class, 'checkout']);

// Ruta de búsqueda
$router->get('/buscar', [Controllers\BuscarController::class, 'buscar']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
