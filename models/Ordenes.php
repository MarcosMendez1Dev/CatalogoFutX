<?php
namespace Model;

class Ordenes extends ActiveRecord{
    protected static $tabla ='ordenes';
    protected static $columnasDB=['idordenes','idcliente','direccion','total','estado','telefono','fecha'];

    public $idordenes;
    public $idcliente;
    public $direccion;
    public $total;
    public $estado;
    public $telefono;
    public $fecha;


    public function __construct($args=[]){
        $this->idordenes = $args['idordenes'] ?? null;
        $this->idcliente = $args['idcliente'] ?? null;
        $this->direccion = $args['direccion'] ?? null;
        $this->total = $args['total'] ?? '0';
        $this->estado = $args['estado'] ?? '0';
        $this->telefono = $args['telefono'] ?? '0';
        $this->fecha = $args['fecha'] ?? '0';
    }
}
?>