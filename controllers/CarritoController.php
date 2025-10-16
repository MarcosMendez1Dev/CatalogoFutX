<?php
namespace Controllers;

use MVC\Router;
use Model\Productos;

class CarritoController {

    public static function agregar() {
        // session_start() ya se llama en Router.php
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $id = $_POST['id'] ?? null;
        if (!$id || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'ID de producto inválido']);
            return;
        }

        $producto = Productos::find($id);
        if (!$producto) {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            return;
        }

        if ($producto->stock <= 0) {
            echo json_encode(['success' => false, 'message' => 'Producto sin stock']);
            return;
        }

        if (isset($_SESSION['carrito'][$id])) {
            if ($_SESSION['carrito'][$id] >= $producto->stock) {
                echo json_encode(['success' => false, 'message' => 'No hay suficiente stock']);
                return;
            }
            $_SESSION['carrito'][$id]++;
        } else {
            $_SESSION['carrito'][$id] = 1;
        }

        echo json_encode(['success' => true, 'message' => 'Producto agregado al carrito']);
    }

    public static function actualizar() {
        // session_start() ya se llama en Router.php
        $id = $_POST['id'] ?? null;
        $cantidad = $_POST['cantidad'] ?? null;

        if (!$id || !is_numeric($id) || !$cantidad || !is_numeric($cantidad) || $cantidad < 0) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            return;
        }

        $producto = Productos::find($id);
        if (!$producto) {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            return;
        }

        if ($cantidad > $producto->stock) {
            echo json_encode(['success' => false, 'message' => 'Cantidad supera el stock disponible']);
            return;
        }

        if ($cantidad == 0) {
            unset($_SESSION['carrito'][$id]);
        } else {
            $_SESSION['carrito'][$id] = $cantidad;
        }

        echo json_encode(['success' => true, 'message' => 'Cantidad actualizada']);
    }

    public static function eliminar() {
        // session_start() ya se llama en Router.php
        $id = $_POST['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            return;
        }

        unset($_SESSION['carrito'][$id]);
        echo json_encode(['success' => true, 'message' => 'Producto eliminado']);
    }

    public static function ver() {
        // session_start() ya se llama en Router.php
        $carrito = $_SESSION['carrito'] ?? [];
        $productos = [];
        $total = 0;

        foreach ($carrito as $id => $cantidad) {
            $producto = Productos::find($id);
            if ($producto) {
                $productos[] = [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'precio' => $producto->precio,
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
                    <div class="carrito-item" data-id="<?= $item['id'] ?>">
                        <img src="/imagenes/<?= htmlspecialchars($item['imagen']) ?>" alt="<?= htmlspecialchars($item['nombre']) ?>" width="50">
                        <div class="item-info">
                            <h4><?= htmlspecialchars($item['nombre']) ?></h4>
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
        if (!isset($_SESSION['login'])) {
            header('Location: /login');
            return;
        }

        $carrito = $_SESSION['carrito'] ?? [];
        if (empty($carrito)) {
            header('Location: /');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Mostrar formulario de checkout
            $productos = [];
            $total = 0;

            foreach ($carrito as $id => $cantidad) {
                $producto = Productos::find($id);
                if ($producto) {
                    $productos[] = [
                        'id' => $producto->id,
                        'nombre' => $producto->nombre,
                        'precio' => $producto->precio,
                        'cantidad' => $cantidad,
                        'subtotal' => $producto->precio * $cantidad
                    ];
                    $total += $producto->precio * $cantidad;
                }
            }

            // Obtener datos del usuario
            $usuario = \Model\Usuario::find($_SESSION['id']);

            $router->render('checkout', [
                'productos' => $productos,
                'total' => $total,
                'usuario' => $usuario
            ]);
        } else {
            // Procesar formulario de checkout
            $nombre = $_POST['nombre'] ?? '';
            $apellido = $_POST['apellido'] ?? '';
            $direccion = $_POST['direccion'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $nit = $_POST['nit'] ?? '';

            if (empty($nombre) || empty($apellido) || empty($direccion) || empty($telefono)) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos marcados con * son obligatorios']);
                return;
            }

            // Verificar stock antes de proceder
            foreach ($carrito as $id => $cantidad) {
                $producto = Productos::find($id);
                if (!$producto || $producto->stock < $cantidad) {
                    echo json_encode(['success' => false, 'message' => 'Stock insuficiente para ' . $producto->nombre]);
                    return;
                }
            }

            // Crear orden
            $orden = new \Model\Ordenes([
                'idcliente' => $_SESSION['id'],
                'direccion' => $direccion,
                'total' => array_sum(array_map(function($id, $cantidad) {
                    $producto = Productos::find($id);
                    return $producto ? $producto->precio * $cantidad : 0;
                }, array_keys($carrito), $carrito)),
                'estado' => 'pendiente',
                'telefono' => $telefono,
                'fecha' => date('Y-m-d H:i:s')
            ]);

            $resultado = $orden->guardar();
            if (!$resultado) {
                echo json_encode(['success' => false, 'message' => 'Error al crear la orden']);
                return;
            }

            // Crear detalles de orden y actualizar stock
            foreach ($carrito as $id => $cantidad) {
                $producto = Productos::find($id);
                $detalle = new \Model\detalleOrden([
                    'idordenes' => $orden->idordenes,
                    'idproducto' => $id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $producto->precio
                ]);
                $detalle->guardar();

                // Actualizar stock
                $producto->stock -= $cantidad;
                $producto->guardar();
            }

            // Vaciar carrito
            unset($_SESSION['carrito']);

            // Preparar mensaje para WhatsApp
            $mensaje = "Hola, he realizado un pedido:\n\n";
            foreach ($carrito as $id => $cantidad) {
                $producto = Productos::find($id);
                $mensaje .= "- {$producto->nombre} x{$cantidad} = Q" . number_format($producto->precio * $cantidad, 2) . "\n";
            }
            $mensaje .= "\nTotal: Q" . number_format($orden->total, 2);
            $mensaje .= "\nDirección: {$direccion}";
            $mensaje .= "\nTeléfono: {$telefono}";
            if (!empty($nit)) {
                $mensaje .= "\nNIT: {$nit}";
            }
            $mensaje .= "\n\nPor favor, confirmar el pago por transferencia/depósito.";

            $mensaje_encoded = urlencode($mensaje);
            $whatsapp_url = "https://wa.me/50212345678?text={$mensaje_encoded}"; // Reemplazar con número real

            echo json_encode(['success' => true, 'whatsapp_url' => $whatsapp_url]);
        }
    }
}
