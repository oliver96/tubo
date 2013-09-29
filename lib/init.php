<?php
require("functions.php");

// 载入基本类
import("com.zcx.core.Action");
import("com.zcx.core.Model");
import("com.zcx.core.Form");
import("com.zcx.core.Request");
import("com.zcx.core.Response");
import('com.zcx.lang.Message');
import("com.zcx.lang.ServerUtils");

/**
 * 核心基类，用于保存一些常用变量，方法
 */
class Core {
    public static $constants = array();
    
    /**
     * 生成url
     *
     * @param array $params URL参数
     */
    public static function url($params = array()) {
        $config = c('CONFIG');
        return $config->getContext()->url($params);
    }
    
    /**
     * 连接数据库
     * 
     * @return 返回数据库链接
     */
    public static function & connect() {
        $config     = c('CONFIG');
        $context    = $config->getContext();
        $driverName = $context->getDriverName();
        $driverUrl  = $context->getDriverUrl();
        
        if(!empty($driverName)) {
            $dbLinks    = c('DB_LINKS');
            
            if(!isset($dbLinks[$driverName])) {
                // 注册数据库驱动
                DriverManager::registerDriver($driverName);
                
                // 获取数据库操作连接
                $connection             = DriverManager::getConnection($driverUrl);
                $dbLinks[$driverName]   = ref($connection);
                
                def('DB_LINKS', ref($dbLinks));
            }
            return $dbLinks[$driverName];
        }
        else {
            $ret = null;
        }
        return $ret;
    }
    
    /**
     * 打开事务处理
     */
    public static function beginTrans() {
        $enabled = c('TRANSACTION');
        if(!$enabled) {
            def('TRANSACTION', 1);
            
            $con = Core::connect();
            $con->beginTrans();
        }
    }
    
    /**
     * 回退当前数据操作，结合Core::beginTrans或beginTrans使用
     */
    public static function rollback() {
        $enabled = c('TRANSACTION');
        if($enabled) {
            def('TRANSACTION', 0);
            
            $con = Core::connect();
            $con->rollback();
        }
    }
    
    /**
     * 提交数据操作，结合Core::beginTrans或beginTrans使用
     */
    public static function commit() {
        $enabled = c('TRANSACTION');
        if($enabled) {
            def('TRANSACTION', 0);
        
            $con = Core::connect();
            $con->commit();
        }
    }
    
    /**
     * 关闭数据库联接
     */
    public static function close() {
        $con = Core::connect();
        $con->close();
        
        $config     = c('CONFIG');
        $context    = $config->getContext();
        $driverName = $context->getDriverName();
        
        if(!empty($driverName)) {
            $dbLinks    = c('DB_LINKS');
            if(!isset($dbLinks[$driverName])) {
                $dbLinks[$driverName] = null;
                unset($dbLinks[$driverName]);
                def('DB_LINKS', ref($dbLinks));
            }
        }
    }
}
?>
