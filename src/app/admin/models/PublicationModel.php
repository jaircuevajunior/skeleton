<?php

namespace src\app\admin\models;

use src\app\admin\validators\BaseValidator as validator;
use src\app\admin\models\essential\BaseModelAdm;
use Din\DataAccessLayer\Select;
use src\app\admin\helpers\PaginatorAdmin;
use src\app\admin\helpers\MoveFiles;

/**
 *
 * @package app.models
 */
class PublicationModel extends BaseModelAdm
{

  public function __construct ()
  {
    parent::__construct();
    $this->setTable('publication');
  }

  public function getList ( $arrFilters = array() )
  {
    $arrCriteria = array(
        'is_del = ?' => '0',
        'title LIKE ?' => '%' . $arrFilters['title'] . '%'
    );

    $select = new Select('publication');
    $select->addField('id_publication');
    $select->addField('active');
    $select->addField('title');
    $select->where($arrCriteria);
    $select->order_by('title');

    $this->_paginator = new PaginatorAdmin($this->_itens_per_page, $arrFilters['pag']);
    $this->setPaginationSelect($select);

    $result = $this->_dao->select($select);

    return $result;
  }

  public function insert ( $input )
  {
    $this->setNewId();
    $this->setTimestamp('inc_date');
    $this->setIntval('active', $input['active']);
    $this->setDefaultUri($input['title'], 'publicacoes');

    $validator = new validator($this->_table);
    $validator->setInput($input);
    $validator->setId($this->getId());
    $validator->setRequiredString('title', 'Título');
    $mf = new MoveFiles;
    $validator->setFile('file', $mf);
    $validator->throwException();

    $mf->move();

    $this->dao_insert();
  }

  public function update ( $input )
  {
    $this->setIntval('active', $input['active']);
    $this->setDefaultUri($input['title'], 'publicacoes', $input['uri']);

    $validator = new validator($this->_table);
    $validator->setInput($input);
    $validator->setId($this->getId());
    $validator->setRequiredString('title', 'Título');
    $mf = new MoveFiles;
    $validator->setFile('file', $mf);
    $validator->throwException();

    $mf->move();

    $this->dao_update();
  }

}