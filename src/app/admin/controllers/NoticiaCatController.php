<?php

namespace src\app\admin\controllers;

use src\app\admin\models\NoticiaCatModel;
use src\app\admin\helpers\PaginatorPainel;
use Din\Http\Get;
use Din\Http\Post;
use Din\ViewHelpers\JsonViewHelper;
use \Exception;
use src\app\admin\helpers\Form;
use src\app\admin\helpers\Arrays;

/**
 *
 * @package app.controllers
 */
class NoticiaCatController extends BaseControllerAdm
{

  protected $_model;

  public
          function __construct ()
  {
    parent::__construct();

    $this->_model = new NoticiaCatModel();
  }

  public function get_lista ()
  {

    $arrFilters = array(
        'titulo' => Get::text('titulo'),
        'home' => Get::text('home'),
    );

    $paginator = new PaginatorPainel(20, 7, Get::text('pag'));
    $this->_data['list'] = $this->_model->listar($arrFilters, $paginator);
    $this->_data['busca'] = $arrFilters;
    $this->_data['busca']['home'] = Form::Dropdown('home', Arrays::$simNao, $arrFilters['home'], 'Home?');

    $this->setErrorSessionData();

    $this->setListTemplate('noticiacat_lista.phtml', $paginator);
  }

  public function get_cadastro (
  $id = null )
  {
    if ( $id ) {
      $this->_data['table'] = $this->_model->getById($id);
    } else {
      $this->_data['table'] = array();
    }

    $this->_data['table']['capa'] = Form::Upload('capa', @$this->_data['table']['capa'], 'imagem');

    $this->setCadastroTemplate('noticiacat_cadastro.phtml');
  }

  public function post_cadastro ( $id = null )
  {
    try {
      $info = array(
          'ativo' => Post::checkbox('ativo'),
          'titulo' => Post::text('titulo'),
          'home' => Post::checkbox('home'),
          'capa' => Post::upload('capa')
      );

      if ( !$id ) {
        $id = $this->_model->inserir($info);
      } else {
        $this->_model->atualizar($id, $info);
      }

      $this->setRegistroSalvoSession();

      $redirect = '/admin/noticia_cat/cadastro/' . $id . '/';
      if ( Post::text('redirect') == 'lista' ) {
        $redirect = '/admin/noticia_cat/lista/';
      }

      JsonViewHelper::redirect($redirect);
    } catch (Exception $e) {
      JsonViewHelper::display_error_message($e);
    }
  }

}