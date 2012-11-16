<?php

/**
 *
 * @author Stefano Torresi <webdeveloper@stefanotorresi.it>
 */

namespace MyErrorHandler\Exception;

use Exception;
use InvalidArgumentException;

class MyException extends Exception implements MyExceptionInterface
{
    
    const OUTPUT_HTML = 'html';
    const OUTPUT_JSON = 'json';
    
    /**
     *
     * @var int
     */
    protected $http_code;
    
    /**
     *
     * @var string
     */
    protected $output_format;

    /**
     * 
     * @param strnig    $message
     * @param int       $http_code
     * @param string    $output_format
     * @param int       $code
     * @param mixed     $previous
     */
    public function __construct($message, $http_code = 500, $output_format = self::OUTPUT_HTML)
    {
        $this->setHttpCode($http_code);
        $this->setOutputFormat($output_format);
        
        parent::__construct($message);
    }
    
    /**
     * 
     * @return int
     */
    public function getOutputFormat()
    {
        return $this->output_format;
    }
    
    /**
     * 
     * @param string $output_format
     * @return MyException
     * @throws InvalidArgumentException
     */
    public function setOutputFormat($output_format)
    {
        if ($output_format != self::OUTPUT_HTML && $output_format != self::OUTPUT_JSON) {
            throw new InvalidArgumentException();
        }
        
        $this->output_format = $output_format;
        
        return $this;
    }
    
    /**
     * 
     * @return int
     */
    public function getHttpCode()
    {
        return $this->http_code;
    }
    
    /**
     * 
     * @param int $http_code
     * @return MyException
     */
    public function setHttpCode($http_code)
    {
        $this->http_code = intval($http_code);
        
        return $this;
    }
    
    /**
     * 
     * @return MyException
     */
    public function setJsonOutput()
    {
        return $this->setOutputFormat(self::OUTPUT_JSON);;
    }
    
    /**
     * 
     * @return MyException
     */
    public function setHtmlOutput()
    {
        return $this->setOutputFormat(self::OUTPUT_HTML);;
    }
    
}

