<?php

namespace App\TwigExtension;

class FlashMessages extends \Slim\Views\TwigExtension
{
    private $flashMessages;

    public function __construct($flashMessages)
    {
        $this->flashMessages = $flashMessages;
    }

    public function getName()
    {
        return 'flashMessages';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('flashMessagesDisplay', array($this, 'flashMessagesDisplay')),
            new \Twig_SimpleFunction('flashMessagesHave', array($this, 'flashMessagesHave'))
        ];
    }

    public function flashMessagesDisplay ()
    {
        $this->flashMessages->setCloseBtn(null);
        $this->flashMessages->display();
    }

    public function flashMessagesHave ()
    {
        return $this->flashMessages->hasMessages();
    }
}
