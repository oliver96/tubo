<?php
/**
 *==============================================================================
 * 基本常规函数
 *==============================================================================
 */
 
/**
 * 定义一个全局变量
 *
 * @param string $name 变量名称
 * @param mixed $value 变量值
 */
function def($name, $value) {
    Core::$constants[$name] = $value;
}

/**
 * 获取一个全局变量值
 *
 * @param string $name 变量名称
 *
 * @return mixed 返回变量值
 */
function c($name, $key = '') {
    if(isset(Core::$constants[$name])) {
        if('' != $key) {
            return Core::$constants[$name][$key];
        }
        else {
            return Core::$constants[$name];
        }
    }
    return null;
}

/**
 * 获取变量引用地址
 *
 * @param mixed $value 变量
 *
 * @return ref 返回引用（指针）
 */
function & ref($value) {
    return $value;
}

/**
 * 载入类库文件
 * @param string $name 类库域名
 */
function import($name) {
    include_once str_replace('.', DS, $name) . '.php';
}

/**
 * 根据指定的url参数生成url
 *
 * @return 返加url字符串
 */
function url($params = array()) {
    return Core::url($params);
}

/**
 *==============================================================================
 * 数据库基本函数
 *==============================================================================
 */
 
/**
 * 连接数据库，并返回连接对象
 */
function & connect() {
    return Core::connect();
}

/**
 * 开始事务处理
 */
function beginTrans() {
    Core::beginTrans();
}

/**
 * 回退事务
 */
function rollback() {
    Core::rollback();
}

/**
 * 提交事务
 */
function commit() {
    Core::commit();
}

/**
 * 关闭数据库连接
 */
function close() {
    Core::close();
}

/**
 *==============================================================================
 * 兼容旧版本的函数
 *==============================================================================
 */
if(!function_exists('lcfirst')) {
    function lcfirst($string = null) {
        if (!$string) return null;
        $string{0} = strtolower($string{0});
        return $string;
    }
}
?>
