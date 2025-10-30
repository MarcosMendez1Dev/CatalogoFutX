<?php
namespace Model;

class Terreno extends ActiveRecord {
    protected static $tabla = 'terreno';
    protected static $columnasDB = ['idterreno', 'nombre', 'descripcion', 'imagen'];

    public $idterreno;
    public $nombre;
    public $descripcion;
    public $imagen;

    public function __construct($args = []) {
        $this->idterreno = $args['idterreno'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->imagen = $args['imagen'] ?? '';
    }

    public function validarTerreno() {
        self::$alertas = [];

        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre del terreno es obligatorio';
        }

        if (!$this->descripcion) {
            self::$alertas['error'][] = 'La descripción es obligatoria';
        }

        return self::$alertas;
    }
}
?>
