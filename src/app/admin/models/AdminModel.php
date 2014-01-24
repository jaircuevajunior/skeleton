<?php

namespace src\app\admin\models;

use Din\Paginator\Paginator;
use Din\DataAccessLayer\Select;
use src\app\admin\validators\AdminValidator as validator;
use src\app\admin\models\essential\BaseModelAdm;

/**
 *
 * @package app.models
 */
class AdminModel extends BaseModelAdm
{

  public function insert ( $info )
  {
    $validator = new validator;
    $validator->setActive($info['ativo']);
    $validator->setName($info['name']);
    $validator->setEmail($info['email']);
    $validator->setPassword($info['password']);
    $validator->setPermission($info['permission']);
    $validator->setIncDate();
    $id = $validator->setId($this);

    $validator->setFile('avatar', $info['avatar'], $id, false);
    $validator->throwException();

    $this->_dao->insert($validator->getTable());
    $this->log('C', $info['nome'], $validator->getTable());

    return $id;
  }

  public function update ( $id, $info )
  {
    $validator = new validator;
    $validator->setActive($info['ativo']);
    $validator->setName($info['name']);
    $validator->setEmail($info['email']);
    $validator->setPassword($info['password']);
    $validator->setPermission($info['permission']);

    $validator->setFile('avatar', $info['avatar'], $id, false);
    $validator->throwException();

    $tableHistory = $this->getById($id);
    $this->_dao->update($validator->getTable(), array('id_admin = ?' => $id));
    $this->log('U', $info['name'], $validator->getTable(), $tableHistory);
  }

  public function save_config ( $id, $info )
  {
    $validator = new validator;
    $validator->setName($info['name']);
    $validator->setEmail($info['email']);
    $validator->setPassword($info['password']);

    $validator->setFile('avatar', $info['avatar'], $id, false);
    $validator->throwException();

    $tableHistory = $this->getById($id);
    $this->_dao->update($validator->getTable(), array('id_admin = ?' => $id));
    $this->log('U', $info['name'], $validator->getTable(), $tableHistory);
  }

  public function getList ( $arrFilters = array(), Paginator $paginator = null )
  {
    $arrCriteria = array(
        'name LIKE ?' => '%' . $arrFilters['name'] . '%',
        'email LIKE ?' => '%' . $arrFilters['email'] . '%',
        'email <> ?' => 'suporte@dindigital.com'
    );

    $select = new Select('admin');
    $select->addField('id_admin');
    $select->addField('active');
    $select->addField('name');
    $select->addField('email');
    $select->addField('inc_date');
    $select->where($arrCriteria);
    $select->order_by('name');

    $this->setPaginationSelect($select, $paginator);

    $result = $this->_dao->select($select);

    return $result;
  }

  public function delete ( $id )
  {
    $this->delete_permanent($id);
  }

}