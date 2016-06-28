<?php

$app->get  ('/',               'App\Controller\UserController:home')->setName ('home');
$app->get  ('/page_edit',      'App\Controller\UserController:pageEdit')->setName ('pageEdit');
$app->get  ('/page_edit/{id}', 'App\Controller\UserController:pageEdit')->setName ('pageEditExisting');
$app->get  ('/page/{id}',      'App\Controller\UserController:page')->setName ('page');

$app->post ('/page_edit',      'App\Controller\UserFormController:pageEdit')->setName ('pageEdit.post');


