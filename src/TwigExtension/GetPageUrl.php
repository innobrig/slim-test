<?php

namespace App\TwigExtension;

class GetPageUrl extends \Slim\Views\TwigExtension
{
    private $container;
    private $router;
    private $settings;

    public function __construct($container)
    {
        $this->container = $container;
        $this->router    = $container->get('router');
        $this->settings  = $container->get('settings');
    }

    public function getName()
    {
        return 'getPageUrl';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('getPageUrl', array($this, 'getPageUrl'))
        ];
    }

    public function getPageUrl ($name, $lang=null)
    {
        $defaultLang = isset($this->settings['defaultLanguage']) ? $this->settings['defaultLanguage'] : 'en';
        if (!$lang) {
            $lang = \App\Helper\Session::get ('lang', $defaultLang);
        }

        $page = \App\Model\Page::getPageByFields (['name'=>$name, 'lang'=>$lang]);
        if (!$page) {
            if ($lang != $defaultLang && $this->settings['defaultLocaleContentFallback']) {
                $page = \App\Model\Page::getPageByFields (['name'=>$name, 'lang'=>$defaultLang]);
            }
            if (!$page) {
                return '#PageNotFound';
            }
        }

        if ($page->slug) {
            return $this->router->pathFor('page', ['id'=>$page->slug]);
        }

        return $this->router->pathFor('page', ['id'=>$page->id]);
    }
}
