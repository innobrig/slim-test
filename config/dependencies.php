<?php


$container = $app->getContainer ();


// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------


// database
$capsule = new Illuminate\Database\Capsule\Manager ();
$capsule->addConnection ($container->get('settings')['database'], 'default');
$capsule->setAsGlobal ();
$capsule->bootEloquent ();


// Flash messages
$container['flash'] = function ($c) {
    $flashMessageHandler = new \Plasticbrain\FlashMessages\FlashMessages();
    return new $flashMessageHandler;
};


// CSRF Guard
$container['csrf'] = function ($c) {
    $guard = new \Slim\Csrf\Guard();
    $guard->setFailureCallable (function ($request, $response, $next) {
        $request = $request->withAttribute ("csrf_status", false);
        return $next ($request, $response);
    });

    return $guard;
};


// Twig template engine
$container['view'] = function ($c) {
    $settings = $c->get ('settings');
    $view = new \Slim\Views\Twig ($settings['view']['template_path'], $settings['view']['twig']);

    // Add extensions
    $view->addExtension (new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri ()));
    $view->addExtension (new Twig_Extension_Debug());
    $view->addExtension (new Twig_Extensions_Extension_Array());
    $view->addExtension (new Twig_Extensions_Extension_Date());
    $view->addExtension (new Twig_Extensions_Extension_I18n());
    $view->addExtension (new Twig_Extensions_Extension_Intl());
    $view->addExtension (new Twig_Extensions_Extension_Text());
    $view->addExtension (new App\TwigExtension\Csrf($c['csrf']));
    $view->addExtension (new App\TwigExtension\FlashMessages($c['flash']));
    $view->addExtension (new App\TwigExtension\GetPageUrl($c));
    $view['settings']      = $settings;
    $view['flashMessages'] = $c->get ('flash');

    return $view;
};
$GLOBALS['view'] = $container['view'];  // hack for GetPageUrl()


$container['notFoundHandler'] = function ($c) {
    exit ("Route not found. Sorry, this is just a short test/demo and doesn't implement proper error handling.");
};


// -----------------------------------------------------------------------------
// Controller factories
// -----------------------------------------------------------------------------

$container['App\Controller\UserController'] = function ($c) {
    return new App\Controller\UserController($c);
};

$container['App\Controller\UserFormController'] = function ($c) {
    return new App\Controller\UserFormController($c);
};



// -----------------------------------------------------------------------------
// i18n
// -----------------------------------------------------------------------------

$locale = 'en_EN.UTF-8'; // locality should be determined here
if (defined('LC_MESSAGES')) {
    /**
     * Set the specific locale information we want to change. We could also
     * use LC_MESSAGES, but because we may want to use other locale information
     * for things like number separators, currency signs, we'll say all locale
     * information should be updated.
     *
     * The second parameter is the locale and encoding you want to use. You
     * will need this locale and encoding installed on your system before you
     * can use it.
     *
     * On an Ubuntu/Debian system, adding a new locale is simple.
     *
     * $ sudo apt-get install language-pack-de # German
     * $ sudo apt-get install language-pack-ja # Japanese
     *
     * You can also generate a specific locale using locale-gen.
     *
     * $ sudo locale-gen en_US.UTF-8
     * $ sudo locale-gen de_DE.UTF-8
     */
    setlocale(LC_MESSAGES, $locale); // Linux
} else {
    putenv("LC_ALL={$locale}"); // Windows
}
if (!function_exists('gettext')) {
    echo "You do not have the gettext library installed with PHP.";
    exit(1);
}
/**
 * Because the .po file is named messages.po, the text domain must be named
 * that as well. The second parameter is the base directory to start
 * searching in.
 */
//bindtextdomain('messages', '../locale');
/**
 * Tell the application to use this text domain, or messages.mo.
 */
//textdomain('messages');


// -----------------------------------------------------------------------------
// Data stuff we need to access in various places -> review for Hackyness
// -----------------------------------------------------------------------------


// FIXME: this is hacky, there's got to be a better way
$GLOBALS['flashMessages'] = $container['flash'];

