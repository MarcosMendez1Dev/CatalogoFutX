<?php
namespace Controllers;
use MVC\Router;
use Model\Productos;
use Model\Categoria;
use Model\Marcas;
use Model\Ordenes;
use Model\Deportes;
use Model\Terreno;

class AdminController{
    public static function index(Router $router){
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        isAdmin();

        $alertas = [];

        // Verificar alertas de URL
        if(isset($_GET['alerta'])) {
            switch($_GET['alerta']) {
                case 'orden_aprobada':
                    $alertas['exito'][] = 'Orden aprobada exitosamente';
                    break;
                case 'orden_rechazada':
                    $alertas['exito'][] = 'Orden rechazada exitosamente';
                    break;
                case 'error_aprobar':
                    $alertas['error'][] = 'Error al aprobar la orden';
                    break;
                case 'error_rechazar':
                    $alertas['error'][] = 'Error al rechazar la orden';
                    break;
            }
        }

        //Consultar Base de Datos
        $consulta = "SELECT
        o.idordenes,
        CONCAT(u.nombre, ' ', u.apellido) AS cliente,
        u.email,
        o.direccion_exacta,
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
        foreach ($productos as $prod) {
            if (!is_array($prod->imagenes)) {
                $prod->imagenes = [];
            }
        }

       $router ->render('admin/index', [
        'nombre'=>$_SESSION['nombre'],
        'ordenes' => $ordenes,
        'productos' => $productos,
        'alertas' => $alertas
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
                $terrenos = Terreno::all();
                $productos = Productos::all();
                $router->render('admin/agregar-producto', [
                    'producto' => $producto,
                    'categorias' => $categorias,
                    'marcas' => $marcas,
                    'deportes' => $deportes,
                    'terrenos' => $terrenos,
                    'alertas' => $alertas,
                    'productos' => $productos
                ]);
            }
    
        } else {
            $categorias = Categoria::all();
            $marcas = Marcas::all();
            $deportes = Deportes::all();
            $terrenos = Terreno::all();
            $productos = Productos::all();
            $router->render('admin/agregar-producto', [
                'producto' => $producto,
                'categorias' => $categorias,
                'marcas' => $marcas,
                'deportes' => $deportes,
                'terrenos' => $terrenos,
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
            // Ensure imagenes is always an array
            if (!is_array($producto->imagenes)) {
                $producto->imagenes = [];
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

            // Manejo de imágenes múltiples para edición
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

                // Reemplazar imágenes anteriores con las nuevas
                if (!empty($uploadedFileNames)) {
                    // Eliminar imágenes anteriores del sistema de archivos
                    $imagenesActuales = is_array($producto->imagenes) ? $producto->imagenes : [];
                    foreach ($imagenesActuales as $oldImage) {
                        $oldPath = $uploadFileDir . $oldImage;
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                    $producto->imagenes = $uploadedFileNames;
                }

                // Ensure imagenes is always an array
                if (!is_array($producto->imagenes)) {
                    $producto->imagenes = [];
                }
            }

            $alertas = $producto->validarProducto();

            if (empty($alertas)) {
                $resultado = $producto->guardar();
                if ($resultado === true || (is_array($resultado) && isset($resultado['resultado']) && $resultado['resultado'])) {
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
                foreach ($productos as $prod) {
                    if (!is_array($prod->imagenes)) {
                        $prod->imagenes = [];
                    }
                }
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
        $terrenos = Terreno::all();
        $router->render('admin/editar-producto', [
            'producto' => $producto,
            'categorias' => $categorias,
            'marcas' => $marcas,
            'deportes' => $deportes,
            'terrenos' => $terrenos,
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
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        isAdmin();

        $alertas = [];
        $categoria = null;

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $id = $_POST['id'] ?? null;
            if(!$id) {
                header('Location: /admin');
                exit;
            }
            $categoria = Categoria::find($id);
            if(!$categoria) {
                header('Location: /admin');
                exit;
            }
            $categoria->sincronizar($_POST);

            // Handle image upload if provided
            if(isset($_FILES['imagen']) && !empty($_FILES['imagen']['name'])) {
                $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'avif'];
                $uploadFileDir = __DIR__ . '/../public/imagenes/';
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }
                $fileTmpPath = $_FILES['imagen']['tmp_name'];
                $fileName = $_FILES['imagen']['name'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $dest_path = $uploadFileDir . $newFileName;

                    if(move_uploaded_file($fileTmpPath, $dest_path)) {
                        // Delete old image if exists
                        if(!empty($categoria->imagen)) {
                            $oldPath = $uploadFileDir . $categoria->imagen;
                            if(file_exists($oldPath)) {
                                unlink($oldPath);
                            }
                        }
                        $categoria->imagen = $newFileName;
                    } else {
                        $alertas['error'][] = 'Error al mover el archivo subido: ' . $fileName;
                    }
                } else {
                    $alertas['error'][] = 'Tipo de archivo no permitido para archivo: ' . $fileName;
                }
            }

            $alertas = $categoria->validarCategoria();

            if(empty($alertas)){
                $resultado = $categoria->guardar();
                if($resultado['resultado']){
                    header('Location: /admin');
                    exit;
                } else {
                    $alertas['error'][] = 'Error al guardar la categoría en la base de datos.';
                }
            }
        } else {
            $id = $_GET['id'] ?? null;
            if(!$id) {
                // Si no hay ID, mostrar lista de categorías para seleccionar
                $categorias = Categoria::all();
                $router->render('admin/editar-categoria', [
                    'categorias' => $categorias,
                    'alertas' => $alertas
                ]);
                return;
            }
            $categoria = Categoria::find($id);
            if(!$categoria) {
                header('Location: /admin');
                exit;
            }
        }

        $router->render('admin/editar-categoria', [
            'categoria' => $categoria,
            'alertas' => $alertas
        ]);
    }

    public static function agregarVenta(Router $router) {
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        isAdmin();

        $alertas = [];
        $orden = new \Model\Ordenes;
        $detalle = new \Model\DetalleOrden;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orden->sincronizar($_POST);
            $detalle->sincronizar($_POST);

            // Check if new client data is provided
            $nuevoCliente = false;
            if(!empty($_POST['cliente_nombre']) && !empty($_POST['cliente_email'])) {
                $nuevoCliente = true;
                $cliente = new \Model\Usuario;
                $cliente->nombre = $_POST['cliente_nombre'];
                $cliente->apellido = $_POST['cliente_apellido'] ?? '';
                $cliente->email = $_POST['cliente_email'];
                $cliente->telefono = $_POST['cliente_telefono'] ?? '';
                $cliente->password = password_hash('temporal123', PASSWORD_BCRYPT); // Temporary password
                $cliente->confirmado = 1; // Mark as confirmed
                $cliente->admin = 0; // Not admin
                $cliente->token = uniqid();

                // Validate new client
                if(!$cliente->nombre) {
                    $alertas['error'][] = 'El nombre del cliente es obligatorio';
                }
                if(!$cliente->email) {
                    $alertas['error'][] = 'El email del cliente es obligatorio';
                }
                if($cliente->existeUsuario()) {
                    $alertas['error'][] = 'El cliente ya existe con ese email';
                }
            }

            // Check if new product data is provided
            $nuevoProducto = false;
            if(!empty($_POST['producto_nombre']) && !empty($_POST['producto_precio'])) {
                $nuevoProducto = true;
                $producto = new \Model\Productos;
                $producto->nombre = $_POST['producto_nombre'];
                $producto->colorway = $_POST['producto_colorway'] ?? '';
                $producto->precio = $_POST['producto_precio'];
                $producto->descripcion = $_POST['producto_descripcion'] ?? '';
                $producto->categoria_id = $_POST['producto_categoria'] ?? null;
                $producto->marca = $_POST['producto_marca'] ?? null;
                $producto->deporte_id = $_POST['producto_deporte'] ?? null;
                $producto->imagenes = []; // No images for now

                // Validate new product
                if(!$producto->nombre) {
                    $alertas['error'][] = 'El nombre del producto es obligatorio';
                }
                if(!$producto->precio || $producto->precio <= 0) {
                    $alertas['error'][] = 'El precio del producto debe ser mayor a 0';
                }
            }

            // Validations
            if(!$orden->idcliente && !$nuevoCliente) {
                $alertas['error'][] = 'Debe seleccionar un cliente existente o ingresar datos de un nuevo cliente';
            }
            if(!$detalle->idproducto && !$nuevoProducto) {
                $alertas['error'][] = 'Debe seleccionar un producto existente o ingresar datos de un nuevo producto';
            }
            if(!$detalle->cantidad || $detalle->cantidad <= 0) {
                $alertas['error'][] = 'La cantidad debe ser mayor a 0';
            }
            if(!$detalle->talla) {
                $alertas['error'][] = 'Debe seleccionar una talla';
            }

            if(empty($alertas)) {
                // Create new client if needed
                if($nuevoCliente) {
                    $resultadoCliente = $cliente->crear();
                    if($resultadoCliente['resultado']) {
                        $orden->idcliente = $resultadoCliente['id'];
                    } else {
                        $alertas['error'][] = 'Error al crear el cliente';
                    }
                }

                // Create new product if needed
                if($nuevoProducto) {
                    $resultadoProducto = $producto->crear();
                    if($resultadoProducto['resultado']) {
                        $detalle->idproducto = $resultadoProducto['id'];
                    } else {
                        $alertas['error'][] = 'Error al crear el producto';
                    }
                }

                if(empty($alertas)) {
                    // Get product price
                    $productoFinal = \Model\Productos::find($detalle->idproducto);
                    $detalle->precio_unitario = $productoFinal->precio;
                    $orden->total = $detalle->cantidad * $detalle->precio_unitario;
                    $orden->estado = 'pendiente';
                    $orden->fecha = date('Y-m-d H:i:s');

                    // Save order
                    $resultadoOrden = $orden->crear();
                    if($resultadoOrden['resultado']) {
                        $detalle->idordenes = $resultadoOrden['id'];
                        $resultadoDetalle = $detalle->crear();
                        if($resultadoDetalle['resultado']) {
                            header('Location: /admin');
                            exit;
                        } else {
                            $alertas['error'][] = 'Error al guardar el detalle de la orden';
                        }
                    } else {
                        $alertas['error'][] = 'Error al guardar la orden';
                    }
                }
            }
        }

        // Get data for form
        $clientes = \Model\Usuario::all();
        $productos = \Model\Productos::all();

        $router->render('admin/agregar-venta', [
            'orden' => $orden,
            'detalle' => $detalle,
            'clientes' => $clientes,
            'productos' => $productos,
            'alertas' => $alertas
        ]);
    }

    public static function editarVenta(Router $router) {
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        isAdmin();

        $alertas = [];
        $orden = null;
        $detalle = null;

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $id = $_POST['id'] ?? null;
            if(!$id) {
                header('Location: /admin');
                exit;
            }
            $orden = Ordenes::find($id);
            if(!$orden) {
                header('Location: /admin');
                exit;
            }

            // Get current detail
            $detalleActual = \Model\DetalleOrden::where('idordenes', $id);
            if(!$detalleActual) {
                $alertas['error'][] = 'No se encontró el detalle de la orden';
            } else {
                $detalle = $detalleActual[0];
                $detalle->sincronizar($_POST);
                $orden->sincronizar($_POST);

                // Validations
                if(!$orden->idcliente) {
                    $alertas['error'][] = 'Debe seleccionar un cliente';
                }
                if(!$detalle->idproducto) {
                    $alertas['error'][] = 'Debe seleccionar un producto';
                }
                if(!$detalle->cantidad || $detalle->cantidad <= 0) {
                    $alertas['error'][] = 'La cantidad debe ser mayor a 0';
                }
                if(!$detalle->talla) {
                    $alertas['error'][] = 'Debe seleccionar una talla';
                }

                if(empty($alertas)) {
                    // Get product price
                    $producto = Productos::find($detalle->idproducto);
                    $detalle->precio_unitario = $producto->precio;
                    $orden->total = $detalle->cantidad * $detalle->precio_unitario;

                    // Save changes
                    $resultadoOrden = $orden->guardar();
                    $resultadoDetalle = $detalle->guardar();

                    if($resultadoOrden['resultado'] && $resultadoDetalle['resultado']) {
                        header('Location: /admin');
                        exit;
                    } else {
                        $alertas['error'][] = 'Error al guardar los cambios';
                    }
                }
            }
        } else {
            $id = $_GET['id'] ?? null;
            if(!$id) {
                // Si no hay ID, mostrar lista de órdenes para seleccionar
                $consulta = "SELECT
                o.idordenes,
                CONCAT(u.nombre, ' ', u.apellido) AS cliente,
                u.email,
                o.direccion_exacta,
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
            ORDER BY o.idordenes DESC";
                $ordenes = Ordenes::consultarSQL($consulta);
                $router->render('admin/editar-venta', [
                    'ordenes' => $ordenes,
                    'alertas' => $alertas
                ]);
                return;
            }
            $orden = Ordenes::find($id);
            if(!$orden) {
                header('Location: /admin');
                exit;
            }
            $detalleActual = \Model\DetalleOrden::where('idordenes', $id);
            if(!$detalleActual) {
                $alertas['error'][] = 'No se encontró el detalle de la orden';
            } else {
                $detalle = $detalleActual[0];
            }
        }

        // Get data for form
        $clientes = \Model\Usuario::all();
        $productos = \Model\Productos::all();

        $router->render('admin/editar-venta', [
            'orden' => $orden,
            'detalle' => $detalle,
            'clientes' => $clientes,
            'productos' => $productos,
            'alertas' => $alertas
        ]);
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
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        isAdmin();

        $alertas = [];
        $marca = null;

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $id = $_POST['id'] ?? null;
            if(!$id) {
                header('Location: /admin');
                exit;
            }
            $marca = Marcas::find($id);
            if(!$marca) {
                header('Location: /admin');
                exit;
            }
            $marca->sincronizar($_POST);

            // Handle image upload if provided
            if(isset($_FILES['imagen']) && !empty($_FILES['imagen']['name'])) {
                $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'avif'];
                $uploadFileDir = __DIR__ . '/../public/imagenes/';
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }
                $fileTmpPath = $_FILES['imagen']['tmp_name'];
                $fileName = $_FILES['imagen']['name'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $dest_path = $uploadFileDir . $newFileName;

                    if(move_uploaded_file($fileTmpPath, $dest_path)) {
                        // Delete old image if exists
                        if(!empty($marca->imagen)) {
                            $oldPath = $uploadFileDir . $marca->imagen;
                            if(file_exists($oldPath)) {
                                unlink($oldPath);
                            }
                        }
                        $marca->imagen = $newFileName;
                    } else {
                        $alertas['error'][] = 'Error al mover el archivo subido: ' . $fileName;
                    }
                } else {
                    $alertas['error'][] = 'Tipo de archivo no permitido para archivo: ' . $fileName;
                }
            }

            $alertas = $marca->validarMarcas();

            if(empty($alertas)){
                $resultado = $marca->guardar();
                if($resultado['resultado']){
                    header('Location: /admin');
                    exit;
                } else {
                    $alertas['error'][] = 'Error al guardar la marca en la base de datos.';
                }
            }
        } else {
            $id = $_GET['id'] ?? null;
            if(!$id) {
                // Si no hay ID, mostrar lista de marcas para seleccionar
                $marcas = Marcas::all();
                $router->render('admin/editar-marca', [
                    'marcas' => $marcas,
                    'alertas' => $alertas
                ]);
                return;
            }
            $marca = Marcas::find($id);
            if(!$marca) {
                header('Location: /admin');
                exit;
            }
        }

        $router->render('admin/editar-marca', [
            'marca' => $marca,
            'alertas' => $alertas
        ]);
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
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        isAdmin();

        $id = $_GET['id'] ?? null;
        if(!$id) {
            header('Location: /admin');
            exit;
        }

        $orden = Ordenes::find($id);
        if(!$orden) {
            header('Location: /admin');
            exit;
        }

        $orden->estado = 'aprobada';
        $resultado = $orden->guardar();

        if($resultado['resultado']) {
            header('Location: /admin?alerta=orden_aprobada');
        } else {
            header('Location: /admin?alerta=error_aprobar');
        }
        exit;
    }

    public static function rechazarOrden(Router $router) {
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        isAdmin();

        $id = $_GET['id'] ?? null;
        if(!$id) {
            header('Location: /admin');
            exit;
        }

        $orden = Ordenes::find($id);
        if(!$orden) {
            header('Location: /admin');
            exit;
        }

        $orden->estado = 'rechazada';
        $resultado = $orden->guardar();

        if($resultado['resultado']) {
            header('Location: /admin?alerta=orden_rechazada');
        } else {
            header('Location: /admin?alerta=error_rechazar');
        }
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
