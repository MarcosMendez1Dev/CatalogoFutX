// Toggle carrito sidebar
document.addEventListener('DOMContentLoaded', function() {
    const carritoIcon = document.querySelector('.carrito-icon');
    const carrito = document.querySelector('.carrito');

    if (carritoIcon) {
        carritoIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            carrito.classList.toggle('mostrar');
            if (carrito.classList.contains('mostrar')) {
                cargarCarrito();
            }
        });
    }

    // Close on outside click
    document.addEventListener('click', function(e) {
        if (!carrito.contains(e.target) && !carritoIcon.contains(e.target)) {
            carrito.classList.remove('mostrar');
        }
    });

    // Toggle user dropdown
    const userIcon = document.querySelector('#user-icon');
    const userDropdown = document.querySelector('#user-dropdown');

    if (userIcon && userDropdown) {
        userIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('mostrar');
        });

        // Close on outside click
        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target) && !userIcon.contains(e.target)) {
                userDropdown.classList.remove('mostrar');
            }
        });
    }

    // Event listeners para botones agregar al carrito
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('agregar-carrito')) {
            e.preventDefault();
            const id = e.target.getAttribute('data-id');
            agregarAlCarrito(id);
        }
    });
});

function agregarAlCarrito(id) {
    fetch('/carrito/agregar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + encodeURIComponent(id)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            // Opcional: cargar carrito si está abierto
            if (document.querySelector('.carrito').classList.contains('mostrar')) {
                cargarCarrito();
            }
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al agregar al carrito');
    });
}

function cargarCarrito() {
    fetch('/carrito/ver')
    .then(response => response.text())
    .then(html => {
        document.getElementById('carrito-contenido').innerHTML = html;
        // Agregar event listeners a los botones dinámicos
        agregarEventListenersCarrito();
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function agregarEventListenersCarrito() {
    // Botones de cantidad
    document.querySelectorAll('.btn-menos').forEach(btn => {
        btn.addEventListener('click', function() {
            const item = this.closest('.carrito-item');
            const id = item.getAttribute('data-id');
            const cantidadSpan = item.querySelector('.cantidad');
            let cantidad = parseInt(cantidadSpan.textContent);
            if (cantidad > 1) {
                cantidad--;
                actualizarCantidad(id, cantidad);
            }
        });
    });

    document.querySelectorAll('.btn-mas').forEach(btn => {
        btn.addEventListener('click', function() {
            const item = this.closest('.carrito-item');
            const id = item.getAttribute('data-id');
            const cantidadSpan = item.querySelector('.cantidad');
            let cantidad = parseInt(cantidadSpan.textContent);
            cantidad++;
            actualizarCantidad(id, cantidad);
        });
    });

    // Botones eliminar
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', function() {
            const item = this.closest('.carrito-item');
            const id = item.getAttribute('data-id');
            eliminarDelCarrito(id);
        });
    });

    // Botón cerrar
    const closeBtn = document.querySelector('#closeCarrito');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            document.querySelector('.carrito').classList.remove('mostrar');
        });
    }

    // Botón proceder al pago
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-comprar')) {
            window.location.href = '/checkout';
        }
    });
}

function actualizarCantidad(id, cantidad) {
    fetch('/carrito/actualizar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + encodeURIComponent(id) + '&cantidad=' + encodeURIComponent(cantidad)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cargarCarrito();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar cantidad');
    });
}

function eliminarDelCarrito(id) {
    fetch('/carrito/eliminar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + encodeURIComponent(id)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cargarCarrito();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar producto');
    });
}


//Categorias