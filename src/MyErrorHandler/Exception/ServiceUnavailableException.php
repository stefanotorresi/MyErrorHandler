<?php

/**
 *
 * @author Stefano Torresi <webdeveloper@stefanotorresi.it>
 */

namespace MyErrorHandler\Exception;

use MyErrorHandler\Module as MyErrorHandler;

class ServiceUnavailableException extends MyException
{
    public function __construct($message = 'Service Unavailable',
            $http_code = 503, $output_format = MyErrorHandler::RENDERER_HTML)
    {
        parent::__construct($message, $http_code, $output_format);
    }
}
