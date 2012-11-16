<?php

/**
 *
 * @author Stefano Torresi <webdeveloper@stefanotorresi.it>
 */

namespace MyErrorHandler\Exception;

use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\View\Http\ExceptionStrategy;
use Zend\Stdlib\ResponseInterface;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class MyExceptionStrategy extends ExceptionStrategy
{
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
        
        $exception = $e->getParam('exception');

        if ($exception instanceof MyExceptionInterface) {
            
            $services = $e->getApplication()->getServiceManager();
            $translator = $services->get('translator');            
            $message = $translator->translate($exception->getMessage(),'exceptions');
        
            $status_code = $exception->getHttpCode();
            $output_format = $exception->getOutputFormat();

            switch ($output_format) {
                case MyException::OUTPUT_JSON :
                    $model = new JsonModel;
                    $error = array(
                        'code'  => $status_code,
                        'message'   => $message,
                    );
                    if ($this->displayExceptions()) {
                        $error['stack_trace'] = $exception->getTraceAsString();
                    }
                    $model->setVariable('error', $error);
                    break;
                case MyException::OUTPUT_HTML :
                default :
                    $model = new ViewModel;
                    $variables = $e->getViewModel()->getVariables();
                    $model->setVariables($variables)
                            ->setVariable('message', $message)
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
            
        } else {
            parent::prepareExceptionViewModel($e);
        }
    }
}

