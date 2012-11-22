<?php

/**
 *
 * @author Stefano Torresi <webdeveloper@stefanotorresi.it>
 */

namespace MyErrorHandler\Exception;

use Exception;
use InvalidArgumentException;
use MyErrorHandler\Module as MyErrorHandler;

class MyException extends Exception implements MyExceptionInterface
{
    
    /**
     *
     * @var int
     */
    protected $http_code;
    
    /**
     *
     * @var string
     */
    protected $renderer;

    /**
     * 
     * @param strnig    $message
     * @param int       $http_code
     * @param string    $renderer
     * @param int       $code
     * @param mixed     $previous
     */
    public function __construct($message = '', $http_code = 500, $renderer = MyErrorHandler::RENDERER_HTML)
    {
        $this->setHttpCode($http_code);
        $this->setRenderer($renderer);
        
        parent::__construct($message);
    }
    
    /**
     * 
     * @return int
     */
    public function getRenderer()
    {
        return $this->renderer;
    }
    
    /**
     * 
     * @param string $renderer
     * @return MyException
     * @throws InvalidArgumentException
     */
    public function setRenderer($renderer)
    {
        if ($renderer != MyErrorHandler::RENDERER_HTML && $renderer != MyErrorHandler::RENDERER_JSON) {
            throw new InvalidArgumentException();
        }
        
        $this->renderer = $renderer;
        
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
        $this->http_code = (int) $http_code;
        
        return $this;
    }
    
    /**
     * 
     * @return MyException
     */
    public function setJsonRenderer()
    {
        return $this->setRenderer(MyErrorHandler::RENDERER_JSON);
    }
    
    /**
     * 
     * @return MyException
     */
    public function setHtmlRenderer()
    {
        return $this->setRenderer(MyErrorHandler::RENDERER_HTML);;
    }
    
}

