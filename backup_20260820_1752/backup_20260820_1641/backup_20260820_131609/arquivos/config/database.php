<?php
$host = "localhost";
$dbname = "u119221664_ferrobras_site";
$user = "u119221664_ferrobras_user";
$pass = "Ferrobras@2026";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("❌ Erro de conexão: " . $e->getMessage());
}

function query($sql) {
    global $pdo;
    return $pdo->exec($sql);
}

function fetchAll($sql) {
    global $pdo;
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetchOne($sql) {
    global $pdo;
    $stmt = $pdo->query($sql);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>