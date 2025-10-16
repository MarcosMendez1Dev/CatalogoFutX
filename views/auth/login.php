<h1 class="nombre-pagina">Login</h1>


<?php 
    include_once __DIR__ . "/../templates/alertas.php";
?>

<form action="/login" class="formulario" method="POST">
    <div class="campo">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="futx.contacto@gmail.com" >
    </div>

    <div class="campo">
        <label for="password">Contraseña</label>
        <input type="password" id="password" placeholder="12341q2w3e" name="password">
    </div>

    <input type="submit" class="boton" value="Iniciar Sesión">
</form>

<div class="acciones">
    <a href="/crear-cuenta">¿Aún no formas parte de la Familia FutX?</a>
    <a href="/olvide">¿Olvidaste tu Contraseña?</a>
</div>