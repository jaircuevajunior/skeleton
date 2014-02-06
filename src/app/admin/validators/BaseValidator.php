<?php

namespace src\app\admin\validators;

use Exception;
use Din\Filters\String\Uri;
use Din\Exception\JsonException;
use src\app\admin\helpers\Entities;
use Din\UrlShortener\Bitly\Bitly;
use src\app\admin\helpers\MoveFiles;
use Din\DataAccessLayer\Table\iTable;

class BaseValidator
{

  protected $_table;

  public function __construct ( iTable $table )
  {
    $this->_table = $table;
  }

  public function setId ( $model )
  {
    $entity = Entities::getThis($model);
    $property = $entity['id'];

    $this->_table->{$property} = md5(uniqid());

    return $this->_table->{$property};
  }

  public function setActive ( $active )
  {
    $active = intval($active);

    $this->_table->active = $active;
  }

  public function setIsDel ( $is_del )
  {
    $is_del = intval($is_del);

    $this->_table->is_del = $is_del;
  }

  public function setIncDate ()
  {
    $this->_table->inc_date = date('Y-m-d H:i:s');
  }

  public function setDelDate ()
  {
    $this->_table->del_date = date('Y-m-d H:i:s');
  }

  public function setSequence ( $sequence )
  {
    if ( !is_numeric($sequence) )
      return JsonException::addException('ordem deve ser numérica');

    $this->_table->sequence = $sequence;
  }

  /**
   * Adiciona o valor do campo LINK na tabela, seguindo o padrão de desenvolvimento.
   * Caso haja necessidade de criação de um link diferente, criar um método setLink
   * no validator da própria tabela.
   * @param String $title - Titulo do conteúdo (obrigatório)
   * @param String $id - ID do conteúdo (obrigatório)
   * @param String $prefix - Prefixo para formar o padrão da URI, caso não adicione nada
   *                      o link ficará como "/titulo-id/". Adicionando a string:
   *                      "flores/rosas", teremos "/flores/rosas/titulo-id/"
   * @param String $link - Usado no editar, possibilita que o administrador altere
   *                       a formação do link (area do título), mantendo o
   *                       padrão (prefixo e id).
   */
  public function setDefaultUri ( $title, $id, $prefix = '', $uri = null )
  {
    $id = substr($id, 0, 4);
    $uri = is_null($uri) || $uri == '' ? Uri::format($title) : Uri::format($uri);
    if ( $prefix != '' ) {
      $prefix = '/' . $prefix;
    }
    $this->_table->uri = "{$prefix}/{$uri}-{$id}/";
  }

  public function setShortenerLink ()
  {
    if ( URL && BITLY && $this->_table->uri ) {
      $url = URL . $this->_table->uri;
      $bitly = new Bitly(BITLY);
      $bitly->shorten($url);
      if ( $bitly->check() ) {
        $this->_table->short_link = $bitly;
      }
    }
  }

  public function setFile ( $fieldname, $file, $id, MoveFiles $mf )
  {
    /**
     * Início, verica se existe uma tentativa correta de realizar upload.
     */
    if ( !isset($file [0]) )
      return; //Array de upload vazio

    $file = $file[0];

    if ( !(count($file) == 2 && isset($file['tmp_name']) && isset($file['name'])) )
      return; //Array de upload não possui os índices necessários: tmp_name, name

    /**
     *  Chegou até aqui, então possui a intenção correta de realizar um upload
     *  Vamos validar e setar o valor do campo da tabela.
     */
    if ( !is_writable('public/system') )
      throw new Exception('A pasta public/system precisa ter permissão de escrita');

    $tmp_name = $file['tmp_name'];
    $name = $file['name'];

    $origin = 'tmp' . DIRECTORY_SEPARATOR . $tmp_name;

    if ( !is_file($origin) )
      throw new Exception('O arquivo temporário de upload não foi encontrado, certifique-se de dar permissão a pasta tmp ');

    $pathinfo = pathinfo($name);
    $name = Uri::format($pathinfo['filename']) . '.' . $pathinfo['extension'];

    $table_folder = $this->_table->getName();
    $destiny = "/system/uploads/{$table_folder}/{$id}/{$fieldname}/{$name}";

    $this->_table->{$fieldname} = $destiny;

    $mf->addFile($origin, 'public' . $destiny);
  }

  public function getTable ()
  {
    return $this->_table;
  }

  public function throwException ()
  {
    JsonException::throwException();
  }

}
