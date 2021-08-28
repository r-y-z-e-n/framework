<?php

namespace ryzen\framework;

/**
 * @author razoo.choudhary@gmail.com
 * Class Response
 * @package ryzen\framework
 */

class Response
{
    public function setStatusCode(int $code){

        http_response_code($code);
    }

    public function redirect(string $string){

        header('Location:'.$string);
    }
    public function json($jsonArray){

        return json_encode($jsonArray);
    }
}