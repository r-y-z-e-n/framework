<?php

namespace ryzen\framework\middlewares;

/**
 * @author razoo.choudhary@gmail.com
 * Class BaseMiddleware
 * @package ryzen\framework\middlewares
 */
abstract class BaseMiddleware
{
    abstract public function execute();
}