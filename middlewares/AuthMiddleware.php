<?php

namespace ryzen\framework\middlewares;

use ryzen\framework\Application;
use ryzen\framework\exception\ForbiddenException;

/**
 * @author razoo.choudhary@gmail.com
 * Class AuthMiddleware
 * @package ryzen\framework\middlewares
 */
class AuthMiddleware extends BaseMiddleware
{

    public array $actions = [];

    /**
     * AuthMiddleware constructor.
     * @param array $actions
     */

    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    /**
     * @throws ForbiddenException
     */
    public function execute()
    {
        if (Application::isGuest()) {

            if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)) {

                throw new ForbiddenException();
            }
        }
    }
}