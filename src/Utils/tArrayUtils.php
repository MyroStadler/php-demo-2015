<?php

namespace Acme\Utils;

trait tArrayUtils 
{
  private function consumeArray(&$arr)
  {
    return array_splice($arr, 0, count($arr));
  }

  private function addRandomTo($options, &$addTo) 
  {
    $addTo[] = $options[array_rand($options)];
  }
}