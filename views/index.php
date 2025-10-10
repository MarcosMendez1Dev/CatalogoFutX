<main class="productos">
    <?php foreach($productos as $producto): ?>
        <div class="producto">
            <?php if (!empty($producto->imagenes)): ?>
                <img src="/build/img/<?php echo htmlspecialchars($producto->imagenes[0]); ?>" alt="<?php echo htmlspecialchars($producto->nombre); ?>">
            <?php endif; ?>
            <h3><?php echo htmlspecialchars($producto->nombre); ?></h3>
            <div class="caracteristicas">
                <p>$<?php echo htmlspecialchars($producto->precio); ?></p>
                <button class="agregar-carrito" data-id="<?php echo $producto->id; ?>">Agregar al Carrito</button>
            </div>
        </div>
    <?php endforeach; ?>
</main>
