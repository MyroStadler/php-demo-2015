<?php

namespace Acme\Commander;

use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

class Event extends SymfonyEvent
{

  public $data = null;

  const QUIT = 'Acme\Commander\Event.quit';
  const SAY = 'Acme\Commander\Event.say';

  public function __construct($data=null) 
  {
    $this->data = $data;
  }
}
