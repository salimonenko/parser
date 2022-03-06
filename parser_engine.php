<?php

//phpinfo();
//die();


//if(!defined('flag_perfom_working') || (flag_perfom_working != 'parsing')) {die("Access forbidden: ". basename(__FILE__));}

Error_Reporting(E_ALL & ~E_NOTICE);
mb_internal_encoding("UTF-8");
set_time_limit(0);    // Попытка установить своё время выполнения скрипта

header('Content-Type: text/html; charset=utf-8');

require_once 'curl_get_contents.php';
require_once 'error_functions.php';

/* --- 1 --- Инициализируем переменные для запроса */
$time_start = time();
$error = array();
$error_page = array();
$action = 0;
$site_url = "";
 $charset = "";    // Исходная кодировка страницы
$uni_name = date("d-m-Y-H-i-s", time());

/* --- 1.1 --- Переопределяем переменные на основе GET или POST параметров */
if(isset($_REQUEST['site_url'])){
    $site_url = trim($_REQUEST['site_url']);
}


if(isset($_REQUEST['action'])){
    $action = $_REQUEST['action'];
}

if(!function_exists('http_response_code')){
    require_once 'functions_PHP5_3/http_response_code.php';
}



/* --- 2 --- Получение контента из каталога site */
if($action) { // При отправке данных из формы после нажатия кнопки Старт
    if(!empty($site_url)) {
        $site_url = trim($site_url);

        $proto =  parse_url($site_url, PHP_URL_SCHEME);
        $domen = parse_url($site_url, PHP_URL_HOST);
            if(strtolower(substr($domen, 0, 4)) === 'www.'){
                $domen = substr($domen, 4);
            }


        $res_arr = get_site_price($site_url, $proto, $domen);

        $price_list = $res_arr['price_list'];
        $error_page = $res_arr['error_page'];
        $error = $res_arr['error'];
    } else {
        $error[] = "Не задан адрес страницы ";
    }
 }else{ // При переходе по ссылкам на спарсенной странице

    $site_url = isset($_REQUEST['URL']) ? $_REQUEST['URL'] : '';

    $proto =  parse_url($site_url, PHP_URL_SCHEME);
    $domen = parse_url($site_url, PHP_URL_HOST);
    if(strtolower(substr($domen, 0, 4)) === 'www.'){
        $domen = substr($domen, 4);
    }

    $res_arr = get_site_price($site_url, $proto, $domen);

 }






/* --- 1.3 --- Парсинг цены */
function get_site_price($site_url, $proto, $domen) {

    function cript($str){
//        $str = base64_encode($str);
//        $str = substr($str, 0, strlen($str)-2);

        return $str;
    }



    setlocale(LC_ALL, 'ru_RU');

    $base_domen = $domen;
    $FLAG_change = $_REQUEST['FLAG_change'];


    if(!preg_match("|^http:\/\/|", $site_url)){
        $site_url = $proto. '://'.$base_domen. $site_url;
    }

    $flag_header = 1; // Надо 1, иначе спарсенные файлы стилей не работают(!)
//  echo 'OK'; // Это если НЕ передавать заголовки ответа сервера, тогда хотя бы передать какие-то символы, иначе стили не подключаются

    $response_arr = curl_get_contents($site_url, $proto .'://'.$base_domen, 1, 1, $flag_header);

    $page = $response_arr['html'];
    $page_code = $response_arr['code'];
    $page_err = $response_arr['errors'];


    if(!empty($page) && ($page_code == 200)) {

        $this_dir = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
        $this_FILE = substr(__FILE__, strlen($_SERVER['DOCUMENT_ROOT']));
        $this_FILE = str_replace('\\', '/', $this_FILE);



        $base_domen_to_change = $_SERVER['HTTP_HOST']. $this_FILE. '?URL='. $proto. '://' .$base_domen;
        $base_domen_to_change = $_SERVER['HTTP_HOST']. $this_dir. "/base_url.php/?proto=". $proto. '&domen='.$domen. '&resourse=';

        if(strtolower(substr($base_domen_to_change, 0, 4)) === 'www.'){
            $base_domen_to_change = substr($base_domen_to_change, 4);
        }

        $base_domen_to_change_a = $_SERVER['HTTP_HOST']. $_SERVER['PHP_SELF']. '?URL='. $proto. '://' .$base_domen;
        if(strtolower(substr($base_domen_to_change_a, 0, 4)) === 'www.'){
            $base_domen_to_change_a = substr($base_domen_to_change_a, 4);
        }

        $base_domen_to_change_null = $_SERVER['HTTP_HOST']. $this_dir. "/null_url.php?URL=r";


        if($FLAG_change === 'YES' ){ // Если в контенте, спарсенном с сайта, следует сделать замены

        preg_match_all("|<head[^>]*>([\s\S]*?)<\/head>|", $page, $matches_h, PREG_PATTERN_ORDER); // Берем содержимое раздела <head>...</head>

        preg_match_all("|([\s\S]*?)<meta([^>]+?)(charset\s*=([^>]+?))>[\s\S]*?|", $matches_h[1][0], $matches, PREG_PATTERN_ORDER); // Находим все метатеги, в которых есть charset

        $search = array("/", "'", '"');
        $encoding_Arr = array();
        foreach ($matches[4] as $elem){
            $encoding_Arr[] = trim(str_replace($search, '', $elem));
        }
        $encoding = array_pop($encoding_Arr); // Выбираем кодировку из самого последнего метатега

        if($encoding != "UTF-8") {
            $page = iconv($encoding, "UTF-8//TRANSLIT", $page);
        }

        if(!preg_match("|(<head[^>]*>)([\s\S]*?)<base([\s\S]*?)<\/head>|", $page)){ // Вставляем тег <base ... />, если его еще нет
// Чтобы корректно скачивались ресурсы, имеющие относительные пути
            $page = preg_replace("|(<head[^>]*>)([\s\S]*?)<\/head>|", "$1". '<base href="http://'. $_SERVER['HTTP_HOST']. $this_dir. '/base_url.php/'.$proto.'/'.$domen .'/" />' ."$2"."</head>", $page);
        }

// Делаем замену
        $page = preg_replace("|(<link\s[^>]*?)". preg_quote($base_domen). "([^>]*?>)|", '$1'. $base_domen_to_change. '$2', $page); // link абсолютный-href


            $page = preg_replace("|(<a\s[^>]*?)". preg_quote($base_domen). "([^>]*?>)|", '$1'. $base_domen_to_change_a . '$2', $page); // a



        $page = preg_replace("|(<script\s[^>]*?)". preg_quote($base_domen). "([^>]*?>)|", '$1'. $base_domen_to_change. '$2', $page); // script
        $page = preg_replace("|(<img\s[^>]*?)". preg_quote($base_domen). "([^>]*?>)|", '$1'. $base_domen_to_change. '$2', $page); // img

        $page = preg_replace("|(https?:\/\/)([\w]+\.)*". preg_quote('yandex.'). "[\w]+|i", 'http://'. $base_domen_to_change_null. '', $page); // Убираем ссылки на yandex
        $page = preg_replace("|(https?:\/\/)([\w]+\.)*". preg_quote('google.'). "[\w]+|i", 'http://'. $base_domen_to_change_null. '', $page); // Убираем ссылки на google

        echo $page;

//        file_put_contents('results.html', $page );

        }elseif ($FLAG_change === 'NO'){
            echo $page;
        }

    } else {
        http_response_code($page_code); // Устанавливаем код ответа Bad request: на сервере, с которого производится парсинг, нет такого файла или он недоступен
        echo 'Cannot find such file on parsed server: ';
        error_page_list_html($page_err);
    }

    return $page_err;
 }









