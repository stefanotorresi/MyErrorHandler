<?php

namespace MyErrorHandler;

return array(
    'factories' => array(
        'Logger' => function($services){
            $globalConfig = $services->get('config');
            $config = $globalConfig[__NAMESPACE__];

            $logFile = sprintf($config['log_file'], date('Y-m-d'));

            $logger = new \Zend\Log\Logger;
            $writer = new \Zend\Log\Writer\Stream($logFile);
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
    ),
    'invokables' => array(
        'MyErrorHandler\Strategy\NotFoundStrategy' => 'MyErrorHandler\Strategy\NotFoundStrategy',
    ),
    'initializers' => array(
        'TranslatorAwareStrategyInitializer' => function($instance, $services) {
            if ( ! $instance instanceof Strategy\StrategyInterface ) {
                return;
            }

            if (!$services->has('translator')) {
                return;
            }

            $translator = $services->get('translator');

            $instance->setTranslator($translator);
        },
    ),
);
