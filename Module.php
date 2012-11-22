<?php

/**
 *
 * @author Stefano Torresi <webdeveloper@stefanotorresi.it>
 */

namespace MyErrorHandler;

use Zend\Mvc\MvcEvent;

class Module
{    
    
    const RENDERER_HTML = 'html';
    const RENDERER_JSON = 'json';
    
    public function onBootstrap(MvcEvent $e)
    {
        $events = $e->getApplication()->getEventManager();
        $services  = $e->getApplication()->getServiceManager();

        // Attach the new strategy
        $services->get('MyErrorHandler\Strategy\XHRExceptionStrategy')->attach($events);
        $services->get('MyErrorHandler\Strategy\XHRNotFoundStrategy')->attach($events);
    }

    public function getAutoloaderConfig()
    {
        return include __DIR__ . '/config/autoloader.config.php';
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getServiceConfig()
    {
        return include __DIR__ . '/config/service.config.php';
    }
}
