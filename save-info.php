<?php
$data = array(
    'partner_login'     => $_REQUEST['partner_login'],
    'partner_password'  => $_REQUEST['partner_password'],
    'response_to'       => $_REQUEST['response_to'],
    'error_response_to' => $_REQUEST['error_response_to'],
    'news_title'        => $_REQUEST['news_title'],
    'news_description'  => $_REQUEST['news_description'],
    'news_category'     => $_REQUEST['news_category'],
    'news_url'          => $_REQUEST['news_url'],
    'news_image'        => $_REQUEST['news_image'],
    'news_has_video'    => $_REQUEST['news_has_video'],
);

$file = 'tmp/info.dat';
fclose(fopen($file, "a+b"));
$f = fopen($file, "r+t");
flock($f, LOCK_EX);
ftruncate($f, 0);
fseek($f, 0, SEEK_SET);
fwrite($f, serialize($data));
fclose($f);
