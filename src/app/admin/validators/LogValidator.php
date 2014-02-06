<?php

namespace src\app\admin\validators;

use src\app\admin\validators\BaseValidator;

class LogValidator extends BaseValidator
{

  public function setAdmin ( $admin )
  {
    $this->_table->admin = $admin;
  }

  public function setName ( $name )
  {
    $this->_table->name = $name;
  }

  public function setAction ( $action )
  {
    $this->_table->action = $action;
  }

  public function setDescription ( $description )
  {
    $this->_table->description = $description;
  }

  public function setContent ( $content )
  {
    $this->_table->content = $content;
  }

}
