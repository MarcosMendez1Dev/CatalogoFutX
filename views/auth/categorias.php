<?php if(isset($productos) && isset($categoria)): ?>
    <!-- Navegación superior con imágenes de categorías -->
    <nav class="categorias-navbar">
        <div class="categorias-nav-container">
            <?php foreach ($categorias as $cat): ?>
                <a href="/categorias?id=<?= htmlspecialchars($cat->idcategoria) ?>"
                   class="categoria-nav-item <?= ($cat->idcategoria == $categoria->idcategoria) ? 'active' : '' ?>"
                   title="<?= htmlspecialchars($cat->nombre) ?>">
                    <?php if (!empty($cat->imagen)): ?>
                        <img src="/imagenes/<?= htmlspecialchars($cat->imagen) ?>"
                             alt="<?= htmlspecialchars($cat->nombre) ?>"
                             class="categoria-nav-img"
                             width="100"
                             height="100">
                    <?php else: ?>
                        <div class="categoria-nav-placeholder">
                            <?= htmlspecialchars(substr($cat->nombre, 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="categoria-content">
        <div class="container">
            <header class="categoria-header">
                <h1 class="categoria-title">Productos de la Categoría</h1>
                <span class="categoria-name"><?= htmlspecialchars($categoria->nombre) ?></span>
            </header>

            <div class="productos">
                <?php if (!empty($productos)): ?>
                    <?php foreach ($productos as $producto): ?>
                        <div class="producto">
                            <a href="/producto?id=<?= htmlspecialchars($producto->id) ?>">
                                <?php if (!empty($producto->imagenes) && is_array($producto->imagenes) && !empty($producto->imagenes[0])): ?>
                                    <img src="/imagenes/<?= htmlspecialchars($producto->imagenes[0]) ?>"
                                         alt="<?= htmlspecialchars($producto->nombre) ?>"
                                         width="150">
                                <?php endif; ?>
                                <h3><?= htmlspecialchars($producto->nombre) ?></h3>
                            </a>
                            <div class="caracteristicas">
                                <p>Q<?= htmlspecialchars($producto->precio) ?></p>
                                <button data-id="<?= htmlspecialchars($producto->id) ?>" class="agregar-carrito">Agregar al carrito</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-productos">
                        <p>No hay productos disponibles en esta categoría.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
<?php else: ?>
    <!-- Vista de todas las categorías -->
    <main class="categorias-main">
        <div class="container">
            <header class="categorias-header">
                <h1 class="categorias-title">Categorías Disponibles</h1>
                <p class="categorias-subtitle">Explora nuestras categorías de productos</p>
            </header>

            <div class="categorias-grid">
                <?php foreach ($categorias as $categoria): ?>
                    <article class="categoria-card">
                        <a href="/categorias?id=<?= htmlspecialchars($categoria->idcategoria) ?>" class="categoria-link">
                            <div class="categoria-imagen">
                                <?php if (!empty($categoria->imagen)): ?>
                                    <img src="/imagenes/<?= htmlspecialchars($categoria->imagen) ?>"
                                         alt="<?= htmlspecialchars($categoria->nombre) ?>"
                                         loading="lazy">
                                <?php else: ?>
                                    <div class="categoria-placeholder">
                                        <span><?= htmlspecialchars(substr($categoria->nombre, 0, 1)) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="categoria-info">
                                <h3 class="categoria-nombre"><?= htmlspecialchars($categoria->nombre) ?></h3>
                                <p class="categoria-descripcion"><?= htmlspecialchars($categoria->descripcion) ?></p>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
<?php endif; ?>
