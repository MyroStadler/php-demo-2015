<?php

namespace Acme\Commander;

class Responder 
{
  public $synonyms = array(
      'hello' => array('hi', 'hey', 'howzit', 'ahoy-hoy', 'hello'),
      'how are you?' => array('how are you?', 'you alright?', 'keeping well?', 
          'are you well?', 'how have you been?'),
      'goodbye' => array('bye', 'ciao', 'cheers', 'take care', 'goodbye'),
      'random' => array('ho hum', 'uh huh', 'yeah', 'yup', 'yu-uh', 'totally', 
          'c-c-c-combo breaker!')
  );
  public $follow;
  public $roots = null;
  public function __construct() 
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
    $this->follow = array(
        'hello' => array('how are you?'),
        'goodbye' => 
            function ($root=null)
            {
              if (php_sapi_name() == 'cli') {
                exit(); 
              }
            }
    );
  }
  
  public $speech = array();
  
  public function act() 
  {
    return array_splice($this->speech, 0, count($this->speech));
  }
  
  public function consider($input) 
  {
    $key = strtolower(trim($input));
    switch ($key) {
      // this is to show you can control from here if you want
      //  not just by adding to synonyms
      case 'hey':
      case 'hi':
      case 'good day':
      case 'good morning':
      case 'good afternoon':
      case 'good evening':
      case 'hello':
        $this->say('hello');
        break;
      default:
        // check synonyms if no direct match
        if (array_key_exists($key, $this->roots)) {
          $this->say($this->roots[$key]);
        }else{
          $this->say('random');
        }
        break;
    }
    return $this;
  }
  
  public function say($root) 
  {
    if (array_key_exists($root, $this->synonyms)){
      $synonyms = $this->synonyms[$root];
    } else {
      $synonyms = $this->synonyms['random'];
    }
    if (isset($synonyms)) {
      $this->addRandomToSpeech($synonyms);
    }
    if (array_key_exists($root, $this->follow)){
      $follows = $this->follow[$root];
      if(is_callable($follows)){
        $follows($root);
      }else{
        // assume array
        $this->addRandomToSpeech($follows);
      }
    }
  }
  
  // PRIVATE
  private function addRandomToSpeech($options) 
  {
    $this->speech[] = $options[array_rand($options)];
  }
}
