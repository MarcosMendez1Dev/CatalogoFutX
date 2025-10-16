<div class="dashboard">
    <h1>Agregar Deporte</h1>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <form action="/admin/agregar-deporte" method="POST" enctype="multipart/form-data" class="formulario">
        <fieldset>
            <legend>Información del Deporte</legend>

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
                <input type="file" id="imagen" name="imagen" accept="image/*" required>
                <p class="ayuda">Selecciona una imagen para el deporte (JPG, PNG, GIF)</p>
            </div>
        </fieldset>

        <input type="submit" value="Agregar Deporte" class="boton-verde">
    </form>
</div>
