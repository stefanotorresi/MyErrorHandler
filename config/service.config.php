<?php

namespace MyErrorHandler;

return array(
    'factories' => array(
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
