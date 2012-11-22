<?php

/**
 *
 * @author Stefano Torresi <webdeveloper@stefanotorresi.it>
 */

namespace MyErrorHandler\Strategy;

use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\View\Http\RouteNotFoundStrategy;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use MyErrorHandler\Module as MyErrorHandler;

class XHRNotFoundStrategy extends RouteNotFoundStrategy
{
    
    /**
     * 
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'prepareNotFoundViewModel'), -99);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'prepareNotFoundViewModel'), -99);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'setViewModelErrorFlag'), -100);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'setViewModelErrorFlag'), -100);
    }
    
    /**
     * we may want to inform the view layer that we just have an error
     * 
     * @param MvcEvent $e
     * @return void
     */
    public function setViewModelErrorFlag(MvcEvent $e)
    {
        $error = $e->getError();
        $response = $e->getResponse();
        
        if ( empty($error) && $response->getStatusCode() != 404) {
            return;
        }
        
        if (!isset($e->getViewModel()->error)) {
            $e->getViewModel()->error = true;
        }
    }
    
    /**
     * Create and return a 404 view model
     *
     * @param  MvcEvent $e
     * @return void
     */
    public function prepareNotFoundViewModel(MvcEvent $e)
    {
        
        // Do nothing if the result is a response object
        $result = $e->getResult();
        if ($result instanceof ResponseInterface) {
            return;
        }
        
        $response = $e->getResponse();
        if ($response->getStatusCode() != 404) {
            // Only handle 404 responses
            return;
        }
        
        $request = $e->getRequest();
        if (!$request->isXmlHttpRequest()) {
            // Only handle XHR requests
            return;
        }
            
        $services = $e->getApplication()->getServiceManager();
        $translator = $services->get('translator');            
        $message = $translator->translate('Page not found','exceptions');

        $accept = $request->getHeader('Accept');
        
        if (0 === strpos($accept->getFieldValue(), 'application/json')) {
            $renderer = MyErrorHandler::RENDERER_JSON;
        } else {
            $renderer = MyErrorHandler::RENDERER_HTML;
        }
        
        switch ($renderer) {
            case MyErrorHandler::RENDERER_JSON :
                $model = new JsonModel();
                $model->setVariable('error', array(
                    'code'      => $response->getStatusCode(),
                    'message'   => $message,
                ));
                break;
            case MyErrorHandler::RENDERER_HTML :
            default :
                $model = new ViewModel;
                $variables = $e->getViewModel()->getVariables();
                $model->setVariables($variables);
                $model->setVariables(array(
                            'message'   => $message,
                            'error'     => true
                        ))
                        ->setTemplate('error/plain')
                        ->setTerminal(true);
        }
        
        $e->setViewModel($model);
        
    }
}

