
<h1 class="titulo-productos">Productos Disponibles</h1>

<div class="productos">

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
</div>
