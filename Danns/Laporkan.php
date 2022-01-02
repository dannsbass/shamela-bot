<?php

namespace Danns\Bot;

use Bot;
use Danns\Bot\Get;

class Laporkan
{

  public $query;

  public function __construct($query)
  {
    $this->query = $query;
  }

  public function keChannel()
  {
    $first_name = Get::firstName();
    $from_id = Get::fromId();
    $sender = "<a href='tg://user?id=$from_id'>$first_name</a>";
    $query = $this->query;
    Bot::sendMessage("$sender:\n$query",['chat_id'=>-1001689105046, 'parse_mode' => 'html']);
  }
}