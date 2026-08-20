<?php
// rga/catalogo.php - CatÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡logo em formato CARDS
session_start();
require_once '../includes/functions_rga.php';
require_once '../config/database_rga.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$db = db_rga();
$usuario = $db->fetchOne("SELECT nivel FROM usuarios WHERE id = ?", [$_SESSION['user_id']]);
$nivel = $usuario['nivel'] ?? 'visualizador';
$podeEditar = in_array($nivel, ['admin', 'gerente', 'operador']);

$mensagem = '';
$tipoMensagem = '';

// Adicionar produto
if (isset($_POST['acao']) && $_POST['acao'] == 'adicionar' && $podeEditar) {
    $dados = [
        'nome' => $_POST['nome'],
        'categoria' => $_POST['categoria'],
        'material' => $_POST['material'],
        'especificacao' => $_POST['especificacao'] ?? '',
        'acabamento' => $_POST['acabamento'] ?? '',
        'formato' => $_POST['formato'],
        'tipo_formato' => $_POST['tipo_formato'] ?? 'redondo',
        'tipo_produto' => $_POST['tipo_produto'] ?? 'maciÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§o',
        'simbolo' => $_POST['simbolo'] ?? 'ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒâ€šÃ‚Â¦',
        'densidade_padrao' => floatval($_POST['densidade_padrao'] ?? 0.00785),
        'ativo' => isset($_POST['ativo']) ? 1 : 0
    ];
    
    try {
        $rga = rga();
        $rga->adicionarCatalogo($dados);
        $mensagem = "Produto adicionado com sucesso!";
        $tipoMensagem = "success";
    } catch (Exception $e) {
        $mensagem = "Erro: " . $e->getMessage();
        $tipoMensagem = "danger";
    }
}

// Excluir produto
if (isset($_POST['acao']) && $_POST['acao'] == 'excluir' && $podeEditar) {
    $id = intval($_POST['id'] ?? 0);
    if ($id) {
        $rga = rga();
        $rga->excluirCatalogo($id);
        $mensagem = "Produto excluÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â­do!";
        $tipoMensagem = "success";
    }
}

$catalogo = $db->fetchAll("SELECT * FROM catalogo_produtos ORDER BY nome");
$acabamentos = $db->fetchAll("SELECT * FROM acabamentos WHERE ativo = 1 ORDER BY ordem");

// Cores para cada tipo
$cores = [
    'tubo' => '#dc3545',
    'quadrado' => '#007bff',
    'maciÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§o' => '#28a745',
    'chapa' => '#ffc107',
    'sextavado' => '#6f42c1'
];

$icones = [
    'tubo' => 'fa-solid fa-circle',
    'quadrado' => 'fa-solid fa-square',
    'maciÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§o' => 'fa-solid fa-circle',
    'chapa' => 'fa-solid fa-square',
    'sextavado' => 'fa-solid fa-hexagon'
];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CatÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡logo - RGA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; }
        
        .header { background: white; padding: 20px 30px; border-radius: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .header h1 { color: #333; }
        .header h1 span { color: #d61935; }
        
        .menu { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; background: white; padding: 15px 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .menu a { padding: 10px 20px; border-radius: 8px; text-decoration: none; color: #333; transition: all 0.3s; font-weight: 500; }
        .menu a:hover { background: #d61935; color: white; transform: translateY(-2px); }
        .menu a.active { background: #d61935; color: white; }
        
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 600; transition: all 0.3s; font-size: 0.9rem; }
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #218838; transform: translateY(-2px); }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; transform: translateY(-2px); }
        .btn-warning { background: #ffc107; color: #333; }
        .btn-warning:hover { background: #e0a800; transform: translateY(-2px); }
        .btn-back { background: #6c757d; color: white; }
        .btn-back:hover { background: #5a6268; transform: translateY(-2px); }
        .btn-sm { padding: 4px 10px; font-size: 0.8rem; }
        .logout { color: #dc3545; text-decoration: none; font-weight: 600; padding: 8px 16px; border: 2px solid #dc3545; border-radius: 6px; }
        .logout:hover { background: #dc3545; color: white; }
        
        .toolbar { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; align-items: center; }
        .toolbar input, .toolbar select { padding: 10px; border: 2px solid #ddd; border-radius: 6px; font-size: 0.9rem; flex: 1; min-width: 150px; }
        .toolbar input:focus, .toolbar select:focus { border-color: #d61935; outline: none; }
        .toolbar .total { background: white; padding: 10px 20px; border-radius: 6px; font-weight: 600; color: #666; }
        
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; display: inline-block; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-tubo { background: #dc3545; color: white; }
        .badge-quadrado { background: #007bff; color: white; }
        .badge-macico { background: #28a745; color: white; }
        .badge-chapa { background: #ffc107; color: #333; }
        .badge-sextavado { background: #6f42c1; color: white; }
        
        .msg { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .msg-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .msg-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        /* CARDS GRID */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        
        .card-item {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border-top: 4px solid #ddd;
            position: relative;
        }
        .card-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        .card-item .card-header {
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
        }
        .card-item .card-header .simbolo {
            font-size: 2rem;
        }
        .card-item .card-header .tipo-badge {
            font-size: 0.7rem;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
        }
        .card-item .card-body {
            padding: 20px;
        }
        .card-item .card-body .nome {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        .card-item .card-body .material {
            font-size: 0.9rem;
            color: #666;
        }
        .card-item .card-body .info-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 0.85rem;
            color: #666;
            border-bottom: 1px solid #f5f5f5;
        }
        .card-item .card-body .info-row:last-child {
            border-bottom: none;
        }
        .card-item .card-body .info-row .label { font-weight: 500; color: #333; }
        .card-item .card-footer {
            padding: 12px 20px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        .card-item .card-footer .acoes { display: flex; gap: 5px; }
        .card-item .status-indicator { display: flex; align-items: center; gap: 5px; font-size: 0.8rem; }
        .card-item .status-indicator .dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
        .card-item .status-indicator .dot.active { background: #28a745; }
        .card-item .status-indicator .dot.inactive { background: #dc3545; }
        .card-item.inactive { opacity: 0.6; }
        .card-item .card-top { position: relative; }
        
        /* Cores por tipo */
        .card-item.tubo { border-top-color: #dc3545; }
        .card-item.quadrado { border-top-color: #007bff; }
        .card-item.macico { border-top-color: #28a745; }
        .card-item.chapa { border-top-color: #ffc107; }
        .card-item.sextavado { border-top-color: #6f42c1; }
        
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; padding: 20px; }
        .modal-content { background: white; padding: 30px; border-radius: 10px; max-width: 700px; width: 100%; max-height: 90vh; overflow-y: auto; }
        .modal-content .close { float: right; font-size: 1.5rem; cursor: pointer; color: #999; }
        .modal-content .close:hover { color: #333; }
        .modal-content h2 { color: #d61935; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 0.9rem; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; font-size: 0.95rem; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #d61935; outline: none; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        
        @media (max-width: 768px) {
            .form-row { grid-template-columns: 1fr; }
            .header { flex-direction: column; text-align: center; }
            .menu { flex-direction: column; }
            .menu a { text-align: center; }
            .cards-grid { grid-template-columns: 1fr 1fr; }
            .toolbar { flex-direction: column; }
            .toolbar input { width: 100%; }
        }
        @media (max-width: 480px) {
            .cards-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div>
                <h1>ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¹ <span>CatÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡logo de Produtos</span></h1>
                <p style="color:#666;font-size:0.9rem;">VisualizaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o em cards</p>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a href="dashboard.php" class="btn btn-back">ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â Ãƒâ€šÃ‚Â Voltar</a>
                <?php if ($podeEditar): ?>
                <button class="btn btn-success" onclick="abrirModal()">ÃƒÆ’Ã‚Â¢Ãƒâ€¦Ã‚Â¾ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¢ Novo Produto</button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Menu -->
        <div class="menu">
            <a href="dashboard.php">ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒâ€¦Ã‚Â  Dashboard</a>
            <a href="estoque.php">ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒâ€šÃ‚Â¦ Estoque</a>
            <a href="catalogo.php" class="active">ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¹ CatÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡logo</a>
            <a href="usuarios.php">ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã‹Å“Ãƒâ€šÃ‚Â¥ UsuÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡rios</a>
            <a href="backup.php">ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢Ãƒâ€šÃ‚Â¾ Backup</a>
            <a href="logout.php">ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸Ãƒâ€¦Ã‚Â¡Ã‚Âª Sair</a>
        </div>

        <?php if ($mensagem): ?>
        <div class="msg msg-<?php echo $tipoMensagem; ?>"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <!-- Toolbar -->
        <div class="toolbar">
            <input type="text" id="buscar" placeholder="ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã‚ÂÃƒâ€šÃ‚Â Buscar produtos..." onkeyup="filtrarCards()">
            <select id="filtroTipo" onchange="filtrarCards()">
                <option value="">Todos os Tipos</option>
                <option value="tubo">ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã‚ÂÃƒâ€šÃ‚Â´ Tubo</option>
                <option value="quadrado">ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸Ãƒâ€¦Ã‚Â¸Ãƒâ€šÃ‚Â¦ Quadrado</option>
                <option value="maciÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§o">ÃƒÆ’Ã‚Â¢Ãƒâ€¦Ã‚Â¡Ãƒâ€šÃ‚Â« MaciÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§o</option>
                <option value="chapa">ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã‚ÂÃƒâ€šÃ‚Â² Chapa</option>
                <option value="sextavado">ÃƒÆ’Ã‚Â¢Ãƒâ€šÃ‚Â¬Ãƒâ€šÃ‚Â¡ Sextavado</option>
            </select>
            <select id="filtroStatus" onchange="filtrarCards()">
                <option value="">Todos</option>
                <option value="1">Ativos</option>
                <option value="0">Inativos</option>
            </select>
            <span class="total">Total: <?php echo count($catalogo); ?> produtos</span>
        </div>

        <!-- Cards Grid -->
        <div class="cards-grid" id="cardsGrid">
            <?php if (empty($catalogo)): ?>
            <p style="text-align:center;padding:40px;color:#999;grid-column:1/-1;">Nenhum produto cadastrado.</p>
            <?php else: foreach ($catalogo as $produto): 
                $tipo = $produto['tipo_produto'] ?? 'maciÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§o';
                $simbolo = $produto['simbolo'] ?? 'ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒâ€šÃ‚Â¦';
                $cor = $cores[$tipo] ?? '#6c757d';
                $badgeClass = 'badge-macico';
                if ($tipo == 'tubo') $badgeClass = 'badge-tubo';
                elseif ($tipo == 'quadrado') $badgeClass = 'badge-quadrado';
                elseif ($tipo == 'chapa') $badgeClass = 'badge-chapa';
                elseif ($tipo == 'sextavado') $badgeClass = 'badge-sextavado';
            ?>
            <div class="card-item <?php echo $tipo; ?> <?php echo $produto['ativo'] ? '' : 'inactive'; ?>" 
                 data-nome="<?php echo strtolower($produto['nome']); ?>"
                 data-tipo="<?php echo $tipo; ?>"
                 data-status="<?php echo $produto['ativo']; ?>">
                <div class="card-header">
                    <span class="simbolo"><?php echo $simbolo; ?></span>
                    <span class="tipo-badge <?php echo $badgeClass; ?>"><?php echo ucfirst($tipo); ?></span>
                </div>
                <div class="card-body">
                    <div class="nome"><?php echo htmlspecialchars($produto['nome']); ?></div>
                    <div class="material"><strong>Material:</strong> <?php echo htmlspecialchars($produto['material']); ?></div>
                    <div style="margin-top:10px;">
                        <div class="info-row"><span class="label">EspecificaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o</span> <span><?php echo htmlspecialchars($produto['especificacao'] ?? '-'); ?></span></div>
                        <div class="info-row"><span class="label">Acabamento</span> <span><?php echo htmlspecialchars($produto['acabamento'] ?? '-'); ?></span></div>
                        <div class="info-row"><span class="label">Formato</span> <span><?php echo htmlspecialchars($produto['formato']); ?></span></div>
                        <div class="info-row"><span class="label">Densidade</span> <span><?php echo number_format($produto['densidade_padrao'], 5, ',', '.'); ?> g/cmÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â³</span></div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="status-indicator">
                        <span class="dot <?php echo $produto['ativo'] ? 'active' : 'inactive'; ?>"></span>
                        <?php echo $produto['ativo'] ? 'Ativo' : 'Inativo'; ?>
                    </div>
                    <?php if ($podeEditar): ?>
                    <div class="acoes">
                        <button class="btn btn-warning btn-sm" onclick="editar(<?php echo htmlspecialchars(json_encode($produto)); ?>)">ÃƒÆ’Ã‚Â¢Ãƒâ€¦Ã¢â‚¬Å“Ãƒâ€šÃ‚ÂÃƒÆ’Ã‚Â¯Ãƒâ€šÃ‚Â¸Ãƒâ€šÃ‚Â</button>
                        <button class="btn btn-danger btn-sm" onclick="excluir(<?php echo $produto['id']; ?>, '<?php echo htmlspecialchars($produto['nome']); ?>')">ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã¢â‚¬ÂÃƒÂ¢Ã¢â€šÂ¬Ã‹Å“ÃƒÆ’Ã‚Â¯Ãƒâ€šÃ‚Â¸Ãƒâ€šÃ‚Â</button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <!-- Modal -->
    <div id="modalCatalogo" class="modal">
        <div class="modal-content">
            <span class="close" onclick="fecharModal()">&times;</span>
            <h2 id="modalTitulo">ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒâ€šÃ‚Â Novo Produto</h2>
            <form method="POST">
                <input type="hidden" name="id" id="produtoId">
                <input type="hidden" name="acao" id="acao" value="adicionar">
                
                <div class="form-group">
                    <label>Nome do Produto *</label>
                    <input type="text" name="nome" id="nome" placeholder="Ex: Tubo MecÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢nico sem Costura" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Tipo de Produto *</label>
                        <select name="tipo_produto" id="tipo_produto" required>
                            <option value="tubo">ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã‚ÂÃƒâ€šÃ‚Â´ Tubo</option>
                            <option value="quadrado">ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸Ãƒâ€¦Ã‚Â¸Ãƒâ€šÃ‚Â¦ Quadrado</option>
                            <option value="maciÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§o">ÃƒÆ’Ã‚Â¢Ãƒâ€¦Ã‚Â¡Ãƒâ€šÃ‚Â« MaciÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§o</option>
                            <option value="chapa">ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã‚ÂÃƒâ€šÃ‚Â² Chapa</option>
                            <option value="sextavado">ÃƒÆ’Ã‚Â¢Ãƒâ€šÃ‚Â¬Ãƒâ€šÃ‚Â¡ Sextavado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>SÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â­mbolo</label>
                        <select name="simbolo" id="simbolo">
                            <option value="ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã‚ÂÃƒâ€šÃ‚Â´">ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã‚ÂÃƒâ€šÃ‚Â´ Tubo</option>
                            <option value="ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸Ãƒâ€¦Ã‚Â¸Ãƒâ€šÃ‚Â¦">ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸Ãƒâ€¦Ã‚Â¸Ãƒâ€šÃ‚Â¦ Quadrado</option>
                            <option value="ÃƒÆ’Ã‚Â¢Ãƒâ€¦Ã‚Â¡Ãƒâ€šÃ‚Â«">ÃƒÆ’Ã‚Â¢Ãƒâ€¦Ã‚Â¡Ãƒâ€šÃ‚Â« MaciÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§o</option>
                            <option value="ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã‚ÂÃƒâ€šÃ‚Â²">ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã‚ÂÃƒâ€šÃ‚Â² Chapa</option>
                            <option value="ÃƒÆ’Ã‚Â¢Ãƒâ€šÃ‚Â¬Ãƒâ€šÃ‚Â¡">ÃƒÆ’Ã‚Â¢Ãƒâ€šÃ‚Â¬Ãƒâ€šÃ‚Â¡ Sextavado</option>
                            <option value="ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒâ€šÃ‚Â¦">ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒâ€šÃ‚Â¦ PadrÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Categoria *</label>
                        <select name="categoria" id="categoria" required>
                            <option value="barra_macica">Barra MaciÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§a</option>
                            <option value="tubo">Tubo</option>
                            <option value="chapa">Chapa</option>
                            <option value="sextavado">Sextavado</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Formato *</label>
                        <select name="formato" id="formato" required>
                            <option value="redondo">Redondo</option>
                            <option value="chapa">Chapa / Bloco</option>
                            <option value="sextavado">Sextavado</option>
                            <option value="tuboRedondo">Tubo Redondo</option>
                            <option value="tuboQuadrado">Tubo Quadrado</option>
                            <option value="tuboRetangular">Tubo Retangular</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Material *</label>
                        <input type="text" name="material" id="material" placeholder="Ex: SAE 1045" required>
                    </div>
                    <div class="form-group">
                        <label>EspecificaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o</label>
                        <input type="text" name="especificacao" id="especificacao" placeholder="Ex: AÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§o Carbono">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Acabamento</label>
                        <select name="acabamento" id="acabamento">
                            <option value="">Selecione...</option>
                            <?php foreach ($acabamentos as $acab): ?>
                            <option value="<?php echo htmlspecialchars($acab['nome']); ?>"><?php echo htmlspecialchars($acab['nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Densidade (g/cmÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â³)</label>
                        <input type="number" name="densidade_padrao" id="densidade_padrao" step="0.00001" value="0.00785">
                    </div>
                </div>
                
                <div class="form-group">
                    <label><input type="checkbox" name="ativo" id="ativo" checked> Produto Ativo</label>
                </div>
                
                <button type="submit" class="btn btn-success" style="width:100%;padding:12px;margin-top:10px;">ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢Ãƒâ€šÃ‚Â¾ Salvar</button>
            </form>
        </div>
    </div>

    <script>
        function abrirModal(tipo, dados) {
            document.getElementById('modalCatalogo').style.display = 'flex';
            
            if (tipo === 'novo') {
                document.getElementById('modalTitulo').textContent = 'ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒâ€šÃ‚Â Novo Produto';
                document.getElementById('acao').value = 'adicionar';
                document.getElementById('produtoId').value = '';
                document.getElementById('nome').value = '';
                document.getElementById('tipo_produto').value = 'maciÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§o';
                document.getElementById('simbolo').value = 'ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒâ€šÃ‚Â¦';
                document.getElementById('categoria').value = 'barra_macica';
                document.getElementById('formato').value = 'redondo';
                document.getElementById('material').value = '';
                document.getElementById('especificacao').value = '';
                document.getElementById('acabamento').value = '';
                document.getElementById('densidade_padrao').value = '0.00785';
                document.getElementById('ativo').checked = true;
            }
        }

        function editar(dados) {
            document.getElementById('modalCatalogo').style.display = 'flex';
            document.getElementById('modalTitulo').textContent = 'ÃƒÆ’Ã‚Â¢Ãƒâ€¦Ã¢â‚¬Å“Ãƒâ€šÃ‚ÂÃƒÆ’Ã‚Â¯Ãƒâ€šÃ‚Â¸Ãƒâ€šÃ‚Â Editar Produto';
            document.getElementById('acao').value = 'editar';
            document.getElementById('produtoId').value = dados.id;
            document.getElementById('nome').value = dados.nome;
            document.getElementById('tipo_produto').value = dados.tipo_produto || 'maciÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§o';
            document.getElementById('simbolo').value = dados.simbolo || 'ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒâ€šÃ‚Â¦';
            document.getElementById('categoria').value = dados.categoria;
            document.getElementById('formato').value = dados.formato;
            document.getElementById('material').value = dados.material;
            document.getElementById('especificacao').value = dados.especificacao || '';
            document.getElementById('acabamento').value = dados.acabamento || '';
            document.getElementById('densidade_padrao').value = dados.densidade_padrao;
            document.getElementById('ativo').checked = dados.ativo == 1;
        }

        function excluir(id, nome) {
            if (confirm('Tem certeza que deseja excluir o produto "' + nome + '"?')) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = '<input type="hidden" name="acao" value="excluir"><input type="hidden" name="id" value="' + id + '">';
                document.body.appendChild(form);
                form.submit();
            }
        }

        function fecharModal() {
            document.getElementById('modalCatalogo').style.display = 'none';
        }

        function filtrarCards() {
            const busca = document.getElementById('buscar').value.toLowerCase();
            const filtroTipo = document.getElementById('filtroTipo').value;
            const filtroStatus = document.getElementById('filtroStatus').value;
            
            document.querySelectorAll('.card-item').forEach(card => {
                const nome = card.dataset.nome || '';
                const tipo = card.dataset.tipo || '';
                const status = card.dataset.status || '';
                
                const matchBusca = nome.includes(busca);
                const matchTipo = filtroTipo === '' || tipo === filtroTipo;
                const matchStatus = filtroStatus === '' || status === filtroStatus;
                
                card.style.display = (matchBusca && matchTipo && matchStatus) ? '' : 'none';
            });
            
            // Atualizar contador
            const visiveis = document.querySelectorAll('.card-item[style*="display: none"]').length;
            const total = document.querySelectorAll('.card-item').length;
            document.querySelector('.total').textContent = 'Mostrando ' + (total - visiveis) + ' de ' + total + ' produtos';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('modalCatalogo')) {
                fecharModal();
            }
        }
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') fecharModal();
        });
    </script>
</body>
</html>




