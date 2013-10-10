<?php
include "Spider.php";

$spider = new Spider();

$url = "http://news.163.com/13/1008/03/9AKPJROF00014AED.html?hl";
$matchTpl = '';
//$spider->addPage($url, $matchTpl);
$spider->addPage('http://www.php.net/manual/en/function.fsockopen.php', '');
$spider->addPage('http://www.php.net/manual/en/function.gethostbynamel.php', '');
$spider->addPage('http://www.adsame.com/', '');
$spider->run();
?>
