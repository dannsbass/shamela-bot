<?php

namespace Danns\Bot;

use Danns\Bot\Get;
use Danns\Bot\Pesan;

class Proses
{

    private $data, $msg_id, $respon;
    public function __construct($data, $msg_id)
    {
        $this->data = $data;
        $this->msg_id = $msg_id;

        $html = Get::HTML($this->data);
        $url = Get::dataToURL($data);
        \preg_match('/\d+$/', $data, $cocok);
        $data_baru = substr($data,0,-(strlen($cocok[0])));
        $next = $data_baru . ((int)$cocok[0] + 1);
        $prev = $data_baru . ((int)$cocok[0] - 1);
        
        // $isi = strip_tags(($html->find('p[id=page_content]', 0))->innertext, '<a><b><i>');
        // $isi = iconv('windows-1256', 'utf-8', $isi);
        // $isi = str_replace('ـــــــــــــــــــــــــــــ',"\n\n",$isi);

        $isi = $html->find('p[id=page_content]');
        foreach ($isi as $key => $value) {
            $fonts = $value->find('font');
            foreach ($fonts as $k => $v) {
                $v->tag = 'b';
            }
        }
        
        $isi = strip_tags($isi[0]->innertext,'<a><b><i>');
        $isi = iconv('windows-1256', 'utf-8', $isi);
        $isi = str_replace('ـــــــــــــــــــــــــــــ',"\n\n",$isi);


        $keyboard[] = [
            ['text' => 'التالية⬅️',          'callback_data' => 'next' . $next . "@" . $this->msg_id],
            ['text' => '➡️السابقة',          'callback_data' => 'prev' . $prev . "@" . $this->msg_id],
        ];

        $keyboard[] = [
            ['text' => '📔بطاقة الكتاب', 'callback_data' => 'betaka' . $data],
        ];

        $keyboard[] = [
            ['text' => '🔗الرابط',          'url' => $url],
        ];

        $keyboard[] = [
            ['text' => '😎المصمم',          'callback_data' => 'author'],
        ];

        $options = [
            'reply' => true,
            'parse_mode' => 'html',
            'disable_web_page_preview' => true,
            'reply_markup' => ['inline_keyboard' => $keyboard],
        ];

        $this->respon = Pesan::kirim($isi, $options);
    }

    public function getString(){
      return $this->respon;
    }
}