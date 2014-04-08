<?php

namespace src\app\admin\controllers;

use src\app\admin\models\NewsCatModel as model;
use Din\Http\Get;

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

    $this->_model = new model;
    $this->setEntityData();
    $this->require_permission();
  }

  public function get ()
  {

    $arrFilters = array(
        'title' => Get::text('title'),
        'is_home' => Get::text('is_home'),
        'pag' => Get::text('pag'),
    );

    $this->_model->setFilters($arrFilters);
    $this->_data['list'] = $this->_model->getList();
    $this->_data['search'] = $this->_model->formatFilters();

    $this->setErrorSessionData();

    $this->setListTemplate('newscat_list.phtml');
  }

}
