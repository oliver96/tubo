<?php
class HttpClient {
    private $version = 'HTTP/1.1';
    private $url = '';
    private $urlAry = array();
    private $timeout = 30;
    private $reqHeaders = array();
    private $resHeaders = array();
    private $cookies = array();
    private $fps = array();

    public function HttpClient($timeout = 30) {
        $this->timeout = $timeout;
    }
    
    public function setHeader($head, $value) {
        //if(!isset($this->reqHeaders[$head])) {
            $this->reqHeaders[$head] = $value;
        //}
    }
    
    private function initHeaders() {
        $this->reqHeaders = array(
            'Cache-Control' => 'max-age=0',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.66 Safari/537.36',
            'Accept-Encoding' => 'gzip,deflate,sdch',
            'Accept-Language' => 'zh-CN,zh;q=0.8',
            'Connection' => 'keep-alive'
        );
    }

    public function get($url) {
        $this->initHeaders();
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
        if(isset($this->cookies[$host])) {
            foreach($this->cookies[$host] as $cookie) {
                $expires = '';
                if(isset($cookie['expires'])) {
                    $expires = $cookie['expires'];
                    unset($cookie['expires']);
                }
                $path = '/';
                if(isset($cookie['path'])) {
                    $path = $cookie['path'];
                    unset($cookie['path']);
                }
                $domain = '';
                if(isset($cookie['domain'])) {
                    $domain = $cookie['domain'];
                    unset($cookie['domain']);
                }
                if(!empty($cookie)) {
                    $tmpCookies = array();
                    foreach($cookie as $name => $value) {
                        $tmpCookies[] = sprintf("%s=%s", $name, $value);
                    }
                    $this->setHeader('Cookie', implode('; ', $tmpCookies));
                }
            }
        }

        $reqHeader = sprintf("GET %s %s \r\n", $path, $this->version);
        if(!empty($this->reqHeaders)) {
            foreach($this->reqHeaders as $head => $value) {
                $reqHeader .= sprintf("%s: %s\r\n", $head, $value);
            }
        }
        echo $reqHeader . "\r\n";
        
        fwrite($this->fps[$host], $reqHeader . "\r\n");
        $cache = '';
        $buf = '';
        $lastCh = '';
        $response = array();
        $isHeader = false;
        $isBody = false;
        $isChunked = false;
        $isChunkBody = false;
        $chunkSize = 0;
        $cotentLength = 0;
        while(!feof($this->fps[$host])) {
            if(false == $isBody) {
                $ch = fgetc($this->fps[$host]);
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
                                    $contentLength = intval($value);
                                break;
                                case 'transfer-encoding' : 
                                    $isChunked = $value == 'chunked' ? true : false;
                                break;
                                case 'set-cookie' :
                                    $cookieValues = explode(';', $value);
                                    if(!empty($cookieValues)) {
                                        if(isset($this->cookies[$host])) {
                                            $this->cookies[$host] = array();
                                        }
                                        $cookie = array();
                                        foreach($cookieValues as $cookieValue) {
                                            $pos = strpos($cookieValue, '=');
                                            $cname = trim(substr($cookieValue, 0, $pos));
                                            $cvalue = trim(substr($cookieValue, $pos + 1));
                                            $cookie[$cname] = $cvalue;
                                        }
                                        $this->cookies[$host][] = $cookie;
                                    }
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
                    if(false == $isChunkBody) {
                        $ch = fgetc($this->fps[$host]);
                        $buf .= "\r" == $ch || "\n" == $ch ? '' : $ch;
                        if("\r" == $lastCh && "\n" == $ch) {
                            $chunkSize = (integer)hexdec(trim($buf));
                            if($chunkSize <= 0) {
                                fseek($this->fps[$host], 2, SEEK_CUR);
                                break;
                            }
                            $isChunkBody = true;
                            $buf = '';
                        }
                        $lastCh = $ch;
                    }
                    else if($chunkSize > 0) {
                        $content .= fgetc($this->fps[$host]);
                        $chunkSize --;
                    }
                    else {
                        fseek($this->fps[$host], 2, SEEK_CUR);
                        $isChunkBody = false;
                    }
                }
                else {
                    if($contentLength > 0) {
                        $content .= fgetc($this->fps[$host]);
                        $contentLength --;
                    }
                    else {
                        fseek($this->fps[$host], 2, SEEK_CUR);
                        break;
                    }
                }
            }
        }
        
        if('gzip' == $this->resHeaders['Content-Encoding']) {
            $content = gzdecode($content);
        }
        
        return $content;
    }
    
    public function getHeaders() {
        return $this->resHeaders;
    }
    
    public function getCookies() {
        return $this->cookies;
    }
}
?>
