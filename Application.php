<?php

namespace ryzen\framework;

use ryzen\framework\db\Database;
use ryzen\framework\db\DbModel;
use ryzen\framework\func\BaseFunctions;
use Exception;

/**
 * @author razoo.choudhary@gmail.com
 * Class Application
 * @package ryzen\framework
 */
class Application
{

    public static string $ROOT_DIR;

    public string $layout = 'app';
    public string $userClass;
    public static Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public Database $db;
    public ?DbModel $user;
    public BaseFunctions $functions;
    public View $view;


    public static Application $app;
    public ?Controller $controller = null;

    /**
     * @throws Exception
     */
    public function __construct($rootPath, array $config)
    {
        $this->userClass = $config['userClass'];
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;

        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        self::$router = new Router($this->request, $this->response);
        $this->view = new View();
        $this->db = new Database($config['db']);
        $this->session->set('csrf_token_auto_gen', bin2hex(random_bytes(32)));

        $primaryValue = $this->session->get('user');
        if ($primaryValue) {

            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);

        } else {

            $this->user = null;
        }
    }

    public function run()
    {
        try {

            echo self::$router->resolve();

        } catch (Exception $e) {

            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('_error', ['exception' => $e]);
        }
    }

    /**
     * @return Controller
     */

    public function getController(): Controller
    {

        return $this->controller;
    }

    /**
     * @param Controller $controller
     */

    public function setController(Controller $controller): void
    {

        $this->controller = $controller;
    }

    public function login(DbModel $user): bool
    {

        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);

        return true;
    }

    public function logout()
    {

        $this->user = null;
        $this->session->remove('user');
    }

    public static function isGuest(): bool
    {

        return !self::$app->user;
    }
}