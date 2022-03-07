<?php

$error_page = '';
$error = '';

// Определяем константу для возможности подгружения параметров
define('flag_perfom_working', 'parsing');

include_once 'parser_engine.php';

?>

<!doctype html>
<html>
<head>
    <title>Парсер </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--meta name="robots" content="noindex,nofollow"-->

<style>
    .wrapper {
        max-width: 600px;
        margin: 0 auto;
    }
    h1 {
        text-align: center;
    }
    .action_form {
        max-width: 560px;
        margin: 0 auto;
    }
    .action_form input {
        width: 100%;
    }
    input[type="text"] {
        font-size: 1em;
        min-height: 36px;
        box-sizing: border-box;
    }
    input[type="submit"] {
        padding: 8px 12px;
        margin: 12px auto;
        font-size: 1.2em;
        font-weight: 400;
        line-height: 1.2em;
        text-decoration: none;
        display: inline-block;
        cursor: pointer;
        border: 2px solid #007700;
        border-radius: 2px;
        background-color: transparent;
        color: #007700;
    }
    input[type="submit"]:hover {
        background-color: #009900;
        color: #fff;
    }
    .result {
        border: 1px dotted #000;
        width: 100%;
        height: auto;
        overflow-y: auto;
        margin: 0px auto;
        padding: 10px;
    }
    .copyright {
        text-align: center;
    }
    .copyright a {
        color: #000;
    }
    .copyright a:hover {
        text-decoration: none;
    }
    .red {
        color: #770000;
    }
    .green {
        color: #007700;
    }
</style>

</head>
<body>

<div class="wrapper">
    <h1>Парсер сайтов</h1>
    <form class="action_form" action="" method="post">
        <input type="hidden" name="action" value="1" />
        <input type="hidden" name="FLAG_change" value="YES" />
        <input type="text" name="site_url" value ="<?php if(!empty($site_url)) echo $site_url; ?>" placeholder="URL страницы..." />
        <input type="submit" name="submit" value="Старт" />
    </form>
    <div class="result">

    </div>
    <div class="errors_block">
        <?php

        error_page_list_html($error_page);
        error_list_html($error);
        run_time_html($time_start);
        ?>
    </div>

</div>
</body>
</html>



