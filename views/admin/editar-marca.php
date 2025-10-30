<style>

</style>

<div class="dashboard">
    <h1 style="color: black;">Editar Marca</h1>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <?php if(isset($marcas) && !isset($marca)): ?>
        <div class="seleccionar-marca">
            <h2>Seleccionar Marca a Editar</h2>
            <div class="marcas">
                <?php foreach($marcas as $mar): ?>
                    <div class="marca">
                        <a href="/admin/editar-marca?id=<?php echo $mar->idmarca; ?>">
                            <?php if (!empty($mar->imagen)): ?>
                                <img src="/imagenes/<?= htmlspecialchars($mar->imagen) ?>" alt="<?= htmlspecialchars($mar->nombre) ?>" width="150">
                            <?php endif; ?>
                            <h3><?= htmlspecialchars($mar->nombre) ?></h3>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>

    <form action="/admin/editar-marca" method="POST" enctype="multipart/form-data" class="formulario">
        <fieldset>
            <legend>Información de la Marca</legend>

            <input type="hidden" name="id" value="<?php echo $marca->idmarca; ?>">

            <div class="campo">
                <label for="nombre">Nombre de la Marca</label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre de la Marca" value="<?php echo s($marca->nombre); ?>" required>
            </div>

            <div class="campo">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" placeholder="Descripción de la Marca" required><?php echo s($marca->descripcion); ?></textarea>
            </div>

            <div class="campo">
                <label>Imagen Actual de la Marca</label>
                <div class="imagen-actual">
                    <?php if (!empty($marca->imagen)): ?>
                        <img src="/imagenes/<?= htmlspecialchars($marca->imagen) ?>" alt="Imagen de la marca" width="150">
                    <?php else: ?>
                        <p style="color: black;">No hay imagen actual.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="campo">
                <label for="imagen">Cambiar Imagen de la Marca</label>
                <input type="file" id="imagen" name="imagen" accept="image/*">
                <p class="ayuda">Deja vacío si no quieres cambiar la imagen (JPG, PNG, AVIF).</p>
            </div>
        </fieldset>

        <input type="submit" value="Actualizar Marca" class="boton-verde">
    </form>
    <?php endif; ?>
</div>
