<?php

namespace Controllers;

use MVC\Router;
use Model\Productos;

class BuscarController {

    public static function buscar(Router $router) {
        $q = trim($_GET['q'] ?? '');

        if (empty($q)) {
            $productos = [];
        } else {
            // Buscar productos por nombre usando el método where del modelo
            $productos = Productos::where('nombre', 'LIKE', '%' . $q . '%');
        }

        $router->render('buscar', [
            'productos' => $productos,
            'query' => $q
        ]);
    }
}
