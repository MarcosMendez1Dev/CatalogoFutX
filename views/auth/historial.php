<h1 class="nombre-pagina">Historial de Pedidos</h1>

<?php if (empty($ordenes)): ?>
    <p>No tienes pedidos realizados aún.</p>
<?php else: ?>
    <div class="ordenes-historial">
        <?php
        $currentOrderId = null;
        foreach ($ordenes as $orden):
            // Si es una nueva orden, cerramos la anterior (si existía)
            if ($currentOrderId !== $orden->idordenes):
                if ($currentOrderId !== null): ?>
                        </tbody>
                    </table>
                    <p class="total-orden">
                        <strong>Total: Q<?php echo number_format($ultimoTotal, 2); ?></strong>
                    </p>
                </div>
                <?php endif; ?>

                <!-- Nueva orden -->
                <div class="orden">
                    <h3>
                        Pedido #<?php echo $orden->idordenes; ?> -
                        Estado: <?php echo ucfirst($orden->estado); ?> -
                        Fecha: <?php echo $orden->fecha; ?>
                    </h3>
                    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($orden->departamento . ', ' . $orden->municipio . ', ' . $orden->direccion_exacta); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($orden->telefono); ?></p>

                    <table class="tabla-ordenes">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Talla</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Dirección de Entrega</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                <?php
                $currentOrderId = $orden->idordenes;
                $ultimoTotal = $orden->total_orden;
            endif;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($orden->producto); ?></td>
                    <td><?php echo htmlspecialchars($orden->talla ?? 'N/A'); ?></td>
                    <td><?php echo $orden->cantidad; ?></td>
                    <td>Q<?php echo number_format($orden->precio_unitario, 2); ?></td>
                    <td><?php echo htmlspecialchars($orden->departamento . ', ' . $orden->municipio . ', ' . $orden->direccion_exacta); ?></td>
                    <td>Q<?php echo number_format($orden->subtotal, 2); ?></td>
                </tr>
        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p class="total-orden">
                        <strong>Total: Q<?php echo number_format($ultimoTotal, 2); ?></strong>
                    </p>
                </div>
    </div>
<?php endif; ?>

<div class="acciones">
    <a href="/">Volver al Inicio</a>
</div>
