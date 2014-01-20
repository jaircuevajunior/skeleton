<?php

namespace src\app\admin\models\essential;

use src\app\admin\models\essential\BaseModelAdm;
use Din\Form\Listbox\Listbox;
use src\app\admin\helpers\Entities;
use Exception;

/**
 *
 * @package app.models
 */
class PermissaoModel extends BaseModelAdm
{

  public function getListbox ( $selected = array() )
  {
    if ( !is_array($selected) ) {
      $selected = json_decode($selected);
    }
    $arrOptions = array();
    foreach ( Entities::$entities as $tbl => $entity ) {
      $arrOptions[$entity['name']] = $entity['secao'];
    }

    $d = new Listbox('permissao');
    $d->setOptionsArray($arrOptions);
    $d->setClass('form-control');
    $d->setSelected($selected);

    return $d->getElement();
  }

  public function block ( $model, $user )
  {
    $permissoes = $this->getArray($user);
    $entity = Entities::getThis($model);

    if ( !in_array($entity['name'], $permissoes) ) {
      throw new Exception('Permissão negada.');
    }
  }

  public function getArray ( $user )
  {
    if ( $user['email'] == 'suporte@dindigital.com' ) {
      $permissoes = array();
      foreach ( Entities::$entities as $tbl => $entity ) {
        $permissoes[] = $entity['name'];
      }
    } else {
      $permissoes = json_decode($user['permissao']);
    }


    return $permissoes;
  }

}