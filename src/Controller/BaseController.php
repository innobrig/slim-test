<?php
namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;


//use \Slim\Http\Response as Responses;


/**
 *
 */
class BaseController
{
    protected $container;
    protected $flashMessages;
    protected $router;
    protected $settings;
    protected $view;


    public function __construct (\Interop\Container\ContainerInterface $container)
    {
        $this->container     = $container;
        $this->flashMessages = $container->get ('flash');
        $this->router        = $container->get ('router');
        $this->settings      = $container->get ('settings');
        $this->view          = $container->get ('view');
    }


    protected function setGlobalParameters (Request $request, $type)
    {
        $GLOBALS['requestType'] = $type;
        $GLOBALS['baseUrl']     = $request->getUri()->getBaseUrl();
        $GLOBALS['basePath']    = $request->getUri()->getBasePath();
    }
}

