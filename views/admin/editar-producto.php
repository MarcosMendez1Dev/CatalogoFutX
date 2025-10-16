<div class="dashboard">
    <h1>Editar Producto</h1>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <?php if(isset($productos) && !isset($producto)): ?>
        <div class="seleccionar-producto">
            <h2>Seleccionar Producto a Editar</h2>
            <ul>
                <?php foreach($productos as $prod): ?>
                    <li><a href="/admin/editar-producto?id=<?php echo $prod->id; ?>"><?php echo $prod->nombre; ?> - <?php echo $prod->colorway; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>

    <form action="/admin/editar-producto" method="POST" enctype="multipart/form-data" class="formulario">
        <fieldset>
            <legend>Información del Producto</legend>

            <input type="hidden" name="id" value="<?php echo $producto->id; ?>">

            <div class="campo">
                <label for="nombre">Nombre del Producto</label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre del Producto" value="<?php echo s($producto->nombre); ?>" required>
            </div>

            <div class="campo">
                <label for="colorway">Colorway</label>
                <input type="text" id="colorway" name="colorway" placeholder="Colorway" value="<?php echo s($producto->colorway); ?>" required>
            </div>

            <div class="campo">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" placeholder="Descripción del Producto" required><?php echo s($producto->descripcion); ?></textarea>
            </div>

            <div class="campo">
                <label for="categoria_id">Categoría</label>
                <select id="categoria_id" name="categoria_id" required>
                    <option value="">-- Seleccionar Categoría --</option>
                    <?php foreach($categorias as $categoria): ?>
                        <option value="<?php echo $categoria->idcategoria; ?>" <?php echo ($producto->categoria_id == $categoria->idcategoria) ? 'selected' : ''; ?>>
                            <?php echo s($categoria->nombre); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="marca">Marca</label>
                <select id="marca" name="marca" required>
                    <option value="">-- Seleccionar Marca --</option>
                    <?php foreach($marcas as $marca): ?>
                        <option value="<?php echo $marca->idmarca; ?>" <?php echo ($producto->marca == $marca->idmarca) ? 'selected' : ''; ?>>
                            <?php echo s($marca->nombre); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="stock">Stock</label>
                <input type="number" id="stock" name="stock" placeholder="Stock" value="<?php echo s($producto->stock); ?>" min="0" required>
            </div>

            <div class="campo">
                <label for="precio">Precio</label>
                <input type="number" id="precio" name="precio" placeholder="Precio" value="<?php echo s($producto->precio); ?>" step="0.01" min="0" required>
            </div>

            <div class="campo">
                <label for="costo">Costo</label>
                <input type="number" id="costo" name="costo" placeholder="Costo" value="<?php echo s($producto->costo); ?>" step="0.01" min="0" required>
            </div>

            <div class="campo">
                <label for="deporte_id">Deporte</label>
                <select id="deporte_id" name="deporte_id" required>
                    <option value="">-- Seleccionar Deporte --</option>
                    <?php foreach($deportes as $deporte): ?>
                        <option value="<?php echo $deporte->iddeporte; ?>" <?php echo ($producto->deporte_id == $deporte->iddeporte) ? 'selected' : ''; ?>>
                            <?php echo s($deporte->nombre); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="terreno">Terreno</label>
                <input type="text" id="terreno" name="terreno" placeholder="Terreno" value="<?php echo s($producto->terreno); ?>" required>
            </div>

            <div class="campo">
                <label for="imagenes">Imágenes del Producto</label>
                <input type="file" id="imagenes" name="imagenes[]" accept="image/*" multiple>
                <p class="ayuda">Puedes seleccionar múltiples imágenes (JPG, PNG, GIF). Si no seleccionas nuevas imágenes, se mantendrán las actuales.</p>
            </div>
        </fieldset>

        <input type="submit" value="Actualizar Producto" class="boton-verde">
    </form>
    <?php endif; ?>
</div>
