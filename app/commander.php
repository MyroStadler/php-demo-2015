<?php
/*
* A simple responder. It understands hello, how are you? and goodbye.
  It is moderately responsive when ignorant.

	$ cd .../app
	$ php commander.php
*/
$loader = require '../vendor/autoload.php';

use \Symfony\Component\EventDispatcher\EventDispatcher;
use Acme\Commander\Responder\Responder;
use Acme\Commander\Event as CommanderEvent;


$dispatcher = new EventDispatcher();

$dispatcher->addListener('Acme\Commander.exit', function (CommanderEvent $e) {
  if (php_sapi_name() == 'cli') {
    exit(); 
  }
});

$dispatcher->addListener('Acme\Commander.say', function (CommanderEvent $e) {
	echo implode("\n", $e->data) . "\n";
});

$responder = new Responder($dispatcher);

while ($line = fgets(STDIN)) {
  $responder->consider($line);
}



