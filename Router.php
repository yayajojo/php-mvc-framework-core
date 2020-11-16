<?php

namespace mayjhao\phphmvc;

use mayjhao\phphmvc\exceptions\NotFoundException;

class Router
{
    protected $routes = [];
    protected $request;
    protected $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
    public function get($path, $action)
    {
        $this->routes['get'][$path] = $action;
    }
    
    public function post($path, $action)
    {
        $this->routes['post'][$path] = $action;
    }

    
    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->method();
        $action =  $this->routes[$method][$path] ?? false;
        $this->routeCallback($action);
    }
    protected function routeCallback($action)
    {
        if ($action == false) {
            throw new NotFoundException();
        } elseif (is_array($action)) {
            $controller = new $action[0]();
            $action = $action[1];
            Application::$app->controller = $controller;
            Application::$app->controller->action = $action;
            foreach($controller->getMiddlewares() as $middleware){ 
                $middleware->execute();
            }
            echo call_user_func([$controller,$action],$this->request, $this->response);
        } else{
            echo call_user_func($action);
        }
    }
}
