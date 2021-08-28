<?php

namespace ryzen\framework;

/**
 * @author razoo.choudhary@gmail.com
 * Class Model
 * @package ryzen\framework
 */

abstract class Model
{
    public const RULE_REQUIRED  = 'required';
    public const RULE_EMAIL     = 'email';
    public const RULE_MIN       = 'min';
    public const RULE_MAX       = 'max';
    public const RULE_MATCH     = 'match';
    public const RULE_UNIQUE    = 'unique';

    public function loadData($data){

        foreach ($data as $key => $value){

            if(property_exists($this, $key)){

                $this->{$key} = $value;
            }
        }
    }

    abstract public function rules() : array;

    public function labels(){ return []; }

    public function getLabel($attribute){

        return $this->labels()[$attribute] ?? $attribute;
    }

    public array $errors = [];

    public function validate(){

        foreach ($this->rules() as $attribute => $rules){

            $value = $this->{$attribute};

            foreach ($rules as $rule){

                $ruleName = $rule;

                if( !is_string($ruleName) ){

                    $ruleName = $rule[0];
                }
                if($ruleName === self::RULE_REQUIRED && !$value){

                    $this->addErrorForRule($attribute, self::RULE_REQUIRED);
                }
                if($ruleName == self::RULE_EMAIL && !filter_var($value,FILTER_VALIDATE_EMAIL)){

                    $this->addErrorForRule($attribute, self::RULE_EMAIL);
                }
                if($ruleName == self::RULE_MIN && strlen($value) < $rule['min']){

                    $this->addErrorForRule($attribute, self::RULE_MIN, $rule);
                }
                if($ruleName == self::RULE_MAX && strlen($value) > $rule['max']){

                    $this->addErrorForRule($attribute, self::RULE_MAX, $rule);
                }
                if($ruleName == self::RULE_MATCH && $value !== $this->{$rule['match']}){

                    $rule['match'] = $this->getLabel($rule['match']);
                    $this->addErrorForRule($attribute, self::RULE_MATCH, $rule);
                }

                if($ruleName == self::RULE_UNIQUE){

                    $className      = $rule['class'];
                    $uniAttribute   = $rule['attribute'] ?? $attribute ;
                    $tableName      = $className::tableName();
                    $statement      = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $uniAttribute = :attr");

                    $statement->bindValue(":attr", $value);
                    $statement->execute();
                    $result = $statement->fetchObject();

                    if($result){

                        $this->addErrorForRule($attribute, self::RULE_UNIQUE, ['field' => $this->getLabel($attribute)]);
                    }
                }
            }
        }

        return empty($this->errors);
    }

    private function addErrorForRule(string $attribute, string $rule, $params =[]){

        $message = $this->errorMessages()[$rule] ?? '';

        foreach ($params as $key => $value){

            $message = str_replace("{{$key}}", $value, $message);
        }

        $this->errors[$attribute][] = $message;
    }

    public function addError(string $attribute, string $message){

        $this->errors[$attribute][] = $message;
    }

    public function errorMessages(){

        return [
            self::RULE_REQUIRED => 'This Field IS Required',
            self::RULE_EMAIL    => 'This Field Must Be A Valid Email Address',
            self::RULE_MAX      => 'This Field Cannot Exceed Max Of {max}',
            self::RULE_MIN      => 'This Field Cannot Be Under {min}',
            self::RULE_MATCH    => 'This Field Must Be Same As {match}',
            self::RULE_UNIQUE   => 'There Already Exists A Record With {field}',
            ];
    }

    public function hasError($attribute){

        return $this->errors[$attribute] ?? false;
    }

    public function getFirstError($attribute){

       return $this->errors[$attribute][0] ?? false;
    }
}