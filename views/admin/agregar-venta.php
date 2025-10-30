<style>
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
    <h1 style="color: black;">Agregar Venta</h1>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <form action="/admin/agregar-venta" method="POST" class="formulario">
        <fieldset>
            <legend>Información del Cliente</legend>

            <div class="campo">
                <label for="idcliente">Cliente Existente</label>
                <select id="idcliente" name="idcliente">
                    <option value="">-- Seleccionar Cliente Existente --</option>
                    <?php foreach($clientes as $cliente): ?>
                        <option value="<?php echo $cliente->id; ?>">
                            <?php echo s($cliente->nombre . ' ' . $cliente->apellido . ' - ' . $cliente->email); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="ayuda">O ingresa los datos de un nuevo cliente abajo</p>
            </div>

            <div class="campo">
                <label for="cliente_nombre">Nombre del Nuevo Cliente</label>
                <input type="text" id="cliente_nombre" name="cliente_nombre" placeholder="Nombre del cliente">
            </div>

            <div class="campo">
                <label for="cliente_apellido">Apellido del Nuevo Cliente</label>
                <input type="text" id="cliente_apellido" name="cliente_apellido" placeholder="Apellido del cliente">
            </div>

            <div class="campo">
                <label for="cliente_email">Email del Nuevo Cliente</label>
                <input type="email" id="cliente_email" name="cliente_email" placeholder="Email del cliente">
            </div>

            <div class="campo">
                <label for="cliente_telefono">Teléfono del Nuevo Cliente</label>
                <input type="text" id="cliente_telefono" name="cliente_telefono" placeholder="Teléfono del cliente">
            </div>
        </fieldset>

        <fieldset>
            <legend>Información del Producto</legend>

            <div class="campo">
                <label for="idproducto">Producto Existente</label>
                <select id="idproducto" name="idproducto">
                    <option value="">-- Seleccionar Producto Existente --</option>
                    <?php foreach($productos as $producto): ?>
                        <option value="<?php echo $producto->id; ?>">
                            <?php echo s($producto->nombre . ' - ' . $producto->colorway . ' - Q' . $producto->precio); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="ayuda">O ingresa los datos de un nuevo producto abajo</p>
            </div>

            <div class="campo">
                <label for="producto_nombre">Nombre del Nuevo Producto</label>
                <input type="text" id="producto_nombre" name="producto_nombre" placeholder="Nombre del producto">
            </div>

            <div class="campo">
                <label for="producto_colorway">Colorway del Nuevo Producto</label>
                <input type="text" id="producto_colorway" name="producto_colorway" placeholder="Colorway del producto">
            </div>

            <div class="campo">
                <label for="producto_precio">Precio del Nuevo Producto</label>
                <input type="number" id="producto_precio" name="producto_precio" placeholder="Precio del producto" step="0.01" min="0">
            </div>

            <div class="campo">
                <label for="producto_descripcion">Descripción del Nuevo Producto</label>
                <textarea id="producto_descripcion" name="producto_descripcion" placeholder="Descripción del producto"></textarea>
            </div>

            <div class="campo">
                <label for="producto_categoria">Categoría del Nuevo Producto</label>
                <select id="producto_categoria" name="producto_categoria">
                    <option value="">-- Seleccionar Categoría --</option>
                    <?php
                    $categorias = \Model\Categoria::all();
                    foreach($categorias as $categoria): ?>
                        <option value="<?php echo $categoria->idcategoria; ?>">
                            <?php echo s($categoria->nombre); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="producto_marca">Marca del Nuevo Producto</label>
                <select id="producto_marca" name="producto_marca">
                    <option value="">-- Seleccionar Marca --</option>
                    <?php
                    $marcas = \Model\Marcas::all();
                    foreach($marcas as $marca): ?>
                        <option value="<?php echo $marca->idmarca; ?>">
                            <?php echo s($marca->nombre); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="producto_deporte">Deporte del Nuevo Producto</label>
                <select id="producto_deporte" name="producto_deporte">
                    <option value="">-- Seleccionar Deporte --</option>
                    <?php
                    $deportes = \Model\Deportes::all();
                    foreach($deportes as $deporte): ?>
                        <option value="<?php echo $deporte->iddeporte; ?>">
                            <?php echo s($deporte->nombre); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </fieldset>

        <fieldset>
            <legend>Detalles de la Venta</legend>

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
                        <option value="<?php echo $producto->id; ?>">
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

        <input type="submit" value="Crear Venta" class="boton-verde">
    </form>
</div>
