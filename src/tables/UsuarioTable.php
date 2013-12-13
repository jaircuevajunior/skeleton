<?php

namespace src\tables;

use Din\DataAccessLayer\Table\AbstractTable;
use Din\DataAccessLayer\Table\iTable;

/**
 *
 * @package tables
 */
class UsuarioTable extends AbstractTable implements iTable
{

  /**
   * @var int(11) NOT NULL
   */
  protected $id_usuario;

  /**
   * @var tinyint(4) NOT NULL
   */
  protected $ativo;

  /**
   * @var varchar(45) NOT NULL
   */
  protected $nome;

  /**
   * @var varchar(255) NOT NULL
   */
  protected $email;

  /**
   * @var char(32) NOT NULL
   */
  protected $senha;

  /**
   * @var varchar(255) DEFAULT NULL
   */
  protected $avatar;

  /**
   * @var datetime NOT NULL
   */
  protected $inc_data;

  /**
   * @var int(11) DEFAULT NULL
   */
  protected $id_conselho;

}
