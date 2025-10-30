<?php
namespace Controllers;

use MVC\Router;
use Model\Productos as Producto;
use Model\Deportes as Deportes;
use Model\Marcas as Marcas;
use Model\Categoria as Categoria;

class HomeController{

    public static function inicio(Router $router){
        $productos = Producto::all();
        $router->render('auth/index',[
            'productos' => $productos
        ]);
    }

    public static function deportes(Router $router){
        if(isset($_GET['id'])){
            $productos = Producto::consultarSQL("SELECT * FROM productos WHERE deporte_id = '{$_GET['id']}'");
            $deporte = Deportes::where('iddeporte', $_GET['id']);
            $deportes = Deportes::all();
            $router->render('auth/deportes',[
                'productos' => $productos,
                'deporte' => $deporte,
                'deportes' => $deportes
            ]);
        }else{
            $deportes = Deportes::all();
            $router->render('auth/deportes',[
                'deportes' => $deportes
            ]);
        }
    }

    public static function marcas(Router $router){
        if(isset($_GET['id'])){
            $productos = Producto::consultarSQL("SELECT * FROM productos WHERE marca = '{$_GET['id']}'");
            $marca = Marcas::where('idmarca', $_GET['id']);
            $marcas = Marcas::all();
            $router->render('auth/marcas',[
                'productos' => $productos,
                'marca' => $marca,
                'marcas' => $marcas
            ]);
        }else{
            $marcas = Marcas::all();
            $router->render('auth/marcas',[
                'marcas' => $marcas
            ]);
        }
    }

    public static function categorias(Router $router){
        $categorias = Categoria::all();
        if(isset($_GET['id'])){
            $productos = Producto::consultarSQL("SELECT * FROM productos WHERE categoria_id = '{$_GET['id']}'");
            $categoria = Categoria::where('idcategoria', $_GET['id']);
            $router->render('auth/categorias',[
                'productos' => $productos,
                'categoria' => $categoria,
                'categorias' => $categorias
            ]);
        }else{
            $router->render('auth/categorias',[
                'categorias' => $categorias
            ]);
        }
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
    
    public static function sobreNosotros(Router $router){
        $router->render('auth/sobre-nosotros');
    }

}
