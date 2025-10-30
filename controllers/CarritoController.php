<?php
namespace Controllers;

use MVC\Router;
use Model\Productos;
use Model\Usuario;
use Model\Ordenes;
use Model\DetalleOrden;

class CarritoController {

    public static function agregar() {
        // session_start() ya se llama en Router.php
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $id = $_POST['id'] ?? null;
        $talla = $_POST['talla'] ?? null;
        if (!$id || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'ID de producto inválido']);
            return;
        }

        $producto = Productos::find($id);
        if (!$producto) {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            return;
        }

        // Permitir agregar productos con stock 0 (productos internacionales)

        // Crear clave única para producto con talla
        $clave = $id . '_' . $talla;

        if (isset($_SESSION['carrito'][$clave])) {
            if ($producto->stock > 0 && $_SESSION['carrito'][$clave]['cantidad'] >= $producto->stock) {
                echo json_encode(['success' => false, 'message' => 'No hay suficiente stock']);
                return;
            }
            $_SESSION['carrito'][$clave]['cantidad']++;
        } else {
            $_SESSION['carrito'][$clave] = [
                'id' => $id,
                'talla' => $talla,
                'cantidad' => 1
            ];
        }

        echo json_encode(['success' => true, 'message' => 'Producto agregado al carrito']);
    }

    public static function actualizar() {
        // session_start() ya se llama en Router.php
        $clave = $_POST['clave'] ?? null;
        $cantidad = $_POST['cantidad'] ?? null;

        if (!$clave || !$cantidad || !is_numeric($cantidad) || $cantidad < 0) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            return;
        }

        if (!isset($_SESSION['carrito'][$clave])) {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado en el carrito']);
            return;
        }

        $id = $_SESSION['carrito'][$clave]['id'];
        $producto = Productos::find($id);
        if (!$producto) {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            return;
        }

        if ($producto->stock > 0 && $cantidad > $producto->stock) {
            echo json_encode(['success' => false, 'message' => 'Cantidad supera el stock disponible']);
            return;
        }

        if ($cantidad == 0) {
            unset($_SESSION['carrito'][$clave]);
        } else {
            $_SESSION['carrito'][$clave]['cantidad'] = $cantidad;
        }

        echo json_encode(['success' => true, 'message' => 'Cantidad actualizada']);
    }

    public static function eliminar() {
        // session_start() ya se llama en Router.php
        $clave = $_POST['clave'] ?? null;

        if (!$clave) {
            echo json_encode(['success' => false, 'message' => 'Clave inválida']);
            return;
        }

        unset($_SESSION['carrito'][$clave]);
        echo json_encode(['success' => true, 'message' => 'Producto eliminado']);
    }

    public static function ver() {
        // session_start() ya se llama en Router.php
        $carrito = $_SESSION['carrito'] ?? [];
        $productos = [];
        $total = 0;

        foreach ($carrito as $clave => $item) {
            if (is_array($item)) {
                // Nuevo formato: ['id' => ..., 'talla' => ..., 'cantidad' => ...]
                $id = $item['id'];
                $talla = $item['talla'];
                $cantidad = $item['cantidad'];
            } else {
                // Formato antiguo: $carrito[$id] = cantidad
                $id = $clave;
                $talla = '';
                $cantidad = $item;
            }

            $producto = Productos::find($id);
            if ($producto) {
                $productos[] = [
                    'clave' => $clave,
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'precio' => $producto->precio,
                    'talla' => $talla,
                    'cantidad' => $cantidad,
                    'subtotal' => $producto->precio * $cantidad,
                    'imagen' => !empty($producto->imagenes) ? $producto->imagenes[0] : ''
                ];
                $total += $producto->precio * $cantidad;
            }
        }

        ob_start();
        ?>
        <h2>Carrito de Compras</h2>
        <?php if (empty($productos)): ?>
            <p>El carrito está vacío.</p>
        <?php else: ?>
            <div class="carrito-items">
                <?php foreach ($productos as $item): ?>
                    <div class="carrito-item" data-clave="<?= $item['clave'] ?>">
                        <img src="/imagenes/<?= htmlspecialchars($item['imagen']) ?>" alt="<?= htmlspecialchars($item['nombre']) ?>" width="50">
                        <div class="item-info">
                            <h4><?= htmlspecialchars($item['nombre']) ?></h4>
                            <p>Talla: <?= htmlspecialchars($item['talla']) ?></p>
                            <p>Q<?= number_format($item['precio'], 2) ?></p>
                            <div class="cantidad-controls">
                                <button class="btn-menos">-</button>
                                <span class="cantidad"><?= $item['cantidad'] ?></span>
                                <button class="btn-mas">+</button>
                            </div>
                        </div>
                        <div class="item-total">
                            <p>Q<?= number_format($item['subtotal'], 2) ?></p>
                            <button class="btn-eliminar">Eliminar</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="carrito-total">
                <h3>Total: Q<?= number_format($total, 2) ?></h3>
                <button class="btn-comprar">Proceder al Pago</button>
            </div>
        <?php endif; ?>
        <button id="closeCarrito">Cerrar</button>
        <?php
        $html = ob_get_clean();
        echo $html;
    }

    public static function checkout($router) {
        // session_start() ya se llama en Router.php

        $carrito = $_SESSION['carrito'] ?? [];
        if (empty($carrito)) {
            // Para solicitudes AJAX, devolver JSON en lugar de redirigir
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode(['success' => false, 'message' => 'El carrito está vacío', 'redirect' => '/']);
                return;
            }
            header('Location: /');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Mostrar formulario de checkout
            $productos = [];
            $total = 0;

            foreach ($carrito as $clave => $item) {
                $producto = Productos::find($item['id']);
                if ($producto) {
                    $productos[] = [
                        'id' => $producto->id,
                        'nombre' => $producto->nombre,
                        'precio' => $producto->precio,
                        'talla' => $item['talla'],
                        'cantidad' => $item['cantidad'],
                        'subtotal' => $producto->precio * $item['cantidad'],
                        'imagen' => !empty($producto->imagenes) ? $producto->imagenes[0] : ''
                    ];
                    $total += $producto->precio * $item['cantidad'];
                }
            }

            // Obtener datos del usuario solo si está logeado
            $usuario = isset($_SESSION['login']) ? Usuario::find($_SESSION['id']) : null;

            $router->render('auth/checkout', [
                'productos' => $productos,
                'total' => $total,
                'usuario' => $usuario
            ]);
        } else {
            // Procesar formulario de checkout
            header('Content-Type: application/json; charset=utf-8');
            ini_set('display_errors', 0);
            error_reporting(0);
            ob_clean(); // Limpiar cualquier salida previa

            try {
                $nombre = trim($_POST['nombre'] ?? '');
                $apellido = trim($_POST['apellido'] ?? '');
                $departamento = trim($_POST['departamento'] ?? '');
                $municipio = trim($_POST['municipio'] ?? '');
                $direccion_exacta = trim($_POST['direccion_exacta'] ?? '');
                $telefono = trim($_POST['telefono'] ?? '');
                $nit = trim($_POST['nit'] ?? '');

                if (empty($nombre) || empty($apellido) || empty($departamento) || empty($municipio) || empty($direccion_exacta) || empty($telefono)) {
                    ob_clean();
                    echo json_encode(['success' => false, 'message' => 'Todos los campos marcados con * son obligatorios']);
                    exit;
                }

                // Verificar stock antes de proceder (solo para productos con stock > 0)
                foreach ($carrito as $clave => $item) {
                    $producto = Productos::find($item['id']);
                    if (!$producto || ($producto->stock > 0 && $producto->stock < $item['cantidad'])) {
                        ob_clean();
                        echo json_encode(['success' => false, 'message' => 'Stock insuficiente para ' . ($producto->nombre ?? 'un producto')]);
                        exit;
                    }
                }

                // Crear orden
                $total = array_sum(array_map(function($clave, $item) {
                    $producto = Productos::find($item['id']);
                    return $producto ? $producto->precio * $item['cantidad'] : 0;
                }, array_keys($carrito), $carrito));

                $orden = new Ordenes([
                    'idcliente' => isset($_SESSION['login']) ? $_SESSION['id'] : null,
                    'departamento' => $departamento,
                    'municipio' => $municipio,
                    'direccion_exacta' => $direccion_exacta,
                    'total' => $total,
                    'estado' => 'pendiente',
                    'telefono' => $telefono,
                    'fecha' => date('Y-m-d H:i:s')
                ]);

                $resultado = $orden->guardar();
                if (!$resultado['resultado']) {
                    ob_clean();
                    echo json_encode(['success' => false, 'message' => 'Error al crear la orden']);
                    exit;
                }

                // Crear detalles de orden y actualizar stock (solo para productos con stock > 0)
                foreach ($carrito as $clave => $item) {
                    $producto = Productos::find($item['id']);
                    $detalle = new DetalleOrden([
                        'idordenes' => $orden->idordenes,
                        'idproducto' => $item['id'],
                        'talla' => $item['talla'] ?? '',
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $producto->precio
                    ]);
                    $resultado_detalle = $detalle->guardar();
                    if (!$resultado_detalle['resultado']) {
                        ob_clean();
                        echo json_encode(['success' => false, 'message' => 'Error al crear detalle de orden']);
                        exit;
                    }

                    // Actualizar stock solo si el producto tiene stock > 0
                    if ($producto->stock > 0) {
                        $producto->stock -= $item['cantidad'];
                        $producto->guardar();
                    }
                }

                // Preparar mensaje para WhatsApp antes de vaciar carrito
                $mensaje = "Hola, he realizado un pedido. Número de orden: {$orden->idordenes}\n\n";
                $total_calculado = 0;
                foreach ($carrito as $clave => $item) {
                    $producto = Productos::find($item['id']);
                    if ($producto) {
                        $subtotal = $producto->precio * $item['cantidad'];
                        $total_calculado += $subtotal;
                        $mensaje .= "- {$producto->nombre} (Talla: {$item['talla']}) x{$item['cantidad']} = Q" . number_format($subtotal, 2) . "\n";
                    }
                }

                // Vaciar carrito
                unset($_SESSION['carrito']);
                $mensaje .= "\nTotal: Q" . number_format($total_calculado, 2);
                $mensaje .= "\nDirección: {$departamento}, {$municipio}, {$direccion_exacta}";
                $mensaje .= "\nTeléfono: {$telefono}";
                if (!empty($nit)) {
                    $mensaje .= "\nNIT: {$nit}";
                }
                $mensaje .= "\n\nPor favor, confirmar el pago por transferencia/depósito.";

                $mensaje_encoded = urlencode($mensaje);
                $whatsapp_url = "https://wa.me/50235654214?text={$mensaje_encoded}"; // Reemplazar con número real
                $instagram_url = "https://www.instagram.com/direct/inbox/?text={$mensaje_encoded}";
                $facebook_url = "https://www.facebook.com/messages/t/17hNjJzkEE?text={$mensaje_encoded}";

                ob_clean();
                echo json_encode([
                    'success' => true,
                    'order_id' => $orden->idordenes,
                    'message' => "Compra iniciada. Número de orden: {$orden->idordenes}. Para adquirir los productos, continúa la compra en Facebook, Instagram o WhatsApp. Debes cancelar el producto primero para poder proseguir.",
                    'whatsapp_url' => $whatsapp_url,
                    'instagram_url' => $instagram_url,
                    'facebook_url' => $facebook_url
                ]);
                exit;
            } catch (\Throwable $e) {
                error_log('Error en checkout: ' . $e->getMessage());
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()]);
                exit;
            }
        }
    }
}
