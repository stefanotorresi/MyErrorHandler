<?php

namespace MyErrorHandler;

return array(
    'factories' => array(
        'MyErrorHandler\Strategy\XHRExceptionStrategy' => function ($services) {
            $config   = $services->get('config');

            $listener = new Strategy\XHRExceptionStrategy();
            if (isset($config['view_manager']['display_exceptions'])) {
                $listener->setDisplayExceptions($config['view_manager']['display_exceptions']);
            }

            return $listener;
        },
        'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
    ),
    'invokables' => array(
        'MyErrorHandler\Strategy\XHRNotFoundStrategy' => 'MyErrorHandler\Strategy\XHRNotFoundStrategy',
    )
);
