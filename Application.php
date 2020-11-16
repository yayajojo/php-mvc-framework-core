<?php

namespace mayjhao\phphmvc;

use mayjhao\phphmvc\db\Database;
use mayjhao\phphmvc\db\DbModel;
use app\models\User;
use Exception;

class Application
{
    public static $ROOT_DIR;
    public $request;
    public $response;
    public $router;
    public static $app;
    public $db;
    public $session;
    public $controller;
    public $userClass;
    public $view;
    public $user;

    public function __construct(string $rootPath, array $config)
    {
        self::$ROOT_DIR = $rootPath;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        self::$app = $this;
        $this->db = new Database($config['db']);
        $this->session = new Session();
        $this->userClass = $config['userClass'];
        $this->view = new View();
        $primaryValue = $this->session->get('user');
        if ($primaryValue) {
            $primaryKey = $this->userClass::primaryKey();
            $this->user = (new $this->userClass())->findOne([$primaryKey => $primaryValue]);
        } else {
            $this->user = null;
        }
    }
    public function run()
    {
        try{
            $this->router->resolve();
        }catch(Exception $e){
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('_error','main',['exception'=>$e]);
        }
    }

    public function isGuest()
    {
        return !(bool)$this->user;
    }
    public function login(DbModel $user)
    {
        $primaryKey = $this->userClass::primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);
        return true;
    }

    public function logout()
    {
        $this->user = null;
        $this->session->remove('user');
    }
}
