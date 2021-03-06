<?php

namespace Admin\Controllers;

use Din\Essential\Models\YouTubeModel as model;
use Din\Http\Get;
use Din\Http\Header;
use Din\Session\Session;

/**
 *
 * @package app.controllers
 */
class YoutubeController extends BaseControllerAdm
{

  protected $_model;

  public function get ()
  {
    $this->_model = new model();
    $code = Get::text('code');
    $this->_model->auth($code);

    $session = new Session('adm_session');
    $session->set('saved_msg', 'Atenticado no Youtube com sucesso');

    Header::redirect($session->get('referer'));

  }

}
