<h1 class="nombre-pagina">Mi Perfil</h1>

<?php
    include_once __DIR__ . "/../templates/alertas.php";
?>

<form action="/perfil" class="formulario" method="POST">
    <div class="campo">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo s($usuario->nombre); ?>" required>
    </div>

    <div class="campo">
        <label for="apellido">Apellido</label>
        <input type="text" id="apellido" name="apellido" value="<?php echo s($usuario->apellido); ?>" required>
    </div>

    <div class="campo">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo s($usuario->email); ?>" required>
    </div>

    <div class="campo">
        <label for="telefono">Teléfono</label>
        <input type="text" id="telefono" name="telefono" value="<?php echo s($usuario->telefono); ?>">
    </div>

    <div class="campo">
        <label for="nit">NIT</label>
        <input type="text" id="nit" name="nit" value="<?php echo s($usuario->nit); ?>">
    </div>

    <div class="campo">
        <label for="nacimiento">Fecha de Nacimiento</label>
        <input type="date" id="nacimiento" name="nacimiento" value="<?php echo s($usuario->nacimiento); ?>">
    </div>

    <input type="submit" class="boton" value="Actualizar Perfil">
</form>

<div class="acciones">
    <a href="/">Volver al Inicio</a>
</div>
