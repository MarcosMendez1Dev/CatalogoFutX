<?php
namespace Model;

class marca extends ActiveRecord{
    protected static $tabla ='marcas';
    protected static $columnasDB=['idmarca','nombre','descripcion','imagen'];

    public $idmarca;
    public $nombre;
    public $descripcion;
    public $imagen;


    public function __construct($args=[]){
        $this->iddetalle = $args['idmarca'] ?? null;
        $this->idordenes = $args['nombre'] ?? '';
        $this->idproducto = $args['descripcion'] ?? '';
        $this->cantidad = $args['imagen'] ?? '';
    }
}
?>