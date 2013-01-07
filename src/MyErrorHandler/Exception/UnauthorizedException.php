<?php

/**
 *
 * @author Stefano Torresi <webdeveloper@stefanotorresi.it>
 */

namespace MyErrorHandler\Exception;

use MyErrorHandler\Module as MyErrorHandler;

class UnauthorizedException extends Exception
{
    public function __construct($message = 'Unauthorized',
            $http_code = 401, $output_format = MyErrorHandler::RENDERER_HTML)
    {
        parent::__construct($message, $http_code, $output_format);
    }
}
