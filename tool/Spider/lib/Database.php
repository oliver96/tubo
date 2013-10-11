<?php
class Database {
    public static function connect() {
        $conn = mysqli_connect("localhost", "root", "point9*", "tubo");
        $conn->query(sprintf("set character_set_client=%s", 'utf8'));
        $conn->query(sprintf("set character_set_connection=%s", 'utf8'));
        $conn->query(sprintf("set character_set_results=%s", 'utf8'));
        return $conn;
    }
}
?>
