<?php
namespace app\core\form;

use app\core\Model;

abstract class InputField extends BaseField
{
    const TYPE_TEXT = 'text'; 
    const TYPE_PASSWORD = 'password';
    protected $type;
    public function __construct(Model $model, string $attribute)
    {
        parent::__construct($model, $attribute);
        $this->tyep = self::TYPE_TEXT;
        
    } 
    public function passwordField()
    {
        $this->type = self::TYPE_PASSWORD;
        return $this;
    }
    protected function renderInput()
    {
        return sprintf('<input type="%s" 
        class="form-control %s" 
         name="%s" 
         id="%s"
         value="%s"
         >',
         $this->type,
         $this->model->hasError($this->attribute) ? ' is-invalid' : '',
         $this->attribute,
         $this->attribute,
         $this->model->{$this->attribute});
    }
}