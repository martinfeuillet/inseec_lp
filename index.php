<?php
require_once __DIR__.'/vendor/autoload.php';

use Inseec\App\Controller\BachelorController;
use Inseec\App\Controller\JpoController;
use Inseec\App\Routing\Router;



$router = new Router();
$router->addRoute('/lp_inseec/', new JpoController(), 'index');
$router->addRoute('/lp_inseec/bachelor', new BachelorController(), 'index');

// Get the current URL and dispatch to the appropriate controller and method
$currentUrl = $_SERVER['REQUEST_URI'];
$router->dispatch($currentUrl);
