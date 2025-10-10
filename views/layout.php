<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FutX</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;700;900&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="/build/css/app.css">
</head>
<body>

    <header class="header">
        <div class="header-icons">
            <div class="home">
                <a href="/"> <img src="build/img/pagina-de-inicio.webp" alt="Inicio"></a>
            </div>
            <div class="carrito">
                <img src="build/img/carrito-de-compras.avif" alt="Carrito de Compras">
            </div>
        </div>
        <div class="mobile-menu">
            <img src="build/img/menu-hamburguesa.webp" alt="Menu Hamburguesa">
        </div>
        <div class="titulo">
            <h1>Fut<span>X</span></h1>
            <h3>Futbol Experience</h3>
        </div>
        <div class="barra-busqueda">
            <form id="search-form" method="get" action="/buscar">
                <fieldset>
                    <input type="text" id="search-input" name="q" placeholder="Buscar...">
                        <button type="submit">
                        <i class="search-icon">Buscar</i>
                        </button>
                </fieldset>
            </form>
        </div>
        
        
        <div class="derecha">
            <nav class="navegacion-principal mostrar">
                    <a href="/productos">Productos</a>
                        <a href="/marcas">Marcas</a>
                        <a href="/categorias">Categorias</a>
                        <a href="/sobre-nosotros">Sobre Nosotros</a>
            </nav>
            <div class="vertical-tab" id="carritoTab">
                <h2>Carrito de Compras</h2>
                <p>Aquí va el contenido del carrito.</p>
                <button id="closeTab">Cerrar</button>
        </div>
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
                <nav class="navegacion-footer ">
                        <a href="/productos">Futbol</a>
                        <a href="/categorias">Basketball</a>
                        <a href="/armar-tu-setup">Gimnasio</a>
                        <a href="/sobre-nosotros">Tenis/Padel</a>
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
    
</section>

<script src="/build/js/app.js"></script>
            
</body>
</html>
