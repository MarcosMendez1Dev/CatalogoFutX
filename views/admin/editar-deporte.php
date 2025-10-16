<div class="dashboard">
    <h1>Editar Deporte</h1>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <?php if(isset($deportes) && !isset($deporte)): ?>
        <div class="seleccionar-deporte">
            <h2>Seleccionar Deporte a Editar</h2>
            <ul>
                <?php foreach($deportes as $dep): ?>
                    <li><a href="/admin/editar-deporte?id=<?php echo $dep->iddeporte; ?>"><?php echo $dep->nombre; ?></a></li>
                <?php endforeach; ?>
            </ul>
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
                <label for="imagen">Imagen del Deporte</label>
                <input type="file" id="imagen" name="imagen" accept="image/*">
                <p class="ayuda">Selecciona una nueva imagen si deseas cambiarla (JPG, PNG, GIF). Si no seleccionas una nueva imagen, se mantendrá la actual.</p>
            </div>
        </fieldset>

        <input type="submit" value="Actualizar Deporte" class="boton-verde">
    </form>
    <?php endif; ?>
</div>
