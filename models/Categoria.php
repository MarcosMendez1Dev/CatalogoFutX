<?php
namespace Model;
class Categoria extends ActiveRecord{
    protected static $tabla = 'categorias';
    protected static $columnasDB = ['idcategoria','nombre','descripcion','imagen'];
    public $idcategoria;
    public $nombre;
    public $descripcion;
    public $imagen;

    public function __construct($args=[]){
        $this->idcategoria=$args['idcategoria'] ?? null;
        $this->nombre=$args['nombre'] ?? null;
        $this->descripcion=$args['descripcion'] ?? null;
        $this->imagen=$args['imagen'] ?? null;
    }

    public function validarCategoria() {
        self::$alertas = [];

        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre de la categoría es obligatorio';
        }

        if (!$this->descripcion) {
            self::$alertas['error'][] = 'La descripción es obligatoria';
        }

        return self::$alertas;
    }
}
?>
