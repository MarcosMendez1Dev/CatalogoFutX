<main class="contenedor seccion">
    <h1 class="nombre-pagina">Checkout</h1>
    <p class="descripcion-pagina">Completa tu pedido</p>

    <div class="dashboard">
        <div class="admin-opciones">
            <div class="opcion">
                <h3>Resumen del Pedido</h3>
                <div class="checkout-items">
                    <?php foreach ($productos as $producto): ?>
                        <div class="checkout-item">
                            <h4><?php echo htmlspecialchars($producto['nombre']); ?></h4>
                            <p>Cantidad: <?php echo $producto['cantidad']; ?></p>
                            <p>Precio unitario: Q<?php echo number_format($producto['precio'], 2); ?></p>
                            <p>Subtotal: Q<?php echo number_format($producto['subtotal'], 2); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="checkout-total">
                    <h3>Total: Q<?php echo number_format($total, 2); ?></h3>
                </div>
            </div>

            <div class="opcion">
                <h3>Datos de Envío</h3>
                <form id="checkout-form" method="POST" action="/checkout">
                    <div class="campo">
                        <label for="nombre">Nombre *</label>
                        <input type="text" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($usuario->nombre ?? ''); ?>" placeholder="Ingresa tu nombre">
                    </div>
                    <div class="campo">
                        <label for="apellido">Apellido *</label>
                        <input type="text" id="apellido" name="apellido" required value="<?php echo htmlspecialchars($usuario->apellido ?? ''); ?>" placeholder="Ingresa tu apellido">
                    </div>
                    <div class="campo">
                        <label for="direccion">Dirección de Envío *</label>
                        <textarea id="direccion" name="direccion" required placeholder="Ingresa tu dirección completa"></textarea>
                    </div>
                    <div class="campo">
                        <label for="telefono">Teléfono *</label>
                        <input type="tel" id="telefono" name="telefono" required value="<?php echo htmlspecialchars($usuario->telefono ?? ''); ?>" placeholder="Ingresa tu número de teléfono">
                    </div>
                    <div class="campo">
                        <label for="nit">NIT (opcional)</label>
                        <input type="text" id="nit" name="nit" value="<?php echo htmlspecialchars($usuario->nit ?? ''); ?>" placeholder="Ingresa tu NIT si aplica">
                    </div>
                    <input type="submit" value="Finalizar Pedido" class="boton-verde">
                </form>
            </div>
        </div>
    </div>
</main>

<script>
document.getElementById('checkout-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('/checkout', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.whatsapp_url;
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar el pedido');
    });
});
</script>
