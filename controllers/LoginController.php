<?php

namespace Controllers;
use Controllers\LoginController;

use Model\Usuario;
use Model\Ordenes;
use MVC\Router;
use Classes\Email;

class LoginController {
    public static function login(Router $router){
       $alertas =[];
       
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);
            $alertas= $auth->validarLogin();

            if(empty($alertas)){
               //Comprobar que exista el usuario
               $usuario = Usuario::where('email', $auth->email);

               if($usuario){
                    if($usuario->comprobarPasswordAndVerificado($auth->password)){
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . ' ' . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //Redireccionamiento
                        if($usuario->admin==="1"){
                           $_SESSION['admin']=$usuario->admin??null;
                           header('Location: /admin');
                        }else{
                            header('Location: /');
                        }

                    }
                }else{
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }

            }
           
        }

    $alertas= Usuario::getAlertas();
     $router->render('auth/login',[
        'alertas' => $alertas,
     ]);
}
    public static function logout(){
        session_start();
        $_SESSION = [];
        session_destroy();
        header('Location: /');
    }

    public static function perfil(Router $router){
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if(!isset($_SESSION['login']) || $_SESSION['login'] !== true){
            header('Location: /login');
            exit;
        }

        $alertas = [];
        $usuario = Usuario::where('id', $_SESSION['id']);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarActualizacionPerfil();

            if(empty($alertas)){
                $resultado = $usuario->guardar();
                if($resultado){
                    Usuario::setAlerta('exito', 'Perfil actualizado correctamente');
                    $_SESSION['nombre'] = $usuario->nombre . ' ' . $usuario->apellido;
                    $_SESSION['email'] = $usuario->email;
                } else {
                    Usuario::setAlerta('error', 'Error al actualizar el perfil');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/perfil', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function historial(Router $router){
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if(!isset($_SESSION['login']) || $_SESSION['login'] !== true){
            header('Location: /login');
            exit;
        }

        $consulta = "SELECT
        o.idordenes,
        o.departamento,
        o.municipio,
        o.direccion_exacta,
        o.estado,
        o.telefono,
        o.fecha,
        p.nombre AS producto,
        d.talla,
        d.cantidad,
        d.precio_unitario,
        (d.cantidad * d.precio_unitario) AS subtotal,
        o.total AS total_orden
    FROM ordenes AS o
    INNER JOIN detalleorden AS d
        ON o.idordenes = d.idordenes
    INNER JOIN productos AS p
        ON d.idproducto = p.id
    WHERE o.idcliente = {$_SESSION['id']}
    ORDER BY o.idordenes DESC";

        $ordenes = \Model\Ordenes::consultarSQL($consulta);

        $router->render('auth/historial', [
            'ordenes' => $ordenes
        ]);
    }
    public static function olvide(Router $router){
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)){
               $usuario = Usuario::where('email', $auth->email);
                if($usuario && $usuario->confirmado ==="1"){
                    $usuario->crearToken();
                    $usuario->guardar();

                    //Enviar Email
                    $email = new Email($usuario->email, $usuario ->nombre, $usuario->token);
                    $email->enviarInstrucciones();
                    Usuario::setAlerta('exito','Revisa tu Email');
                }else{
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
                $alertas= Usuario::getAlertas();
            }
        }
        
        $router->render('auth/olvide-password',[
            'alertas'=>$alertas
        ]);
    }
    public static function recuperar(Router $router){
    $alertas = [];
    $error = false;
    
    $token = s($_GET['token']);
    

    //Buscar cliente por token
    $usuario=Usuario::where('token', $token);
    if(empty($usuario)){
        Usuario::setAlerta('error', 'Token no válido');
        $error = true;
    }

    if($_SERVER['REQUEST_METHOD']==='POST'){
        $password  = new Usuario($_POST);
        $alertas = $password->validarPassword();

        if(empty($alertas)){
            $usuario->password = null;
            $usuario->password= $password->password;
            $usuario->hashPassword();
            $usuario->token=null;
            $resultado=$usuario->guardar();
            if($resultado){
                header('Location: /login');
            }
        }
    }
    
    
    //debuguear($cliente);
    $alertas = Usuario::getAlertas();
    $router->render('auth/recuperar-password',[
        'alertas'=>$alertas,
        'error'=>$error
    ]);
}
    public static function crear(Router $router){

        $usuario = new Usuario;

        //Alertas Vacias
        $alertas =[];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
           $usuario->sincronizar($_POST);
           $alertas =$usuario->validarNuevaCuenta();

           //Revisar que alerta este vacio
           if(empty($alertas)){
            
            //Verificar que no este registrado
            $resultado= $usuario->existeUsuario();

            if($resultado->num_rows){
                $alertas = Usuario::getAlertas();
            }else{
                //Hashear el Password
                $usuario->hashPassword();
                

                //Generar Token
                $usuario->crearToken();

                //Enviar el Email
                $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                $email->enviarConfirmacion();

                //Crear el usuario
                $resultado = $usuario->guardar();
                if($resultado['resultado']){
                    header('Location: /mensaje');
                }

                

                
            }
           }
        }
        $router->render('auth/crear-cuenta',[
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router){
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router){
        $alertas = [];
        $token=s($_GET['token']);
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            //Mostrar mensaje de error
            Usuario::setAlerta('error','Token no valido');
        }else{
            //Modificar a usuario confirmado
            $usuario->confirmado ="1";
            $usuario->token=null;
            $resultado = $usuario->guardar();
            if($resultado){
                Usuario::setAlerta('exito','Cuenta Confirmada Correctamente');
            } else {
                Usuario::setAlerta('error','Error al confirmar la cuenta');
            }
        }
              
        $alertas = Usuario::getAlertas();
        $router->render('auth/confirmar-cuenta',[
            'alertas'=>$alertas
        ]);
    }
    
}

