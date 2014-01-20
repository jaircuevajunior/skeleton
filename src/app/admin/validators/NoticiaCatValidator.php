<?php

namespace src\app\admin\validators;

use src\app\admin\validators\BaseValidator;
use src\tables\NoticiaCatTable;
use Din\Exception\JsonException;

class NoticiaCatValidator extends BaseValidator
{

  public function __construct ()
  {
    $this->_table = new NoticiaCatTable();
  }

  public function setTitulo ( $titulo )
  {
    if ( $titulo == '' )
      return JsonException::addException('Titulo é obrigatório');

    $this->_table->titulo = $titulo;
  }

  public function setHome ( $home )
  {
    $home = intval($home);

    $this->_table->home = $home;
  }

}
