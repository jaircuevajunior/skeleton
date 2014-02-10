<?php

namespace src\app\admin\controllers;

use src\app\admin\models\PollModel as model;
use Din\Http\Get;
use Din\Http\Post;
use Din\ViewHelpers\JsonViewHelper;
use Exception;
use src\app\admin\controllers\essential\BaseControllerAdm;
use src\app\admin\viewhelpers\PollViewHelper as vh;

/**
 *
 * @package app.controllers
 */
class PollController extends BaseControllerAdm
{

  protected $_model;

  public function __construct ()
  {
    parent::__construct();

    $this->_model = new model;
    $this->setEntityData();
    $this->require_permission();
  }

  public function get_list ()
  {

    $arrFilters = array(
        'question' => Get::text('question'),
        'pag' => Get::text('pag'),
    );

    $this->_data['list'] = vh::formatResult($this->_model->getList($arrFilters));
    $this->_data['search'] = vh::formatFilters($arrFilters);

    $this->setErrorSessionData();

    $this->setListTemplate('poll_list.phtml');
  }

  public function get_save ( $id = null )
  {
    $this->_model->setId($id);
    $excluded_fields = array(
        'id_poll',
        'uri'
    );
    $row = $id ? $this->_model->getById() : $this->getPrevious($excluded_fields);

    $this->_data['table'] = vh::formatRow($row);

    $this->setSaveTemplate('poll_save.phtml');
  }

  public function post_save ( $id = null )
  {
    try {
      $this->_model->setId($id);

      $info = array(
          'active' => Post::checkbox('active'),
          'question' => Post::text('question'),
          'uri' => Post::text('uri'),
          'option' => Post::aray('option'),
      );

      $this->saveAndRedirect($info);
    } catch (Exception $e) {
      JsonViewHelper::display_error_message($e);
    }
  }

}