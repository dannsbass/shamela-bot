<?php
foreach (glob(__DIR__ . '/Danns/*.php') as $file) {
    require $file;
}

use Danns\Bot\Callback;
use Danns\Bot\Get;
use Danns\Bot\Pesan;
use Danns\Bot\Proses;
use Danns\Bot\Laporkan;

$bot = bot();

// START
$bot->cmd('/start', function () {
    $first_name = Get::firstName();
    $from_id = Get::fromId();
    $respon = "<a href='tg://user?id=$from_id'>$first_name</a>\n\n";
    $respon .= "مرحبا، تفضل اكتب ما تريد أن تبحث عنه";
    return Bot::sendMessage($respon, ['reply' => true, 'parse_mode' => 'html']);
});

// PESAN TEKS BIASA
$bot->cmd('*', function ($query) {

  if(preg_match('/[a-zA-Z_\d]+/i',$query)) return Bot::sendMessage('Please use Arabic letters');

    Bot::sendMessage('انتظر قليلا، سأبحث لك..', ['reply' => true]);

    $ok = (new Laporkan($query))->keChannel();

    $query = urlencode(iconv("utf-8", "windows-1256", $query));
    $category = 5;

    $url = "http://islamport.com/cgi-bin/l/search.cgi?zoom_query=$query&zoom_cat=$category&zoom_per_page=100&zoom_xml=0&zoom_and=1&zoom_sort=0";

    $html = file_get_html($url);

    if (empty($html)) return Bot::sendMessage('معذرة، الصفحة فارغة', ['reply' => true]);

    //integers
    $hasil_pencarian = (int) trim(preg_replace('/[^\d]+/', '', $html->find('.summary', 0)->innertext));

    //arrays
    $titles = $html->find('.result_title');
    $contexts = $html->find('.context');

    //strings
    $katakunci = $html->find('input', 2)->value; // $katakunci = $query;
    $kategori = $html->find('option[selected]', 1)->value;  // $kategori = $category;

    $links = [];
    foreach ($html->find('a') as $a) {
        if (preg_match('/^http.+\d+.+htm/is', $a->href)) {
            // $a->href = 'http://islamport.com/l/srh/2286/3569.htm';
            $link = str_replace(['http://islamport.com/', '.htm'], '', $a->href);
            $link = '/' . str_replace('/', '_', $link); # /l_srh_2286_3569
            $links[] = $link;
        }
    }

    $hasil = '';
    foreach ($titles as $key => $value) {
        $hasil .= "<b>" . strip_tags($value, '<i>') . "</b>\n\n";
        $context = str_replace('</span>', '</b>', $contexts[$key]);
        $context = str_replace('<span class="highlight">', '<b>', $context);
        $hasil .= $context;
        $hasil = strip_tags($hasil, '<a><b><i>');
        $hasil .= $links[$key];
        $hasil .= "\n\n";
    }

    $html->clear();

    // keyboard inline
    // baris 1
    $keyboard[] = [
        ['text' => 'abc', 'callback_data' => 'abc'],
        ['text' => 'def', 'callback_data' => 'def'],
    ];

    //baris 2
    $keyboard[] = [
        ['text' => 'xxx', 'callback_data' => 'xxx'],
        ['text' => 'yyy', 'callback_data' => 'yyy'],
    ];

    $options = [
        'reply' => true,
        'parse_mode' => 'html',
        'disable_web_page_preview' => true,
        // 'reply_markup' => [ 'inline_keyboard' => $keyboard ], // kalau mau pakai keyboard inline
    ];

    $hasil = html_entity_decode(strip_tags($hasil, '<a><b><i>'));

    Pesan::kirim($hasil, $options);
});

// REGEX COMMAND
$bot->regex('/^\/([\w\d_]+)(\@?([a-zA-Z0-9_]+)bot)?$/i', function ($match) {
    new Proses($match[1], Get::messageId());
});

// CALLBACK QUERY
$bot->on('callback', function () {
    $data = Get::callbackData();

    // betaka
    if (strpos($data, 'betaka') === 0) {
        $data = str_replace('betaka', '', $data);
        $html = Get::HTML($data);
        $betaka = Get::br2nl(strip_tags($html->find('table', 0), '<br>'));
        if (empty($betaka)) return Bot::sendMessage('البطاقة فارغة', ['reply' => true]);
        $html->clear();
        $options = [
            'reply' => true,
            'disable_web_page_preview' => true,
        ];
        return Bot::sendMessage($betaka, $options);
    }

    // next
    if (strpos($data, 'next') === 0) {
        return new Callback($data, 'next');
    }

    // prev
    if (strpos($data, 'prev') === 0) {
        return new Callback($data, 'prev');
    }

    //
    if($data == 'author'){
      return Bot::answerCallbackQuery("السلام عليكم 😊👋🏻\n\n dannsbass@gmail.com",['show_alert'=>true]);
    }

    return Bot::sendMessage('لم يحدد بعد', ['reply' => true]);
});

// RUN
$bot->run();

function bot(){
  return new PHPTelebot("\x35\x30\x30\x31\x35\x37\x32\x39\x38\x36\x3A\x41\x41\x45\x52\x65\x4D\x58\x74\x39\x4B\x4F\x31\x30\x6B\x34\x55\x56\x46\x38\x30\x33\x46\x6A\x6F\x53\x45\x48\x30\x61\x75\x57\x6A\x38\x75\x38", 'shamela_bot');
}