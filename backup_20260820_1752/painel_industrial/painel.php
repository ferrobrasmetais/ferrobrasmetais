<?php
session_start();
require_once __DIR__ . '/config.php';
requireAdmin();
require_once __DIR__ . '/../config/database.php';

$user = fetchOne("SELECT * FROM admin_usuarios WHERE id = " . $_SESSION['admin_id']);

$total_produtos = fetchOne("SELECT COUNT(*) as total FROM produtos")['total'] ?? 0;
$total_banners = fetchOne("SELECT COUNT(*) as total FROM galeria")['total'] ?? 0;
$total_usuarios = fetchOne("SELECT COUNT(*) as total FROM admin_usuarios")['total'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Ferrobras</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:Arial,sans-serif;background:#f0f2f5}
        .sidebar{width:220px;background:#1a1a1a;position:fixed;top:0;left:0;height:100%;padding-top:20px;overflow-y:auto}
        .sidebar a{display:block;padding:12px 20px;color:#aaa;text-decoration:none;border-left:3px solid transparent}
        .sidebar a:hover,.sidebar a.active{background:#2d2d2d;color:#fff;border-left-color:#d61935}
        .sidebar a i{width:20px;margin-right:10px}
        .header{background:#fff;padding:15px 25px;box-shadow:0 2px 4px rgba(0,0,0,0.05);display:flex;justify-content:space-between;align-items:center;margin-left:220px}
        .header h1{color:#d61935}
        .header .user{color:#666}
        .header .user a{color:#d61935;text-decoration:none;margin-left:15px}
        .main{margin-left:220px;padding:20px}
        .container{background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.05)}
        .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:15px;margin-bottom:20px}
        .stat-box{background:#fff;padding:20px;text-align:center;border-radius:8px;border:1px solid #e9ecef}
        .stat-box .number{font-size:32px;font-weight:700;color:#d61935}
        .stat-box .label{color:#666;font-size:14px;margin-top:5px}
        @media(max-width:768px){.sidebar{width:100%;height:auto;position:relative;padding-top:0}.sidebar a{display:inline-block;padding:10px 15px;border-left:none;border-bottom:2px solid transparent}.header{margin-left:0}.main{margin-left:0;padding:15px}.stats-grid{grid-template-columns:1fr 1fr}}
        @media(max-width:480px){.stats-grid{grid-template-columns:1fr}.header{flex-direction:column;text-align:center;gap:10px}}
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="painel.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
        <a href="produtos.php"><i class="fas fa-box"></i> Produtos</a>
        <a href="galeria.php"><i class="fas fa-images"></i> Galeria</a>
        <a href="usuarios.php"><i class="fas fa-users"></i> Usuários</a>
        <a href="configuracoes.php"><i class="fas fa-cog"></i> Configurações</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
    </div>

    <div class="header">
        <h1><i class="fas fa-cogs"></i> Dashboard</h1>
        <div class="user">
            <i class="fas fa-user"></i> <?php echo htmlspecialchars($user['nome'] ?? 'Admin'); ?>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </div>
    </div>

    <div class="main">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="number"><?php echo $total_produtos; ?></div>
                    <div class="label">📦 Produtos</div>
                </div>
                <div class="stat-box">
                    <div class="number"><?php echo $total_banners; ?></div>
                    <div class="label">🖼️ Banners</div>
                </div>
                <div class="stat-box">
                    <div class="number"><?php echo $total_usuarios; ?></div>
                    <div class="label">👥 Usuários</div>
                </div>
                <div class="stat-box">
                    <div class="number">0</div>
                    <div class="label">📂 Categorias</div>
                </div>
            </div>
            <p style="color:#999;text-align:center;padding:30px;font-size:16px;">
                Selecione um módulo no menu lateral para começar.
            </p>
        </div>
    </div>
</body>
</html>