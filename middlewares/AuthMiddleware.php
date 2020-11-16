<?php

namespace mayjhao\phphmvc\middlewares;

use mayjhao\phphmvc\Application;
use mayjhao\phphmvc\exceptions\ForbiddenException;

class AuthMiddleware extends BaseMiddleware
{
    protected $actions = [];
    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }
    public function execute()
    {
        if (Application::$app->isGuest()) {
            if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)) {
                throw new ForbiddenException();
            }
        } else {
        }
    }
}
