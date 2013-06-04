<?php

/**
 *
 * @author Stefano Torresi <webdeveloper@stefanotorresi.it>
 */

namespace MyErrorHandler\Strategy;

use MyErrorHandler\Module as MyErrorHandler;
use MyErrorHandler\Exception\Exception;
use MyErrorHandler\Exception\ExceptionInterface;
use Zend\Http\Response as HttpResponse;
use Zend\I18n\Translator\Translator;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\View\Http\ExceptionStrategy as ZendExceptionStrategy;
use Zend\Stdlib\ResponseInterface;
use Zend\View\Model;

class ExceptionStrategy extends ZendExceptionStrategy implements StrategyInterface
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     *
     * @param MvcEvent $e
     */
    public function prepareExceptionViewModel(MvcEvent $e)
    {
        // Do nothing if no error in the event
        $vars      = $e->getError();
        if (empty($vars)) {
            return;
        }

        // Do nothing if the result is a response object
        $result = $e->getResult();
        if ($result instanceof ResponseInterface) {
            return;
        }

        switch ($vars) {
            case Application::ERROR_CONTROLLER_NOT_FOUND:
            case Application::ERROR_CONTROLLER_INVALID:
            case Application::ERROR_ROUTER_NO_MATCH:
                // Specifically not handling these
                return;
        }

        $exception = $e->getParam('exception');

        if ($exception instanceof ExceptionInterface) {
            $status_code = $exception->getHttpCode();
            $renderer = $exception->getRenderer();
            $message = $exception->getMessage();
        } else {
            $message = Exception::DEFAULT_MESSAGE;
            $status_code = 500;
        }

        $request = $e->getRequest();

        if (!isset($renderer)) {
            $accept = $request->getHeader('Accept');

            if (0 === strpos($accept->getFieldValue(), 'application/json')) {
                $renderer = MyErrorHandler::RENDERER_JSON;
            } else {
                $renderer = MyErrorHandler::RENDERER_HTML;
            }
        }

        if ($renderer == MyErrorHandler::RENDERER_HTML && !$request->isXmlHttpRequest()) {
            // Only handle XHR requests if output is HTML
            return;
        }

        switch ($renderer) {
            case MyErrorHandler::RENDERER_JSON :
                $model = new Model\JsonModel();

                if ($this->translator) {
                    $message = $this->translator->translate($message, MyErrorHandler::TEXT_DOMAIN);
                }

                $vars = array(
                    'code'  => $status_code,
                    'message'   => $message,
                );

                $model->setVariable('error', $vars);

                break;

            case MyErrorHandler::RENDERER_HTML :
            default :
                $model = new Model\ViewModel();

                $model->setVariables(array(
                            'message'   => $message,
                            'error'     => true
                        ))
                        ->setTemplate('error/plain')
                        ->setTerminal(true);

                if ($this->displayExceptions()) {
                    $model->setVariable('exception', $exception);
                }
        }

        $response = $e->getResponse();
        if (!$response) {
            $response = new HttpResponse();
            $e->setResponse($response);
        }
        $response->setStatusCode($status_code);

        $e->setResult($model);
    }

    /**
     * @param Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }
}
