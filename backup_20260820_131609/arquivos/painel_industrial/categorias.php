<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login_simples.php');
    exit;
}

// Verificar se a tabela categorias existe, se não criar
$tabelaExiste = fetchOne("SHOW TABLES LIKE 'categorias'");
if (!$tabelaExiste) {
    query("CREATE TABLE IF NOT EXISTS categorias (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        descricao TEXT,
        slug VARCHAR(100) NOT NULL,
        ordem INT DEFAULT 0,
        ativo TINYINT(1) DEFAULT 1,
        criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}

$msg = '';
$msg_tipo = '';
$editando = null;

if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editando = fetchOne("SELECT * FROM categorias WHERE id = " . intval($_GET['edit']));
}

if (isset($_POST['salvar'])) {
    $id = intval($_POST['id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '-', $nome)));
    $ordem = intval($_POST['ordem'] ?? 0);
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    if ($id > 0) {
        query("UPDATE categorias SET nome='$nome', descricao='$descricao', slug='$slug', ordem=$ordem, ativo=$ativo WHERE id=$id");
        $msg = "✅ Categoria atualizada!";
    } else {
        query("INSERT INTO categorias (nome, descricao, slug, ordem, ativo) VALUES ('$nome', '$descricao', '$slug', $ordem, $ativo)");
        $msg = "✅ Categoria cadastrada!";
    }
    $msg_tipo = "success";
    header('Location: categorias.php?msg=' . urlencode($msg) . '&tipo=' . $msg_tipo);
    exit;
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    query("DELETE FROM categorias WHERE id = " . intval($_GET['delete']));
    header('Location: categorias.php?msg=✅ Excluida!&tipo=success');
    exit;
}

if (isset($_GET['msg'])) { $msg = $_GET['msg']; $msg_tipo = $_GET['tipo'] ?? 'success'; }
$categorias = fetchAll("SELECT * FROM categorias ORDER BY ordem ASC");
?>
<!DOCTYPE html>
<html>
<head><title>Categorias</title>
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
.form-card input, .form-card textarea { padding: 10px; border: 1px solid #ddd; border-radius: 5px; width: 100%; max-width: 300px; }
.form-group { margin-bottom: 10px; }
.form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
table { width: 100%; border-collapse: collapse; }
th { background: #f8f9fa; padding: 10px; text-align: left; border-bottom: 2px solid #ddd; }
td { padding: 10px; border-bottom: 1px solid #eee; }
.msg { padding: 15px; border-radius: 5px; margin-bottom: 15px; }
.msg-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.msg-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
.logout { background: #dc3545; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; }
.logout:hover { background: #c82333; }
</style>
</head>
<body>
<div class="header">
    <h1>📂 Categorias</h1>
    <div><a href="painel.php" class="btn btn-voltar">← Voltar</a> <a href="logout.php" class="logout">Sair</a></div>
</div>
<div class="container">
    <h2>📂 Gerenciar Categorias</h2>
    <?php if (!empty($msg)): ?><div class="msg msg-<?php echo $msg_tipo; ?>"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
    <div class="form-card">
        <h3><?php echo $editando ? '✏️ Editando' : '➕ Nova Categoria'; ?></h3>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $editando ? $editando['id'] : 0; ?>">
            <div class="form-group"><label>Nome *</label><input type="text" name="nome" value="<?php echo $editando ? htmlspecialchars($editando['nome']) : ''; ?>" required></div>
            <div class="form-group"><label>Descrição</label><textarea name="descricao"><?php echo $editando ? htmlspecialchars($editando['descricao']) : ''; ?></textarea></div>
            <div class="form-group"><label>Ordem</label><input type="number" name="ordem" value="<?php echo $editando ? $editando['ordem'] : 0; ?>"></div>
            <div class="form-group"><label><input type="checkbox" name="ativo" value="1" <?php echo (!$editando || $editando['ativo']) ? 'checked' : ''; ?>> Ativo</label></div>
            <button type="submit" name="salvar" class="btn btn-primary">💾 Salvar</button>
            <?php if ($editando): ?><a href="categorias.php" class="btn btn-voltar">❌ Cancelar</a><?php endif; ?>
        </form>
    </div>
    <table>
        <thead><tr><th>ID</th><th>Nome</th><th>Slug</th><th>Status</th><th>Ações</th></tr></thead>
        <tbody>
            <?php if (count($categorias) > 0): ?>
                <?php foreach ($categorias as $c): ?>
                    <tr>
                        <td><?php echo $c['id']; ?></td>
                        <td><?php echo htmlspecialchars($c['nome']); ?></td>
                        <td><?php echo htmlspecialchars($c['slug']); ?></td>
                        <td><?php echo $c['ativo'] ? '✅ Ativo' : '⛔ Inativo'; ?></td>
                        <td>
                            <a href="categorias.php?edit=<?php echo $c['id']; ?>" class="btn btn-warning" style="font-size:12px; padding:5px 10px;">✏️</a>
                            <a href="categorias.php?delete=<?php echo $c['id']; ?>" class="btn btn-danger" style="font-size:12px; padding:5px 10px;" onclick="return confirm('Excluir?')">🗑️</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center;color:#999;padding:20px;">Nenhuma categoria cadastrada</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>