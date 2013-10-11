<?php

/**
 *
 * @author Stefano Torresi <webdeveloper@stefanotorresi.it>
 */

namespace MyErrorHandler;

use Zend\ModuleManager\Feature;
use Zend\Mvc\MvcEvent;

class Module implements
    Feature\AutoloaderProviderInterface,
    Feature\ConfigProviderInterface,
    Feature\ServiceProviderInterface
{
    const TEXT_DOMAIN = __NAMESPACE__;
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
                    $exception = $e->getParam('exception');
                    while ($exception) {
                        $services->get('Logger')->crit($exception);
                        $exception = $exception->getPrevious();
                    }
                }
            );
        }
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/../../autoload_classmap.php'
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__  => __DIR__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return include __DIR__ . '/../../config/service.config.php';
    }
}
