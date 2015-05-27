<?php

namespace Acme\Utils;

trait tRandomUtils 
{
  private function random()
  {
    return mt_rand() / mt_getrandmax();
  }
}