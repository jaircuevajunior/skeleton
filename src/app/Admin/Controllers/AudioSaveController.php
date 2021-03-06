<?php

namespace Admin\Controllers;

use Admin\Models\AudioModel as model;
use Din\Http\Post;
use Helpers\JsonViewHelper;
use Exception;

/**
 *
 * @package app.controllers
 */
class AudioSaveController extends BaseControllerAdm
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
    $this->defaultSavePage('audio_save.phtml', $this->_id);

  }

  public function post ()
  {
    try {
      $this->_model->setId($this->_id);

      $info = array(
          'is_active' => Post::checkbox('is_active'),
          'title' => Post::text('title'),
          'date' => Post::text('date'),
          'description' => Post::text('description'),
          'uri' => Post::text('uri'),
          'file' => Post::upload('file'),
          'file_delete' => Post::checkbox('file_delete'),
          'publish_sc' => Post::checkbox('publish_sc'),
          'republish_sc' => Post::checkbox('republish_sc'),
      );

      $this->saveAndRedirect($info);
    } catch (Exception $e) {
      JsonViewHelper::display_error_message($e);
    }

  }

}
