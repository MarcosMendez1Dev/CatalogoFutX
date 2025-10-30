

<div class="dashboard">
    <h1 style="color: black;">Editar Categoría</h1>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <?php if(isset($categorias) && !isset($categoria)): ?>
        <div class="seleccionar-categoria">
            <h2>Seleccionar Categoría a Editar</h2>
            <div class="categorias">
                <?php foreach($categorias as $cat): ?>
                    <div class="categoria">
                        <a href="/admin/editar-categoria?id=<?php echo $cat->idcategoria; ?>">
                            <?php if (!empty($cat->imagen)): ?>
                                <img src="/imagenes/<?= htmlspecialchars($cat->imagen) ?>" alt="<?= htmlspecialchars($cat->nombre) ?>" width="150">
                            <?php endif; ?>
                            <h3><?= htmlspecialchars($cat->nombre) ?></h3>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>

    <form action="/admin/editar-categoria" method="POST" enctype="multipart/form-data" class="formulario">
        <fieldset>
            <legend>Información de la Categoría</legend>

            <input type="hidden" name="id" value="<?php echo $categoria->idcategoria; ?>">

            <div class="campo">
                <label for="nombre">Nombre de la Categoría</label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre de la Categoría" value="<?php echo s($categoria->nombre); ?>" required>
            </div>

            <div class="campo">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" placeholder="Descripción de la Categoría" required><?php echo s($categoria->descripcion); ?></textarea>
            </div>

            <div class="campo">
                <label>Imagen Actual de la Categoría</label>
                <div class="imagen-actual">
                    <?php if (!empty($categoria->imagen)): ?>
                        <img src="/imagenes/<?= htmlspecialchars($categoria->imagen) ?>" alt="Imagen de la categoría" width="150">
                    <?php else: ?>
                        <p style="color: black;">No hay imagen actual.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="campo">
                <label for="imagen">Cambiar Imagen de la Categoría</label>
                <input type="file" id="imagen" name="imagen" accept="image/*">
                <p class="ayuda">Deja vacío si no quieres cambiar la imagen (JPG, PNG, AVIF).</p>
            </div>
        </fieldset>

        <input type="submit" value="Actualizar Categoría" class="boton-verde">
    </form>
    <?php endif; ?>
</div>
