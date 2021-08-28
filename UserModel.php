<?php

namespace ryzen\framework;

use ryzen\framework\db\DbModel;

/**
 * @author razoo.choudhary@gmail.com
 * Class UserModel
 * @package ryzen\framework
 */
abstract class UserModel extends DbModel
{
    abstract public function getDisplayName(): string;
}