<h1 class="nombre-pagina">Mi Perfil</h1>

<?php
    include_once __DIR__ . "/../templates/alertas.php";
?>

<form action="/perfil" class="formulario" method="POST">
    <div class="campo">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo s($usuario->nombre); ?>" required>
    </div>

    <div class="campo">
        <label for="apellido">Apellido</label>
        <input type="text" id="apellido" name="apellido" value="<?php echo s($usuario->apellido); ?>" required>
    </div>

    <div class="campo">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo s($usuario->email); ?>" required>
    </div>

    <div class="campo">
        <label for="telefono">Teléfono</label>
        <input type="text" id="telefono" name="telefono" value="<?php echo s($usuario->telefono); ?>">
    </div>

    <div class="campo">
        <label for="departamento">Departamento</label>
        <select id="departamento" name="departamento">
            <option value="">Selecciona un departamento</option>
        </select>
    </div>

    <div class="campo">
        <label for="municipio">Municipio</label>
        <select id="municipio" name="municipio" disabled>
            <option value="">Selecciona un municipio</option>
        </select>
    </div>

    <div class="campo">
        <label for="direccion_exacta">Dirección Exacta</label>
        <input type="text" id="direccion_exacta" name="direccion_exacta" value="<?php echo s($usuario->direccion_exacta); ?>">
    </div>

    <div class="campo">
        <label for="nit">NIT</label>
        <input type="text" id="nit" name="nit" value="<?php echo s($usuario->nit); ?>">
    </div>

    <div class="campo">
        <label for="nacimiento">Fecha de Nacimiento</label>
        <input type="date" id="nacimiento" name="nacimiento" value="<?php echo s($usuario->nacimiento); ?>">
    </div>

    <input type="submit" class="boton" value="Actualizar Perfil">
</form>

<div class="acciones">
    <a href="/">Volver al Inicio</a>
</div>

<!-- JS -->
<script>
// Cargar departamentos y municipios
document.addEventListener('DOMContentLoaded', function() {
    fetch('/departamentos.json')
        .then(response => response.json())
        .then(data => {
            const departamentoSelect = document.getElementById('departamento');
            const municipioSelect = document.getElementById('municipio');

            // Llenar select de departamentos
            data.departamentos.forEach(depto => {
                const option = document.createElement('option');
                option.value = depto.nombre;
                option.textContent = depto.nombre;
                // Seleccionar el departamento actual del usuario
                if (depto.nombre === "<?php echo s($usuario->departamento); ?>") {
                    option.selected = true;
                }
                departamentoSelect.appendChild(option);
            });

            // Event listener para departamento
            departamentoSelect.addEventListener('change', function() {
                const selectedDepto = this.value;
                municipioSelect.innerHTML = '<option value="">Selecciona un municipio</option>';
                municipioSelect.disabled = true;

                if (selectedDepto) {
                    const depto = data.departamentos.find(d => d.nombre === selectedDepto);
                    if (depto) {
                        depto.municipios.forEach(municipio => {
                            const option = document.createElement('option');
                            option.value = municipio;
                            option.textContent = municipio;
                            // Seleccionar el municipio actual del usuario
                            if (municipio === "<?php echo s($usuario->municipio); ?>") {
                                option.selected = true;
                            }
                            municipioSelect.appendChild(option);
                        });
                        municipioSelect.disabled = false;
                    }
                }
            });

            // Trigger change event to load municipalities for current department
            if ("<?php echo s($usuario->departamento); ?>") {
                departamentoSelect.dispatchEvent(new Event('change'));
            }
        })
        .catch(error => console.error('Error cargando departamentos:', error));
});
</script>
