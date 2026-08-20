<?php
// config/database_site.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ferrobras_site');
define('DB_USER', 'ferrobras_user');
define('DB_PASS', 'SenhaForte@2026!');

class DatabaseSite {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("Erro de conexÃƒÂ£o: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchOne($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    public function insert($table, $data) {
        $keys = array_keys($data);
        $values = array_values($data);
        $placeholders = implode(',', array_fill(0, count($keys), '?'));

        $sql = "INSERT INTO {$table} (" . implode(',', $keys) . ") VALUES ({$placeholders})";
        $this->query($sql, $values);
        return $this->connection->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = []) {
        $set = [];
        $params = [];

        foreach ($data as $key => $value) {
            $set[] = "{$key} = ?";
            $params[] = $value;
        }

        $params = array_merge($params, $whereParams);
        $sql = "UPDATE {$table} SET " . implode(',', $set) . " WHERE {$where}";
        return $this->query($sql, $params)->rowCount();
    }
}

function db_site() {
    return DatabaseSite::getInstance();
}
