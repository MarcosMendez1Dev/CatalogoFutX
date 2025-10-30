 // Función para mostrar alertas
function mostrarAlerta(tipo, mensaje) {
    const alerta = document.createElement('div');
    alerta.className = `alerta ${tipo}`;
    alerta.setAttribute('data-temporizador', '');
    alerta.textContent = mensaje;
    document.body.appendChild(alerta);

    // Auto-remover después de 5 segundos
    setTimeout(() => {
        alerta.classList.add('fade-out');
        setTimeout(() => {
            alerta.remove();
        }, 500);
    }, 5000);
}

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

    // Toggle mobile menu
    const mobileMenu = document.querySelector('.mobile-menu');
    const navegacionPrincipal = document.querySelector('.navegacion-principal');

    if (mobileMenu && navegacionPrincipal) {
        mobileMenu.addEventListener('click', function(e) {
            e.stopPropagation();
            navegacionPrincipal.classList.toggle('mostrar');
        });

        // Close on outside click
        document.addEventListener('click', function(e) {
            if (!navegacionPrincipal.contains(e.target) && !mobileMenu.contains(e.target)) {
                navegacionPrincipal.classList.remove('mostrar');
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
            mostrarAlerta('exito', data.message);
            // Opcional: cargar carrito si está abierto
            if (document.querySelector('.carrito').classList.contains('mostrar')) {
                cargarCarrito();
            }
        } else {
            mostrarAlerta('error', data.message);
        }
    })
    .catch(error => {
        mostrarAlerta('error', 'Error al agregar al carrito');
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
            const clave = item.getAttribute('data-clave');
            const cantidadSpan = item.querySelector('.cantidad');
            let cantidad = parseInt(cantidadSpan.textContent);
            if (cantidad > 1) {
                cantidad--;
                actualizarCantidad(clave, cantidad);
            }
        });
    });

    document.querySelectorAll('.btn-mas').forEach(btn => {
        btn.addEventListener('click', function() {
            const item = this.closest('.carrito-item');
            const clave = item.getAttribute('data-clave');
            const cantidadSpan = item.querySelector('.cantidad');
            let cantidad = parseInt(cantidadSpan.textContent);
            cantidad++;
            actualizarCantidad(clave, cantidad);
        });
    });

    // Botones eliminar
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', function() {
            const item = this.closest('.carrito-item');
            const clave = item.getAttribute('data-clave');
            eliminarDelCarrito(clave);
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

function actualizarCantidad(clave, cantidad) {
    fetch('/carrito/actualizar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'clave=' + encodeURIComponent(clave) + '&cantidad=' + encodeURIComponent(cantidad)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cargarCarrito();
        } else {
            mostrarAlerta('error', data.message);
        }
    })
    .catch(error => {
        mostrarAlerta('error', 'Error al actualizar cantidad');
    });
}
function eliminarDelCarrito(clave) {
    fetch('/carrito/eliminar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'clave=' + encodeURIComponent(clave)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cargarCarrito();
        } else {
            mostrarAlerta('error', data.message);
        }
    })
    .catch(error => {
        mostrarAlerta('error', 'Error al eliminar producto');
    });
}


//Categorias