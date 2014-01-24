<?php

namespace src\app\admin\controllers;

use src\app\admin\models\NewsCatModel as model;
use src\app\admin\helpers\PaginatorPainel;
use Din\Http\Get;
use Din\Http\Post;
use Din\ViewHelpers\JsonViewHelper;
use Exception;
use src\app\admin\controllers\essential\BaseControllerAdm;
use src\app\admin\viewhelpers\NewsCatViewHelper as vh;

/**
 *
 * @package app.controllers
 */
class NewsCatController extends BaseControllerAdm
{

  protected $_model;

  public function __construct ()
  {
    parent::__construct();

    $this->_model = new model();
    $this->setEntityData();
    $this->require_permission();
  }

  public function get_list ()
  {

    $arrFilters = array(
        'title' => Get::text('title'),
        'is_home' => Get::text('is_home'),
    );

    $paginator = new PaginatorPainel(20, 7, Get::text('pag'));
    $this->_data['list'] = vh::formatResult($this->_model->get_list($arrFilters, $paginator));
    $this->_data['search'] = vh::formatFilters($arrFilters);

    $this->setErrorSessionData();

    $this->setListTemplate('newscat_lista.phtml', $paginator);
  }

  public function get_save ( $id = null )
  {
    $excluded_fields = array(
        'cover'
    );
    $row = $id ? $this->_model->getById($id) : $this->getPrevious($excluded_fields);

    $this->_data['table'] = vh::formatRow($row);

    $this->setSaveTemplate('newscat_cadastro.phtml');
  }

  public function post_cadastro ( $id = null )
  {
    try {
      $info = array(
          'active' => Post::checkbox('active'),
          'title' => Post::text('title'),
          'is_home' => Post::checkbox('is_home'),
          'cover' => Post::upload('cover')
      );

      $this->saveAndRedirect($info, $id);
    } catch (Exception $e) {
      JsonViewHelper::display_error_message($e);
    }
  }

}