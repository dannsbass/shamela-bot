<?php

namespace Danns\Bot;

use Bot;
use simple_html_dom;

class Pesan
{
  public static $respon = '';
    public static function kirim(string $isi, $options = null)
    {
        if (empty($isi)) return Bot::sendMessage('معذرة، لا توجد نتيجة', ['reply' => true]);
        if (strlen($isi) <= 4096) return Bot::sendMessage($isi, $options);
        $pecahan = self::potong($isi, 4096);
        foreach ($pecahan as $no => $teks) {
            $teks = self::cekTag($teks,'a,b,i');
            $pilihan = $options;
            // $respon = '';
            if($no === 0){
                unset($pilihan['reply_markup']);
                self::$respon .= Bot::sendMessage($teks, $pilihan);
            }elseif($no < (count($pecahan) - 1)) {
                unset($pilihan['reply']);
                unset($pilihan['reply_markup']);
                self::$respon .= Bot::sendMessage($teks, $pilihan);
            }else{
                unset($options['reply']);
                self::$respon .= Bot::sendMessage($teks, $options);
            }
        }
        return self::$respon;
    }

    public static function potong(string $text, int $jml_kar)
    {
        $panjang = strlen($text);
        $ke = 0;
        $pecahan = [];
        while ($panjang > $jml_kar) {
            $no = $jml_kar;
            $karakter = $text[$no];
            while ($karakter != ' ' and $karakter != "\n" and $karakter != "\r" and $karakter != "\r\n") {
                $karakter = $text[--$no];
            }
            $pecahan[] = substr($text, 0, $no);
            $panjang = strlen($pecahan[$ke]);
            $text = trim(substr($text, $panjang));
            $panjang = strlen($text);
            $ke++;
        }
        return array_merge($pecahan, array($text));
    }

    public static function cekTag(string $value, string $tags)
    {
        $html = (new simple_html_dom())->load($value);
        foreach ($html->find($tags) as $k => $v) {
            if (preg_match_all('/\<([^\>]+)\>/i', $v->outertext, $c)) {
                // jika tidak simetris, berarti ada tag yang tidak tertutup
                if (count($c[0]) % 2 !== 0) {
                    // cari posisinya
                    $pos = strrpos($value, $v->outertext);
                    // ambil bagian terakhir
                    $bagian_akhir = substr($value, $pos);
                    $bagian_awal = substr($value, 0, $pos);
                    if ($bagian_akhir === $v->outertext) {
                        // tambahi tag penutup di akhir
                        $v->outertext = $v->outertext . '</' . $v->tag . '>';
                        $value = $bagian_awal . $v->outertext;
                        return $value;
                        break;
                    }
                }
            }
        }
        // jika ada tag penutup yang tidak tidak diawali dengan tag pembuka
        if (preg_match('/\<\/(\s+)?([^\>]+)(\s+)?\>/i', $value, $c)) {
            $awal = substr($value, 0, strpos($value, $c[0]));
            if ( strpos($awal, '<' . $c[2]) === false ) {
                $value = '<' . $c[2] . '>' . $value;
            }
        }
        return $value;
    }
}
