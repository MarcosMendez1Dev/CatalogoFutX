<?php
    $alertas = $alertas ?? [];
    foreach($alertas as $key => $mensajes):
        foreach($mensajes as $mensaje):
?>
    <div class="alerta <?php echo $key; ?>" data-temporizador>
        <?php echo $mensaje; ?>
    </div>
<?php
        endforeach;
    endforeach;
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alertas = document.querySelectorAll('[data-temporizador]');
        alertas.forEach(alerta => {
            setTimeout(() => {
                alerta.classList.add('fade-out');
                setTimeout(() => {
                    alerta.remove();
                }, 500); // Tiempo para la transición de fade-out
            }, 5000); // 5 segundos
        });
    });
</script>
