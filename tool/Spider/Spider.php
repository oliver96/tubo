<?php
include "lib/HttpClient.php";

class Spider {
    private $pages = array();

    public function addPage($url, $matchTpl) {
        $pageIndex = md5($url);
        if(!isset($this->pages[$pageIndex])) {
            $this->pages[$pageIndex] = array(
                'url' => $url,
                'tpl' => $matchTpl
            );
        }
        else {
            echo sprintf("URL: %s has added\r\n", $url);
        }
    }

    public function run() {
        if(!empty($this->pages)) {
            $httpClient = new HttpClient();
            foreach($this->pages as $pageInfo) {
                echo "Load " . $pageInfo['url'] . "\r\n";
                $content = $httpClient->get($pageInfo['url']);
                //print_r($httpClient->getHeaders());
                //print_r($httpClient->getCookies());
                echo "\r\n";
                echo $content;
            }
        }
    }

}
?>
