<?php

/**
 *
 * @author Stefano Torresi <webdeveloper@stefanotorresi.it>
 */

namespace MyErrorHandler\Exception;

use Exception as SplException;
use InvalidArgumentException;
use MyErrorHandler\Module as MyErrorHandler;

class Exception extends SplException implements ExceptionInterface
{
    /**
     *
     * @var int
     */
    protected $httpCode;

    /**
     *
     * @var string
     */
    protected $renderer;

    /**
     *
     * @param string $message
     * @param int    $httpCode
     * @param string $renderer
     * @param int    $code
     * @param mixed  $previous
     */
    public function __construct($message = '', $httpCode = 500, $renderer = null)
    {
        $this->setHttpCode($httpCode);

        if ($renderer) {
            $this->setRenderer($renderer);
        }

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
     * @param  string                   $renderer
     * @return Exception
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
        return $this->httpCode;
    }

    /**
     *
     * @param  int         $httpCode
     * @return Exception
     */
    public function setHttpCode($httpCode)
    {
        $this->httpCode = (int) $httpCode;

        return $this;
    }

    /**
     *
     * @return Exception
     */
    public function setJsonRenderer()
    {
        return $this->setRenderer(MyErrorHandler::RENDERER_JSON);
    }

    /**
     *
     * @return Exception
     */
    public function setHtmlRenderer()
    {
        return $this->setRenderer(MyErrorHandler::RENDERER_HTML);;
    }

}
