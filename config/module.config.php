<?php

namespace MyErrorHandler;

return array(
    __NAMESPACE__ => array(
        'log_exceptions' => false,
        'log_file' => './data/logs/%s-error.log',
    ),
    'view_manager' => array(
        'not_found_template'       => 'error/404',
        'template_map' => array(
            'error'         => __DIR__ . '/../view/error/default.phtml',
            'error/404'     => __DIR__ . '/../view/error/404.phtml',
            'error/plain'   => __DIR__ . '/../view/error/plain.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type'          => 'gettext',
                'base_dir'      => __DIR__ . '/../language',
                'pattern'       => '%s.mo',
                'text_domain'   => 'exceptions'
            ),
        ),
    ),

);
