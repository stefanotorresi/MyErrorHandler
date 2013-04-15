<?php

namespace MyErrorHandler;

return array(
    'factories' => array(
        'Logger' => function($services){
            $globalConfig = $services->get('config');
            $config = $globalConfig[__NAMESPACE__];

            $logger = new \Zend\Log\Logger;
            $writer = new \Zend\Log\Writer\Stream($config['log_file']);
            $logger->addWriter($writer);

            return $logger;
        },
        'MyErrorHandler\Strategy\ExceptionStrategy' => function ($services) {
            $config   = $services->get('config');

            $listener = new Strategy\ExceptionStrategy();
            if (isset($config['view_manager']['display_exceptions'])) {
                $listener->setDisplayExceptions($config['view_manager']['display_exceptions']);
            }

            return $listener;
        },
        'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
    ),
    'invokables' => array(
        'MyErrorHandler\Strategy\NotFoundStrategy' => 'MyErrorHandler\Strategy\NotFoundStrategy',
    )
);
