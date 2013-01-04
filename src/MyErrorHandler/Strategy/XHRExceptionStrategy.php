<?php

/**
 *
 * @author Stefano Torresi <webdeveloper@stefanotorresi.it>
 */

namespace MyErrorHandler\Strategy;

use Zend\Http\Response as HttpResponse;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\View\Http\ExceptionStrategy;
use Zend\Stdlib\ResponseInterface;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use MyErrorHandler\Module as MyErrorHandler;
use MyErrorHandler\Exception\MyExceptionInterface;

class XHRExceptionStrategy extends ExceptionStrategy
{
    /**
     *
     * @param MvcEvent $e
     */
    public function prepareExceptionViewModel(MvcEvent $e)
    {
        // Do nothing if no error in the event
        $error      = $e->getError();
        if (empty($error)) {
            return;
        }

        // Do nothing if the result is a response object
        $result = $e->getResult();
        if ($result instanceof ResponseInterface) {
            return;
        }

        $request = $e->getRequest();
        if (!$request->isXmlHttpRequest()) {
            // Only handle XHR requests
            return;
        }

        switch ($error) {
            case Application::ERROR_CONTROLLER_NOT_FOUND:
            case Application::ERROR_CONTROLLER_INVALID:
            case Application::ERROR_ROUTER_NO_MATCH:
                // Specifically not handling these
                return;
        }

        $exception = $e->getParam('exception');

        if ($exception instanceof MyExceptionInterface) {
            $status_code = $exception->getHttpCode();
            $renderer = $exception->getRenderer();
        } else {
            $status_code = 500;
            $renderer = MyErrorHandler::RENDERER_HTML;
        }

        $services = $e->getApplication()->getServiceManager();
        $translator = $services->get('translator');
        $message = $translator->translate($exception->getMessage(), 'exceptions');

        switch ($renderer) {
            case MyErrorHandler::RENDERER_JSON :
                $model = new JsonModel();
                $error = array(
                    'code'  => $status_code,
                    'message'   => $message,
                );
                if ($this->displayExceptions()) {
                    $error['stack_trace'] = $exception->getTraceAsString();
                }
                $model->setVariable('error', $error);
                break;
            case MyErrorHandler::RENDERER_HTML :
            default :
                $model = new ViewModel();
                $variables = $e->getViewModel()->getVariables();
                $model->setVariables($variables);
                $model->setVariables(array(
                            'message'   => $message,
                            'error'     => true
                        ))
                        ->setTemplate('error/plain')
                        ->setTerminal(true);
                if ($this->displayExceptions()) {
                    $model->setVariable('stack_trace', $exception->getTraceAsString());
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

}
