<?php

namespace mayjhao\phphmvc;

class View
{
    public $title;
    protected function layout($layout)
    {
        ob_start();
        include_once Application::$ROOT_DIR . "/views/layouts/$layout.php";
        return ob_get_clean();
    }

    protected function viewContent($view, array $params = [])
    {
        foreach($params as $key=>$val){
            $$key = $val;
        }
        ob_start();
        include_once Application::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean();
    }
    public function renderView($view, $layout = 'main' ,array $params= [])
    {
        $viewContent = $this->viewContent($view, $params);
        
        $layoutContent = $this->layout($layout);
        
        return str_replace("{{content}}", $viewContent ,$layoutContent);
    }

    public function renderOnlyView($view, array $params= [])
    {
        $viewContent = $this->viewContent($view, $params);
        return $viewContent;
    } 

}