<main class="contenedor seccion">
    <h1 class="nombre-pagina">Checkout</h1>
    <p class="descripcion-pagina">Completa tu pedido</p>

    <div class="dashboard">
        <div class="admin-opciones">
            <!-- Resumen del pedido -->
            <div class="opcion">
                <h3>Resumen del Pedido</h3>
                <div class="checkout-items">
                    <?php foreach ($productos as $producto): ?>
                        <div class="checkout-item">
                            <div class="item-imagen">
                                <img src="/imagenes/<?php echo htmlspecialchars($producto['imagen'] ?? ''); ?>" 
                                     alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                            </div>
                            <div class="item-info">
                                <h4><?php echo htmlspecialchars($producto['nombre']); ?></h4>
                                <p><strong>Talla:</strong> <?php echo htmlspecialchars($producto['talla']); ?></p>
                                <p><strong>Cantidad:</strong> <?php echo $producto['cantidad']; ?></p>
                                <p><strong>Precio unitario:</strong> Q<?php echo number_format($producto['precio'], 2); ?></p>
                                <p><strong>Subtotal:</strong> Q<?php echo number_format($producto['subtotal'], 2); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="checkout-total">
                    <h3>Total: Q<?php echo number_format($total, 2); ?></h3>
                </div>
            </div>

            <!-- Datos de envío -->
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
                        <label for="departamento">Departamento *</label>
                        <select id="departamento" name="departamento" required>
                            <option value="">Selecciona un departamento</option>
                        </select>
                    </div>
                    <div class="campo">
                        <label for="municipio">Municipio *</label>
                        <select id="municipio" name="municipio" required disabled>
                            <option value="">Selecciona un municipio</option>
                        </select>
                    </div>
                    <div class="campo">
                        <label for="direccion_exacta">Dirección Exacta *</label>
                        <input type="text" id="direccion_exacta" name="direccion_exacta" required value="<?php echo htmlspecialchars($usuario->direccion_exacta ?? ''); ?>" placeholder="Ingresa tu dirección exacta">
                    </div>
                    <div class="campo">
                        <label for="telefono">Teléfono *</label>
                        <input type="tel" id="telefono" name="telefono" required value="<?php echo htmlspecialchars($usuario->telefono ?? ''); ?>" placeholder="Ingresa tu número de teléfono">
                    </div>
                    <div class="campo">
                        <label for="nit">NIT (opcional)</label>
                        <input type="text" id="nit" name="nit" value="<?php echo htmlspecialchars($usuario->nit ?? ''); ?>" placeholder="Ingresa tu NIT si aplica">
                    </div>
                    <?php if (!isset($_SESSION['login'])): ?>
                        <div class="campo">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required placeholder="Ingresa tu email">
                        </div>
                    <?php endif; ?>
                    <input type="submit" value="Finalizar Pedido" class="boton-verde">
                </form>
            </div>
        </div>
    </div>
</main>

<!-- Modal de opciones de seguimiento -->
<div id="followup-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <h3>¡Pedido realizado con éxito!</h3>
        <p><strong>Número de orden:</strong> <span id="order-number"></span></p>
        <p>Elige cómo deseas seguir con tu pedido:</p>
        <div class="modal-buttons">
            <a href="#" id="whatsapp-link" class="btn-whatsapp">WhatsApp</a>
            <a href="#" id="instagram-link" class="btn-instagram">Instagram</a>
            <a href="#" id="facebook-link" class="btn-facebook">Facebook</a>
        </div>
        <button id="close-modal" class="btn-close">Cerrar</button>
    </div>
</div>

<!-- JS -->
<script>
// Cargar departamentos y municipios
document.addEventListener('DOMContentLoaded', function() {
    fetch('/departamentos.json')
        .then(response => response.json())
        .then(data => {
            const departamentoSelect = document.getElementById('departamento');
            const municipioSelect = document.getElementById('municipio');

            // Llenar select de departamentos
            data.departamentos.forEach(depto => {
                const option = document.createElement('option');
                option.value = depto.nombre;
                option.textContent = depto.nombre;
                // Seleccionar el departamento actual del usuario
                if (depto.nombre === "<?php echo s($usuario->departamento); ?>") {
                    option.selected = true;
                }
                departamentoSelect.appendChild(option);
            });

            // Event listener para departamento
            departamentoSelect.addEventListener('change', function() {
                const selectedDepto = this.value;
                municipioSelect.innerHTML = '<option value="">Selecciona un municipio</option>';
                municipioSelect.disabled = true;

                if (selectedDepto) {
                    const depto = data.departamentos.find(d => d.nombre === selectedDepto);
                    if (depto) {
                        depto.municipios.forEach(municipio => {
                            const option = document.createElement('option');
                            option.value = municipio;
                            option.textContent = municipio;
                            // Seleccionar el municipio actual del usuario
                            if (municipio === "<?php echo s($usuario->municipio); ?>") {
                                option.selected = true;
                            }
                            municipioSelect.appendChild(option);
                        });
                        municipioSelect.disabled = false;
                    }
                }
            });

            // Trigger change event to load municipalities for current department
            if ("<?php echo s($usuario->departamento); ?>") {
                departamentoSelect.dispatchEvent(new Event('change'));
            }
        })
        .catch(error => console.error('Error cargando departamentos:', error));
});

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
            // Mostrar modal con opciones
            document.getElementById('order-number').textContent = data.order_id;
            document.getElementById('whatsapp-link').href = data.whatsapp_url;
            document.getElementById('instagram-link').href = data.instagram_url;
            document.getElementById('facebook-link').href = data.facebook_url;
            document.getElementById('followup-modal').style.display = 'flex';
        } else {
            mostrarAlerta('error', data.message);
        }
    })
    .catch(error => {
        mostrarAlerta('error', 'Error al procesar el pedido');
    });
});

// Cerrar modal
document.getElementById('close-modal').addEventListener('click', function() {
    document.getElementById('followup-modal').style.display = 'none';
});

// Cerrar modal al hacer clic fuera
window.addEventListener('click', function(event) {
    const modal = document.getElementById('followup-modal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});
</script>

<!-- 💅 ESTILOS CSS -->
<style>

</style>
