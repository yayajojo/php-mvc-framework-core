<?php

namespace mayjhao\phphmvc;

class Request
{

    public function method()
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        return $method;
    }
    public function isGet()
    {
        return $this->method() === 'get';
    }
    public function isPost()
    {
        return $this->method() === 'post';
    }

    public function getPath()
    {
        $path  = $_SERVER['REQUEST_URI'] ?? '/';
        $path = explode("?", $path)[0];
        return $path;
    }

    public function getBody()
    {
        $body = [];
        if ($this->method() === 'get') {
            foreach ($_GET as $key => $val) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        };
        if ($this->method() === 'post') {
            foreach ($_POST as $key => $val) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        };
        return $body;
    }
}
