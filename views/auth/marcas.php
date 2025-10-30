<?php if(isset($productos) && isset($marca)): ?>
    <!-- Navegación superior con imágenes de marcas -->
    <nav class="marcas-navbar">
        <div class="marcas-nav-container">
            <?php foreach ($marcas as $mar): ?>
                <a href="/marcas?id=<?= htmlspecialchars($mar->idmarca) ?>"
                   class="marca-nav-item <?= ($mar->idmarca == $marca->idmarca) ? 'active' : '' ?>"
                   title="<?= htmlspecialchars($mar->nombre) ?>">
                    <?php if (!empty($mar->imagen)): ?>
                        <img src="/imagenes/<?= htmlspecialchars($mar->imagen) ?>"
                             alt="<?= htmlspecialchars($mar->nombre) ?>"
                             class="marca-nav-img"
                             width="100"
                             height="100">
                    <?php else: ?>
                        <div class="marca-nav-placeholder">
                            <?= htmlspecialchars(substr($mar->nombre, 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="marca-content">
        <div class="container">
            <header class="marca-header">
                <h1 class="marca-title">Productos de la Marca</h1>
                <span class="marca-name"><?= htmlspecialchars($marca->nombre) ?></span>
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
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-productos">
                        <p>No hay productos disponibles en esta marca.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
<?php else: ?>
    <!-- Vista de todas las marcas -->
    <main class="marcas-main">
        <div class="container">
            <header class="marcas-header">
                <h1 class="marcas-title">Marcas Disponibles</h1>
                <p class="marcas-subtitle">Explora nuestras marcas</p>
            </header>

            <div class="marcas-grid">
                <?php foreach ($marcas as $marca): ?>
                    <article class="marca-card">
                        <a href="/marcas?id=<?= htmlspecialchars($marca->idmarca) ?>" class="marca-link">
                            <div class="marca-imagen">
                                <?php if (!empty($marca->imagen)): ?>
                                    <img src="/imagenes/<?= htmlspecialchars($marca->imagen) ?>"
                                         alt="<?= htmlspecialchars($marca->nombre) ?>"
                                         loading="lazy">
                                <?php else: ?>
                                    <div class="marca-placeholder">
                                        <span><?= htmlspecialchars(substr($marca->nombre, 0, 1)) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="marca-info">
                                <h3 class="marca-nombre"><?= htmlspecialchars($marca->nombre) ?></h3>
                                <p class="marca-descripcion"><?= htmlspecialchars($marca->descripcion) ?></p>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
<?php endif; ?>
