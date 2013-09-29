<?php
interface DataRow {
    public function setModel(& $model);
    public function isEmpty();
    public function set($key, $val, $force = false);
    public function get($key);
    public function save();
    public function toArray();
    public function getFields();
}
?>
