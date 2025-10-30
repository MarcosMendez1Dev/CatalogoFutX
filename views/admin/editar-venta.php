<style>
.seleccionar-venta {
    margin-bottom: 2rem;
}
.seleccionar-venta h2 {
    color: black;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}
.ventas {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
}
.venta {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 1rem;
    background-color: #f9f9f9;
    transition: box-shadow 0.3s;
}
.venta:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.venta a {
    text-decoration: none;
    color: inherit;
}
.venta h3 {
    color: black;
    margin-top: 0.5rem;
    font-size: 1.1rem;
}
.venta p {
    color: black;
    margin: 0.25rem 0;
    font-size: 0.9rem;
}
.formulario fieldset legend {
    color: black;
}
.campo label {
    color: black;
}
.ayuda {
    color: #666;
    font-size: 0.9rem;
}
</style>

<div class="dashboard">
    <h1 style="color: black;">Editar Venta</h1>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <?php if(isset($ordenes) && !isset($orden)): ?>
        <div class="seleccionar-venta">
            <h2>Seleccionar Venta a Editar</h2>
            <div class="ventas">
                <?php foreach($ordenes as $ord): ?>
                    <div class="venta">
                        <a href="/admin/editar-venta?id=<?php echo $ord->idordenes; ?>">
                            <h3>Orden #<?php echo $ord->idordenes; ?> - <?php echo htmlspecialchars($ord->cliente); ?></h3>
                            <p><strong>Producto:</strong> <?php echo htmlspecialchars($ord->producto); ?></p>
                            <p><strong>Cantidad:</strong> <?php echo $ord->cantidad; ?> | <strong>Total:</strong> Q<?php echo number_format($ord->total_orden, 2); ?></p>
                            <p><strong>Estado:</strong> <?php echo htmlspecialchars($ord->estado); ?> | <strong>Fecha:</strong> <?php echo htmlspecialchars($ord->fecha); ?></p>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>

    <form action="/admin/editar-venta" method="POST" class="formulario">
        <fieldset>
            <legend>Información de la Venta</legend>

            <input type="hidden" name="id" value="<?php echo $orden->idordenes; ?>">

            <div class="campo">
                <label for="idcliente">Cliente</label>
                <select id="idcliente" name="idcliente" required>
                    <option value="">-- Seleccionar Cliente --</option>
                    <?php foreach($clientes as $cliente): ?>
                        <option value="<?php echo $cliente->id; ?>" <?php echo ($orden->idcliente == $cliente->id) ? 'selected' : ''; ?>>
                            <?php echo s($cliente->nombre . ' ' . $cliente->apellido . ' - ' . $cliente->email); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="departamento">Departamento</label>
                <input type="text" id="departamento" name="departamento" placeholder="Departamento" value="<?php echo s($orden->departamento); ?>" required>
            </div>

            <div class="campo">
                <label for="municipio">Municipio</label>
                <input type="text" id="municipio" name="municipio" placeholder="Municipio" value="<?php echo s($orden->municipio); ?>" required>
            </div>

            <div class="campo">
                <label for="direccion_exacta">Dirección Exacta</label>
                <input type="text" id="direccion_exacta" name="direccion_exacta" placeholder="Dirección Exacta" value="<?php echo s($orden->direccion_exacta); ?>" required>
            </div>

            <div class="campo">
                <label for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" placeholder="Teléfono" value="<?php echo s($orden->telefono); ?>" required>
            </div>

            <div class="campo">
                <label for="idproducto">Producto</label>
                <select id="idproducto" name="idproducto" required>
                    <option value="">-- Seleccionar Producto --</option>
                    <?php foreach($productos as $producto): ?>
                        <option value="<?php echo $producto->id; ?>" <?php echo ($detalle->idproducto == $producto->id) ? 'selected' : ''; ?>>
                            <?php echo s($producto->nombre . ' - ' . $producto->colorway . ' - Q' . $producto->precio); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="talla">Talla</label>
                <input type="text" id="talla" name="talla" placeholder="Talla (ej: M, L, XL)" value="<?php echo s($detalle->talla); ?>" required>
            </div>

            <div class="campo">
                <label for="cantidad">Cantidad</label>
                <input type="number" id="cantidad" name="cantidad" placeholder="Cantidad" value="<?php echo s($detalle->cantidad); ?>" min="1" required>
            </div>
        </fieldset>

        <input type="submit" value="Actualizar Venta" class="boton-verde">
    </form>
    <?php endif; ?>
</div>
