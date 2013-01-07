<?php

/**
 *
 * @author Stefano Torresi <webdeveloper@stefanotorresi.it>
 */

namespace MyErrorHandler\Exception;

interface ExceptionInterface
{
    public function getRenderer();
    public function setRenderer($output_format);
    public function getHttpCode();
    public function setHttpCode($http_code);
}
