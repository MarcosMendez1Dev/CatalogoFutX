<?php
namespace Model;

class Productos extends ActiveRecord{
    protected static $tabla = 'productos';
    protected static $columnasDB = ['id','nombre','colorway','descripcion','categoria_id','marca','stock','imagenes','precio','costo','destacado'];
    public $id;
    public $nombre;
    public $colorway;
    public $descripcion;
    public $categoria_id;
    public $marca;
    public $stock;
    public $imagenes;
    public $precio;
    public $costo;
    public $destacado;

    public function __construct($args=[]){
        $this->id=$args['id'] ?? null;
        $this->nombre=$args['nombre'] ?? null;
        $this->colorway=$args['colorway'] ?? null;
        $this->descripcion=$args['descripcion'] ?? null;
        $this->categoria_id=$args['categoria_id'] ?? null;
        $this->marca=$args['marca'] ?? null;
        $this->stock=$args['stock'] ?? null;
        if (isset($args['imagenes']) && !empty($args['imagenes']) && is_string($args['imagenes'])) {
            $decoded = json_decode($args['imagenes'], true);
            $this->imagenes = is_array($decoded) ? $decoded : [];
        } else if (isset($args['imagenes']) && is_array($args['imagenes'])) {
            $this->imagenes = $args['imagenes'];
        } else {
            $this->imagenes = [];
        }
        $this->precio=$args['precio'] ?? null;
        $this->costo=$args['costo'] ?? null;
        $this->destacado=$args['destacado'] ?? null;
    }
}
?>