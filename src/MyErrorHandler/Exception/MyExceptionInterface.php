<?php

/**
 *
 * @author Stefano Torresi <webdeveloper@stefanotorresi.it>
 */

namespace MyErrorHandler\Exception;

interface MyExceptionInterface
{
    public function getOutputFormat();
    public function setOutputFormat($output_format);
    public function getHttpCode();
    public function setHttpCode($http_code);
}

