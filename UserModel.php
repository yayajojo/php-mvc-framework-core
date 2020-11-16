<?php

namespace mayjhao\phphmvc;

use mayjhao\phphmvc\db\DbModel;

abstract class UserModel extends DbModel
{
    abstract public function disPlayName();
}