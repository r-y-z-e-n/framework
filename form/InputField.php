<?php

namespace ryzen\framework\form;

use ryzen\framework\Model;

/**
 * @author razoo.choudhary@gmail.com
 * Class Field
 * @package ryzen\framework\form
 */
class InputField extends BaseField
{

    public const TYPE_TEXT = 'text';
    public const TYPE_EMAIl = 'email';
    public const TYPE_PASSWORD = 'password';
    public const TYPE_NUMBER = 'number';

    public string $type;

    /**
     * Field constructor.
     * @param Model $model
     * @param string $attribute
     */

    public function __construct(Model $model, string $attribute)
    {
        $this->type = self::TYPE_TEXT;
        parent::__construct($model, $attribute);
    }

    public function passwordField()
    {

        $this->type = self::TYPE_PASSWORD;
        return $this;
    }

    public function renderInput(): string
    {
        return sprintf('<input type="%s" name="%s" value="%s" class="%s" >', $this->type,
            $this->attribute,
            $this->model->{$this->attribute},
            $this->model->hasError($this->attribute) ? ' is-invalid' : '');
    }
}