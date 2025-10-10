<?php
namespace Controllers;

use MVC\Router;
use Model\Productos as Producto;

class HomeController{

    public static function inicio(Router $router){
        $productos = Producto::all();
        $router->render('index',[
            'productos' => $productos
        ]);
    }

    public static function productos(Router $router){
        $productos = Producto::all();
        $router->render('auth/productos',[
            'productos' => $productos
        ]);
    }

    public static function producto(Router $router){
        if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
            header('Location: /productos');
            exit;
        }
        $id = (int) $_GET['id'];
        $producto = Producto::find($id);
        if(!$producto){
            header('Location: /productos');
            exit;
        }
        $router->render('auth/producto', [
            'producto' => $producto
        ]);
    }

    
}
