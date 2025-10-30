<?php
namespace Model;
 

class detalleOrden extends ActiveRecord{
    protected static $tabla ='detalleorden';
    protected static $columnasDB=['iddetalle','idordenes','idproducto','talla','cantidad','precio_unitario'];

    public $iddetalle;
    public $idordenes;
    public $idproducto;
    public $talla;
    public $cantidad;
    public $precio_unitario;

    public function __construct($args=[]){
        $this->iddetalle = $args['iddetalle'] ?? null;
        $this->idordenes = $args['idordenes'] ?? null;
        $this->idproducto = $args['idproducto'] ?? null;
        $this->talla = $args['talla'] ?? '';
        $this->cantidad = $args['cantidad'] ?? '0';
        $this->precio_unitario = $args['precio_unitario'] ?? '';

    }
}
?>