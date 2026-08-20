<?php
session_start();
require_once '../config/database.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    
    if (empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos!';
    } else {
        // Buscar na tabela admin_usuarios
        $user = fetchOne("SELECT * FROM admin_usuarios WHERE email = '$email'");
        
        if ($user && password_verify($senha, $user['senha'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_nome'] = $user['nome'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_nivel'] = 'admin';
            
            // REDIRECIONAR PARA admin.php (NÃO painel.php)
            header('Location: admin.php');
            exit;
        } else {
            $erro = '❌ Email ou senha incorretos!';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login - Ferrobras</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:Arial,sans-serif;background:linear-gradient(135deg,#1a1a2e,#16213e);display:flex;justify-content:center;align-items:center;min-height:100vh}
        .login-box{background:#fff;padding:40px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,0.3);width:100%;max-width:400px}
        .login-box h1{color:#d61935;text-align:center;margin-bottom:10px}
        .login-box p{text-align:center;color:#666;margin-bottom:30px}
        .form-group{margin-bottom:20px}
        .form-group label{display:block;font-weight:600;margin-bottom:5px;color:#333}
        .form-group input{width:100%;padding:12px;border:1px solid #ddd;border-radius:5px;font-size:16px}
        .form-group input:focus{border-color:#d61935;outline:none}
        .btn{width:100%;padding:12px;background:#d61935;color:#fff;border:none;border-radius:5px;font-size:16px;font-weight:600;cursor:pointer}
        .btn:hover{background:#b01229}
        .erro{background:#f8d7da;color:#721c24;padding:12px;border-radius:5px;margin-bottom:20px;text-align:center}
        .logo{text-align:center;margin-bottom:20px}
        .logo img{max-height:80px}
        .voltar{text-align:center;margin-top:15px}
        .voltar a{color:#666;text-decoration:none;font-size:13px}
        .voltar a:hover{color:#d61935}
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">
            <img src="/assets/images/ferrobrasmetais_logo.webp" alt="Ferrobras">
        </div>
        <h1>🔐 Login</h1>
        <p>Painel Administrativo</p>
        
        <?php if($erro): ?>
            <div class="erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" placeholder="ferrobrasmetais@gmail.com" required>
            </div>
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn">Entrar</button>
        </form>
        
        <div class="voltar">
            <a href="../index.php">← Voltar para o site</a>
        </div>
    </div>
</body>
</html>