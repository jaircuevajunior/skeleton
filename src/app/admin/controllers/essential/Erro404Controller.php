<?php

namespace src\app\admin\controllers\essential;

use Din\Http\Header;

/**
 *
 * @package app.controllers
 */
class Erro404Controller
{

  public function get_display ()
  {
    Header::set404();
    $view = new \Din\Mvc\View\View();
    $view->addFile('src/app/admin/views/essential/404.phtml');
    $view->display_html();
  }

}