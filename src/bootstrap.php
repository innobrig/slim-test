<?php

require __DIR__ . '/../vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

session_start ();
$settings = require __DIR__ . '/../config/settings.php';
$app = new \Slim\App ($settings);

require __DIR__ . '/../config/dependencies.php';
require __DIR__ . '/../config/middleware.php';
require __DIR__ . '/../config/routes.php';

$c = $app->getContainer ();

$c['errorHandler'] = function ($c) {
    return function (ServerRequestInterface $request, ResponseInterface $response, Exception $exception) use ($c) {
        $tracePrettyPrint = App\Util\CMSUtil::_varDump ($exception->getTrace(), 20);
        $error            = $exception->getMessage();

        return $c['response']->withStatus (500)
            ->withHeader ('Content-Type', 'text/html')
            ->write ("Oops! $error<br />$tracePrettyPrint");
    };
};


