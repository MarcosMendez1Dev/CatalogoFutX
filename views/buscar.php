<main class="contenedor seccion">
    <h1 class="nombre-pagina">Resultados de Búsqueda</h1>
    <p class="descripcion-pagina">Resultados para: "<?php echo htmlspecialchars($query); ?>"</p>

    <?php if (empty($productos)): ?>
        <div class="no-resultados">
            <h3>No se encontraron productos</h3>
            <p>No encontramos productos que coincidan con tu búsqueda: "<?php echo htmlspecialchars($query); ?>"</p>
            <a href="/" class="boton-verde">Volver al Inicio</a>
        </div>
    <?php else: ?>
        <div class="productos-grid">
            <?php foreach ($productos as $producto): ?>
                <div class="producto">
                    <div class="producto-imagen">
                        <img src="/imagenes/<?php echo htmlspecialchars($producto->imagenes[0] ?? ''); ?>" alt="<?php echo htmlspecialchars($producto->nombre); ?>">
                    </div>
                    <div class="producto-info">
                        <h3><?php echo htmlspecialchars($producto->nombre); ?></h3>
                        <p class="producto-precio">Q<?php echo number_format($producto->precio, 2); ?></p>
                        <a href="/producto?id=<?php echo $producto->id; ?>" class="boton-verde">Ver Producto</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
