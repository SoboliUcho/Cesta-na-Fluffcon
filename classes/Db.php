<?php
class Db
{
    private static $connection;
    public static function connect($host, $database, $user, $password)
	{
        self::$connection = new mysqli($host, $user, $password, $database);
        if (self::$connection->connect_error) {
            die("Connection failed: " . self::$connection->connect_error);
        }
        self::$connection->set_charset("utf8mb4");
    }
    public static function query($query) {
        $result = self::$connection->query($query);
        if ($result === false) {
            // Zobraz dotaz a chybu, která se stala
            die("Chyba SQL dotazu: " . self::$connection->error . "<br>SQL dotaz: " . $query);
        }
        return $result;
    }
    public static function querySingle($query) {
        $result = self::$connection->query($query);
        if ($result === false) {
            // Zobraz dotaz a chybu, která se stala
            die("Chyba SQL dotazu: " . self::$connection->error . "<br>SQL dotaz: " . $query);
        }
        $row = $result->fetch_row();
        return $row[0];
    }
    public static function queryOne($query) {
        $result = self::$connection->query($query);
        if ($result === false) {
            // Zobraz dotaz a chybu, která se stala
            die("Chyba SQL dotazu: " . self::$connection->error . "<br>SQL dotaz: " . $query);
        }
        $row = $result->fetch_assoc();
        return $row;
    }
    public static function queryAll($query) {
        $result = self::$connection->query($query);
        $rows = [];
        if ($result === false) {
            // Zobraz dotaz a chybu, která se stala
            die("Chyba SQL dotazu: " . self::$connection->error . "<br>SQL dotaz: " . $query);
        }
        // print_r($result);
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }
    public static function queryInsert($query) {
        self::$connection->query($query);
        return self::$connection->insert_id;
    }
    public static function queryUpdate($query) {
        self::$connection->query($query);
        return self::$connection->affected_rows;
    }
    public static function queryDelete($query) {
        self::$connection->query($query);
        return self::$connection->affected_rows;
    }
    public static function escape($string) {
        return self::$connection->real_escape_string($string);
    }
    public static function getLastId() {
        return self::$connection->insert_id;
    }
    public static function getAffectedRows() {
        return self::$connection->affected_rows;
    }
    public static function getError() {
        return self::$connection->error;
    }
    public static function getErrorNo() {
        return self::$connection->errno;
    }
    public static function close() {
        self::$connection->close();
    }
    public static function debugTimestamp() {
        echo(strtotime(date('his')));
    }

}