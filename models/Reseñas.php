<?php
namespace Model;
class Reseñas extends ActiveRecord{
    protected static $tabla = 'reseñas';
    protected static $columnasDB = ['idreseña','idusuario','idproducto','calificacion','comentario','fecha'];
    public $idreseña;
    public $idusuario;
    public $idproducto;
    public $calificacion;
    public $comentario;
    public $fecha;

    public function __construct($args=[]){
        $this->idreseña=$args['idreseña'] ?? null;
        $this->idusuario=$args['idusuario'] ?? null;
        $this->idproducto=$args['idproducto'] ?? null;
        $this->calificacion=$args['calificacion'] ?? null;
        $this->comentario=$args['comentario'] ?? null;
        $this->fecha=$args['fecha'] ?? null;
    }
}
?>