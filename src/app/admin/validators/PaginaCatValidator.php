<?php

namespace src\app\admin\validators;

use src\app\admin\validators\BaseValidator;
use src\tables\PaginaCatTable;
use Din\Exception\JsonException;

class PaginaCatValidator extends BaseValidator
{

  public function __construct ()
  {
    $this->_table = new PaginaCatTable();
  }

  public function setIdPaginaCat ()
  {
    $this->_table->id_pagina_cat = $this->_table->getNewId();

    return $this;
  }

  public function setTitulo ( $titulo )
  {
    if ( $titulo == '' )
      return JsonException::addException('Titulo é obrigatório');

    $this->_table->titulo = $titulo;
  }

  public function setConteudo ( $conteudo )
  {
    $this->_table->conteudo = $conteudo;
  }

  public function setDescription ( $description )
  {
    $this->_table->description = $description;
  }

  public function setKeywords ( $keywords )
  {
    $this->_table->keywords = $keywords;
  }

}