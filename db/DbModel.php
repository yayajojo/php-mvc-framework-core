<?php

namespace mayjhao\phphmvc\db;

use mayjhao\phphmvc\Application;
use mayjhao\phphmvc\Model;
use mayjhao\phphmvc\UserModel;

abstract class DbModel extends Model
{
    abstract public function tableName();

    abstract public function attributes();

    abstract public static function primaryKey();
    
    public function save()
    {
        $statement = $this->insert();
        foreach ($this->attributes() as $attr) {
            $statement->bindValue(':' . $attr, $this->{$attr});
        }
        if ($statement->execute()) {
            return true;
        } else {
            return false;
        };
    }

    public function insert()
    {
        $params = array_map(function ($attr) {
            return ':' . $attr;
        }, $this->attributes());
        $sql = "INSERT INTO " . $this->tableName() . "(" . implode(',', $this->attributes()) . ")VALUES(" . implode(',', $params) . ")";
        return self::prepare($sql);
    }
    protected static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    } 
    public function findOne(array $where):?UserModel
    {
        $attrbutes = array_map(function ($attr) {
            return "$attr = :$attr";
        }, array_keys($where));
        $attrbutes = implode(" AND ",$attrbutes);
        $sql = "SELECT * FROM " . $this->tableName() . " WHERE ". $attrbutes;
        
        $statement = self::prepare($sql);
        foreach($where as $attr=>$val){
            $statement->bindValue($attr, $val);
        }
        $statement->execute();
        $obj = $statement->fetchObject(static::class);
        return $obj;
    }
}
