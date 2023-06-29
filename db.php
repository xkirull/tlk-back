<?php

class DB
{
    const DB_HOST = 'localhost';
    const DB_USER = 'u1196731_tlk';
    const DB_PASSWORD = 'u1196731_tlk';
    const DB_NAME = 'u1196731_tlk';
    const CHARSET = 'utf8';
    const DB_PREFIX = '';

    static private $db;
    protected static $instance = null;

    public function __construct()
    {
        if (self::$instance === null) {
            try {
                self::$db = new PDO(
                    'mysql:host=' . self::DB_HOST . ';dbname=' . self::DB_NAME,
                    self::DB_USER,
                    self::DB_PASSWORD,
                    $options = [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . self::CHARSET
                    ]
                );
            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
        }
        return self::$instance;
    }

    public static function query($stmt)
    {
        return self::$db->query($stmt);
    }

    public static function prepare($stmt)
    {
        return self::$db->prepare($stmt);
    }

    static public function exec($query)
    {
        return self::$db->exec($query);
    }

    static public function lastInsertId()
    {
        return self::$db->lastInsertId();
    }

    public static function run($query, $args = [])
    {
        try {
            if (!$args) {
                return self::query($query);
            }
            $stmt = self::prepare($query);
            $stmt->execute($args);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function getRow($query, $args = [])
    {
        return self::run($query, $args)->fetch();
    }

    public static function getRows($query, $args = [])
    {
        return self::run($query, $args)->fetchAll();
    }

    public static function getValue($query, $args = [])
    {
        $result = self::getRow($query, $args);
        if (!empty($result)) {
            $result = array_shift($result);
        }
        return $result;
    }

    public static function getColumn($query, $args = [])
    {
        return self::run($query, $args)->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function sql($query, $args = [])
    {
        self::run($query, $args);
    }
}
