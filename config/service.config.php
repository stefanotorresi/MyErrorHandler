<?php

namespace MyErrorHandler;

return array(
    'factories' => array(
        'MyExceptionStrategy' => function ($services) {
            $config   = $services->get('config');

            $listener = new Exception\MyExceptionStrategy();
            if (isset($config['view_manager']['display_exceptions'])) {
                $listener->setDisplayExceptions($config['view_manager']['display_exceptions']);
            }

            return $listener;
        },
        'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
    ),
);
