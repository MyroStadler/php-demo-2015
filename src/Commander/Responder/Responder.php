<?php

namespace Acme\Commander\Responder;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Acme\Utils\tArrayUtils;
use Acme\Utils\tRandomUtils;
use Acme\Commander\Event as CommanderEvent;

class Responder implements iResponder
{

  // traits
  use tArrayUtils; 
  use tRandomUtils;

  public $synonyms = array(
      'hello' => array('hi', 'hey', 'howzit', 'ahoy-hoy', 'hello'),
      'good' => array('excellent', 'amaze, so much good!', 'well good', 'sparkling', 'terrible'),
      'bad' => array('not so great', 'not good', 'doubleplus ungood', 'a little off', 'terriffic'),
      'how are you?' => array('how are you?', 'you alright?', 'keeping well?', 
          'are you well?', 'how have you been?'),
      'goodbye' => array('bye', 'ciao', 'cheers', 'take care', 'goodbye'),
      'random' => array('ho hum', 'uh huh', 'yeah', 'yup', 'yu-uh', 'totally', 
          'hmmm', 'what?', 'pardon?', 'DESTROY THE HUMANOID')
  );
  public $sayBuffer = array();
  public $actBuffer = array();
  public $roots = null;
  public $actionsByRoot = null;
  
  public function __construct(EventDispatcher $dispatcher) 
  {
    $this->dispatcher = $dispatcher;
    $this->processSynonyms();
    $self = $this;
    // these need managing methods
    $this->actionsByRoot = array(
      'hello' => function() use ($self) {
        $self->say('hello')->say('how are you?')->dispatchSayBuffer();
      },
      'goodbye' => function() use ($self, $dispatcher) {
        $self->say('goodbye')->dispatchSayBuffer();
        $dispatcher->dispatch('Acme\Commander.exit', new CommanderEvent());
      },
      'how are you?' => function() use ($self, $dispatcher) {
        $self->say($self->random() >= 0.5 ? 'good' : 'bad')
            ->say('how are you?')->dispatchSayBuffer();
      }
    );
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
      // I know what you said. 
      // I will respond if I have anything to say.
      if (array_key_exists($root, $this->actionsByRoot)) {
        $this->act($root);
      }
    } else {
      // i have no idea what you said.
      // Will I make a noise anyway?
      $this->random() >= 0.5 || $this->say('random');
    }
    // now that we know what our resulting performances are, dispatch them with actions last
    $this->dispatch();
    return $this;
  }
  

  //*********************************************
  // PRIVATE
  //*********************************************

  private function processSynonyms()
  {
    $this->roots = array();
    foreach ($this->synonyms as $root => $synonyms) {
      // write roots to themselves!
      $this->roots[$root] = $root;
      foreach ($synonyms as $synonym) {
        // design flaw alert! if two roots have the same synonym the first will
        //  be clobbered by the second
        $this->roots[$synonym] = $root;
      }
    }
  } 

  private function say($root) 
  {
    if (array_key_exists($root, $this->synonyms)){
      $synonyms = $this->synonyms[$root];
    } else {
      $synonyms = $this->synonyms['random'];
    }
    if (isset($synonyms)) {
      $this->addRandomTo($synonyms, $this->sayBuffer);
    }
    return $this;
  }

  private function dispatchSayBuffer() 
  {
    if (count($this->sayBuffer)) {
      $this->dispatcher->dispatch('Acme\Commander.say', 
        new CommanderEvent($this->consumeArray($this->sayBuffer)));
    }
    return $this;
  }
  
  private function act($root) 
  {
    $this->actBuffer[] = $this->actionsByRoot[$root];
    return $this;
  } 

  private function dispatchActBuffer() 
  {
    if (count($this->actBuffer)) {
      $actions = $this->consumeArray($this->actBuffer);
      foreach ($actions as $action) {
        if (is_callable($action)) {
          $action();
        }
      }
    }
    return $this;
  }

  private function dispatch() 
  {
    // say what's been queued first
    $this->dispatchSayBuffer();
    // then execute closures that have been queued
    $this->dispatchActBuffer();
    return $this;
  }
}

