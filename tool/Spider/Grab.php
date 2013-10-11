<?php
include "Spider.php";

$spider = new Spider();


$spider->addPage(
    'http://wei.sohu.com/roll/' // 列表地址
    , "|<a test=a href='(.+?)' target='_blank'>(.+?)</a>|is" // 地址匹配
    , '|<div itemprop=\"articleBody\">(.+?)</div>|is' // 内容匹配
);

/*
$spider->addPage(
    'http://wei.sohu.com/20131009/n387828430.shtml'
);
*/
$spider->run();
?>
