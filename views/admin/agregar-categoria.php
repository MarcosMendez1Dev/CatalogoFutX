<main class="agregar-productos">
    <h1 class="titulo">Agregar Categoría</h1>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <form action="/admin/agregar-categoria" method="POST" enctype="multipart/form-data" class="formulario">
        <fieldset>
            <legend>Información de la Categoría</legend>

            <div class="campo">
                <label for="nombre">Nombre de la Categoría</label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre de la Categoría" value="<?php echo s($categoria->nombre); ?>" required>
            </div>

            <div class="campo">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" placeholder="Descripción de la Categoría" required><?php echo s($categoria->descripcion); ?></textarea>
            </div>

            <div class="campo">
                <label for="imagen">Imagen de la Categoría</label>
                <input type="file" id="imagen" name="imagen" accept="image/*">
                <p class="ayuda">Selecciona una imagen para la categoría (JPG, PNG, GIF)</p>
            </div>
        </fieldset>

        <input type="submit" value="Agregar Categoría" class="boton-verde">
    </form>
</main>
