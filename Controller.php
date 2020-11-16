<?php
namespace mayjhao\phphmvc;

use mayjhao\phphmvc\middlewares\BaseMiddleware;

abstract class Controller
{  
    /**
     * @var mayjhao\phphmvc\middlewares\BaseMiddleware[]
     */
    
    protected $middlewares = [];
    public $action;
    public function render($view, $layout, array $params = [])
    {
        return Application::$app->view->renderView($view, $layout, $params);
    }

    public function registerMiddleware(BaseMiddleware $middleware)
    {
          $this->middlewares[] = $middleware;
          
    }
    public function getMiddlewares()
    {
        return $this->middlewares;
    }
}
