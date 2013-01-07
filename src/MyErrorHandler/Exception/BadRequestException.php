<?php

/**
 *
 * @author Stefano Torresi <webdeveloper@stefanotorresi.it>
 */

namespace MyErrorHandler\Exception;

use MyErrorHandler\Module as MyErrorHandler;

class BadRequestException extends Exception
{
    public function __construct($message = 'Bad Request',
            $http_code = 400, $output_format = MyErrorHandler::RENDERER_HTML)
    {
        parent::__construct($message, $http_code, $output_format);
    }
}
