<?php

namespace App\TwigExtension;

class Csrf extends \Slim\Views\TwigExtension
{
    private $csrfGuard;
    private $tokenPair;

    public function __construct($csrfGuard)
    {
        $this->csrfGuard = $csrfGuard;
        $this->tokenPair = $csrfGuard->generateToken();
    }

    public function getName()
    {
        return 'csrf';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('csrfName', array($this, 'csrfName')),
            new \Twig_SimpleFunction('csrfValue', array($this, 'csrfValue')),
            new \Twig_SimpleFunction('csrfTokenName', array($this, 'csrfTokenName')),
            new \Twig_SimpleFunction('csrfTokenValue', array($this, 'csrfTokenValue'))
        ];
    }

    public function csrfName ()
    {
        return $this->tokenPair[$this->csrfTokenName()];
    }

    public function csrfValue ()
    {
        return $this->tokenPair[$this->csrfTokenValue()];
    }

    public function csrfTokenName ()
    {
        return $this->csrfGuard->getTokenNameKey();
    }

    public function csrfTokenValue ()
    {
        return $this->csrfGuard->getTokenValueKey();
    }
}
