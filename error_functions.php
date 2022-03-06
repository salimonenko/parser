<?php


/* --- Вывод ошибок */
function error_list_html($error) {
    if (!empty($error)) {
        echo "<p>Во время обработки запроса произошли следующие ошибки:</p>\n";
        echo "<ul>\n";
        foreach($error as $error_row) {
            echo "<li>" . $error_row . "</li>\n";
        }
        echo "</ul>\n";
        echo "<p>Статус: <span class=\"red\">FAIL</span></p>\n";
    } else {
        echo "<p>Статус: <span class=\"green\">OK</span></p>\n";
    }
}
/* --- Вывод ошибок загрузки страниц */
function error_page_list_html($error_page) {
    if (!empty($error_page)) {
        echo "<ul>\n";
        foreach($error_page as $error_row) {
            echo "<li>[" . $error_row[0] . "] " . $error_row[1] . " - " . $error_row[2] . "</li>\n";
        }
        echo "</ul>\n";
    }
}
/* --- Вывод работы скрипта */
function run_time_html($time_start) {
    if(!empty($time_start))
        echo "<!--p>Время работы: " . (time() - $time_start) . "</p-->\n";
}




