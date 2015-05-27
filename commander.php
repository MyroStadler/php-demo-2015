<?php

$loader = require 'vendor/autoload.php';
//$loader->add('Acme', __DIR__.'/src/');

use Acme\Commander\Responder;

$responder = new Responder();

while ($line = fgets(STDIN)) {
  echo implode("\n", $responder->consider($line)->speak()) . "\n";
}



