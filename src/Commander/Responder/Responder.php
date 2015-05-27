<?php

namespace Acme\Commander\Responder;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Acme\Utils\tArrayUtils;
use Acme\Commander\Event as CommanderEvent;

class Responder implements iResponder
{

  use tArrayUtils; // array utils used here

  public $synonyms = array(
      'hello' => array('hi', 'hey', 'howzit', 'ahoy-hoy', 'hello'),
      'how are you?' => array('how are you?', 'you alright?', 'keeping well?', 
          'are you well?', 'how have you been?'),
      'goodbye' => array('bye', 'ciao', 'cheers', 'take care', 'goodbye'),
      'random' => array('ho hum', 'uh huh', 'yeah', 'yup', 'yu-uh', 'totally', 
          'c-c-c-combo breaker!')
  );
  public $sayBuffer = array();
  public $actBuffer = array();
  public $roots = null;
  public $actionsByRoot = null;
  
  public function __construct(EventDispatcher $dispatcher) 
  {
    $this->dispatcher = $dispatcher;
    $this->roots = array();
    foreach ($this->synonyms as $root => $synonyms) {
      // write roots to themselves!
      $this->roots[$root] = $root;
      foreach ($synonyms as $synonym) {
        // design flaw alert! if two roots have the same synonym the first will
        //  be clobbered by the second
        $this->roots[$synonym] = $root;
      }
      $this->actionsByRoot = array(
        'goodbye' => function() use ($dispatcher) {
          $dispatcher->dispatch('Acme\Commander.exit', new CommanderEvent());
        }
      );
    }
  }



  //*********************************************
  // PUBLIC
  //*********************************************  
  
  public function consider($input) 
  {
    $key = strtolower(trim($input));
    // actions to take?
    // response to say?
    if (array_key_exists($key, $this->roots)) {
      $root = $this->roots[$key];
      // actions are parsed only for roots that exost.
      // if you dont want to say anything include an empty string as the only synonym for that root
      if (array_key_exists($root, $this->actionsByRoot)) {
        $this->act($root);
      }
      $this->say($root);
    } else {
      $this->say('random');
    }
    // now that we know what our resulting performances are, dispatch them with actions last
    $this->dispatch();
    return $this;
  }
  

  //*********************************************
  // PRIVATE
  //*********************************************
  
  private function act($root) 
  {
    $this->actBuffer[] = $this->actionsByRoot[$root];
  }  

  private function say($root) 
  {
    if (trim($root) == '') {
      return;
    }
    if (array_key_exists($root, $this->synonyms)){
      $synonyms = $this->synonyms[$root];
    } else {
      $synonyms = $this->synonyms['random'];
    }
    if (isset($synonyms)) {
      $this->addRandomTo($synonyms, $this->sayBuffer);
    }
  }

  private function dispatch() 
  {
    // say stuff that's been queued
    if (count($this->sayBuffer)) {
      $this->dispatcher->dispatch('Acme\Commander.say', 
        new CommanderEvent($this->consumeArray($this->sayBuffer)));
    }
    // do stuff that's been queued, like dispatching events other than say
    if (count($this->actBuffer)) {
      $actions = $this->consumeArray($this->actBuffer);
      foreach ($actions as $action) {
        if (is_callable($action)) {
          $action();
        }
      }
    }
  }
}
