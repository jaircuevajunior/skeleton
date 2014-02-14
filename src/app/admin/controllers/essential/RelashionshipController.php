<?php

namespace src\app\admin\controllers\essential;

use src\app\admin\models\essential\RelationshipModel as model;
use Din\Http\Get;
use Din\Http\Post;
use Din\ViewHelpers\JsonViewHelper;

/**
 *
 * @package app.controllers
 */
class RelashionshipController
{

  protected $_model;

  public function __construct ()
  {
    $this->_model = new model;
  }

  public function get_ajax ()
  {
    $this->_model->setRelationshipSection(Get::text('relationshipSection'));
    $result = $this->_model->getAjax(Get::text('q'));

    JsonViewHelper::display($result);
  }

  public function post_ajax ()
  {
    $this->_model->setCurrentSection(Post::text('currentSection'));
    $this->_model->setRelationshipSection(Post::text('relationshipSection'));
    $result = $this->_model->getAjaxCurrent(Post::text('id'));

    JsonViewHelper::display($result);
  }

}