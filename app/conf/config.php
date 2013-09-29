<?php
return array(
    /**
     * 基本设置
     *==========================================================================
     */
     
    // 应用名称
    'appName'       => 'Web Test Frame',
    // 应用目录
    'appDir'        => '/app',
    
    // URL 模式：URL_STANDARD，URL_REWRITE URL_PATHINFO, URL_REST
    'urlMode'       => URL_REST,
    
    /**
     * 数据源设置
     *==========================================================================
     */
     
    // 数据驱动名称
    'driverName'    => 'mysql',
    // 数据驱动链接
    'driverUrl'     => 'mysql://localhost/samedata_new?user=root&pass=point9*',
    // 数据库连接用户
    'dbUser'        => '',
    // 数据库连接认证密码
    'dbPass'        => '',
    
    // 是否开启应用分组
    'group'         => true,
    // 默认分组名称
    'groupName'     => 'front',

    /*
    'moduleName'    => 'adplan',
    'actionName'    => 'save',
    */
    
    /**
     * 输出设置
     *==========================================================================
     */
     
    // 输出编码
    'charset'       => 'utf-8',
    // 视图输出文件扩展名称
    //'viewExt'       => '.phtml',
    
    /**
     * 用户认证设置
     *==========================================================================
     */
     
     // 是否开启session功能
    'enableSession' => true,
    
    
    /**
     * 其它设置，可以通过c函数访问
     */
    // 广告投放主机及产品名称
    'adHost'        => 'demo.adsame.com',
    'adZone'        => 'sammix',
    
    // samedata服务入口地址
    'samedataUrl'   => 'http://samedata.adsame.com',
   
    // 竞价成功回调链接
    'winNoticeUrl'  => 'http://tbid.adsame.com:7007/?ext=${EXT}&price=${AUCTION_ENCRYPT_PRICE}&key_ver=${AUCTION_ENCRYPT_VER}&impid=${AUCTION_IMP_ID}'
);
?>
