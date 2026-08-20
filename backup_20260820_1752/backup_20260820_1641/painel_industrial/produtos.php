<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login_simples.php');
    exit;
}

$msg = '';
$msg_tipo = '';
$editando = null;

// Buscar dados para edição
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editando = fetchOne("SELECT * FROM produtos WHERE id = " . $_GET['edit']);
}

// Processar cadastro/edição
if (isset($_POST['salvar'])) {
    $id = $_POST['id'] ?? 0;
    $nome = $_POST['nome'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $preco = $_POST['preco'] ?? 'Sob consulta';
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    $pasta = '../imagens/produtos/';
    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }
    
    $nome_arquivo = '';
    $upload_ok = false;
    
    if (!empty($_FILES['imagem']['name']) && $_FILES['imagem']['error'] === 0) {
        $arquivo = $_FILES['imagem'];
        $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $extensoes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($ext, $extensoes)) {
            $nome_arquivo = time() . '_' . basename($arquivo['name']);
            $destino = $pasta . $nome_arquivo;
            
            if (move_uploaded_file($arquivo['tmp_name'], $destino)) {
                $upload_ok = true;
                $msg = '✅ Imagem enviada: ' . $nome_arquivo;
                $msg_tipo = 'success';
            } else {
                $msg = '❌ Erro ao enviar imagem.';
                $msg_tipo = 'danger';
            }
        } else {
            $msg = '❌ Formato não permitido. Use JPG, PNG, GIF, WEBP.';
            $msg_tipo = 'danger';
        }
    }
    
    if ($id > 0) {
        if ($upload_ok && $nome_arquivo) {
            $img_antiga = fetchOne("SELECT imagem FROM produtos WHERE id = $id");
            if ($img_antiga && $img_antiga['imagem']) {
                $caminho_antigo = $pasta . $img_antiga['imagem'];
                if (file_exists($caminho_antigo)) {
                    unlink($caminho_antigo);
                }
            }
            $sql = "UPDATE produtos SET 
                    nome = '$nome',
                    categoria = '$categoria',
                    descricao = '$descricao',
                    preco = '$preco',
                    imagem = '$nome_arquivo',
                    ativo = $ativo
                    WHERE id = $id";
        } else {
            $sql = "UPDATE produtos SET 
                    nome = '$nome',
                    categoria = '$categoria',
                    descricao = '$descricao',
                    preco = '$preco',
                    ativo = $ativo
                    WHERE id = $id";
        }
        query($sql);
        $msg = '✅ Produto atualizado com sucesso!';
        $msg_tipo = 'success';
    } else {
        $sql = "INSERT INTO produtos (nome, categoria, descricao, preco, imagem, ativo) 
                VALUES ('$nome', '$categoria', '$descricao', '$preco', '$nome_arquivo', $ativo)";
        query($sql);
        $msg = '✅ Produto cadastrado com sucesso!';
        $msg_tipo = 'success';
    }
    
    header('Location: produtos.php?msg=' . urlencode($msg) . '&tipo=' . $msg_tipo);
    exit;
}

// Excluir produto
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $img = fetchOne("SELECT imagem FROM produtos WHERE id = $id");
    if ($img && $img['imagem']) {
        $caminho = '../imagens/produtos/' . $img['imagem'];
        if (file_exists($caminho)) {
            unlink($caminho);
        }
    }
    query("DELETE FROM produtos WHERE id = $id");
    header('Location: produtos.php?msg=✅ Produto excluído!&tipo=success');
    exit;
}

if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
    $msg_tipo = $_GET['tipo'] ?? 'success';
}

// Buscar produtos (apenas 6 principais, sem duplicatas)
$produtos = fetchAll("SELECT DISTINCT * FROM produtos WHERE ativo = 1 ORDER BY id DESC LIMIT 6");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Produtos - Ferrobras Metais</title>
    <link rel="icon" type="image/png" href="../img/ferrobrasmetais_logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; background: #f4f6f8; }
        .header { background: #1a1a1a; color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; }
        .header h1 { color: #d61935; }
        .container { max-width: 1200px; margin: 30px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn { display: inline-block; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold; border: none; cursor: pointer; }
        .btn-primary { background: #d61935; color: white; }
        .btn-primary:hover { background: #b01229; }
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #218838; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .btn-voltar { background: #6c757d; color: white; }
        .btn-voltar:hover { background: #5a6268; }
        .btn-warning { background: #ffc107; color: #333; }
        .btn-warning:hover { background: #e0a800; }
        .form-produto { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 2px solid #ddd; }
        .form-produto input, .form-produto textarea, .form-produto select { 
            padding: 10px; margin: 5px 0; border: 1px solid #ddd; border-radius: 5px; width: 100%; max-width: 300px; 
        }
        .form-produto textarea { max-width: 100%; height: 80px; }
        .form-group { margin-bottom: 10px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
        .produtos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }
        .produto-item { background: #f8f9fa; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .produto-item img { width: 100%; height: 150px; object-fit: cover; background: #e9ecef; }
        .produto-item .info { padding: 10px; font-size: 14px; }
        .produto-item .info .nome { font-weight: bold; color: #333; font-size: 16px; }
        .produto-item .info .categoria { color: #999; font-size: 12px; }
        .produto-item .info .descricao { color: #666; font-size: 13px; margin-top: 5px; }
        .produto-item .info .preco { color: #d61935; font-weight: bold; margin-top: 5px; }
        .produto-item .acoes { padding: 10px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .msg { padding: 15px; border-radius: 5px; margin-bottom: 15px; }
        .msg-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .msg-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .vazio { text-align: center; padding: 40px; color: #999; }
        .categoria-badge { display: inline-block; padding: 3px 10px; background: #e9ecef; border-radius: 12px; font-size: 12px; color: #333; }
        .sem-imagem { width: 100%; height: 150px; background: #e9ecef; display: flex; align-items: center; justify-content: center; color: #999; font-size: 14px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        @media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>📦 Produtos</h1>
        <div>
            <a href="painel.php" class="btn btn-voltar">← Voltar</a>
            <a href="logout.php" class="btn btn-danger">Sair</a>
        </div>
    </div>
    <div class="container">
        <h2>📦 Gerenciar Produtos</h2>
        
        <?php if ($msg): ?>
        <div class="msg msg-<?php echo $msg_tipo; ?>">
            <?php echo $msg; ?>
        </div>
        <?php endif; ?>
        
        <!-- Formulário de Cadastro/Edição -->
        <div class="form-produto">
            <h3><?php echo $editando ? '✏️ Editar Produto' : '➕ Novo Produto'; ?></h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $editando ? $editando['id'] : 0; ?>">
                
                <?php if ($editando && $editando['imagem']): ?>
                <div class="form-group">
                    <label>Imagem atual:</label>
                    <?php 
                    $caminho_img = '../imagens/produtos/' . $editando['imagem'];
                    if (file_exists($caminho_img)): 
                    ?>
                    <img src="<?php echo $caminho_img; ?>" style="max-height:100px;display:block;margin:5px 0;">
                    <?php else: ?>
                    <p style="color:#999;">Imagem não encontrada no servidor</p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Nova imagem (opcional):</label>
                    <input type="file" name="imagem" accept="image/*">
                    <small style="color:#999;">Selecione uma imagem para substituir a atual</small>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Nome do Produto *</label>
                        <input type="text" name="nome" value="<?php echo $editando ? htmlspecialchars($editando['nome']) : ''; ?>" required style="max-width:100%;">
                    </div>
                    <div class="form-group">
                        <label>Categoria</label>
                        <input type="text" name="categoria" value="<?php echo $editando ? htmlspecialchars($editando['categoria']) : ''; ?>" style="max-width:100%;">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Descrição</label>
                    <textarea name="descricao" style="max-width:100%;"><?php echo $editando ? htmlspecialchars($editando['descricao']) : ''; ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Preço</label>
                        <input type="text" name="preco" value="<?php echo $editando && isset($editando['preco']) ? htmlspecialchars($editando['preco']) : 'Sob consulta'; ?>" style="max-width:100%;" placeholder="Sob consulta">
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="ativo" value="1" <?php echo ($editando && $editando['ativo']) || !$editando ? 'checked' : ''; ?>>
                            Ativo
                        </label>
                    </div>
                </div>
                
                <button type="submit" name="salvar" class="btn btn-primary">💾 Salvar</button>
                <?php if ($editando): ?>
                <a href="produtos.php" class="btn btn-voltar">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Lista de Produtos -->
        <?php if ($produtos && count($produtos) > 0): ?>
        <div class="produtos-grid">
            <?php foreach ($produtos as $p): ?>
            <div class="produto-item">
                <?php 
                $caminho_img = '../imagens/produtos/' . $p['imagem'];
                if ($p['imagem'] && file_exists($caminho_img)): 
                ?>
                <img src="<?php echo $caminho_img; ?>" alt="<?php echo htmlspecialchars($p['nome']); ?>">
                <?php else: ?>
                <div class="sem-imagem">📷 Sem imagem</div>
                <?php endif; ?>
                <div class="info">
                    <div class="nome"><?php echo htmlspecialchars($p['nome']); ?></div>
                    <div class="categoria">
                        <?php if ($p['categoria']): ?>
                        <span class="categoria-badge"><?php echo htmlspecialchars($p['categoria']); ?></span>
                        <?php endif; ?>
                        <?php echo $p['ativo'] ? '✅ Ativo' : '❌ Inativo'; ?>
                    </div>
                    <?php if ($p['descricao']): ?>
                    <div class="descricao"><?php echo htmlspecialchars(substr($p['descricao'], 0, 60)) . (strlen($p['descricao']) > 60 ? '...' : ''); ?></div>
                    <?php endif; ?>
                    <?php if (isset($p['preco']) && $p['preco']): ?>
                    <div class="preco">💰 <?php echo htmlspecialchars($p['preco']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="acoes">
                    <a href="?edit=<?php echo $p['id']; ?>" class="btn btn-warning">✏️ Editar</a>
                    <a href="?delete=<?php echo $p['id']; ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este produto?')">🗑️ Excluir</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <p style="margin-top:10px;color:#999;">Total: <?php echo count($produtos); ?> produtos</p>
        <?php else: ?>
        <div class="vazio">
            <p>📭 Nenhum produto cadastrado ainda.</p>
            <p style="font-size:14px;">Use o formulário acima para cadastrar um novo produto.</p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>