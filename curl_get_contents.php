<?php

/* --- 1.2 --- Загрузка страницы при помощи cURL */
function curl_get_contents($page_url, $base_domen, $pause_time, $retry, $flag_header) {
    /*
    $page_url - адрес страницы-источника
    $base_domen - адрес страницы для поля REFERER
    $pause_time - пауза между попытками парсинга
    $retry - 0 - не повторять запрос, 1 - повторить запрос при неудаче
    */



$headers = array(
'cache-control: max-age=0',
'upgrade-insecure-requests: 1',
'user-agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36',
'sec-fetch-user: ?1',
'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/png,*/*;q=0.8,application/signed-exchange;v=b3',
'x-compress: null',
    'Content-Type: text/plain',
'sec-fetch-site: none',
'sec-fetch-mode: navigate',
'accept-encoding: deflate, br',
'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
);




    $error_page = array();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0");
    //curl_setopt($ch, CURLOPT_COOKIEJAR, str_replace("\\", "/", getcwd()).'/site.txt');
    //curl_setopt($ch, CURLOPT_COOKIEFILE, str_replace("\\", "/", getcwd()).'/site.txt');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Автоматом идём по редиректам
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0); // Не проверять SSL сертификат
    curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0); // Не проверять Host SSL сертификата
    curl_setopt($ch, CURLOPT_URL, $page_url); // Куда отправляем (загружаемый URL)
    curl_setopt($ch, CURLOPT_REFERER, $base_domen); // Откуда пришли
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);


    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_HEADER, $flag_header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Возвращаем, но не выводим на экран результат
    $response['html'] = curl_exec($ch);
    $info = curl_getinfo($ch);
    if($info['http_code'] != 200 ) {
        $error_page[] = array(1, $page_url, $info['http_code']);
        if($retry) {
            sleep($pause_time);
            $response['html'] = curl_exec($ch);
            $info = curl_getinfo($ch);
            if($info['http_code'] != 200 )
                $error_page[] = array(2, $page_url, $info['http_code']);
        }
    }

    $response['code'] = $info['http_code'];
    $response['errors'] = $error_page;
    curl_close($ch);
    return $response;
}






