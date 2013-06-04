<?php

/**
 *
 * @author Stefano Torresi <webdeveloper@stefanotorresi.it>
 */

namespace MyErrorHandler\Exception;

class ServiceUnavailableException extends Exception
{
    public function __construct($message = 'Service Unavailable', $http_code = 503)
    {
        parent::__construct($message, $http_code);
    }
}
