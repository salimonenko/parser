<?php

// Сюда перенаправляется запрос, если соответствующий ресурс НЕ нужно показывать на странице

if(!function_exists('http_response_code')){
    require_once 'functions_PHP5_3/http_response_code.php';
}

http_response_code(424 ); // Устанавливаем код ответа и выводим его в браузер
echo 'This content is not necessary:  Failed Dependency';

die();
