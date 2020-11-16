<?php

namespace mayjhao\phphmvc;

use PDO;

abstract class Model
{
     const RULE_INACTIVE = 0;
     const RULE_ACTIVE = 1;
     const RULE_DELETED = 2;
     const RULE_REQUIRED = 'required';
     const RULE_EMAIL = 'email';
     const RULE_MIN = 'min';
     const RULE_MAX = 'max';
     const RULE_MATCH = 'match';
     const RULE_UNIQUE = 'unique';
     const RULE_EXISTS = 'exists';
    

    // abstact class => preventing the commonly used data 
    public function loadData(array $data)
    {
        foreach ($data as $attribute => $value) {
            if (property_exists($this, $attribute)) {
                $this->{$attribute} = $value;
            }
        }
    }
    abstract public function rules();
    public function validate()
    {
        foreach ($this->rules() as $attribute => $rules) {
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (is_array($rule)) {
                    $ruleName = $rule[0];
                }
                $value = $this->{$attribute};

                if ($ruleName === self::RULE_REQUIRED && !$value) {
                    $this->addRuleErrors($attribute, $ruleName);
                } 
                if ($ruleName === self::RULE_EMAIL && !filter_var($this->{$attribute}, FILTER_VALIDATE_EMAIL)) {
                    $this->addRuleErrors($attribute, $ruleName);
                } 
                
                if ($ruleName === self::RULE_MIN && (strlen($value) < $rule[$ruleName])) {
                    $this->addRuleErrors($attribute, $ruleName, $rule);
                } 
                if ($ruleName === self::RULE_MAX && strlen($value) > $rule[$ruleName]) {
                    $this->addRuleErrors($attribute, $ruleName, $rule);
                } 
                if ($ruleName === self::RULE_MATCH && $this->{$attribute} !== $this->{$rule[$ruleName]}) {
                    $this->addRuleErrors($attribute, $ruleName, $rule);
                }
                if($ruleName === self::RULE_UNIQUE && $this->isNotUnique($attribute, $rule)){
                    $this->addRuleErrors($attribute, $ruleName);
                }
                if($ruleName === self::RULE_EXISTS && !$this->exists($attribute, $rule)){
                    $this->addRuleErrors($attribute, $ruleName);
                }
            }
        }
        return empty($this->errors);
    }
    protected function exists($attribute, $rule){
        $table = $rule['table'];
        $column = $rule['column']?? $attribute;
        $statement = Application::$app->db->pdo->prepare("SELECT $column FROM $table WHERE $column = :$attribute");
        $statement->bindValue(":$attribute",$this->{$attribute});
        $statement->execute();
        $existedData = $statement->fetchAll();
        return !empty($existedData);
         
    }
    protected function isNotUnique($attribute, array $rule)
    {
       $value = $this->{$attribute};
       $attr = $rule['attribute']??$attribute;
       $table = (new $rule['class']())->tableName();
       $statement = Application::$app->db->pdo->prepare(
           "SELECT $attr FROM $table WHERE $attr = :attr"
       );
       
       $statement->bindValue(':attr',$value);
       $statement->execute();
       $existedData = $statement->fetchAll();
       if($existedData){
         return true;
       }
       return false;
    }
    protected function addRuleErrors($attribute, $ruleName, $rule= [])
    {
        $errorMessage = $this->errorMessages($ruleName) ?? '';
        if (!empty($rule)) {
            $value = $rule[$ruleName];
            $errorMessage = str_replace("{{$ruleName}}", $value, $errorMessage);
        }
        $this->errors[$attribute][] = $errorMessage;
    }
    public function addErrors($attribute, string $errorMessage){
        $this->errors[$attribute][] = $errorMessage;
    }
    protected function errorMessages($ruleName)
    {
        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_EMAIL => 'This must be a valid email',
            self::RULE_MIN => 'Min length of this field must be {min}',
            self::RULE_MAX => 'Max length of this field must be {max}',
            self::RULE_MATCH => 'This field must be as same as {match}',
            self::RULE_UNIQUE => 'Record of this field already exists',
            self::RULE_EXISTS => 'Record of this field dose not exist',
        ][$ruleName];
    }

    public function hasError($attribute)
    {
    
      return isset($this->errors[$attribute]);
    }

    public function getFirstError($attribute)
    {
        return $this->errors[$attribute][0];
    }
}
