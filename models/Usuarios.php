<?php
namespace Model;
class Usuarios extends ActiveRecord{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id','nombre','apellido','password','email','telefono','nit','token','nacimiento','confirmado','admin'];
    public $id;
    public $nombre;
    public $apellido;
    public $password;
    public $email;
    public $telefono;
    public $nit;
    public $token;
    public $nacimiento;
    public $confirmado;
    public $admin;

    public function __construct($args=[]){
        $this->id=$args['id'] ?? '';
        $this->nombre=$args['nombre'] ??'' ;
        $this->apellido=$args['apellido'] ?? '';
        $this->password=$args['password'] ??'' ;
        $this->email=$args['email'] ?? '';
        $this->telefono=$args['telefono'] ??'' ;
        $this->nit=$args['nit'] ?? '';
        $this->token=$args['token'] ??'' ;
        $this->nacimiento=$args['nacimiento'] ??'' ;
        $this->confirmado=$args['confirmado'] ?? '';
        $this->admin=$args['admin'] ??'' ;
    }
}
?>