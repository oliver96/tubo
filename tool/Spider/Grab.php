<?php
include "Spider.php";

date_default_timezone_set('Asia/Shanghai');

$spider = new Spider();


$spider->addPage(
    'http://wei.sohu.com/roll/' // 列表地址
    , "|<a test=a href='(.+?)' target='_blank'>(.+?)</a>|is" // 地址匹配
    , '|<div itemprop=\"articleBody\">(.+?)<div style=\"display:none;\">|is' // 内容匹配
    , array('|<div class=\"text-pic\">(.+?)<\/div>|is', '|<script[^\>]*>(.+?)<\/script>|is') // 排除内容
);

/*
$spider->addPage(
    'http://wei.sohu.com/20131009/n387828430.shtml'
);
*/
$spider->run();
?>
