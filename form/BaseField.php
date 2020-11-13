<?php

namespace app\core\form;

use app\core\Model;


abstract class BaseField
{
    protected $model;
    protected $attribute;
    

    public function __construct(Model $model, string $attribute)
    {
        $this->model = $model;
        $this->attribute = $attribute;
        
       
    }
    
    abstract public function lables($attribute);
    abstract protected function renderInput();
    public function __toString()
    {
        return sprintf(
            '<div class="form-group">
        <label for="%s">%s</label>
        %s
         <div class="%s">
         %s
         </div>
         </div>',
            $this->attribute,
            $this->lables($this->attribute)??$this->attribute,
            $this->renderInput(),
            $this->model->hasError($this->attribute) ? ' invalid-feedback' : '',
            $this->model->hasError($this->attribute) ? $this->model->getFirstError($this->attribute) : '',
        );
    }
}
