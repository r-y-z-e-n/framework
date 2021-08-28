<?php

namespace ryzen\framework;

use ryzen\framework\exception\NotFoundException;

/**
 * @author razoo.choudhary@gmail.com
 * Class Router
 * @package ryzen\framework
 */
class Router
{

    public static Request $request;
    public static Response $response;

    protected static array $routes = [];

    /**
     * Router constructor.
     * @param Request $request
     * @param Response $response ;
     */

    public function __construct(Request $request, Response $response)
    {
        self::$request = $request;
        self::$response = $response;
    }

    public static function get($path, $callback)
    {

        self::$routes['get'][$path] = $callback;
    }

    public static function post($path, $callback)
    {

        self::$routes['post'][$path] = $callback;
    }

    /**
     * @throws NotFoundException
     */
    public static function resolve()
    {

        $path = self::$request->getPath();
        $method = self::$request->method();
        $callback = self::$routes[$method][$path] ?? false;

        if ($callback === false) {

            self::$response->setStatusCode('404');

            throw new NotFoundException();
        }

        if (is_string($callback)) {

            return Application::$app->view->renderView($callback);
        }

        if (is_array($callback)) {

            /** @var Controller $controller */

            $controller = new $callback[0]();
            Application::$app->controller = $controller;

            $controller->action = $callback[1];
            $callback[0] = $controller;

            foreach ($controller->getMiddlewares() as $middleware) {

                $middleware->execute();
            }
        }
        return call_user_func($callback, self::$request, self::$response);
    }
}