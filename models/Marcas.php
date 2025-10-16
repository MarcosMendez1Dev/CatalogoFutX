<?php
namespace Model;

class Marcas extends ActiveRecord{
    protected static $tabla ='marcas';
    protected static $columnasDB=['idmarca','nombre','descripcion','imagen'];

    public $idmarca;
    public $nombre;
    public $descripcion;
    public $imagen;

    public function __construct($args=[]){
        $this->idmarca = $args['idmarca'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->imagen = $args['imagen'] ?? '';
    }

    public function validarMarcas() {
        self::$alertas = [];

        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre de la marca es obligatorio';
        }

        if (!$this->descripcion) {
            self::$alertas['error'][] = 'La descripción es obligatoria';
        }

        return self::$alertas;
    }
}
?>
