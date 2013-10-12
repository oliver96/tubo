<?php
include "lib/HttpClient.php";
include "lib/Database.php";

class Spider {
    private $pages = array();

    public function addPage($url, $listTpl, $contentTpl, $trimTpl) {
        $pageIndex = md5($url);
        if(!isset($this->pages[$pageIndex])) {
            $this->pages[$pageIndex] = array(
                'url' => $url,
                'listTpl' => $listTpl,
                'contentTpl' => $contentTpl,
                'trimTpl' =>$trimTpl
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
                            // 提取内容
                            if(preg_match($pageInfo['contentTpl'], $content, $match)) {
                                $content = $match[1];
                                if(is_array($pageInfo['trimTpl'])) {
                                    foreach($pageInfo['trimTpl'] as $tpl) {
                                        $content = preg_replace($tpl, '', $content);
                                    }
                                }
                                else {
                                    $content = preg_replace($pageInfo['trimTpl'], '', $content);
                                }
                                $content = $this->cutstrHtml($content);
                                echo $content;
                            }
                            // 提取内容相关的图片
                            $imageUrls = array();
                            if(!empty($content) 
                                && preg_match_all('|<img.*src="(.+?)".*>|is', $content, $imageMatches, PREG_SET_ORDER)) {
                                if(!empty($imageMatches)) {
                                    foreach($imageMatches as $imageMatch) {
                                        $imageTag = $imageMatch[0];
                                        $imageUrl = $imageMatch[1];
                                        
                                        //$content = str_replace($imageTag, '[IMAGE_URL]', $content);
                                        
                                        $imageBin = $httpClient->get($imageUrl);
                                        $path = "../../thumbnail/";
                                        $prefix = date('Y') . '/' . date('m') . '/' . date('d') . '/';
                                        $path .= $prefix;
                                        $fileName = substr($imageUrl, strrpos($imageUrl, '/') + 1);
                                        $this->mkdir($path);
                                        $fp = fopen($path . $fileName, "w+");
                                        fwrite($fp, $imageBin);
                                        fclose($fp);
                                        
                                        $imageUrls[] = $prefix . $fileName;;
                                    }
                                }
                            }
                            // 替换html标签
                            $content = $this->cutstrHtml($content);
                            $sql = sprintf("INSERT INTO `news` SET `title`='%s', `summary`='%s', `thumbnail`='%s', `url`='%s', `dateline`='%s'", $title, $content, implode(',', $imageUrls), $url, date('Y-m-d H:i:s'));
                            $connect->query($sql);
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
    
    private function mkdir($path, $mode = 0777) {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $dirs = explode(DIRECTORY_SEPARATOR , $path);
        $count = count($dirs);
        $path = '.';
        for ($i = 0; $i < $count; ++$i) {
            $path .= DIRECTORY_SEPARATOR . $dirs[$i];
            if (!is_dir($path) && !mkdir($path, $mode)) {
                return false;
            }
        }
        return true;
    }
    
    function cutstrHtml($string, $length = 0, $ellipsis = '…') {
        $string = strip_tags($string);
        $string = preg_replace('/\n/is', '', $string);
        $string = preg_replace('/ |　/is', '', $string);
        $string = preg_replace('/&nbsp;/is', '', $string);
        $string = preg_replace('/&nbsp;/is', '', $string);
        $string = preg_replace('/<\/?[^>]+>/i', '', $string);
        $string = preg_replace('/<\/?[^>]+>/i', '', $string);
        $string = preg_replace('/<!--.+-->/i', '', $string);
        preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $string, $string);
        if(is_array($string) && !empty($string[0])) {
            if(is_numeric($length) && $length > 0) {
                $string = join('', array_slice($string[0], 0, $length)) . $ellipsis;
            }
            else {
                $string = implode('', $string[0]);
            }
            $string = preg_replace('/\s/is', '', $string);
        }
        else {
            $string = '';
        }
        return $string;
    }
}
?>
