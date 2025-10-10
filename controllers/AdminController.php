<?php
namespace Controllers;
use MVC\Router;
use Model\AdminOrden;
use Model\Producto;
use Model\Categoria;

class AdminController{
    public static function index(Router $router){
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        isAdmin();

        //Consultar Base de Datos
        $consulta = "SELECT ordenes.idordenes AS id, productos.nombre as producto, productos.precio as precio, productos.stock, clientes.nombre as nombre, clientes.apellido as apellido, clientes.email as email, clientes.telefono as telefono ";
        $consulta .= " FROM ordenes ";
        $consulta .= " LEFT OUTER JOIN productos ON ordenes.idproducto = productos.id ";
        $consulta .= " LEFT OUTER JOIN clientes ON ordenes.idcliente = clientes.id ";
        //$consulta .= " WHERE productos.nombre LIKE '{$producto}' ";
        // Execute the query and fetch results
        
        $ordenes= AdminOrden::SQL($consulta);


       $router ->render('admin/index', [
        'nombre'=>$_SESSION['nombre'],
        'ordenes' => $ordenes
       ]);
    }


    public static function agregarProducto(Router $router){
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $producto = new Producto;
        $alertas=[];

        if($_SERVER['REQUEST_METHOD']==='POST'){
            $producto->sincronizar($_POST);

            // Ensure categoria_id is a valid integer or null
            if(empty($producto->categoria_id) || !is_numeric($producto->categoria_id) || intval($producto->categoria_id) <= 0) {
                $producto->categoria_id = null;
            } else {
                $producto->categoria_id = (int) $producto->categoria_id;
            }

            // Handle multiple image uploads
            if(isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
                $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $uploadFileDir = __DIR__ . '/../public/imagenes/';
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }
                $uploadedFileNames = [];

                foreach ($_FILES['imagenes']['name'] as $key => $fileName) {
                    $fileTmpPath = $_FILES['imagenes']['tmp_name'][$key];
                    $fileSize = $_FILES['imagenes']['size'][$key];
                    $fileType = $_FILES['imagenes']['type'][$key];
                    $fileNameCmps = explode(".", $fileName);
                    $fileExtension = strtolower(end($fileNameCmps));

                    if (in_array($fileExtension, $allowedfileExtensions)) {
                        $newFileName = md5(time() . $fileName . $key) . '.' . $fileExtension;
                        $dest_path = $uploadFileDir . $newFileName;

                        if(move_uploaded_file($fileTmpPath, $dest_path)) {
                            $uploadedFileNames[] = $newFileName;
                        } else {
                            $alertas['error'][] = 'Error al mover el archivo subido: ' . $fileName;
                        }
                    } else {
                        $alertas['error'][] = 'Tipo de archivo no permitido para archivo: ' . $fileName;
                    }
                }

                if (!empty($uploadedFileNames)) {
                    $producto->imagenes = $uploadedFileNames; // set as array for validation
                }
            }

            $alertas = $producto->validarProducto();

            if(empty($alertas)){
                // Encode imagenes array to JSON string before saving
                if (is_array($producto->imagenes)) {
                    $producto->imagenes = json_encode($producto->imagenes);
                }
                // Use ActiveRecord crear method to insert product
                $resultado = $producto->crear();
                if(!$resultado['resultado']){
                    $alertas['error'][] = 'Error al guardar el producto en la base de datos.';
                } else {
                    header('Location: /agregar-productos');
                    exit;
                }
            } else {
                $categorias = Categoria::all();
                $productos = Producto::all();
                $router->render('admin/agregar-producto', [
                    'producto' => $producto,
                    'categorias' => $categorias,
                    'alertas' => $alertas,
                    'productos' => $productos
                ]);
            }
        }

        else {
            $categorias = Categoria::all();
            $productos = Producto::all();
            $router->render('admin/agregar-producto', [
                'producto' => $producto,
                'categorias' => $categorias,
                'alertas' => $alertas,
                'productos' => $productos
            ]);
        }
    }

    public static function editarProducto(Router $router) {
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $alertas = [];
        $producto = null;
        $categorias = Categoria::all();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $id = $_POST['id'] ?? null;
            if(!$id) {
                header('Location: /admin');
                exit;
            }
            $producto = Producto::find($id);
            if(!$producto) {
                header('Location: /admin');
                exit;
            }
            $producto->sincronizar($_POST);

            // Ensure categoria_id is a valid integer or null
            if(empty($producto->categoria_id) || !is_numeric($producto->categoria_id) || intval($producto->categoria_id) <= 0) {
                $producto->categoria_id = null;
            } else {
                $producto->categoria_id = (int) $producto->categoria_id;
            }

            $alertas = $producto->validarProducto();

            if(empty($alertas)){
                $resultado = $producto->guardar();
                if($resultado['resultado']){
                    header('Location: /admin');
                    exit;
                } else {
                    $alertas['error'][] = 'Error al guardar el producto en la base de datos.';
                }
            }
        } else {
            $id = $_GET['id'] ?? null;
            if(!$id) {
                header('Location: /admin');
                exit;
            }
            $producto = Producto::find($id);
            if(!$producto) {
                header('Location: /admin');
                exit;
            }
        }

        $router->render('admin/editar-producto', [
            'producto' => $producto,
            'categorias' => $categorias,
            'alertas' => $alertas
        ]);
    }

    public static function agregarCategoria(Router $router) {
        $router->render('admin/agregar-categoria');
    }

    public static function editarCategoria(Router $router) {
        $router->render('admin/editar-categoria');
    }

    public static function agregarVenta(Router $router) {
        $router->render('admin/agregar-venta');
    }

    public static function editarVenta(Router $router) {
        $router->render('admin/editar-venta');
    }

    
    public static function destacarProducto(Router $router) {
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        isAdmin();

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get array of product IDs that are checked
            $destacados = $_POST['destacado'] ?? [];

            // Fetch all products
            $productos = Producto::all();

            // Update destacado field for each product
            foreach($productos as $producto) {
                $destacadoValue = in_array($producto->id, $destacados) ? 1 : 0;
                Producto::destacarProducto($producto->id, $destacadoValue);
            }

            // Redirect to avoid resubmission
            header('Location: /destacar-productos');
            exit;
        }

        // Fetch products and categories
        $productos = Producto::all();
        $categorias = Categoria::all();

        // Render view with data
        $router->render('admin/destacar-producto', [
            'productos' => $productos,
            'categorias' => $categorias
        ]);
    }
}
