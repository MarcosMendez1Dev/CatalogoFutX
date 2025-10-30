<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FutX</title>
    <link rel="icon" type="image/png" href="">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;700;900&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="/build/css/app.css">

</head>
<body>
<?php
require_once __DIR__ . '/../includes/app.php';
$categorias = \Model\Categoria::consultarSQL("SELECT * FROM categorias LIMIT 5");
?>

    <header class="header">
        <div class="header-icons">
            <div class="carrito-icon">
                <img src="build/img/carrito-de-compras.avif" alt="Carrito de Compras">
            </div>
            <div class="login-icon">
                <?php if(isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
                    <div class="user-menu">
                        <img src="build/img/login.avif" alt="Usuario" id="user-icon">
                        <div class="user-dropdown" id="user-dropdown">
                            <p>Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
                            <a href="/perfil">Editar Perfil</a>
                            <a href="/historial">Historial de Pedidos</a>
                            <?php if(isset($_SESSION['admin']) && $_SESSION['admin'] === "1"): ?>
                                <a href="/admin">Panel Admin</a>
                            <?php endif; ?>
                            <a href="/logout">Cerrar Sesión</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/login">
                        <img src="build/img/login.avif" alt="Inicio de Sesion">
                    </a>
                <?php endif; ?>
            </div>
            <div class="mobile-menu">
                <img src="build/img/menu-hamburguesa.webp" alt="Menu Hamburguesa">
            </div>
        </div>
        <div class="titulo">
            <a href="/">
                <h1>Fut<span>X</span></h1>
                <h3>Futbol Experience</h3>
            </a>
        </div>
        <div class="barra-busqueda">
            <form id="search-form" method="get" action="/buscar">
                <fieldset>
                    <input type="text" id="search-input" name="q" placeholder="Buscar productos..." required>
                        <button type="submit">
                        <i class="search-icon">Buscar</i>
                        </button>
                </fieldset>
            </form>
        </div>
        
        
        <div class="derecha">
            <nav class="navegacion-principal">
                    <a href="/deportes">Deportes</a>
                        <a href="/marcas">Marcas</a>
                        <a href="/categorias">Categorias</a>
                        <a href="/sobre-nosotros">Sobre Nosotros</a>
            </nav>
        </div>
    </header>

   


    <?php echo $contenido; ?>
    

    <footer class="footer">
        <div class="titulo">
        <h1>Fut<span>X</span></h1>
        <h3>Futbol Experience</h3>
        </div>

        <div class="categorias-container">
            <div class="categorias">
                <h2>Categorias</h2>
                <nav class="navegacion-footer">
                <?php foreach ($categorias as $categoria): ?>
                    <a href="/categorias?id=<?= htmlspecialchars($categoria->idcategoria) ?>"><?= htmlspecialchars($categoria->nombre) ?></a>
                <?php endforeach; ?>
                </nav>
            </div>

            <div class="categorias">
                <h2>Redes Sociales</h2>
                <nav class="navegacion-footer">
                        <a href="/sneakers.php">Facebook</a>
                        <a href="/Prendas.php">Instagram</a>
                        <a href="/nosotros.php">Tiktok</a>
                </nav>
            </div>
            <div class="categorias">
                <h2>Contacto</h2>
                <a class="/navegacion-footer">correo</a>
                <a class="/navegacion-footer">insta</a>
                <a class="/navegacion-footer">facebook</a>
            </div>
            <div class="categorias">
                <h2>Marcas</h2>
                <a class="/navegacion-footer">Nike</a>
                <a class="/navegacion-footer">Adidas</a>
                <a class="/navegacion-footer">Puma</a>
                <a class="/navegacion-footer">Rinat</a>
            </div>
        </div>
</footer>

<section class="carrito" id="carrito">
    <div id="carrito-contenido">
        <h2>Carrito de Compras</h2>
        <p>Cargando...</p>
    </div>
</section>

<script src="/build/js/app.js"></script>
            
</body>
</html>
