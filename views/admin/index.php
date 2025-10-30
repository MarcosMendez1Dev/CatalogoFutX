<div class="dashboard">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
    <h1><?php echo 'Bienvenido ' . $nombre; ?></h1>
    <h2>Panel de Administración</h2>

    <div class="admin-opciones">
        <div class="opcion">
            <h3>Productos</h3>
            <a href="/admin/agregar-producto" class="btn">Agregar Producto</a>
            <a href="/admin/editar-producto" class="btn">Editar Producto</a>
            <a href="/admin/destacar-producto" class="btn">Destacar Producto</a>
        </div>
        <div class="opcion">
            <h3>Categorías</h3>
            <a href="/admin/agregar-categoria" class="btn">Agregar Categoría</a>
            <a href="/admin/editar-categoria" class="btn">Editar Categoría</a>
        </div>
        <div class="opcion">
            <h3>Marcas</h3>
            <a href="/admin/agregar-marca" class="btn">Agregar Marcas</a>
            <a href="/admin/editar-marca" class="btn">Editar Marca</a>
        </div>
        <div class="opcion">
            <h3>Deportes</h3>
            <a href="/admin/agregar-deporte" class="btn">Agregar Deporte</a>
            <a href="/admin/editar-deporte" class="btn">Editar Deporte</a>
        </div>
        <div class="opcion">
            <h3>Ventas</h3>
            <a href="/admin/agregar-venta" class="btn">Agregar Venta</a>
            <a href="/admin/editar-venta" class="btn">Editar Venta</a>
        </div>
        <div class="opcion">
            <h3>Órdenes</h3>
            <a href="/admin/ordenes" class="btn">Ver Órdenes</a>
        </div>
    </div>

    <div class="ordenes-pendientes">
        <h2>Órdenes Pendientes</h2>
        <div class="table-container">
            <table class="compact-table">
                <thead>
                    <tr>
                        <th>ID Orden</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Cliente</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($ordenes as $orden): ?>
                    <tr>
                        <td><?php echo $orden->idordenes; ?></td>
                        <td><?php echo $orden->producto; ?></td>
                        <td><?php echo $orden->precio_unitario; ?></td>
                        <td><?php echo $orden->cantidad; ?></td>
                        <td><?php echo $orden->cliente; ?></td>
                        <td><?php echo $orden->email; ?></td>
                        <td><?php echo $orden->telefono; ?></td>
                        <td>
                            <a href="/admin/orden/<?php echo $orden->idordenes; ?>/aprobar" class="btn btn-small">Aprobar</a>
                            <a href="/admin/orden/<?php echo $orden->idordenes; ?>/rechazar" class="btn btn-small">Rechazar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
