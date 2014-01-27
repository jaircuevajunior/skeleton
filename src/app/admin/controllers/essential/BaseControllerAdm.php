<?php

namespace src\app\admin\controllers\essential;

use Din\Mvc\Controller\BaseController;
use src\app\admin\models\essential\AdminAuthModel;
use Exception;
use Din\Session\Session;
use Din\Image\Picuri;
use Din\Http\Post;
use Din\Http\Header;
use src\app\admin\helpers\Entities;
use src\app\admin\models\essential\PermissionModel;
use Din\ViewHelpers\JsonViewHelper;

/**
 * Classe abstrata que será a base de todos os controllers do adm
 */
abstract class BaseControllerAdm extends BaseController
{

  public function __construct ()
  {
    parent::__construct();

    $this->setAssetsData();
    $this->setUserData();
    $this->setBasicTemplate();
    $this->setDefaultHeaders();
  }

  private function setDefaultHeaders ()
  {
    Header::setNoCache();
  }

  /**
   * Seta os arquivos que compõem o layout do adm
   */
  private function setBasicTemplate ()
  {
    $this->_view->addFile('src/app/admin/views/layouts/layout.phtml');
    $this->_view->addFile('src/app/admin/views/includes/nav.phtml', '{$NAV}');
  }

  protected function setSaveTemplate ( $filename )
  {
    $this->setSavedMsgData();

    $this->_view->addFile('src/app/admin/views/includes/alerts.phtml', '{$ALERTS}');
    $this->_view->addFile('src/app/admin/views/includes/submit.phtml', '{$SUBMIT}');

    $this->_view->addFile('src/app/admin/views/' . $filename, '{$CONTENT}');
    $this->display_html();
  }

  protected function setListTemplate ( $filename, $paginator )
  {
    $this->setSavedMsgData();

    $this->_data['paginator']['subtotal'] = $paginator->getSubTotal();
    $this->_data['paginator']['total'] = $paginator->getTotal();
    $this->_data['paginator']['numbers'] = $paginator->getNumbers();

    $this->_view->addFile('src/app/admin/views/includes/alert_lista.phtml', '{$ALERT}');
    $this->_view->addFile('src/app/admin/views/includes/pagination.phtml', '{$PAGINACAO}');
    $this->_view->addFile('src/app/admin/views/includes/btns_lista_cad-exc.phtml', '{$BTN_LISTA_CAD-EXC}');

    $this->_view->addFile('src/app/admin/views/' . $filename, '{$CONTENT}');
    $this->display_html();
  }

  /**
   * Seta os assets
   */
  private function setAssetsData ()
  {
    $this->_data['assets'] = $this->getAssets();
  }

  /**
   * Verifica se usuário está logado e seta os dados de usuário
   * @throws Exception - Caso o usuário não esteja logado
   */
  private function setUserData ()
  {
    $adminAuthModel = new AdminAuthModel();
    if ( !$adminAuthModel->is_logged() )
      Header::redirect('/admin/');

    $this->_data['admin'] = $adminAuthModel->getUser();
    $this->_data['admin']['avatar_img'] = Picuri::picUri($this->_data['admin']['avatar'], 30, 30, true);

    $permission = new PermissionModel();
    $permissions = $permission->getArray($this->_data['admin']);
    $this->_data['permission'] = array_fill_keys($permissions, '');
  }

  protected function setSavedMsgSession ()
  {
    $session = new Session('adm_session');
    $session->set('saved_msg', 'Registro salvo com sucesso!');
  }

  protected function setSavedMsgData ()
  {
    $session = new Session('adm_session');
    if ( $session->is_set('saved_msg') ) {
      $this->_data['saved_msg'] = $session->get('saved_msg');
    }
    $session->un_set('saved_msg');
  }

  protected function setErrorSession ( $msg )
  {
    $session = new Session('adm_session');
    $session->set('error', $msg);

    Header::redirect(Header::getReferer());
  }

  protected function setErrorSessionData ()
  {
    $session = new Session('adm_session');
    if ( $session->is_set('error') ) {
      $this->_data['error'] = $session->get('error');
    }
    $session->un_set('error');
  }

  protected function setEntityData ()
  {
    $this->_data['entity'] = Entities::getThis($this->_model);
  }

  protected function require_permission ()
  {
    $permission = new PermissionModel();
    $permission->block($this->_model, $this->_data['admin']);
  }

  protected function saveAndRedirect ( $info, $id = null )
  {
    if ( !$id ) {
      $id = $this->_model->insert($info);
    } else {
      $this->_model->update($id, $info);
    }

    $this->setSavedMsgSession();

    $entity = Entities::getThis($this->_model);

    $redirect = '/admin/' . $entity['tbl'] . '/save/' . $id . '/';
    if ( Post::text('redirect') == 'list' ) {
      $redirect = '/admin/' . $entity['tbl'] . '/list/';
    }

    if ( Post::text('redirect') == 'previous' ) {
      $session = new Session('adm_session');
      $session->set('previous_id', $id);
      $redirect = '/admin/' . $entity['tbl'] . '/save/';
    }

    JsonViewHelper::redirect($redirect);
  }

  protected function getPrevious ( $exclude = array() )
  {
    $session = new Session('adm_session');
    $row = array();
    if ( $session->is_set('previous_id') ) {
      $row = $this->_model->getById($session->get('previous_id'));

      foreach ( $exclude as $field ) {
        unset($row[$field]);
      }
    }

    $session->un_set('previous_id');
    return $row;
  }

  public function post_delete ()
  {
    try {
      $itens = Post::aray('itens');

      foreach ( $itens as $item ) {
        $model_name = "\\src\\app\\admin\\models\\{$item['name']}Model";
        $model = new $model_name;
        $model->delete($item['id']);
      }

      Header::redirect(Header::getReferer());
    } catch (Exception $e) {
      $this->setErrorSession($e->getMessage());
    }
  }

  public function post_active ()
  {
    $this->_model->toggleActive(Post::text('id'), Post::checkbox('active'));
  }

  public function post_sequence ()
  {
    try {
      $this->_model->changeSequence(Post::text('id'), Post::text('sequence'));

      Header::redirect(Header::getReferer());
    } catch (Exception $e) {
      $this->setErrorSession($e->getMessage());
    }
  }

}
