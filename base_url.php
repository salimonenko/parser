<?php

require_once 'curl_get_contents.php';
require_once 'error_functions.php';

if(!function_exists('http_response_code')){
    require_once 'functions_PHP5_3/http_response_code.php';
}



// echo 'QUERY_STRING='. $_SERVER['QUERY_STRING']. ' PATH_INFO='. $_SERVER['PATH_INFO']. ' ';

if(isset($_REQUEST['proto']) && (strlen($_REQUEST['proto']) < 10)){
    $proto = $_REQUEST['proto'];
}else{
    $proto = '';
}

if(isset($_REQUEST['domen']) && (strlen($_REQUEST['domen']) < 100)){
    $domen = $_REQUEST['domen'];
}else{
    $domen = '';
}

if(isset($_REQUEST['resourse']) && (strlen($_REQUEST['resourse']) < 300)){
    $resourse = $_REQUEST['resourse'];
}else{
    $resourse = '';
}

// Если значения $_REQUEST['proto'] (и т.д.) - неправильные или пустые, пытаемся разобрать PATH_INFO
if($proto === ''){

    if(isset($_SERVER['PATH_INFO']) && (strlen($_SERVER['PATH_INFO'] < 300))){

    $PATH_INFO_Arr = explode('/', $_SERVER['PATH_INFO']);

    $proto = $PATH_INFO_Arr[1];
    $domen = $PATH_INFO_Arr[2];
    $resourse = $PATH_INFO_Arr[3];
    }else{
        http_response_code(413); // Устанавливаем код ответа Bad request: на сервере, с которого производится парсинг, нет такого файла или он недоступен
        echo '$_SERVER[\'PATH_INFO\'] is not existed or too long';
        die('');
    }
 }

if(substr($resourse, 0, 1) == '/'){
    $resourse = substr($resourse, 1);
}

$proto_domen = $proto. '://'. $domen;


$site_url = $proto_domen. '/' .$resourse;
// die($site_url);



$flag_header = 0;
$response_arr = curl_get_contents($site_url, $proto_domen, 1, 1, $flag_header);

$page = $response_arr['html'];
$page_code = $response_arr['code'];
$page_err = $response_arr['errors'];


$Accept_HTTP_header_initial = 'text/html'; // Изначально, если нижеприведенные проверки дадут отрицательный результат (например, если заголовка Accept: вообще нет в сообщении клиента или он некорректный)
    $Accept_HTTP =  isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : ''; // Заголовок запроса, содержащий MIME-тип(ы)
    $MIME = MIME_determine($Accept_HTTP, $Accept_HTTP_header_initial);

    if(!empty($page) && ($page_code == 200)){ // Если все хорошо и есть контент для отправки клиенты

        header('Content-Type: '.  $MIME); // Вначале отправлем заголовок с типом сообщения
        echo $page; // А потом отправляем само сообщение (контент)

        error_page_list_html($page_err); // Если есть ошибки
//        error_list_html($error);
        run_time_html($time_start);

    } else {

        http_response_code($page_code); // Устанавливаем код ответа и выводим его в браузер
        echo 'There is some problem with this file on parsed server: ';
        error_page_list_html($page_err);
    }



// Функция определяет MIME-тип для ответа клиенту в заголовке Accept:
function MIME_determine($Accept_HTTP, $Accept_HTTP_header_initial){
    $Accept_HTTP_header = $Accept_HTTP_header_initial;
        if($Accept_HTTP){
            $MIME_permitted_Arr = explode(';', $Accept_HTTP);
            $MIME_permitted = explode(',', $MIME_permitted_Arr[0]);

                foreach ($MIME_permitted as $elem){
                    if(stripos($elem, '*/*') !== false){
                        $Accept_HTTP_header = 'text/plain'; // Если подойдет любой тип, то устанавливаем text/plain
                    }else{
                        if(stripos($elem, '*') === false){ // Если в MIME-типе нет символа *, то значит такой тип можно использовать для отправки в заголовке сервера (перед ответом). Если такой символ есть - переходим к следующей  итерации цикла
                            $Accept_HTTP_header = $elem;
                        }
                    }
                }
            }
return $Accept_HTTP_header;
 }

function cript_decode($x){
//    return base64_decode($x);
    return $x;
}


