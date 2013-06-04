<?php

/**
 *
 * @author Stefano Torresi <webdeveloper@stefanotorresi.it>
 */

namespace MyErrorHandler\Exception;

class UnauthorizedException extends Exception
{
    public function __construct($message = 'Unauthorized', $http_code = 401)
    {
        parent::__construct($message, $http_code);
    }
}
