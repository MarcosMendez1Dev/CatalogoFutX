<?php
namespace Controllers;
use MVC\Router;
use Model\Productos;
use Model\Categoria;
use Model\Marcas;
use Model\Ordenes;
use Model\Deportes;

class AdminController{
    public static function index(Router $router){
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        isAdmin();

        //Consultar Base de Datos
        $consulta = "SELECT
        o.idordenes,
        CONCAT(u.nombre, ' ', u.apellido) AS cliente,
        o.direccion,
        o.estado,
        o.telefono,
        o.fecha,
        p.nombre AS producto,
        d.cantidad,
        d.precio_unitario,
        (d.cantidad * d.precio_unitario) AS subtotal,
        o.total AS total_orden
    FROM ordenes AS o
    INNER JOIN usuarios AS u
        ON o.idcliente = u.id
    INNER JOIN detalleorden AS d
        ON o.idordenes = d.idordenes
    INNER JOIN productos AS p
        ON d.idproducto = p.id
    WHERE o.estado = 'pendiente'
    ORDER BY o.idordenes DESC";
        // Execute the query and fetch results

        $ordenes= Ordenes::consultarSQL($consulta);

        // Consultar productos para la lista de edición
        $productos = Productos::all();

       $router ->render('admin/index', [
        'nombre'=>$_SESSION['nombre'],
        'ordenes' => $ordenes,
        'productos' => $productos
       ]);
    }


    public static function agregarProducto(Router $router) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    
        $producto = new Productos;
        $alertas = [];
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $producto->sincronizar($_POST);

            // Validar IDs numéricos o nulos
            $producto->categoria_id = (!empty($producto->categoria_id) && is_numeric($producto->categoria_id) && intval($producto->categoria_id) > 0)
                ? (int) $producto->categoria_id
                : null;

            $producto->marca = (!empty($producto->marca) && is_numeric($producto->marca) && intval($producto->marca) > 0)
                ? (int) $producto->marca
                : null;

            $producto->deporte_id = (!empty($producto->deporte_id) && is_numeric($producto->deporte_id) && intval($producto->deporte_id) > 0)
                ? (int) $producto->deporte_id
                : null;

            // Manejo de imágenes múltiples
            if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
                $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'avif'];
                $uploadFileDir = __DIR__ . '/../public/imagenes/';

                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }

                $uploadedFileNames = [];

                foreach ($_FILES['imagenes']['name'] as $key => $fileName) {
                    if (empty($fileName)) continue;

                    $fileTmpPath = $_FILES['imagenes']['tmp_name'][$key];
                    $fileNameCmps = explode(".", $fileName);
                    $fileExtension = strtolower(end($fileNameCmps));

                    if (in_array($fileExtension, $allowedfileExtensions)) {
                        $newFileName = md5(time() . $fileName . $key) . '.' . $fileExtension;
                        $dest_path = $uploadFileDir . $newFileName;

                        if (move_uploaded_file($fileTmpPath, $dest_path)) {
                            $uploadedFileNames[] = $newFileName;
                        } else {
                            $alertas['error'][] = 'Error al mover el archivo subido: ' . $fileName;
                        }
                    } else {
                        $alertas['error'][] = 'Tipo de archivo no permitido para archivo: ' . $fileName;
                    }
                }

                // Convertir array de nombres a JSON antes de guardar
                if (!empty($uploadedFileNames)) {
                    $producto->imagenes = $uploadedFileNames; // ← array
                } else {
                    $producto->imagenes = [];
                }
            }

            $alertas = array_merge($alertas, $producto->validarProducto());

            // Validar que se hayan subido imágenes
            if (!isset($_FILES['imagenes']) || empty($_FILES['imagenes']['name'][0])) {
                $alertas['error'][] = 'Debe subir al menos una imagen del producto';
            }

            if (empty($alertas)) {
                // Guardar producto en la base de datos
                $resultado = $producto->crear();

                if (!$resultado['resultado']) {
                    $alertas['error'][] = 'Error al guardar el producto en la base de datos.';
                } else {
                    header('Location: /admin');
                    exit;
                }
            } else {
                $categorias = Categoria::all();
                $marcas = Marcas::all();
                $deportes = Deportes::all();
                $productos = Productos::all();
                $router->render('admin/agregar-producto', [
                    'producto' => $producto,
                    'categorias' => $categorias,
                    'marcas' => $marcas,
                    'deportes' => $deportes,
                    'alertas' => $alertas,
                    'productos' => $productos
                ]);
            }
    
        } else {
            $categorias = Categoria::all();
            $marcas = Marcas::all();
            $deportes = Deportes::all();
            $productos = Productos::all();
            $router->render('admin/agregar-producto', [
                'producto' => $producto,
                'categorias' => $categorias,
                'marcas' => $marcas,
                'deportes' => $deportes,
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
        $marcas = Marcas::all();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $id = $_POST['id'] ?? null;
            if(!$id) {
                header('Location: /admin');
                exit;
            }
            $producto = Productos::find($id);
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

            // Ensure marca is a valid integer or null
            if(empty($producto->marca) || !is_numeric($producto->marca) || intval($producto->marca) <= 0) {
                $producto->marca = null;
            } else {
                $producto->marca = (int) $producto->marca;
            }

            // Ensure deporte_id is a valid integer or null
            if(empty($producto->deporte_id) || !is_numeric($producto->deporte_id) || intval($producto->deporte_id) <= 0) {
                $producto->deporte_id = null;
            } else {
                $producto->deporte_id = (int) $producto->deporte_id;
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
                // Si no hay ID, mostrar lista de productos para seleccionar
                $productos = Productos::all();
                $deportes = Deportes::all();
                $router->render('admin/editar-producto', [
                    'productos' => $productos,
                    'categorias' => $categorias,
                    'marcas' => $marcas,
                    'deportes' => $deportes,
                    'alertas' => $alertas
                ]);
                return;
            }
            $producto = Productos::find($id);
            if(!$producto) {
                header('Location: /admin');
                exit;
            }
        }

        $deportes = Deportes::all();
        $router->render('admin/editar-producto', [
            'producto' => $producto,
            'categorias' => $categorias,
            'marcas' => $marcas,
            'deportes' => $deportes,
            'alertas' => $alertas
        ]);
    }

    public static function agregarCategoria(Router $router) {
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        isAdmin();

        $categoria = new Categoria;
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoria->sincronizar($_POST);

            // Handle image upload
            if(isset($_FILES['imagen']) && !empty($_FILES['imagen']['name'])) {
                $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'avif'];
                $uploadFileDir = __DIR__ . '/../public/imagenes/';
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }
                $fileTmpPath = $_FILES['imagen']['tmp_name'];
                $fileName = $_FILES['imagen']['name'];
                $fileSize = $_FILES['imagen']['size'];
                $fileType = $_FILES['imagen']['type'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $dest_path = $uploadFileDir . $newFileName;

                    if(move_uploaded_file($fileTmpPath, $dest_path)) {
                        $categoria->imagen = $newFileName;
                    } else {
                        $alertas['error'][] = 'Error al mover el archivo subido: ' . $fileName;
                    }
                } else {
                    $alertas['error'][] = 'Tipo de archivo no permitido para archivo: ' . $fileName;
                }
            }

            $alertas = $categoria->validarCategoria();

            if(empty($alertas)) {
                $resultado = $categoria->crear();
                if($resultado['resultado']) {
                    header('Location: /admin');
                    exit;
                } else {
                    $alertas['error'][] = 'Error al guardar la categoría en la base de datos.';
                }
            }
        }

        $router->render('admin/agregar-categoria', [
            'categoria' => $categoria,
            'alertas' => $alertas
        ]);
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

    public static function agregarMarca(Router $router) {
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        isAdmin();

        $marca = new Marcas;
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $marca->sincronizar($_POST);

            // Handle image upload
            if(isset($_FILES['imagen']) && !empty($_FILES['imagen']['name'])) {
                $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'avif'];
                $uploadFileDir = __DIR__ . '/../public/imagenes/';
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }
                $fileTmpPath = $_FILES['imagen']['tmp_name'];
                $fileName = $_FILES['imagen']['name'];
                $fileSize = $_FILES['imagen']['size'];
                $fileType = $_FILES['imagen']['type'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $dest_path = $uploadFileDir . $newFileName;

                    if(move_uploaded_file($fileTmpPath, $dest_path)) {
                        $marca->imagen = $newFileName;
                    } else {
                        $alertas['error'][] = 'Error al mover el archivo subido: ' . $fileName;
                    }
                } else {
                    $alertas['error'][] = 'Tipo de archivo no permitido para archivo: ' . $fileName;
                }
            }

            $alertas = $marca->validarMarcas();

            if(empty($alertas)) {
                $resultado = $marca->crear();
                if($resultado['resultado']) {
                    header('Location: /admin');
                    exit;
                } else {
                    $alertas['error'][] = 'Error al guardar la marca en la base de datos.';
                }
            }
        }

        $router->render('admin/agregar-marca', [
            'marca' => $marca,
            'alertas' => $alertas
        ]);
    }

    public static function editarMarca(Router $router) {
        $router->render('admin/editar-marca');
    }

    public static function agregarDeporte(Router $router) {
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        isAdmin();

        $deporte = new Deportes;
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $deporte->sincronizar($_POST);

            // Handle image upload
            if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $fileName = $_FILES['imagen']['name'];
                $fileTmpPath = $_FILES['imagen']['tmp_name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if(in_array($fileExtension, $allowedfileExtensions)) {
                    $newFileName = md5(uniqid()) . '.' . $fileExtension;
                    $uploadPath = __DIR__ . '/../public/imagenes/' . $newFileName;

                    if(move_uploaded_file($fileTmpPath, $uploadPath)) {
                        $deporte->imagen = $newFileName;
                    } else {
                        $alertas['error'][] = 'Error al subir la imagen.';
                    }
                } else {
                    $alertas['error'][] = 'Tipo de archivo no permitido para la imagen.';
                }
            } else {
                $alertas['error'][] = 'La imagen es obligatoria.';
            }

            $alertas = array_merge($alertas, $deporte->validarDeporte());

            if(empty($alertas)) {
                $resultado = $deporte->crear();
                if($resultado['resultado']) {
                    header('Location: /admin');
                    exit;
                } else {
                    $alertas['error'][] = 'Error al guardar el deporte en la base de datos.';
                }
            }
        }

        $router->render('admin/agregar-deporte', [
            'deporte' => $deporte,
            'alertas' => $alertas
        ]);
    }

    public static function editarDeporte(Router $router) {
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        isAdmin();

        $alertas = [];
        $deporte = null;

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $id = $_POST['id'] ?? null;
            if(!$id) {
                header('Location: /admin');
                exit;
            }
            $deporte = Deportes::find($id);
            if(!$deporte) {
                header('Location: /admin');
                exit;
            }
            $deporte->sincronizar($_POST);

            // Handle image upload if provided
            if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $fileName = $_FILES['imagen']['name'];
                $fileTmpPath = $_FILES['imagen']['tmp_name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if(in_array($fileExtension, $allowedfileExtensions)) {
                    $newFileName = md5(uniqid()) . '.' . $fileExtension;
                    $uploadPath = __DIR__ . '/../public/imagenes/' . $newFileName;

                    if(move_uploaded_file($fileTmpPath, $uploadPath)) {
                        $deporte->imagen = $newFileName;
                    } else {
                        $alertas['error'][] = 'Error al subir la imagen.';
                    }
                } else {
                    $alertas['error'][] = 'Tipo de archivo no permitido para la imagen.';
                }
            }

            $alertas = array_merge($alertas, $deporte->validarDeporte());

            if(empty($alertas)){
                $resultado = $deporte->guardar();
                if($resultado['resultado']){
                    header('Location: /admin');
                    exit;
                } else {
                    $alertas['error'][] = 'Error al guardar el deporte en la base de datos.';
                }
            }
        } else {
            $id = $_GET['id'] ?? null;
            if(!$id) {
                // Si no hay ID, mostrar lista de deportes para seleccionar
                $deportes = Deportes::all();
                $router->render('admin/editar-deporte', [
                    'deportes' => $deportes,
                    'alertas' => $alertas
                ]);
                return;
            }
            $deporte = Deportes::find($id);
            if(!$deporte) {
                header('Location: /admin');
                exit;
            }
        }

        $router->render('admin/editar-deporte', [
            'deporte' => $deporte,
            'alertas' => $alertas
        ]);
    }

    public static function verOrdenes(Router $router) {
        $router->render('admin/ordenes');
    }

    public static function aprobarOrden(Router $router) {
        // Logic to approve order
        // For now, just redirect back
        header('Location: /admin');
        exit;
    }

    public static function rechazarOrden(Router $router) {
        // Logic to reject order
        // For now, just redirect back
        header('Location: /admin');
        exit;
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
            $productos = Productos::all();

            // Update destacado field for each product
            foreach($productos as $producto) {
                $destacadoValue = in_array($producto->id, $destacados) ? 1 : 0;
                Productos::destacarProducto($producto->id, $destacadoValue);
            }

            // Redirect to avoid resubmission
            header('Location: /destacar-productos');
            exit;
        }

        // Fetch products and categories
        $productos = Productos::all();
        $categorias = Categoria::all();
        $marcas = Marcas::all();

        // Render view with data
        $router->render('admin/destacar-producto', [
            'productos' => $productos,
            'categorias' => $categorias,
            'marcas' => $marcas
        ]);
    }
}
