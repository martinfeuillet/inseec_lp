<?php

namespace Inseec\App\Routing;
class Router
{
    private $routes = array();

    public function addRoute( $url , $controller , $method ) {
        $this->routes[ $url ] = array('controller' => $controller , 'method' => $method);
    }

    public function dispatch( $url ) {
        if ( array_key_exists( $url , $this->routes ) ) {
            $controller = $this->routes[ $url ]['controller'];
            $method     = $this->routes[ $url ]['method'];
            $controller->$method();
        } else {
            // Handle 404 error
            header( "HTTP/1.0 404 Not Found" );
            echo '404 Not Found';
        }
    }

}