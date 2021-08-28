<?php

namespace ryzen\framework\exception;

/**
 * @author razoo.choudhary@gmail.com
 * Class ForbiddenException
 * @package ryzen\framework\exception
 */
class ForbiddenException extends \Exception
{
    protected $code = 403;
    protected $message = 'Access to page denied';
}