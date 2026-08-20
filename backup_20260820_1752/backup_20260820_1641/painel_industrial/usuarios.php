<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login_simples.php');
    exit;
}

if ($_SESSION['user_nivel'] !== 'admin') {
    header('Location: painel.php');
    exit;
}

$msg = '';
$msg_tipo = '';
$editando = null;

// Buscar usuário para edição
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editando = fetchOne("SELECT * FROM usuarios WHERE id = " . intval($_GET['edit']));
}

// Salvar usuário
if (isset($_POST['salvar'])) {
    $id = intval($_POST['id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $nivel = $_POST['nivel'] ?? 'editor';
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    $nova_senha = $_POST['nova_senha'] ?? '';

    if ($id > 0) {
        // Editar
        if (!empty($nova_senha)) {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            query("UPDATE usuarios SET nome='$nome', email='$email', usuario='$usuario', senha='$senha_hash', nivel='$nivel', ativo=$ativo WHERE id=$id");
        } else {
            query("UPDATE usuarios SET nome='$nome', email='$email', usuario='$usuario', nivel='$nivel', ativo=$ativo WHERE id=$id");
        }
        $msg = "✅ Usuário atualizado!";
    } else {
        // Novo usuário
        if (empty($nova_senha)) {
            $msg = "❌ Senha obrigatória!";
            $msg_tipo = "danger";
        } else {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            query("INSERT INTO usuarios (nome, email, usuario, senha, nivel, ativo) VALUES ('$nome', '$email', '$usuario', '$senha_hash', '$nivel', $ativo)");
            $msg = "✅ Usuário cadastrado!";
        }
    }
    if (empty($msg_tipo)) $msg_tipo = "success";
    header('Location: usuarios.php?msg=' . urlencode($msg) . '&tipo=' . $msg_tipo);
    exit;
}

// Excluir usuário
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $user = fetchOne("SELECT usuario FROM usuarios WHERE id = $id");
    if ($user && $user['usuario'] !== 'admin') {
        query("DELETE FROM usuarios WHERE id = $id");
        $msg = "✅ Usuário excluído!";
    } else {
        $msg = "❌ Não pode excluir o administrador!";
        $msg_tipo = "danger";
    }
    header('Location: usuarios.php?msg=' . urlencode($msg) . '&tipo=' . $msg_tipo);
    exit;
}

// Mensagem
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
    $msg_tipo = $_GET['tipo'] ?? 'success';
}

// Listar usuários
$usuarios = fetchAll("SELECT * FROM usuarios ORDER BY id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Usuários</title>
    <link rel="icon" type="image/png" href="../img/ferrobrasmetais_logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; background: #f4f6f8; }
        .header { background: #1a1a1a; color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { color: #d61935; }
        .container { max-width: 1200px; margin: 30px auto; padding: 20px; background: white; border-radius: 10px; }
        .btn { display: inline-block; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold; border: none; cursor: pointer; }
        .btn-primary { background: #d61935; color: white; }
        .btn-primary:hover { background: #b01229; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .btn-voltar { background: #6c757d; color: white; }
        .btn-voltar:hover { background: #5a6268; }
        .btn-warning { background: #ffc107; color: #333; }
        .btn-warning:hover { background: #e0a800; }
        .form-card { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 2px solid #ddd; }
        .form-card input, .form-card select { padding: 10px; border: 1px solid #ddd; border-radius: 5px; width: 100%; max-width: 300px; }
        .form-group { margin-bottom: 10px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; padding: 10px; text-align: left; border-bottom: 2px solid #ddd; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-success { background: #d4edda; color: #155724; }
        .msg { padding: 15px; border-radius: 5px; margin-bottom: 15px; }
        .msg-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .msg-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .logout { background: #dc3545; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; }
        .logout:hover { background: #c82333; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        @media (max-width:600px){ .row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="header">
    <h1>👥 Usuários</h1>
    <div>
        <a href="painel.php" class="btn btn-voltar">← Voltar</a>
        <a href="logout.php" class="logout">Sair</a>
    </div>
</div>
<div class="container">
    <h2>👥 Gerenciar Usuários</h2>
    <?php if (!empty($msg)): ?>
        <div class="msg msg-<?php echo $msg_tipo; ?>"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <div class="form-card">
        <h3><?php echo $editando ? '✏️ Editando: ' . htmlspecialchars($editando['nome']) : '➕ Novo Usuário'; ?></h3>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $editando ? $editando['id'] : 0; ?>">
            <div class="row">
                <div class="form-group">
                    <label>Nome *</label>
                    <input type="text" name="nome" value="<?php echo $editando ? htmlspecialchars($editando['nome']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="<?php echo $editando ? htmlspecialchars($editando['email']) : ''; ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label>Usuário *</label>
                    <input type="text" name="usuario" value="<?php echo $editando ? htmlspecialchars($editando['usuario']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label><?php echo $editando ? 'Nova Senha (opcional)' : 'Senha *'; ?></label>
                    <input type="password" name="nova_senha" <?php echo $editando ? '' : 'required'; ?>>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label>Nível</label>
                    <select name="nivel">
                        <option value="admin" <?php echo ($editando && $editando['nivel'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="editor" <?php echo ($editando && $editando['nivel'] === 'editor') ? 'selected' : ''; ?>>Editor</option>
                        <option value="visualizador" <?php echo ($editando && $editando['nivel'] === 'visualizador') ? 'selected' : ''; ?>>Visualizador</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="ativo" value="1" <?php echo (!$editando || $editando['ativo']) ? 'checked' : ''; ?>> Ativo</label>
                </div>
            </div>
            <button type="submit" name="salvar" class="btn btn-primary">💾 Salvar</button>
            <?php if ($editando): ?>
                <a href="usuarios.php" class="btn btn-voltar">❌ Cancelar</a>
            <?php endif; ?>
        </form>
    </div>

    <table>
        <thead>
            <tr><th>ID</th><th>Nome</th><th>Usuário</th><th>Email</th><th>Nível</th><th>Status</th><th>Ações</th></tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['nome']); ?></td>
                    <td><?php echo htmlspecialchars($u['usuario']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $u['nivel'] === 'admin' ? 'danger' : ($u['nivel'] === 'editor' ? 'warning' : 'success'); ?>">
                            <?php echo $u['nivel']; ?>
                        </span>
                    </td>
                    <td><?php echo $u['ativo'] ? '✅ Ativo' : '⛔ Inativo'; ?></td>
                    <td>
                        <a href="usuarios.php?edit=<?php echo $u['id']; ?>" class="btn btn-warning" style="font-size:12px; padding:5px 10px;">✏️</a>
                        <?php if ($u['usuario'] !== 'admin'): ?>
                            <a href="usuarios.php?delete=<?php echo $u['id']; ?>" class="btn btn-danger" style="font-size:12px; padding:5px 10px;" onclick="return confirm('Excluir este usuário?')">🗑️</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>