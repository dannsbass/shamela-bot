<?php

namespace Danns\Bot;

use Bot;
use simple_html_dom;

class Get
{
    public static function HTML($data)
    {
        $html = new simple_html_dom(self::dataToURL($data));
        if(empty($html)) return Bot::sendMessage('الصفحة فارغة',['reply' => true]);
        return $html;
    }

    public static function dataToURL($data)
    {
        return 'http://islamport.com/' . str_replace('_', '/', $data) . '.htm';
    }

    public static function br2nl($input)
    {
        return preg_replace('/\<(\s+)?br(\s+)?\/?(\s+)?\>|\<\/?p([^\>]+)?\>/ius', "\n", str_replace("\n", "", str_replace("\r", "", htmlspecialchars_decode($input))));
    }

    public static function messageId()
    {
        return (Bot::message())['message_id'] ?? '';
    }
    
    public static function chatId()
    {
        return (Bot::message())['chat']['id'] ?? '';
    }

    public static function fromId()
    {
        return (Bot::message())['from']['id'] ?? '';
    }

    public static function firstName()
    {
        return (Bot::message())['from']['first_name'] ?? '';
    }

    public static function lastName()
    {
        return (Bot::message())['from']['last_name'] ?? '';
    }

    public static function callbackData()
    {
        return (Bot::message())['data'] ?? '';
    }

}

$chat_id = Get::chatId();
$from_id = Get::fromId();
$first_name = Get::firstName();