<main class="producto-datos">
    <div class="carousel-container">
        <?php if (!empty($producto->imagenes) && is_array($producto->imagenes)): ?>
            <?php foreach ($producto->imagenes as $index => $imagen): ?>
                <img src="/imagenes/<?= htmlspecialchars($imagen) ?>" alt="<?= htmlspecialchars($producto->nombre) ?>" class="carousel-image <?= $index === 0 ? 'active' : '' ?>">
            <?php endforeach; ?>
        <?php else: ?>
            <img src="/imagenes/default.jpg" alt="Imagen no disponible" class="carousel-image active">
        <?php endif; ?>
        <button class="carousel-prev"><</button>
        <button class="carousel-next">></button>
        <div class="carousel-thumbnails">
            <?php if (!empty($producto->imagenes) && is_array($producto->imagenes)): ?>
                <?php foreach ($producto->imagenes as $index => $imagen): ?>
                    <img src="/imagenes/<?= htmlspecialchars($imagen) ?>" alt="Miniatura" class="thumbnail-image <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>">
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="caracteristicas">
        <h1 class="nombre-pagina"><?= htmlspecialchars($producto->nombre) ?></h1>
        <p class="descripcion-pagina"><?= htmlspecialchars($producto->descripcion) ?></p>
        <p><strong>Colorway:</strong> <?= htmlspecialchars($producto->colorway) ?></p>
        <p><strong>Precio:</strong> Q<?= htmlspecialchars($producto->precio) ?></p>
        <p><strong>Stock:</strong> <?php if ($producto->stock == 0): ?>Producto internacional<?php else: ?><?= htmlspecialchars($producto->stock) ?><?php endif; ?></p>

        <div class="tallas">
            <h3>Seleccionar Talla</h3>
            <div class="tallas-lista">
                <?php if (!empty($producto->tallas) && is_array($producto->tallas)): ?>
                    <?php foreach ($producto->tallas as $talla): ?>
                        <button class="talla-btn" data-talla="<?= htmlspecialchars($talla) ?>"><?= htmlspecialchars($talla) ?></button>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay tallas disponibles</p>
                <?php endif; ?>
            </div>
        </div>

        <button class="btn-agregar-carrito" id="agregar-carrito-btn" disabled>Agregar al Carrito</button>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Carrusel de imágenes
    const images = document.querySelectorAll('.carousel-image');
    const thumbnails = document.querySelectorAll('.thumbnail-image');
    const prevBtn = document.querySelector('.carousel-prev');
    const nextBtn = document.querySelector('.carousel-next');
    let currentIndex = 0;

    function showImage(index) {
        images.forEach(img => img.classList.remove('active', 'fullscreen'));
        thumbnails.forEach(thumb => thumb.classList.remove('active'));
        images[index].classList.add('active');
        thumbnails[index].classList.add('active');
        currentIndex = index;
    }

    function toggleFullscreen() {
        const activeImg = document.querySelector('.carousel-image.active');
        if (activeImg.classList.contains('fullscreen')) {
            activeImg.classList.remove('fullscreen');
        } else {
            activeImg.classList.add('fullscreen');
        }
    }

    prevBtn.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        showImage(currentIndex);
    });

    nextBtn.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % images.length;
        showImage(currentIndex);
    });

    thumbnails.forEach((thumb, index) => {
        thumb.addEventListener('click', () => showImage(index));
    });

    images.forEach(img => {
        img.addEventListener('click', toggleFullscreen);
    });

    // Selección de tallas
    const tallaBtns = document.querySelectorAll('.talla-btn');
    const agregarBtn = document.getElementById('agregar-carrito-btn');
    let selectedTalla = null;

    tallaBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            tallaBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            selectedTalla = this.dataset.talla;
            agregarBtn.disabled = false;
        });
    });

    // Agregar al carrito
    agregarBtn.addEventListener('click', function() {
        if (selectedTalla) {
            fetch('/carrito/agregar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + encodeURIComponent(<?= json_encode($producto->id) ?>) + '&talla=' + encodeURIComponent(selectedTalla)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarAlerta('exito', data.message);
                } else {
                    mostrarAlerta('error', data.message);
                }
            })
            .catch(error => {
                mostrarAlerta('error', 'Error al agregar al carrito');
            });
        }
    });
});
</script>
