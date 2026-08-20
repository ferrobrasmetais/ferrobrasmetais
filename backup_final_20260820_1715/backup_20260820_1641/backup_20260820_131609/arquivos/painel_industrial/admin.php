<?php
// ============================================================
// ADMIN.PHP - PAINEL ADMINISTRATIVO FERROBRAS
// VERSÃO COMPLETA E FUNCIONAL
// ============================================================

// INICIAR SESSÃO
session_start();

// ATIVAR EXIBIÇÃO DE ERROS (para debug)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// VERIFICAR SE O USUÁRIO ESTÁ LOGADO
if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
    header('Location: login_simples.php');
    exit;
}

// CONEXÃO COM O BANCO DE DADOS
require_once __DIR__ . '/../config/database.php';

$modulo = $_GET['modulo'] ?? 'dashboard';
$msg = '';
$msg_tipo = 'success';
$editando = null;

// ============================================================
// PROCESSAR AÇÕES DOS MÓDULOS
// ============================================================

// ---- PRODUTOS ----
if ($modulo === 'produtos') {
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $editando = fetchOne("SELECT * FROM produtos WHERE id = " . intval($_GET['edit']));
    }
    if (isset($_POST['salvar_produto'])) {
        $id = intval($_POST['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $categoria = trim($_POST['categoria'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $preco = trim($_POST['preco'] ?? 'Sob consulta');
        $ativo = isset($_POST['ativo']) ? 1 : 0;
        $pasta = '../imagens/produtos/';
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
                $old = fetchOne("SELECT imagem FROM produtos WHERE id = $id");
                if ($old && $old['imagem'] && file_exists($pasta . $old['imagem'])) unlink($pasta . $old['imagem']);
                query("UPDATE produtos SET nome='$nome', categoria='$categoria', descricao='$descricao', preco='$preco', imagem='$nome_arquivo', ativo=$ativo WHERE id=$id");
            } else {
                query("UPDATE produtos SET nome='$nome', categoria='$categoria', descricao='$descricao', preco='$preco', ativo=$ativo WHERE id=$id");
            }
            $msg = "✅ Produto atualizado!";
        } else {
            query("INSERT INTO produtos (nome, categoria, descricao, preco, imagem, ativo) VALUES ('$nome', '$categoria', '$descricao', '$preco', '$nome_arquivo', $ativo)");
            $msg = "✅ Produto cadastrado!";
        }
        header('Location: admin.php?modulo=produtos&msg=' . urlencode($msg));
        exit;
    }
    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        $id = intval($_GET['delete']);
        $img = fetchOne("SELECT imagem FROM produtos WHERE id = $id");
        if ($img && $img['imagem'] && file_exists('../imagens/produtos/' . $img['imagem'])) {
            unlink('../imagens/produtos/' . $img['imagem']);
        }
        query("DELETE FROM produtos WHERE id = $id");
        header('Location: admin.php?modulo=produtos&msg=✅ Produto excluido!');
        exit;
    }
}

// ---- GALERIA ----
if ($modulo === 'galeria') {
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
        header('Location: admin.php?modulo=galeria&msg=' . urlencode($msg));
        exit;
    }
    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        $id = intval($_GET['delete']);
        $img = fetchOne("SELECT imagem FROM banners WHERE id = $id");
        if ($img && $img['imagem'] && file_exists('../imagens/banners/' . $img['imagem'])) {
            unlink('../imagens/banners/' . $img['imagem']);
        }
        query("DELETE FROM banners WHERE id = $id");
        header('Location: admin.php?modulo=galeria&msg=✅ Banner excluido!');
        exit;
    }
}

// ---- CATEGORIAS ----
if ($modulo === 'categorias') {
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $editando = fetchOne("SELECT * FROM categorias WHERE id = " . intval($_GET['edit']));
    }
    if (isset($_POST['salvar_categoria'])) {
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
        header('Location: admin.php?modulo=categorias&msg=' . urlencode($msg));
        exit;
    }
    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        query("DELETE FROM categorias WHERE id = " . intval($_GET['delete']));
        header('Location: admin.php?modulo=categorias&msg=✅ Categoria excluida!');
        exit;
    }
}

// ---- USUARIOS ----
if ($modulo === 'usuarios') {
    if ($_SESSION['admin_nivel'] !== 'admin') {
        header('Location: admin.php?modulo=dashboard&msg=❌ Acesso negado!&tipo=danger');
        exit;
    }
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $editando = fetchOne("SELECT * FROM usuarios WHERE id = " . intval($_GET['edit']));
    }
    if (isset($_POST['salvar_usuario'])) {
        $id = intval($_POST['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $nivel = $_POST['nivel'] ?? 'editor';
        $ativo = isset($_POST['ativo']) ? 1 : 0;
        $nova_senha = $_POST['nova_senha'] ?? '';
        if ($id > 0) {
            if (!empty($nova_senha)) {
                $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                query("UPDATE usuarios SET nome='$nome', email='$email', senha='$senha_hash', nivel='$nivel', ativo=$ativo WHERE id=$id");
            } else {
                query("UPDATE usuarios SET nome='$nome', email='$email', nivel='$nivel', ativo=$ativo WHERE id=$id");
            }
            $msg = "✅ Usuario atualizado!";
        } else {
            if (empty($nova_senha)) {
                $msg = "❌ Senha obrigatoria!";
                $msg_tipo = "danger";
            } else {
                $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                query("INSERT INTO usuarios (nome, email, senha, nivel, ativo) VALUES ('$nome', '$email', '$senha_hash', '$nivel', $ativo)");
                $msg = "✅ Usuario cadastrado!";
            }
        }
        header('Location: admin.php?modulo=usuarios&msg=' . urlencode($msg) . '&tipo=' . $msg_tipo);
        exit;
    }
    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        $id = intval($_GET['delete']);
        $user = fetchOne("SELECT email FROM usuarios WHERE id = $id");
        if ($user && $user['email'] !== 'ferrobrasmetais@gmail.com') {
            query("DELETE FROM usuarios WHERE id = $id");
            $msg = "✅ Usuario excluido!";
        } else {
            $msg = "❌ Nao pode excluir o admin principal!";
            $msg_tipo = "danger";
        }
        header('Location: admin.php?modulo=usuarios&msg=' . urlencode($msg) . '&tipo=' . $msg_tipo);
        exit;
    }
}

// ---- CONFIGURACOES ----
if ($modulo === 'configuracoes') {
    if ($_SESSION['admin_nivel'] !== 'admin') {
        header('Location: admin.php?modulo=dashboard&msg=❌ Acesso negado!&tipo=danger');
        exit;
    }
    if (isset($_POST['salvar_config'])) {
        $configs = [
            'site_nome' => trim($_POST['site_nome'] ?? ''),
            'site_email' => trim($_POST['site_email'] ?? ''),
            'site_telefone' => trim($_POST['site_telefone'] ?? ''),
            'site_whatsapp' => trim($_POST['site_whatsapp'] ?? ''),
            'site_endereco' => trim($_POST['site_endereco'] ?? ''),
            'site_descricao' => trim($_POST['site_descricao'] ?? '')
        ];
        foreach ($configs as $chave => $valor) {
            $existe = fetchOne("SELECT id FROM site_config WHERE chave = '$chave'");
            if ($existe) {
                query("UPDATE site_config SET valor = '$valor' WHERE chave = '$chave'");
            } else {
                query("INSERT INTO site_config (chave, valor) VALUES ('$chave', '$valor')");
            }
        }
        $msg = "✅ Configurações salvas!";
        header('Location: admin.php?modulo=configuracoes&msg=' . urlencode($msg));
        exit;
    }
}

// ---- DASHBOARD ----
if ($modulo === 'dashboard') {
    $total_produtos = fetchOne("SELECT COUNT(*) as total FROM produtos");
    $total_produtos = $total_produtos ? $total_produtos['total'] : 0;
    $total_banners = fetchOne("SELECT COUNT(*) as total FROM banners");
    $total_banners = $total_banners ? $total_banners['total'] : 0;
    $total_usuarios = fetchOne("SELECT COUNT(*) as total FROM usuarios");
    $total_usuarios = $total_usuarios ? $total_usuarios['total'] : 0;
    $total_categorias = fetchOne("SELECT COUNT(*) as total FROM categorias");
    $total_categorias = $total_categorias ? $total_categorias['total'] : 0;
}

if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
    $msg_tipo = $_GET['tipo'] ?? 'success';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Ferrobras</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; background: #f4f6f8; }
        .header { background: #1a1a1a; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; }
        .header h1 { color: #d61935; font-size: 22px; }
        .header .user-info { color: #aaa; font-size: 14px; }
        .header .user-info a { color: #d61935; text-decoration: none; margin-left: 15px; }
        .sidebar { background: #2d2d2d; width: 220px; position: fixed; top: 0; left: 0; height: 100%; padding-top: 70px; overflow-y: auto; }
        .sidebar a { display: block; padding: 12px 20px; color: #aaa; text-decoration: none; font-size: 14px; border-left: 3px solid transparent; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #3d3d3d; color: white; border-left-color: #d61935; }
        .sidebar a i { width: 20px; margin-right: 10px; }
        .main { margin-left: 220px; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .msg { padding: 12px 15px; border-radius: 5px; margin-bottom: 15px; }
        .msg-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .msg-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .btn { display: inline-block; padding: 8px 16px; border-radius: 5px; text-decoration: none; font-weight: bold; border: none; cursor: pointer; font-size: 13px; }
        .btn-primary { background: #d61935; color: white; }
        .btn-primary:hover { background: #b01229; }
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #218838; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .btn-warning { background: #ffc107; color: #333; }
        .btn-warning:hover { background: #e0a800; }
        .btn-voltar { background: #6c757d; color: white; }
        .btn-voltar:hover { background: #5a6268; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th { background: #f8f9fa; padding: 10px; text-align: left; border-bottom: 2px solid #ddd; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px; }
        .stat-box { background: white; padding: 15px; border-radius: 8px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .stat-box .number { font-size: 28px; font-weight: 700; color: #d61935; }
        .stat-box .label { color: #666; font-size: 13px; margin-top: 3px; }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 3px; font-size: 13px; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; max-width: 400px; padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px; }
        .form-group textarea { max-width: 100%; height: 60px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-card { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 2px solid #ddd; }
        .form-card h3 { margin-bottom: 15px; }
        .grid-produtos { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
        .card-item { background: #f8f9fa; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .card-item img { width: 100%; height: 120px; object-fit: cover; background: #e9ecef; }
        .card-item .info { padding: 10px; font-size: 13px; }
        .card-item .info .nome { font-weight: bold; }
        .card-item .acoes { padding: 8px 10px; display: flex; gap: 5px; justify-content: center; flex-wrap: wrap; }
        .sem-imagem { width: 100%; height: 120px; background: #e9ecef; display: flex; align-items: center; justify-content: center; color: #999; font-size: 13px; }
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; padding-top: 0; }
            .sidebar a { display: inline-block; padding: 10px 15px; border-left: none; border-bottom: 2px solid transparent; }
            .main { margin-left: 0; padding: 15px; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .form-row { grid-template-columns: 1fr; }
        }
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
            .header { flex-direction: column; text-align: center; gap: 10px; }
        }
    </style>
</head>
<body>
    <!-- SIDEBAR -->
    <div class="sidebar">
        <a href="admin.php?modulo=dashboard" class="<?php echo $modulo === 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Dashboard</a>
        <a href="admin.php?modulo=produtos" class="<?php echo $modulo === 'produtos' ? 'active' : ''; ?>"><i class="fas fa-box"></i> Produtos</a>
        <a href="admin.php?modulo=galeria" class="<?php echo $modulo === 'galeria' ? 'active' : ''; ?>"><i class="fas fa-images"></i> Galeria</a>
        <a href="admin.php?modulo=categorias" class="<?php echo $modulo === 'categorias' ? 'active' : ''; ?>"><i class="fas fa-folder"></i> Categorias</a>
        <a href="admin.php?modulo=usuarios" class="<?php echo $modulo === 'usuarios' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Usuários</a>
        <a href="admin.php?modulo=configuracoes" class="<?php echo $modulo === 'configuracoes' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Configurações</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
    </div>

    <!-- MAIN -->
    <div class="main">
        <div class="header" style="background:white;color:#333;border-bottom:1px solid #ddd;margin-bottom:20px;border-radius:8px;">
            <h1 style="color:#d61935;font-size:20px;">
                <?php
                $titulos = [
                    'dashboard' => '📊 Dashboard',
                    'produtos' => '📦 Produtos',
                    'galeria' => '🖼️ Galeria',
                    'categorias' => '📂 Categorias',
                    'usuarios' => '👥 Usuários',
                    'configuracoes' => '⚙️ Configurações'
                ];
                echo $titulos[$modulo] ?? '📊 Dashboard';
                ?>
            </h1>
            <div class="user-info">
                <i class="fas fa-user"></i> <?php echo $_SESSION['admin_nome'] ?? $_SESSION['admin_email'] ?? 'Admin'; ?>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>

        <div class="container">
            <?php if (!empty($msg)): ?>
                <div class="msg msg-<?php echo $msg_tipo; ?>"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>

            <?php if ($modulo === 'dashboard'): ?>
                <div class="stats-grid">
                    <div class="stat-box"><div class="number"><?php echo $total_produtos; ?></div><div class="label">📦 Produtos</div></div>
                    <div class="stat-box"><div class="number"><?php echo $total_banners; ?></div><div class="label">🖼️ Banners</div></div>
                    <div class="stat-box"><div class="number"><?php echo $total_categorias; ?></div><div class="label">📂 Categorias</div></div>
                    <div class="stat-box"><div class="number"><?php echo $total_usuarios; ?></div><div class="label">👥 Usuários</div></div>
                </div>
                <p style="color:#999;text-align:center;padding:20px;">Selecione um módulo no menu lateral.</p>
            <?php endif; ?>

            <?php if ($modulo === 'produtos'): 
                $produtos = fetchAll("SELECT * FROM produtos ORDER BY id DESC");
            ?>
                <div class="form-card">
                    <h3><?php echo $editando ? '✏️ Editando' : '➕ Novo Produto'; ?></h3>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $editando ? $editando['id'] : 0; ?>">
                        <div class="form-row">
                            <div class="form-group"><label>Nome *</label><input type="text" name="nome" value="<?php echo $editando ? htmlspecialchars($editando['nome']) : ''; ?>" required></div>
                            <div class="form-group"><label>Categoria</label><input type="text" name="categoria" value="<?php echo $editando ? htmlspecialchars($editando['categoria']) : ''; ?>"></div>
                        </div>
                        <div class="form-group"><label>Descrição</label><textarea name="descricao"><?php echo $editando ? htmlspecialchars($editando['descricao']) : ''; ?></textarea></div>
                        <div class="form-row">
                            <div class="form-group"><label>Preço</label><input type="text" name="preco" value="<?php echo $editando ? htmlspecialchars($editando['preco']) : 'Sob consulta'; ?>"></div>
                            <div class="form-group"><label>Imagem</label><input type="file" name="imagem" accept="image/*"></div>
                        </div>
                        <div class="form-group"><label><input type="checkbox" name="ativo" value="1" <?php echo (!$editando || $editando['ativo']) ? 'checked' : ''; ?>> Ativo</label></div>
                        <button type="submit" name="salvar_produto" class="btn btn-primary">💾 Salvar</button>
                        <?php if ($editando): ?><a href="admin.php?modulo=produtos" class="btn btn-voltar">❌ Cancelar</a><?php endif; ?>
                    </form>
                </div>
                <div class="grid-produtos">
                    <?php if (count($produtos) > 0): ?>
                        <?php foreach ($produtos as $p): ?>
                            <div class="card-item">
                                <?php if (!empty($p['imagem']) && file_exists('../imagens/produtos/' . $p['imagem'])): ?>
                                    <img src="../imagens/produtos/<?php echo $p['imagem']; ?>" alt="<?php echo htmlspecialchars($p['nome']); ?>">
                                <?php else: ?>
                                    <div class="sem-imagem">📷 Sem imagem</div>
                                <?php endif; ?>
                                <div class="info">
                                    <div class="nome"><?php echo htmlspecialchars($p['nome']); ?></div>
                                    <div style="font-size:12px;color:#666;"><?php echo htmlspecialchars($p['categoria']); ?> - <?php echo $p['preco']; ?></div>
                                    <span class="badge <?php echo $p['ativo'] ? 'badge-success' : 'badge-danger'; ?>"><?php echo $p['ativo'] ? 'Ativo' : 'Inativo'; ?></span>
                                </div>
                                <div class="acoes">
                                    <a href="admin.php?modulo=produtos&edit=<?php echo $p['id']; ?>" class="btn btn-warning" style="font-size:11px;padding:3px 10px;">✏️</a>
                                    <a href="admin.php?modulo=produtos&delete=<?php echo $p['id']; ?>" class="btn btn-danger" style="font-size:11px;padding:3px 10px;" onclick="return confirm('Excluir?')">🗑️</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align:center;color:#999;padding:20px;grid-column:1/-1;">Nenhum produto cadastrado</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($modulo === 'galeria'): 
                $banners = fetchAll("SELECT * FROM banners ORDER BY id DESC");
            ?>
                <div class="form-card">
                    <h3><?php echo $editando ? '✏️ Editando' : '➕ Novo Banner'; ?></h3>
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
                        <?php if ($editando): ?><a href="admin.php?modulo=galeria" class="btn btn-voltar">❌ Cancelar</a><?php endif; ?>
                    </form>
                </div>
                <div class="grid-produtos">
                    <?php if (count($banners) > 0): ?>
                        <?php foreach ($banners as $b): ?>
                            <div class="card-item">
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
                                    <a href="admin.php?modulo=galeria&edit=<?php echo $b['id']; ?>" class="btn btn-warning" style="font-size:11px;padding:3px 10px;">✏️</a>
                                    <a href="admin.php?modulo=galeria&delete=<?php echo $b['id']; ?>" class="btn btn-danger" style="font-size:11px;padding:3px 10px;" onclick="return confirm('Excluir?')">🗑️</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align:center;color:#999;padding:20px;grid-column:1/-1;">Nenhum banner cadastrado</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($modulo === 'categorias'): 
                $categorias = fetchAll("SELECT * FROM categorias ORDER BY ordem ASC, id ASC");
            ?>
                <div class="form-card">
                    <h3><?php echo $editando ? '✏️ Editando' : '➕ Nova Categoria'; ?></h3>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $editando ? $editando['id'] : 0; ?>">
                        <div class="form-row">
                            <div class="form-group"><label>Nome *</label><input type="text" name="nome" value="<?php echo $editando ? htmlspecialchars($editando['nome']) : ''; ?>" required></div>
                            <div class="form-group"><label>Ordem</label><input type="number" name="ordem" value="<?php echo $editando ? htmlspecialchars($editando['ordem']) : 0; ?>"></div>
                        </div>
                        <div class="form-group"><label>Descrição</label><textarea name="descricao"><?php echo $editando ? htmlspecialchars($editando['descricao']) : ''; ?></textarea></div>
                        <div class="form-group"><label><input type="checkbox" name="ativo" value="1" <?php echo (!$editando || $editando['ativo']) ? 'checked' : ''; ?>> Ativo</label></div>
                        <button type="submit" name="salvar_categoria" class="btn btn-primary">💾 Salvar</button>
                        <?php if ($editando): ?><a href="admin.php?modulo=categorias" class="btn btn-voltar">❌ Cancelar</a><?php endif; ?>
                    </form>
                </div>
                <table>
                    <thead>
                        <tr><th>ID</th><th>Nome</th><th>Slug</th><th>Ordem</th><th>Status</th><th>Ações</th></tr>
                    </thead>
                    <tbody>
                        <?php if (count($categorias) > 0): ?>
                            <?php foreach ($categorias as $c): ?>
                                <tr>
                                    <td><?php echo $c['id']; ?></td>
                                    <td><?php echo htmlspecialchars($c['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($c['slug']); ?></td>
                                    <td><?php echo $c['ordem']; ?></td>
                                    <td><span class="badge <?php echo $c['ativo'] ? 'badge-success' : 'badge-danger'; ?>"><?php echo $c['ativo'] ? 'Ativo' : 'Inativo'; ?></span></td>
                                    <td>
                                        <a href="admin.php?modulo=categorias&edit=<?php echo $c['id']; ?>" class="btn btn-warning" style="font-size:11px;padding:3px 10px;">✏️</a>
                                        <a href="admin.php?modulo=categorias&delete=<?php echo $c['id']; ?>" class="btn btn-danger" style="font-size:11px;padding:3px 10px;" onclick="return confirm('Excluir?')">🗑️</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center;color:#999;">Nenhuma categoria cadastrada</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php if ($modulo === 'usuarios'): 
                $usuarios = fetchAll("SELECT * FROM usuarios ORDER BY id ASC");
            ?>
                <div class="form-card">
                    <h3><?php echo $editando ? '✏️ Editando' : '➕ Novo Usuário'; ?></h3>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $editando ? $editando['id'] : 0; ?>">
                        <div class="form-row">
                            <div class="form-group"><label>Nome *</label><input type="text" name="nome" value="<?php echo $editando ? htmlspecialchars($editando['nome']) : ''; ?>" required></div>
                            <div class="form-group"><label>Email *</label><input type="email" name="email" value="<?php echo $editando ? htmlspecialchars($editando['email']) : ''; ?>" required></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group"><label>Nova Senha <?php echo $editando ? '(deixe em branco para manter)' : '*'; ?></label><input type="password" name="nova_senha" <?php echo $editando ? '' : 'required'; ?>></div>
                            <div class="form-group">
                                <label>Nível</label>
                                <select name="nivel">
                                    <option value="editor" <?php echo ($editando && $editando['nivel'] === 'editor') ? 'selected' : ''; ?>>Editor</option>
                                    <option value="admin" <?php echo ($editando && $editando['nivel'] === 'admin') ? 'selected' : ''; ?>>Administrador</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group"><label><input type="checkbox" name="ativo" value="1" <?php echo (!$editando || $editando['ativo']) ? 'checked' : ''; ?>> Ativo</label></div>
                        <button type="submit" name="salvar_usuario" class="btn btn-primary">💾 Salvar</button>
                        <?php if ($editando): ?><a href="admin.php?modulo=usuarios" class="btn btn-voltar">❌ Cancelar</a><?php endif; ?>
                    </form>
                </div>
                <table>
                    <thead>
                        <tr><th>ID</th><th>Nome</th><th>Email</th><th>Nível</th><th>Status</th><th>Ações</th></tr>
                    </thead>
                    <tbody>
                        <?php if (count($usuarios) > 0): ?>
                            <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td><?php echo $u['id']; ?></td>
                                    <td><?php echo htmlspecialchars($u['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                                    <td><span class="badge <?php echo $u['nivel'] === 'admin' ? 'badge-warning' : 'badge-info'; ?>"><?php echo $u['nivel']; ?></span></td>
                                    <td><span class="badge <?php echo $u['ativo'] ? 'badge-success' : 'badge-danger'; ?>"><?php echo $u['ativo'] ? 'Ativo' : 'Inativo'; ?></span></td>
                                    <td>
                                        <a href="admin.php?modulo=usuarios&edit=<?php echo $u['id']; ?>" class="btn btn-warning" style="font-size:11px;padding:3px 10px;">✏️</a>
                                        <?php if ($u['email'] !== 'ferrobrasmetais@gmail.com'): ?>
                                            <a href="admin.php?modulo=usuarios&delete=<?php echo $u['id']; ?>" class="btn btn-danger" style="font-size:11px;padding:3px 10px;" onclick="return confirm('Excluir?')">🗑️</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center;color:#999;">Nenhum usuário cadastrado</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php if ($modulo === 'configuracoes'): 
                $configs = fetchAll("SELECT * FROM site_config");
                $configs_arr = [];
                foreach ($configs as $c) {
                    $configs_arr[$c['chave']] = $c['valor'];
                }
            ?>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nome do Site</label>
                            <input type="text" name="site_nome" value="<?php echo htmlspecialchars($configs_arr['site_nome'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="site_email" value="<?php echo htmlspecialchars($configs_arr['site_email'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Telefone</label>
                            <input type="text" name="site_telefone" value="<?php echo htmlspecialchars($configs_arr['site_telefone'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>WhatsApp</label>
                            <input type="text" name="site_whatsapp" value="<?php echo htmlspecialchars($configs_arr['site_whatsapp'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Endereço</label>
                        <input type="text" name="site_endereco" value="<?php echo htmlspecialchars($configs_arr['site_endereco'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea name="site_descricao"><?php echo htmlspecialchars($configs_arr['site_descricao'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" name="salvar_config" class="btn btn-primary">💾 Salvar Configurações</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>