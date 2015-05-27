<?php

namespace Acme\Commander;

use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

class Event extends SymfonyEvent
{

  public $data = null;

  public function __construct($data=null) 
  {
    $this->data = $data;
  }
}
