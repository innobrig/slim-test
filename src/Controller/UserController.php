<?php
namespace App\Controller;

use App\Util\TemplateResolver;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use InnoBrig\FlexInput\Input;


final class UserController extends BaseController
{
    public function home (Request $request, Response $response, $args)
    {
        $objs = \App\Model\Page::orderBy('title', 'asc')->get();

        return $this->view->render($response, TemplateResolver::getTemplate($this->settings, 'home'), [
            'pages' => $objs,
            'pageName' => _('Content Pages')
        ]);

    }


    public function pageEdit (Request $request, Response $response, $args)
    {
        if (isset($args['id']) && $args['id']) {
            $obj = \App\Model\Page::find ((int)$args['id']);
            if (!$obj) {
                $this->flashMessages->error (_ ("Unable to retrieve requested object."));
                return $response->withRedirect (((string)($request->getUri()->withPath($this->router->pathFor('home')))));
            }
        } else {
            $obj = new \App\Model\Page();
        }

        return $this->view->render($response, TemplateResolver::getTemplate($this->settings, 'page_edit'), [
            'page' => $obj,
            'pageName' => _('Content Editor')
        ]);
    }


    public function page (Request $request, Response $response, $args)
    {
        $pid = $args['id'];
        if (!$pid) {
            $this->flashMessages->error (_("Invalid ID Received."));
            return $response->withRedirect ((string)($request->getUri()->withPath($this->router->pathFor('home'))));
        }

        $page = null;
        if (is_numeric($pid)) {
            $page = \App\Model\Page::find($pid);
        } else {
            $page = \App\Model\Page::selectOneByField ($pid, 'slug');
        }

        if (!$page) {
            $this->flashMessages->error (_("Unable to retrieve requested object."));
            return $response->withRedirect ((string)($request->getUri()->withPath($this->router->pathFor('home'))));
        }

        return $this->view->render ($response, TemplateResolver::getTemplate($this->settings, 'page'), [
            'page'    => $page,
            'pageName' => _('Page Display')
        ]);
    }
}

