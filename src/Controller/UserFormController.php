<?php
namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Model\Page;
use App\Util\CMSUtil;
use InnoBrig\FlexInput\Input;


final class UserFormController extends BaseController
{
    public function pageEdit (Request $request, Response $response, $args)
    {
        $data = Input::fromPost ('page', null, FILTER_UNSAFE_RAW);

        if (!$data) {
            $this->flashMessages->error ("Invalid page data received.");
            return $response->withRedirect (((string)($request->getUri()->withPath($this->router->pathFor('home')))));
        }

        $page = null;
        if ($data['id']) {
            $pid = (int)$data['id'];
            $page = Page::find ($pid);

            if (!$page) {
                $this->flashMessages->error (_ ("Unable to retrieve requested object."));
                return $response->withRedirect (((string)($request->getUri()->withPath($this->router->pathFor('home')))));
            }
        } else {
            $page = new Page();
        }

        foreach ($data as $k=>$v) {
            if ($v) {
                $page->$k = $v;
            }
        }
        if (!$data['slug']) {
            $page->slug = CMSUtil::formatPermalink ($data['title']);
        }
        $page->save ();

        $this->flashMessages->success (_ ("Your page has been successfully saved."));
        return $response->withRedirect (((string)($request->getUri()->withPath($this->router->pathFor('home')))));
    }
}

