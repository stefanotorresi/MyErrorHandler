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
        $sharedEvents = $events->getSharedManager();
        $services  = $e->getApplication()->getServiceManager();
        $globalConfig = $services->get('config');
        $config = $globalConfig[__NAMESPACE__];

        // Attach the new strategy
        $services->get('MyErrorHandler\Strategy\ExceptionStrategy')->attach($events);
        $services->get('MyErrorHandler\Strategy\NotFoundStrategy')->attach($events);

        if ($config['log_exceptions']) {
            // attach logger
            $sharedEvents->attach('Zend\Mvc\Application', MvcEvent::EVENT_DISPATCH_ERROR,
                function($e) use ($services) {
                   if ($e->getParam('exception')){
                       $services->get('Logger')->crit($e->getParam('exception'));
                   }
                }
            );
        }
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
