<div class="dashboard">
    <h1>Editar Producto</h1>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <?php if(isset($productos) && !isset($producto)): ?>
        <div class="seleccionar-producto">
            <h2>Seleccionar Producto a Editar</h2>
            <div class="productos">
                <?php foreach($productos as $prod): ?>
                    <div class="producto">
                        <a href="/admin/editar-producto?id=<?php echo $prod->id; ?>">
                            <?php if (!empty($prod->imagenes) && is_array($prod->imagenes) && isset($prod->imagenes[0]) && !empty($prod->imagenes[0])): ?>
                                <img src="/imagenes/<?= htmlspecialchars($prod->imagenes[0]) ?>" alt="<?= htmlspecialchars($prod->nombre) ?>" width="150">
                            <?php endif; ?>
                            <h3><?= htmlspecialchars($prod->nombre) ?> - <?= htmlspecialchars($prod->colorway) ?></h3>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
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
                <label for="terreno_id">Terreno</label>
                <select id="terreno_id" name="terreno_id" required>
                    <option value="">-- Seleccionar Terreno --</option>
                    <?php foreach($terrenos as $terreno): ?>
                        <option value="<?php echo $terreno->idterreno; ?>" <?php echo ($producto->terreno_id == $terreno->idterreno) ? 'selected' : ''; ?>>
                            <?php echo s($terreno->nombre); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="silueta">Silueta</label>
                <input type="text" id="silueta" name="silueta" placeholder="Silueta" value="<?php echo s($producto->silueta); ?>" required>
            </div>

            <div class="campo">
                <label for="tallas">Tallas Disponibles</label>
                <input type="text" id="tallas" name="tallas" placeholder="Ej: S,M,L,XL,XXL" value="<?php echo is_array($producto->tallas) ? implode(',', $producto->tallas) : s($producto->tallas); ?>" required>
                <p class="ayuda">Separa las tallas con comas (ej: S,M,L,XL)</p>
            </div>

            <div class="campo">
                <label>Imágenes Actuales del Producto</label>
                <div class="imagenes-actuales">
                    <?php if (!empty($producto->imagenes) && is_array($producto->imagenes)): ?>
                        <?php foreach ($producto->imagenes as $imagen): ?>
                            <img src="/imagenes/<?= htmlspecialchars($imagen) ?>" alt="Imagen del producto" width="100" style="margin-right: 10px;">
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No hay imágenes actuales.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="campo">
                <label for="imagenes">Agregar Nuevas Imágenes del Producto</label>
                <input type="file" id="imagenes" name="imagenes[]" accept="image/*" multiple>
                <p class="ayuda">Puedes seleccionar múltiples imágenes (JPG, PNG, GIF). Las nuevas imágenes reemplazarán a las actuales.</p>
            </div>
        </fieldset>

        <input type="submit" value="Actualizar Producto" class="boton-verde">
    </form>
    <?php endif; ?>
</div>
