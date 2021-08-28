<?php

namespace ryzen\framework\exception;

/**
 * @author razoo.choudhary@gmail.com
 * Class NotFoundException
 * @package ryzen\framework\exception
 */
class NotFoundException extends \Exception
{
    protected $message = "Page Not Found";
    protected $code = 404;
}