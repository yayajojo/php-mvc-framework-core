<?php
namespace app\core\form;

 abstract class TextareaField extends BaseField
{
    
    public function renderInput()
    {
        return sprintf('
        <br>
        <textarea id="%s" class="form-control %s" name="%s" rows="4" cols="135">
        %s
        </textarea>',
        $this->attribute,
        $this->model->hasError($this->attribute) ? ' is-invalid' : '',
        $this->attribute,
        $this->model->{$this->attribute});
    }

}