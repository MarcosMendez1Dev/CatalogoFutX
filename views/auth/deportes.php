
<?php if(isset($productos) && isset($deporte)): ?>
    <!-- Navegación superior con imágenes de deportes -->
    <nav class="deportes-navbar">
        <div class="deportes-nav-container">
            <?php foreach ($deportes as $dep): ?>
                <a href="/deportes?id=<?= htmlspecialchars($dep->iddeporte) ?>"
                   class="deporte-nav-item <?= ($dep->iddeporte == $deporte->iddeporte) ? 'active' : '' ?>"
                   title="<?= htmlspecialchars($dep->nombre) ?>">
                    <?php if (!empty($dep->imagen)): ?>
                        <img src="/imagenes/<?= htmlspecialchars($dep->imagen) ?>"
                             alt="<?= htmlspecialchars($dep->nombre) ?>"
                             class="deporte-nav-img"
                             width="100"
                             height="100">
                    <?php else: ?>
                        <div class="deporte-nav-placeholder">
                            <?= htmlspecialchars(substr($dep->nombre, 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="deporte-content">
        <div class="container">
            <header class="deporte-header">
                <h1 class="deporte-title">Productos del Deporte</h1>
                <span class="deporte-name"><?= htmlspecialchars($deporte->nombre) ?></span>
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
                        <p>No hay productos disponibles en este deporte.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
<?php else: ?>
    <!-- Vista de todos los deportes -->
    <main class="deportes-main">
        <div class="container">
            <header class="deportes-header">
                <h1 class="deportes-title">Deportes Disponibles</h1>
                <p class="deportes-subtitle">Explora nuestros deportes</p>
            </header>

            <div class="deportes-grid">
                <?php foreach ($deportes as $deporte): ?>
                    <article class="deporte-card">
                        <a href="/deportes?id=<?= htmlspecialchars($deporte->iddeporte) ?>" class="deporte-link">
                            <div class="deporte-imagen">
                                <?php if (!empty($deporte->imagen)): ?>
                                    <img src="/imagenes/<?= htmlspecialchars($deporte->imagen) ?>"
                                         alt="<?= htmlspecialchars($deporte->nombre) ?>"
                                         loading="lazy">
                                <?php else: ?>
                                    <div class="deporte-placeholder">
                                        <span><?= htmlspecialchars(substr($deporte->nombre, 0, 1)) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="deporte-info">
                                <h3 class="deporte-nombre"><?= htmlspecialchars($deporte->nombre) ?></h3>
                                <p class="deporte-descripcion"><?= htmlspecialchars($deporte->descripcion) ?></p>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
<?php endif; ?>
