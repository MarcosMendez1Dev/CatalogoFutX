<?php

namespace MVC;

class Router
{
    public array $getRoutes = [];
    public array $postRoutes = [];

    public function get($url, $fn)
    {
        $this->getRoutes[] = ['pattern' => $url, 'fn' => $fn];
    }

    public function post($url, $fn)
    {
        $this->postRoutes[] = ['pattern' => $url, 'fn' => $fn];
    }

    public function comprobarRutas()    {
        $currentUrl=strtok($_SERVER['REQUEST_URI'],'?');
        // Proteger Rutas...
        session_start();

        // Arreglo de rutas protegidas...
        // $rutas_protegidas = ['/admin', '/propiedades/crear', '/propiedades/actualizar', '/propiedades/eliminar', '/vendedores/crear', '/vendedores/actualizar', '/vendedores/eliminar'];

        // $auth = $_SESSION['login'] ?? null;

        $currentUrl = str_replace('/index.php', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) ?? '/';
        if ($currentUrl === '') {
            $currentUrl = '/';
        }
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {
            $routes = $this->getRoutes;
        } else {
            $routes = $this->postRoutes;
        }

        $fn = null;
        foreach ($routes as $route) {
            if ($this->matchRoute($route['pattern'], $currentUrl)) {
                $fn = $route['fn'];
                break;
            }
        }

        if ( $fn ) {
            // Call user fn va a llamar una función cuando no sabemos cual sera
            call_user_func($fn, $this); // This es para pasar argumentos
        } else {
            echo "Página No Encontrada o Ruta no válida";
        }
    }

    private function matchRoute($pattern, $url)
    {
        // Simple pattern matching for routes with parameters
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        return preg_match("#^$pattern$#", $url);
    }

    public function render($view, $datos = [])
    {

        // Leer lo que le pasamos  a la vista
        foreach ($datos as $key => $value) {
            $$key = $value;  // Doble signo de dolar significa: variable variable, básicamente nuestra variable sigue siendo la original, pero al asignarla a otra no la reescribe, mantiene su valor, de esta forma el nombre de la variable se asigna dinamicamente
        }

        ob_start(); // Almacenamiento en memoria durante un momento...

        // entonces incluimos la vista en el layout
        include_once __DIR__ . "/views/$view.php";
        $contenido = ob_get_clean(); // Limpia el Buffer
        include_once __DIR__ . '/views/layout.php';
    }
}
