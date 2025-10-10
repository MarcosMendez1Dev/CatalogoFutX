<?php

namespace Controllers;
use Controllers\LoginController;

use Model\Cliente;
use MVC\Router;
use Classes\Email;

class LoginController {
    public static function login(Router $router){
       $alertas =[];
       
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Cliente($_POST);
            $alertas= $auth->validarLogin();

            if(empty($alertas)){
               //Comprobar que exista el usuario
               $cliente = Cliente::where('email', $auth->email);

               if($cliente){
                    if($cliente->comprobarPasswordAndVerificado($auth->password)){
                        session_start();
                        $_SESSION['id'] = $cliente->id;
                        $_SESSION['nombre'] = $cliente->nombre . ' ' . $cliente->apellido;
                        $_SESSION['email'] = $cliente->email;
                        $_SESSION['login'] = true;

                        //Redireccionamiento
                        if($cliente->admin==="1"){
                           $_SESSION['admin']=$cliente->admin??null;
                           header('Location: /admin');
                        }else{
                            header('Location: /');
                        }

                    }
                }else{
                    Cliente::setAlerta('error', 'Usuario no encontrado');
                }

            }
           
        }

    $alertas= Cliente::getAlertas();
     $router->render('auth/login',[
        'alertas' => $alertas,
     ]);
}
    public static function logout(){
        echo "Desde Logout";
    }
    public static function olvide(Router $router){
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Cliente($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)){
                $cliente = Cliente::where('email', $auth->email);
                if($cliente && $cliente->confirmado ==="1"){
                    $cliente->crearToken();
                    $cliente->guardar();

                    //Enviar Email
                    $email = new Email($cliente->email, $cliente ->nombre, $cliente->token);
                    $email->enviarInstrucciones();
                    Cliente::setAlerta('exito','Revisa tu Email');
                }else{
                    Cliente::setAlerta('error', 'Usuario no encontrado');
                }
                $alertas= Cliente::getAlertas();
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
    $cliente=Cliente::where('token', $token);
    if(empty($cliente)){
        Cliente::setAlerta('error', 'Token no válido');
        $error = true;
    }

    if($_SERVER['REQUEST_METHOD']==='POST'){
        $password  = new Cliente($_POST);
        $alertas = $password->validarPassword();

        if(empty($alertas)){
            $cliente->password = null;
            $cliente->password= $password->password;
            $cliente->hashPassword();
            $cliente->token=null;
            $resultado=$cliente->guardar();
            if($resultado){
                header('Location: /login');
            }
        }
    }
    
    
    //debuguear($cliente);
    $alertas = Cliente::getAlertas();
    $router->render('auth/recuperar-password',[
        'alertas'=>$alertas,
        'error'=>$error
    ]);
}
    public static function crear(Router $router){

        $cliente = new Cliente;

        //Alertas Vacias
        $alertas =[];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
           $cliente->sincronizar($_POST);
           $alertas =$cliente->validarNuevaCuenta();

           //Revisar que alerta este vacio
           if(empty($alertas)){
            
            //Verificar que no este registrado
            $resultado= $cliente->existeUsuario();

            if($resultado->num_rows){
                $alertas = Cliente::getAlertas();
            }else{
                //Hashear el Password
                $cliente->hashPassword();
                

                //Generar Token
                $cliente->crearToken();

                //Enviar el Email
                $email = new Email($cliente->email, $cliente->nombre, $cliente->token);

                $email->enviarConfirmacion();

                //Crear el usuario
                $resultado = $cliente->guardar();
                if($resultado){
                    header('Location: /mensaje');
                }
                //debuguear($cliente);

                
            }
           }
        }
        $router->render('auth/crear-cuenta',[
            'cliente' => $cliente,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router){
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router){
        $alertas = [];
        $token=s($_GET['token']);
        $cliente = Cliente::where('token', $token);

        if(empty($cliente)){
            //Mostrar mensaje de error
            Cliente::setAlerta('error','Token no valido');
        }else{
            //Modificar a usuario confirmado
            Cliente::setAlerta('exito','Cuenta Confirmadad Correctamente');
            $cliente->confirmado ="1";
            $cliente->token=null;
            $cliente->guardar();
        }
              
        $alertas = Cliente::getAlertas();
        $router->render('auth/confirmar-cuenta',[
            'alertas'=>$alertas
        ]);
    }
    
}

