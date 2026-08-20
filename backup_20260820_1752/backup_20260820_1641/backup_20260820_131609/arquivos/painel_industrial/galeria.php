<?php
session_start();
require_once __DIR__ . '/config.php';
requireAdmin();
require_once __DIR__ . '/../config/database.php';

$modulo = 'galeria';
$msg = '';
$msg_tipo = 'success';
$editando = null;

if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editando = fetchOne("SELECT * FROM banners WHERE id = " . intval($_GET['edit']));
}

if (isset($_POST['salvar_banner'])) {
    $id = intval($_POST['id'] ?? 0);
    $titulo = trim($_POST['titulo'] ?? '');
    $subtitulo = trim($_POST['subtitulo'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    $pasta = '../imagens/banners/';
    if (!is_dir($pasta)) mkdir($pasta, 0777, true);
    $nome_arquivo = '';
    if (!empty($_FILES['imagem']['name']) && $_FILES['imagem']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
            $nome_arquivo = time() . '_' . basename($_FILES['imagem']['name']);
            move_uploaded_file($_FILES['imagem']['tmp_name'], $pasta . $nome_arquivo);
        }
    }
    if ($id > 0) {
        if ($nome_arquivo) {
            $old = fetchOne("SELECT imagem FROM banners WHERE id = $id");
            if ($old && $old['imagem'] && file_exists($pasta . $old['imagem'])) unlink($pasta . $old['imagem']);
            query("UPDATE banners SET titulo='$titulo', subtitulo='$subtitulo', link='$link', imagem='$nome_arquivo', ativo=$ativo WHERE id=$id");
        } else {
            query("UPDATE banners SET titulo='$titulo', subtitulo='$subtitulo', link='$link', ativo=$ativo WHERE id=$id");
        }
        $msg = "✅ Banner atualizado!";
    } else {
        query("INSERT INTO banners (titulo, subtitulo, link, imagem, ativo) VALUES ('$titulo', '$subtitulo', '$link', '$nome_arquivo', $ativo)");
        $msg = "✅ Banner cadastrado!";
    }
    header('Location: galeria.php?msg=' . urlencode($msg));
    exit;
}
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $img = fetchOne("SELECT imagem FROM banners WHERE id = $id");
    if ($img && $img['imagem'] && file_exists('../imagens/banners/' . $img['imagem'])) {
        unlink('../imagens/banners/' . $img['imagem']);
    }
    query("DELETE FROM banners WHERE id = $id");
    header('Location: galeria.php?msg=✅ Banner excluido!');
    exit;
}

$banners = fetchAll("SELECT * FROM banners ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Galeria - Ferrobras</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; background: #f4f6f8; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: #1a1a1a; color: white; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { color: #d61935; }
        .header a { color: #aaa; text-decoration: none; margin-left: 15px; }
        .header a:hover { color: #d61935; }
        .card { background: white; padding: 20px; border-radius: 8px; margin-bottom: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .btn { display: inline-block; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: bold; border: none; cursor: pointer; font-size: 13px; }
        .btn-primary { background: #d61935; color: white; }
        .btn-primary:hover { background: #b01229; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .btn-warning { background: #ffc107; color: #333; }
        .btn-warning:hover { background: #e0a800; }
        .btn-voltar { background: #6c757d; color: white; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
        .item { background: #f8f9fa; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .item img { width: 100%; height: 120px; object-fit: cover; }
        .item .info { padding: 10px; }
        .item .info .nome { font-weight: bold; }
        .item .acoes { padding: 8px 10px; display: flex; gap: 5px; justify-content: center; }
        .sem-imagem { width: 100%; height: 120px; background: #e9ecef; display: flex; align-items: center; justify-content: center; color: #999; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 3px; font-size: 13px; }
        .form-group input, .form-group textarea { width: 100%; max-width: 400px; padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .msg { padding: 12px 15px; border-radius: 5px; margin-bottom: 15px; }
        .msg-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .msg-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🖼️ Galeria</h1>
            <div>
                <a href="admin.php?modulo=dashboard">📊 Dashboard</a>
                <a href="logout.php">🚪 Sair</a>
            </div>
        </div>

        <div class="card">
            <h3><?php echo $editando ? '✏️ Editando' : '➕ Novo Banner'; ?></h3>
            <?php if ($msg): ?>
                <div class="msg msg-<?php echo $msg_tipo; ?>"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $editando ? $editando['id'] : 0; ?>">
                <div class="form-row">
                    <div class="form-group"><label>Título *</label><input type="text" name="titulo" value="<?php echo $editando ? htmlspecialchars($editando['titulo']) : ''; ?>" required></div>
                    <div class="form-group"><label>Subtítulo</label><input type="text" name="subtitulo" value="<?php echo $editando ? htmlspecialchars($editando['subtitulo']) : ''; ?>"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Link</label><input type="text" name="link" value="<?php echo $editando ? htmlspecialchars($editando['link']) : ''; ?>"></div>
                    <div class="form-group"><label>Imagem</label><input type="file" name="imagem" accept="image/*"></div>
                </div>
                <div class="form-group"><label><input type="checkbox" name="ativo" value="1" <?php echo (!$editando || $editando['ativo']) ? 'checked' : ''; ?>> Ativo</label></div>
                <button type="submit" name="salvar_banner" class="btn btn-primary">💾 Salvar</button>
                <?php if ($editando): ?><a href="galeria.php" class="btn btn-voltar">❌ Cancelar</a><?php endif; ?>
            </form>
        </div>

        <div class="grid">
            <?php if (count($banners) > 0): ?>
                <?php foreach ($banners as $b): ?>
                    <div class="item">
                        <?php if (!empty($b['imagem']) && file_exists('../imagens/banners/' . $b['imagem'])): ?>
                            <img src="../imagens/banners/<?php echo $b['imagem']; ?>" alt="<?php echo htmlspecialchars($b['titulo']); ?>">
                        <?php else: ?>
                            <div class="sem-imagem">📷 Sem imagem</div>
                        <?php endif; ?>
                        <div class="info">
                            <div class="nome"><?php echo htmlspecialchars($b['titulo']); ?></div>
                            <div style="font-size:12px;color:#666;"><?php echo htmlspecialchars($b['subtitulo']); ?></div>
                            <span class="badge <?php echo $b['ativo'] ? 'badge-success' : 'badge-danger'; ?>"><?php echo $b['ativo'] ? 'Ativo' : 'Inativo'; ?></span>
                        </div>
                        <div class="acoes">
                            <a href="galeria.php?edit=<?php echo $b['id']; ?>" class="btn btn-warning" style="font-size:11px;padding:3px 10px;">✏️</a>
                            <a href="galeria.php?delete=<?php echo $b['id']; ?>" class="btn btn-danger" style="font-size:11px;padding:3px 10px;" onclick="return confirm('Excluir?')">🗑️</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column:1/-1;text-align:center;color:#999;padding:40px;">Nenhum banner cadastrado</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>