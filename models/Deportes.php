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
        $alertas = [];
    
        if(!$this->nombre) {
            $alertas['error'][] = 'El nombre del deporte es obligatorio';
        }
    
        if(!$this->descripcion) {
            $alertas['error'][] = 'La descripción del deporte es obligatoria';
        }
    
        // If no image is set and this is a new record
        if(!$this->imagen && !$this->id) {
            $alertas['error'][] = 'La imagen del deporte es obligatoria';
        }
    
        return $alertas; // Always returns an array
    }
}
?>
