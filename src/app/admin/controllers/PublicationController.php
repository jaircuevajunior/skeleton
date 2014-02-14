<?php

namespace src\app\admin\controllers;

use src\app\admin\models\PublicationModel as model;
use Din\Http\Get;
use Din\Http\Post;
use Din\ViewHelpers\JsonViewHelper;
use Exception;
use src\app\admin\controllers\essential\BaseControllerAdm;
use src\app\admin\viewhelpers\PublicationViewHelper as vh;

/**
 *
 * @package app.controllers
 */
class PublicationController extends BaseControllerAdm
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
        'title' => Get::text('title'),
        'pag' => Get::text('pag')
    );

    $this->_data['list'] = vh::formatResult($this->_model->getList($arrFilters));
    $this->_data['search'] = vh::formatFilters($arrFilters);

    $this->setErrorSessionData();

    $this->setListTemplate('publication_list.phtml');
  }

  public function get_save ( $id = null )
  {
    $this->_model->setId($id);

    $excluded_fields = array(
        'file'
    );

    $row = $id ? $this->_model->getById() : $this->getPrevious($excluded_fields);
    $this->_data['table'] = vh::formatRow($row);

    $this->setSaveTemplate('publication_save.phtml');
  }

  public function post_save ( $id = null )
  {
    try {
      $this->_model->setId($id);

      $info = array(
          'active' => Post::checkbox('active'),
          'title' => Post::text('title'),
          'uri' => Post::text('uri'),
          'file' => Post::upload('file'),
      );

      $this->saveAndRedirect($info);
    } catch (Exception $e) {
      JsonViewHelper::display_error_message($e);
    }
  }

}