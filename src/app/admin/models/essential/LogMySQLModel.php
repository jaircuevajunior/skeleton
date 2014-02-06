<?php

namespace src\app\admin\models\essential;

use src\app\admin\validators\LogValidator as validator;
use src\app\admin\models\essential\LogAbstract;
use src\app\admin\models\essential\LogInterface;
use Din\DataAccessLayer\Table\Table;

class LogMySQLModel extends LogAbstract implements LogInterface
{

  protected $_table;

  public function __construct ()
  {
    $this->_table = new Table('log');
  }

  public static function save ( $dao, $admin, $action, $msg, $name, $table, $tableHistory )
  {
    $log = new self;
    $log->_dao = $dao;
    $log->admin = $admin;
    $log->msg = $msg;
    $log->name = $name;
    $log->table = $table;
    $log->tableHistory = $tableHistory;

    $log->logicSave($action);
  }

  public function insert ()
  {
    $validator = new validator($this->_table);
    $validator->setAdmin($this->admin['name']);
    $validator->setName($this->name);
    $validator->setAction('C');
    $validator->setDescription($this->msg);
    $validator->setContent(json_encode($this->table->setted_values));
    $validator->setIncDate();

    $this->_dao->insert($this->_table);
  }

  public function update ()
  {

    $diff = array();

    foreach ( $this->table->setted_values as $key => $value ) {
      if ( $value != $this->tableHistory[$key] ) {
        $diff[$key] = array(
            'before' => $this->tableHistory[$key],
            'after' => $value
        );
      }
    }

    if ( count($diff) ) {
      $content = json_encode($diff);
    } else {
      $content = 'Não houveram alterações';
    }

    $validator = new validator($this->_table);
    $validator->setAdmin($this->admin['name']);
    $validator->setName($this->name);
    $validator->setAction('U');
    $validator->setDescription($this->msg);
    $validator->setContent($content);
    $validator->setIncDate();

    $this->_dao->insert($this->_table);
  }

  public function deleteRestore ( $action )
  {
    $validator = new validator($this->_table);
    $validator->setAdmin($this->admin['name']);
    $validator->setName($this->name);
    $validator->setAction($action);
    $validator->setDescription($this->msg);
    $validator->setContent(json_encode($this->tableHistory));
    $validator->setIncDate();

    $this->_dao->insert($this->_table);
  }

}
