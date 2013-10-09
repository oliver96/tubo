<?php
class HttpClient {
    private $version = 'HTTP/1.1';
    private $url = '';
    private $urlAry = array();
    private $timeout = 30;
    private $reqHeaders = array(
        'Cache-Control' => 'max-age=0',
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.66 Safari/537.36',
        'Accept-Encoding' => 'gzip,deflate,sdch',
        'Accept-Language' => 'zh-CN,zh;q=0.8',
        'Connection' => 'keep-alive'
    );
    private $resHeaders = array();
    private $cookies = array();
    private $fps = array();
    private $contentLength = 0;

    public function HttpClient($timeout = 30) {
        $this->timeout = $timeout;
    }
    
    public function setHeader($head, $value) {
        if(!isset($this->reqHeaders[$head])) {
            $this->reqHeaders[$head] = $value;
        }
    }

    public function get($url) {
        $content = '';
        $urlAry = $this->parseUrl($url);
        if(!empty($urlAry)) {
            $this->urlAry = $urlAry;
            $content = $this->getContent();
        }
        return $content;
    }

    private function parseUrl($url) {
        $urlAry = array();

        if(!empty($url)) {
            $urlAry = @parse_url($url);
        }

        return $urlAry;
    }

    private function getContent() {
        $content = '';
        $host = $this->urlAry['host'];
        $port = isset($this->urlAry['port']) ? $this->urlAry['port'] : 80;

        if(!isset($this->fps[$host])) {
            $this->fps[$host] = fsockopen($host, $port, $errno, $errmsg, $this->timeout);
        }

        if(!$this->fps[$host]) {
            echo sprintf("Connect to '%s' was failed(%s:%s)\r\n", $host, $errno, $errmsg);
            return ;
        }
        
        $this->setHeader('Host', $host);
        $path = isset($this->urlAry['path']) ? $this->urlAry['path'] : '/';

        $reqHeader = sprintf("GET %s %s \r\n", $path, $this->version);
        if(!empty($this->reqHeaders)) {
            foreach($this->reqHeaders as $head => $value) {
                $reqHeader .= sprintf("%s: %s\r\n", $head, $value);
            }
        }
        
        fwrite($this->fps[$host], $reqHeader . "\r\n");

        $buf = '';
        $lastCh = '';
        $response = array();
        $isHeader = false;
        $isBody = false;
        $isChunked = false;
        $isChunkBody = false;
        $chunkSize = 0;
        while(!feof($this->fps[$host])) {
            $ch = fgetc($this->fps[$host]);
            if(false == $isBody) {
                $buf .= "\r" == $ch || "\n" == $ch ? '' : $ch;
                if("\r" == $lastCh && "\n" == $ch) {
                    if(false == $isHeader) { // response line
                        list($httpVer, $httpStatus, $httpMsg) = explode(" ", $buf, 3);
                        $isHeader = true;
                    }
                    else { // reponse header
                        if('' == $buf) {
                            $isBody = true;
                            if(320 == $httpStatus) {
                                $content = $this->get($this->resHeaders['Location']);
                                break;
                            }
                        }
                        else {
                            $commaPos = strpos($buf, ':');
                            $head = trim(substr($buf, 0, $commaPos));
                            $value = trim(substr($buf, $commaPos + 1));
                            $this->resHeaders[$head] = $value;
                            switch(strtolower($head)) {
                                case 'content-length' : 
                                    $this->contentLength = intval($value);
                                break;
                                case 'transfer-encoding' : 
                                    $isChunked = $value == 'chunked' ? true : false;
                                break;
                            }
                        }
                    }
                     
                    $buf = '';
                }
                $lastCh = $ch;
            }
            else {
                if(true == $isChunked) {
                    if("\r" == $lastCh && "\n" == $ch) {
                        if($content) echo gzdecode($content);
                        $chunkSize = (integer)hexdec(trim($buf));
                        echo " chunk size: |" . $chunkSize . "|\r\n\r\n";
                        if($chunkSize == 0) break;
                        $isChunkBody = true;
                        $buf = '';
                    }
                    else if(true == $isChunkBody) {
                        if($chunkSize <= 0) {
                            $isChunkBody = false;
                            continue;
                        }
                        $content .= $ch;
                        $chunkSize --;
                    }
                    else {
                        $buf .= $ch;
                        $lastCh = $ch;
                    }
                }
                else {
                    if($this->contentLength <= 0) {
                        break;
                    }
                    $content .= $ch;
                    $this->contentLength --;
                }
            }
        }
        print_r($this->resHeaders);
        if('gzip' == $this->resHeaders['Content-Encoding']) {
            //echo $content;
            $content = gzdecode($content);
        }
        
        return $content;
    }
}
?>