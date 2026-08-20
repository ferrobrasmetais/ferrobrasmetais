<?php
session_start();

function requireAdmin() {
    if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
        header('Location: login_simples.php');
        exit;
    }
}
?>