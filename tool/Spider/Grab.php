<?php
include "Spider.php";

$spider = new Spider();

$url = "http://news.163.com/13/1008/03/9AKPJROF00014AED.html?hl";
$matchTpl = '';
$spider->addPage($url, $matchTpl);

$spider->run();
?>