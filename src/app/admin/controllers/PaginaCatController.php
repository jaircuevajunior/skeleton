<?php

namespace src\app\admin\controllers;

use src\app\admin\models\PaginaCatModel as model;
use src\app\admin\helpers\PaginatorPainel;
use Din\Http\Get;
use src\app\admin\helpers\Form;
use Din\Http\Post;
use Din\ViewHelpers\JsonViewHelper;
use Exception;
use src\app\admin\controllers\essential\BaseControllerAdm;

/**
 *
 * @package app.controllers
 */
class PaginaCatController extends BaseControllerAdm
{

  protected $_model;

  public function __construct ()
  {
    parent::__construct();

    $this->_model = new model();
    $this->setEntityData();
  }

  public function get_lista ()
  {

    $arrFilters = array(
        'titulo' => Get::text('titulo'),
    );

    $paginator = new PaginatorPainel(20, 7, Get::text('pag'));
    $this->_data['list'] = $this->_model->listar($arrFilters, $paginator);
    $this->_data['busca'] = $arrFilters;

    $this->setErrorSessionData();

    $this->setListTemplate('paginacat_lista.phtml', $paginator);
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
    $this->_data['table']['conteudo'] = Form::Ck('conteudo', @$this->_data['table']['conteudo']);

    $this->setCadastroTemplate('paginacat_cadastro.phtml');
  }

  public function post_cadastro ( $id = null )
  {
    try {
      $info = array(
          'ativo' => Post::checkbox('ativo'),
          'titulo' => Post::text('titulo'),
          'capa' => Post::upload('capa'),
          'conteudo' => Post::text('conteudo'),
          'description' => Post::text('description'),
          'keywords' => Post::text('keywords'),
      );

      if ( !$id ) {
        $id = $this->_model->inserir($info);
      } else {
        $this->_model->atualizar($id, $info);
      }

      $this->setRegistroSalvoSession();

      $redirect = '/admin/pagina_cat/cadastro/' . $id . '/';
      if ( Post::text('redirect') == 'lista' ) {
        $redirect = '/admin/pagina_cat/lista/';
      }

      JsonViewHelper::redirect($redirect);
    } catch (Exception $e) {
      JsonViewHelper::display_error_message($e);
    }
  }

}