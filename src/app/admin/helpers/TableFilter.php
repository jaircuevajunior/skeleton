<?php

namespace src\app\admin\helpers;

use Din\DataAccessLayer\Table\Table;
use Din\Crypt\Crypt;
use Din\Filters\String\Uri;
use Din\Filters\String\LimitChars;

class TableFilter
{

  protected $_table;
  protected $_input;

  public function __construct ( Table $table, array $input )
  {
    $this->setTable($table);
    $this->setInput($input);
  }

  protected function setTable ( Table $table )
  {
    $this->_table = $table;
  }

  protected function setInput ( array $input )
  {
    $this->_input = $input;
  }

  protected function getValue ( $field )
  {
    if ( !array_key_exists($field, $this->_input) )
      return JsonException::addException("Índice {$field} não existe no array de input do filter");

    return $this->_input[$field];
  }

  // FILTERS ___________________________________________________________________

  public function setNewId ( $field )
  {
    $this->_table->{$field} = md5(uniqid());
  }

  public function setTimestamp ( $field )
  {
    $this->_table->{$field} = date('Y-m-d H:i:s');
  }

  public function setIntval ( $field )
  {
    $value = intval($this->getValue($field));

    $this->_table->{$field} = $value;
  }

  public function setString ( $field )
  {
    $value = (string) $this->getValue($field);

    $this->_table->{$field} = $value;
  }

  public function setJson ( $field )
  {
    $value = (array) $this->getValue($field);

    $this->_table->{$field} = json_encode($value);
  }

  public function setCrypted ( $field )
  {
    $value = $this->getValue($field);

    if ( $value != '' ) {
      $crypt = new Crypt();
      $this->_table->{$field} = $crypt->crypt($value);
    }
  }

  public function setUploaded ( $field, $path )
  {
    $value = $this->getValue($field);
    $file = $value[0];

    $pathinfo = pathinfo($file['name']);
    $name = \Din\Filters\String\Uri::format($pathinfo['filename']) . '.' . $pathinfo['extension'];

    $this->_table->{$field} = "{$path}/{$name}";
  }

  public function setDefaultUri ( $field, $id, $prefix = '' )
  {
    $title = $this->getValue($field);
    $uri = $this->getValue('uri');
    $id = substr($id, 0, 4);

    $uri = $uri == '' ? Uri::format($title) : Uri::format($uri);
    $uri = LimitChars::filter($uri, 80, '');
    if ( $prefix != '' ) {
      $prefix = '/' . $prefix;
    }

    $this->_table->uri = "{$prefix}/{$uri}-{$id}/";
  }

}
