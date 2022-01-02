<?php

namespace Danns\Bot;

use Danns\Bot\Proses;

class Callback
{

  private $respon;
    
    public function __construct($data, $string)
    {
        $this->respon = (new Proses(
            trim(str_replace($string, '', substr($data, 0, strpos($data, '@')))) ,
            trim(str_replace('@', '', substr($data, strpos($data, '@'))))
        ))->getString();
    }

    public function __toString(){
      return $this->respon;
    }

}