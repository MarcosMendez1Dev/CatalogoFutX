<?php
namespace Model;

class Ordenes extends ActiveRecord{
    protected static $tabla ='ordenes';
    protected static $columnasDB=['idordenes','idcliente','departamento','municipio','direccion_exacta','total','estado','telefono','fecha'];

    public $idordenes;
    public $idcliente;
    public $departamento;
    public $municipio;
    public $direccion_exacta;
    public $total;
    public $estado;
    public $telefono;
    public $fecha;
    public $cliente;
    public $email;
    public $producto;
    public $talla;
    public $cantidad;
    public $precio_unitario;
    public $subtotal;
    public $total_orden;


    public function __construct($args=[]){
        $this->idordenes = $args['idordenes'] ?? null;
        $this->idcliente = $args['idcliente'] ?? null;
        $this->departamento = $args['departamento'] ?? '';
        $this->municipio = $args['municipio'] ?? '';
        $this->direccion_exacta = $args['direccion_exacta'] ?? '';
        $this->total = $args['total'] ?? '0';
        $this->estado = $args['estado'] ?? '0';
        $this->telefono = $args['telefono'] ?? '0';
        $this->fecha = $args['fecha'] ?? '0';
    }
}
?>