<div class="dashboard">
    <h1>Agregar Marca</h1>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <form action="/admin/agregar-marca" method="POST" enctype="multipart/form-data" class="formulario">
        <fieldset>
            <legend>Información de la Marca</legend>

            <div class="campo">
                <label for="nombre">Nombre de la Marca</label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre de la Marca" value="<?php echo s($marca->nombre); ?>" required>
            </div>

            <div class="campo">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" placeholder="Descripción de la Marca" required><?php echo s($marca->descripcion); ?></textarea>
            </div>

            <div class="campo">
                <label for="imagen">Imagen de la Marca</label>
                <input type="file" id="imagen" name="imagen" accept="image/*">
                <p class="ayuda">Selecciona una imagen para la marca (JPG, PNG, GIF)</p>
            </div>
        </fieldset>

        <input type="submit" value="Agregar Marca" class="boton-verde">
    </form>
</div>
