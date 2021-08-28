<?php

namespace ryzen\framework;

class View
{
    public string $title = '';

    public function replacer($container, $viewContent = ''): array
    {

        $stringsToReplace = array(
            '{{content}}' => $viewContent,
            '{{_csrf()}}' => Application::$app->session->get('csrf_token_auto_gen'),
            '@inp_csrf()' => '<input type="hidden" name="_csrf" value="' . Application::$app->session->get('csrf_token_auto_gen') . '" >',
        );

        if ($container === 'key') {
            return array_keys($stringsToReplace);
        } else {
            return array_values($stringsToReplace);
        }
    }

    public function renderView($view, $params = [])
    {

        $viewContent = $this->renderOnlyView($view, $params);
        $layoutContent = $this->layoutContent();
        return str_replace($this->replacer('key'), $this->replacer('value', $viewContent), $layoutContent);

    }

    protected function layoutContent()
    {

        $layout = Application::$app->layout;

        if (Application::$app->controller) {

            $layout = Application::$app->controller->layout;
        }

        ob_start();
        include_once Application::$ROOT_DIR . "/views/layouts/" . $layout . ".ry.php";
        return ob_get_clean();
    }

    public function renderContent($viewContent)
    {

        $layoutContent = $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    protected function renderOnlyView($view, $params)
    {

        foreach ($params as $key => $value) {

            $$key = $value;
        }

        ob_start();
        include_once Application::$ROOT_DIR . "/views/$view.ry.php";
        return ob_get_clean();
    }

}