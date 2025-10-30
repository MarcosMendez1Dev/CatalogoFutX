<style>
</style>

<div class="dashboard">
    <h1 style="color: black;">Editar Deporte</h1>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <?php if(isset($deportes) && !isset($deporte)): ?>
        <div class="seleccionar-deporte">
            <h2>Seleccionar Deporte a Editar</h2>
            <div class="deportes">
                <?php foreach($deportes as $dep): ?>
                    <div class="deporte">
                        <a href="/admin/editar-deporte?id=<?php echo $dep->iddeporte; ?>">
                            <?php if (!empty($dep->imagen)): ?>
                                <img src="/imagenes/<?= htmlspecialchars($dep->imagen) ?>" alt="<?= htmlspecialchars($dep->nombre) ?>" width="150">
                            <?php endif; ?>
                            <h3><?= htmlspecialchars($dep->nombre) ?></h3>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>

    <form action="/admin/editar-deporte" method="POST" enctype="multipart/form-data" class="formulario">
        <fieldset>
            <legend>Información del Deporte</legend>

            <input type="hidden" name="id" value="<?php echo $deporte->iddeporte; ?>">

            <div class="campo">
                <label for="nombre">Nombre del Deporte</label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre del Deporte" value="<?php echo s($deporte->nombre); ?>" required>
            </div>

            <div class="campo">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" placeholder="Descripción del Deporte" required><?php echo s($deporte->descripcion); ?></textarea>
            </div>

            <div class="campo">
                <label>Imagen Actual del Deporte</label>
                <div class="imagen-actual">
                    <?php if (!empty($deporte->imagen)): ?>
                        <img src="/imagenes/<?= htmlspecialchars($deporte->imagen) ?>" alt="Imagen del deporte" width="150">
                    <?php else: ?>
                        <p style="color: black;">No hay imagen actual.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="campo">
                <label for="imagen">Cambiar Imagen del Deporte</label>
                <input type="file" id="imagen" name="imagen" accept="image/*">
                <p class="ayuda">Deja vacío si no quieres cambiar la imagen (JPG, PNG, GIF).</p>
            </div>
        </fieldset>

        <input type="submit" value="Actualizar Deporte" class="boton-verde">
    </form>
    <?php endif; ?>
</div>
