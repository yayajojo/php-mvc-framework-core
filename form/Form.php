<?php
namespace mayjhao\phphmvc\form;


use mayjhao\phphmvc\Model;


class Form
{
    
    public static function begin($method, $action)
    {
       echo sprintf('<form method="%s" action="%s">',$method, $action);
       return new self();
    }

    public function end()
    {
        echo '</form>';
    }
    
    public function field(BaseField $field)
    {
        return $field;
    }
   
}