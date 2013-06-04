<?php

/**
 *
 * @author Stefano Torresi <webdeveloper@stefanotorresi.it>
 */

namespace MyErrorHandler\Exception;

class NotFoundException extends Exception
{
    public function __construct($message = 'Not Found', $http_code = 404)
    {
        parent::__construct($message, $http_code);
    }
}
