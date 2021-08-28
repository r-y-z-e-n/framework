<?php

namespace ryzen\framework;

use ryzen\framework\middlewares\BaseMiddleware;

/**
 * @author razoo.choudhary@gmail.com
 * Class Controller
 * @package ryzen\framework
 */
class Controller
{
    public string $layout = 'app';
    public string $action = '';


    /**
     * @var BaseMiddleware[]
     */

    protected array $middlewares = [];

    public function setLayout($layout)
    {

        $this->layout = $layout;
    }

    public function ry()
    {

        return Application::$app;
    }

    public function render($view, $params = [])
    {

        return $this->ry()->view->renderView($view, $params);
    }

    public function functions(): func\BaseFunctions
    {

        return $this->ry()->functions;
    }

    public function redirect($url)
    {

        return $this->ry()->response->redirect($url);
    }

    public function registerMiddleware(BaseMiddleware $middleware)
    {

        $this->middlewares[] = $middleware;
    }

    /**
     * @return BaseMiddleware[]
     */

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }


}