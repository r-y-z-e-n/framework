<?php

namespace ryzen\framework\db;

use ryzen\framework\Application;
use ryzen\framework\Model;

/**
 * @author razoo.choudhary@gmail.com
 * Class DbModel
 * @package ryzen\framework
 */
abstract class DbModel extends Model
{
    abstract public static function tableName(): string;

    abstract public static function attributes(): array;

    abstract public static function primaryKey(): string;

    public static function findOne($where)
    {

        return self::getAll($where, 'single');
    }

    public static function getAll($where = [], $objectType = '')
    {

        $tableName = static::tableName();

        if ($where) {
            $attributes = array_keys($where);
            $sql = implode("AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));
            $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
            foreach ($where as $key => $item) {
                $statement->bindValue(":$key", $item);
            }
        } else {

            $statement = self::prepare("SELECT * FROM $tableName");
        }

        $statement->execute();

        if ($objectType == 'single') {

            return $statement->fetchObject(static::class);
        }
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function update(array $data, array $where)
    {

        $tableName = static::tableName();
        $attributes = array_keys($data);
        $whereAttribute = array_keys($where);

        $toSET = implode(',', array_map(fn($attr) => "$attr = :$attr", $attributes));
        $whereDataString = implode(" AND ", array_map(fn($attrw) => "$attrw = :$attrw", $whereAttribute));

        $statement = self::prepare("UPDATE $tableName SET $toSET WHERE $whereDataString ");

        foreach (array_merge($data, $where) as $key => $item) {
            $statement->bindValue(":$key", $item);
        }

        $statement->execute();
    }

    public function save()
    {

        $tableName = $this->tableName();
        $attribute = $this->attributes();
        $params = array_map(fn($attr) => ":$attr", $attribute);
        $statement = self::prepare("INSERT INTO $tableName (" . implode(',', $attribute) . ") VALUES (" . implode(",", $params) . ")");

        foreach ($attribute as $attributes) {

            $statement->bindValue(":$attributes", $this->{$attributes});
        }

        $statement->execute();

        return true;
    }

    public static function prepare($sql)
    {

        return Application::$app->db->pdo->prepare($sql);
    }
}