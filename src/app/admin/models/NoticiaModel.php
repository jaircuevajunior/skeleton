<?php

namespace src\app\admin\models;

use src\app\admin\validators\NoticiaValidator;
use src\app\admin\models\essential\BaseModelAdm;
use Din\DataAccessLayer\Select;
use Din\Paginator\Paginator;
use src\app\admin\helpers\Ordem;

/**
 *
 * @package app.models
 */
class NoticiaModel extends BaseModelAdm
{

  public function listar ( $arrFilters = array(), Paginator $paginator = null )
  {
    $arrCriteria = array(
        'a.del = ?' => '0',
        'a.titulo LIKE ?' => '%' . $arrFilters['titulo'] . '%'
    );
    if ( $arrFilters['id_noticia_cat'] != '' && $arrFilters['id_noticia_cat'] != '0' ) {
      $arrCriteria['a.id_noticia_cat = ?'] = $arrFilters['id_noticia_cat'];
    }

    $select = new Select('noticia');
    $select->addField('id_noticia');
    $select->addField('ativo');
    $select->addField('titulo');
    $select->addField('data');
    $select->addField('ordem');
    $select->where($arrCriteria);
    $select->order_by('a.ordem=0,a.ordem,data DESC');

    $select->inner_join('id_noticia_cat', Select::construct('noticia_cat')
                    ->addField('titulo', 'categoria'));

    $result = $this->_dao->select($select);
    $result = Ordem::setDropdown($this, $result, $arrCriteria);

    return $result;
  }

  public function inserir ( $info )
  {
    $validator = new NoticiaValidator();
    $id = $validator->setId($this);
    $validator->setIdNoticiaCat($info['id_noticia_cat']);
    $validator->setAtivo($info['ativo']);
    $validator->setTitulo($info['titulo']);
    $validator->setData($info['data']);
    $validator->setChamada($info['chamada']);
    $validator->setCorpo($info['corpo']);
    $validator->setArquivo('capa', $info['capa'], $id);
    Ordem::setOrdem($this, $validator);
    $validator->setIncData();
    $validator->throwException();

    $this->_dao->insert($validator->getTable());
    $this->log('C', $info['titulo'], $validator->getTable());

    $this->insertRelationship('r_noticia_foto', 'id_noticia', $id, 'id_foto', $info['r_noticia_foto']);
    $this->insertRelationship('r_noticia_video', 'id_noticia', $id, 'id_video', $info['r_noticia_video']);

    return $id;
  }

  public function atualizar ( $id, $info )
  {
    $validator = new NoticiaValidator();
    $validator->setIdNoticiaCat($info['id_noticia_cat']);
    $validator->setAtivo($info['ativo']);
    $validator->setTitulo($info['titulo']);
    $validator->setData($info['data']);
    $validator->setChamada($info['chamada']);
    $validator->setCorpo($info['corpo']);
    $validator->setArquivo('capa', $info['capa'], $id);
    $validator->throwException();

    $tableHistory = $this->getById($id);
    $this->_dao->update($validator->getTable(), array('id_noticia = ?' => $id));
    $this->log('U', $info['titulo'], $validator->getTable(), $tableHistory);

    $this->insertRelationship('r_noticia_foto', 'id_noticia', $id, 'id_foto', $info['r_noticia_foto']);
    $this->insertRelationship('r_noticia_video', 'id_noticia', $id, 'id_video', $info['r_noticia_video']);

    return $id;
  }

  public function getFotoArrayRelationship ( $id )
  {
    return $this->getRelationship('r_noticia_foto', 'id_noticia', 'id_foto', $id);
  }

  public function getVideoArrayRelationship ( $id )
  {
    return $this->getRelationship('r_noticia_video', 'id_noticia', 'id_video', $id);
  }

}
