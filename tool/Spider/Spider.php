<?php
include "lib/HttpClient.php";
include "lib/Database.php";

class Spider {
    private $pages = array();

    public function addPage($url, $listTpl, $contentTpl) {
        $pageIndex = md5($url);
        if(!isset($this->pages[$pageIndex])) {
            $this->pages[$pageIndex] = array(
                'url' => $url,
                'listTpl' => $listTpl,
                'contentTpl' => $contentTpl
            );
        }
        else {
            echo sprintf("URL: %s has added\r\n", $url);
        }
    }

    public function run() {
        if(!empty($this->pages)) {
            $connect = Database::connect();
            $httpClient = new HttpClient();
            foreach($this->pages as $pageInfo) {
                echo "Load " . $pageInfo['url'] . "\r\n";
                $listContent = $httpClient->get($pageInfo['url']);
                if(!empty($listContent)) {
                    $this->convert($listContent);
                }
                
                if(!empty($pageInfo['listTpl'])
                    && preg_match_all($pageInfo['listTpl'], $listContent, $matches, PREG_SET_ORDER)) {
                    if(!empty($matches)) {
                        foreach($matches as $matchRow) {
                            $title = $matchRow['2'];
                            $url = $matchRow['1'];
                            $content = $httpClient->get($url);
                            $this->convert($content);
                            if(preg_match($pageInfo['contentTpl'], $content, $match)) {
                                $content = $match[1];
                            }
                            $connect->query("INSERT INTO `news` SET `title`='$title'");
                        }
                    }
                }
            }
        }
    }
    
    private function convert(& $content) {
        if(preg_match("|charset=(\w+)|", $content, $match)) {
            if(!empty($match) && 'UTF-8' != $match[1]) {
                $inputEncode = strtoupper($match[1]);
                if('GB2312' == $inputEncode) $inputEncode = 'GBK';
                $content = iconv($inputEncode, 'UTF-8', $content);
            }
        }
    }
}
?>
