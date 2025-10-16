<?php
namespace Model;

class Deportes extends ActiveRecord{
    protected static $tabla = 'deportes';
    protected static $columnasDB = ['iddeporte','nombre','descripcion','imagen'];
    public $iddeporte;
    public $nombre;
    public $descripcion;
    public $imagen;

    public function __construct($args=[]){
        $this->iddeporte=$args['iddeporte'] ?? null;
        $this->nombre=$args['nombre'] ?? null;
        $this->descripcion=$args['descripcion'] ?? null;
        $this->imagen=$args['imagen'] ?? null;
    }

    public function validarDeporte() {
        self::$alertas = [];

        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre del deporte es obligatorio';
        }

        if (!$this->descripcion) {
            self::$alertas['error'][] = 'La descripción del deporte es obligatoria';
        }

        return self::$alertas;
    }
}
?>
