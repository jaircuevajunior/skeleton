<?php

namespace Admin\Controllers;

use Admin\Models\PhotoModel as model;
use Din\Http\Post;
use Helpers\JsonViewHelper;
use Exception;

/**
 *
 * @package app.controllers
 */
class PhotoSaveController extends BaseControllerAdm
{

  protected $_model;
  protected $_id;

  public function __construct ( $id )
  {
    $this->_id = $id;
    parent::__construct();
    $this->_model = new model;
    $this->setEntityData();
    $this->require_permission();

  }

  public function get ()
  {
    $this->defaultSavePage('photo_save.phtml', $this->_id);

  }

  public function post ()
  {
    try {
      $this->_model->setId($this->_id);

      $info = array(
          'is_active' => Post::checkbox('is_active'),
          'title' => Post::text('title'),
          'date' => Post::text('date'),
          'gallery_uploader' => Post::upload('gallery_uploader'),
          'sequence' => Post::text('gallery_sequence'),
          'label' => Post::aray('label'),
          'credit' => Post::aray('credit'),
          'uri' => Post::text('uri'),
      );

      $this->saveAndRedirect($info);
    } catch (Exception $e) {
      JsonViewHelper::display_error_message($e);
    }

  }

}
