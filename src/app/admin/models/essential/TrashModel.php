<?php

namespace src\app\admin\models\essential;

use src\app\admin\models\essential\BaseModelAdm;
use Din\DataAccessLayer\Select;
use Exception;
use src\app\admin\helpers\PaginatorAdmin;
use src\app\admin\helpers\Entities;
use src\app\admin\models\essential\SequenceModel;

/**
 *
 * @package app.models
 */
class TrashModel extends BaseModelAdm
{

  public function getList ( $arrFilters = array() )
  {
    $itens = Entities::getTrashItens();

    if ( $arrFilters['section'] != '0' ) {
      if ( isset($itens[$arrFilters['section']]) ) {
        $itens = array($itens[$arrFilters['section']]);
      }
    }

    $i = 0;
    foreach ( $itens as $item ) {

      $name = $item['name'];
      $table_name = $item['tbl'];
      $id_field = $item['id'];
      $title_field = $item['title'];
      $section = $item['section'];

      $select1 = new Select($table_name);
      $select1->addField($id_field, 'id');
      $select1->addField($title_field, 'title');
      $select1->addField('del_date');
      $select1->addSField('section', $section);
      $select1->addSField('name', $name); // AJUSTAR NOME DIFERENTE
      $select1->where(array(
          'is_del = 1' => null,
          $title_field . ' LIKE ?' => '%' . $arrFilters['title'] . '%'
      ));

      if ( $i == 0 ) {
        $select = $select1;
      } else {
        $select->union($select1);
      }

      $i++;
    }

    $select->order_by('del_date DESC');

    $this->_paginator = new PaginatorAdmin($this->_itens_per_page, $arrFilters['pag']);
    $this->setPaginationSelect($select);

    $result = $this->_dao->select($select);

    return $result;
  }

  private function hasParentOnTrash ( $current, $table )
  {
    $parent = Entities::getParent($current['tbl']);
    if ( $parent ) {
      $parent_tbl = $parent['tbl'];
      $parent_id_field = $parent['id'];

      $parend_id_value = $table[$parent_id_field];

      $select = new Select($parent_tbl);
      $select->addField($parent['title'], 'title');
      $select->where(array(
          $parent_id_field . ' = ?' => $parend_id_value,
          'is_del = ?' => 1,
      ));

      $result = $this->_dao->select($select);
      if ( count($result) ) {
        return $result[0]['title'];
      }
    }
  }

  public function restore ( $itens )
  {
    foreach ( $itens as $item ) {
      $current = Entities::getEntityByName($item['name']);
      $model = new $current['model'];
      $tableHistory = $model->getById($item['id']);

      //
      if ( $parent_title = $this->hasParentOnTrash($current, $tableHistory) ) {
        throw new Exception('Favor restaurar o ítem ' . $parent_title . ' primeiro');
      }
      //

      $seq = new SequenceModel($model);
      $seq->setSequence();

      $model->setIntval('is_del', 0);
      $this->_dao->update($model->getTable(), array($current['id'] . ' = ?' => $item['id']));
      $this->log('R', $tableHistory[$current['title']], $current['tbl'], null, $current['name']);
    }
  }

  public function deleteChildren ( $current, $id )
  {
    $children = Entities::getChildren($current['tbl']);

    foreach ( $children as $child ) {
      $select = new Select($child['tbl']);
      $select->addField($child['id'], 'id_children');
      $select->where(array(
          $current['id'] . ' = ? ' => $id
      ));
      $result = $this->_dao->select($select);

      $arr_delete = array();
      foreach ( $result as $row ) {
        $arr_delete[] = array(
            'name' => $child['name'],
            'id' => $row['id_children'],
        );
      }

      $this->delete($arr_delete);
    }
  }

  public function delete ( $itens )
  {
    foreach ( $itens as $item ) {
      $current = Entities::getEntityByName($item['name']);

      $model = new $current['model'];

      //_# Se ele não possui lixeira, chama o deletar proprio do model
      if ( !(isset($current['trash']) && $current['trash']) ) {
        $model->delete(array(array('id' => $item['id'])));
      } else {

        $seq = new SequenceModel($model);
        $seq->changeSequence($item['id'], 0);

        $this->deleteChildren($current, $item['id']);
        $tableHistory = $model->getById($item['id']);

        $model->setTimestamp('del_date');
        $model->setIntval('is_del', 1);
        $this->_dao->update($model->getTable(), array($current['id'] . ' = ?' => $item['id']));
        $this->log('T', $tableHistory[$current['title']], $current['tbl'], $tableHistory, $current['name']);
      }
    }
  }

  public function delete_permanent ( $itens )
  {
    foreach ( $itens as $item ) {
      $current = Entities::getEntityByName($item['name']);

      $model = new $current['model'];
      $info = $model->getById($item['id']);

      //
      if ( $parent_title = $this->hasParentOnTrash($current, $info) ) {
        throw new Exception('Favor excluir o ítem ' . $parent_title . ' primeiro');
      }
      //

      $model->delete(array(array(
              'id' => $item['id']
      )));
    }
  }

  public function getListArray ()
  {
    $arrOptions = array();

    foreach ( Entities::getTrashItens() as $model ) {
      $arrOptions[$model['tbl']] = $model['section'];
    }

    return $arrOptions;
  }

}
