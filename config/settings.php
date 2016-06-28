<?php


$settings = [

    'settings' => [

        ///////////////////////////////////////////////////////
        // Database Connection Info - UPDATE for your server //
        ///////////////////////////////////////////////////////

        'database' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'prj_slimtest',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],

        ////////////////////////////
        // Main App configuration //
        ////////////////////////////

        'debug'                 => true,                                // disable for production
        'displayErrorDetails'   => true,                                // disable for production
        'siteName'              => 'Slim Framework Test',
        'siteSlogan'            => 'DealDash Code Sample',
        'templates' => [
            'home'                      => 'pages.twig',
            'page'                      => 'page.twig',
            'page_edit'                 => 'page_edit.twig'
        ],


        ////////////////////////////////////////////////////////////////////////////
        // Do NOT change anything below here unless you know what you're doing !! //
        ////////////////////////////////////////////////////////////////////////////

        'determineRouteBeforeAppMiddleware' => true, // need this to get route name before Middleware


        // Twig template engine setup
        'view'                => [
            'template_path'   => __DIR__ . '/../templates',
            'twig'            => [
                'cache'       => __DIR__ . '/../var/cache/twig',
                'debug'       => true,
                'auto_reload' => true,
            ],
        ],

        // monolog logging settings
        'logger' => [
            'name' => 'app',
            'path' => __DIR__ . '/../var/log/app.log',
        ]
    ]
];

return $settings;


