<?php

namespace src\app\admin\models;

use src\app\admin\models\essential\BaseModelAdm;
use Din\DataAccessLayer\Select;
use src\app\admin\helpers\PaginatorAdmin;
use src\app\admin\helpers\MoveFiles;
use Din\Filters\Date\DateFormat;
use src\app\admin\helpers\Form;
use Din\Filters\String\Html;
use src\app\admin\helpers\Link;
use src\app\admin\validators\StringValidator;
use src\app\admin\validators\UploadValidator;
use Din\Exception\JsonException;
use src\app\admin\filters\TableFilter;
use src\app\admin\filters\SequenceFilter;
use src\app\admin\helpers\SequenceResult;

/**
 *
 * @package app.models
 */
class PageCatModel extends BaseModelAdm
{

  public function __construct ()
  {
    parent::__construct();
    $this->setEntity('page_cat');
  }

  public function formatTable ( $table )
  {
    $table['title'] = Html::scape($table['title']);
    $table['content'] = Form::Ck('content', $table['content']);
    $table['cover_uploader'] = Form::Upload('cover', $table['cover'], 'image');
    $table['uri'] = Link::formatNavUri($table['uri']);

    return $table;
  }

  public function getList ()
  {
    $arrCriteria = array(
        'is_del = ?' => '0',
        'title LIKE ?' => '%' . $this->_filters['title'] . '%'
    );

    $select = new Select('page_cat');
    $select->addField('id_page_cat');
    $select->addField('active');
    $select->addField('title');
    $select->addField('inc_date');
    $select->addField('sequence');
    $select->addField('uri');
    $select->where($arrCriteria);
    $select->order_by('sequence');

    $this->_paginator = new PaginatorAdmin($this->_itens_per_page, $this->_filters['pag']);
    $this->setPaginationSelect($select);

    $result = $this->_dao->select($select);

    $seq = new SequenceResult($this->_entity, $this->_dao);
    $result = $seq->filterResult($result, $arrCriteria);

    foreach ( $result as $i => $row ) {
      $result[$i]['inc_date'] = DateFormat::filter_date($row['inc_date']);
      $result[$i]['sequence'] = Form::Dropdown('sequence', $row['sequence_list_array'], $row['sequence'], '', $row['id_page_cat'], 'form-control drop_sequence');
    }

    return $result;
  }

  public function insert ( $input )
  {
    $str_validator = new StringValidator($input);
    $str_validator->validateRequiredString('title', "Título");
    //
    $upl_validator = new UploadValidator($input);
    $has_cover = $upl_validator->validateFile('cover');
    //
    JsonException::throwException();
    //
    $filter = new TableFilter($this->_table, $input);
    $filter->setNewId('id_page_cat');
    $filter->setTimestamp('inc_date');
    $filter->setIntval('active');
    $filter->setString('title');
    $filter->setString('content');
    $filter->setString('description');
    $filter->setString('keywords');
    $filter->setNavUri('title');
    //
    $seq_filter = new SequenceFilter($this->_table, $this->_dao, $this->_entity);
    $seq_filter->setSequence();

    //
    $mf = new MoveFiles;
    if ( $has_cover ) {
      $filter->setUploaded('cover', "/system/uploads/page_cat/{$this->getId()}/cover");
      $mf->addFile($input['cover'][0]['tmp_name'], $this->_table->cover);
    }
    $mf->move();

//    $seq = new SequenceModel($this);
//    $seq->setSequence();

    $this->dao_insert();
  }

  public function update ( $input )
  {
    $str_validator = new StringValidator($input);
    $str_validator->validateRequiredString('title', "Título");
    //
    $upl_validator = new UploadValidator($input);
    $has_cover = $upl_validator->validateFile('cover');
    //
    JsonException::throwException();
    //
    $filter = new TableFilter($this->_table, $input);
    $filter->setIntval('active');
    $filter->setString('title');
    $filter->setString('content');
    $filter->setString('description');
    $filter->setString('keywords');
    $filter->setNavUri('title');
    //
    $mf = new MoveFiles;
    if ( $has_cover ) {
      $filter->setUploaded('cover', "/system/uploads/page_cat/{$this->getId()}/cover");
      $mf->addFile($input['cover'][0]['tmp_name'], $this->_table->cover);
    }
    $mf->move();

    $this->dao_update();
  }

  public function formatFilters ()
  {
    $this->_filters['title'] = Html::scape($this->_filters['title']);

    return $this->_filters;
  }

  public function getListArray ()
  {
    $select = new Select('page_cat');
    $select->addField('id_page_cat');
    $select->addField('title');
    $select->where(array(
        'is_del = ? ' => '0'
    ));

    $result = $this->_dao->select($select);

    $arrOptions = array();
    foreach ( $result as $row ) {
      $arrOptions[$row['id_page_cat']] = $row['title'];
    }

    return $arrOptions;
  }
  
  public function getTitle($id_page_cat) {

    $arrCriteria = array(
        'a.id_page_cat = ?' => $id_page_cat
    );
    
    $select = new Select('page_cat');
    $select->addField('title');
    $select->where($arrCriteria);

    $result = $this->_dao->select($select);
    
    if (count($result)) {
        return $result[0]['title'];
    }
  }

}
