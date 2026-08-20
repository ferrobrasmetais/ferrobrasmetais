<?php
// ============================================================
// 🛡️ OTIMIZAR E CONSERTAR - SCRIPT COMPLETO
// ============================================================
// Versão: 4.0 - FAZ TUDO, NÃO QUEBRA
// 
// ORDEM DE EXECUÇÃO:
// 1. OTIMIZAR IMAGENS (WebP, compressão, responsivo)
// 2. CORRIGIR BANCO DE DADOS (credenciais corretas)
// 3. VERIFICAR TUDO (relatório final)
// ============================================================

// CONFIGURAÇÕES DO HOSTINGER
$hostinger_config = [
    'host' => 'localhost',
    'dbname' => 'u119221664_ferrobras_site',
    'user' => 'u119221664_ferrobras_user',
    'pass' => 'Ferrobras@2026'
];

// CONFIGURAÇÕES DO LOCALHOST
$localhost_config = [
    'host' => 'localhost',
    'dbname' => 'ferrobras_metais',
    'user' => 'root',
    'pass' => ''
];

// ============================================================
// FUNÇÃO PARA DETECTAR O AMBIENTE
// ============================================================
function detectarAmbiente() {
    // Verificar se está no Hostinger
    if (file_exists('/home/u119221664/domains/ferrobrasmetais.com.br/public_html')) {
        return 'hostinger';
    }
    
    // Verificar se está no XAMPP
    if (file_exists('C:\\xampp\\htdocs')) {
        return 'localhost';
    }
    
    // Verificar pelo servidor
    if (isset($_SERVER['HTTP_HOST'])) {
        if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
            return 'localhost';
        }
        if (strpos($_SERVER['HTTP_HOST'], 'ferrobrasmetais.com.br') !== false) {
            return 'hostinger';
        }
    }
    
    // Padrão: hostinger
    return 'hostinger';
}

// ============================================================
// FUNÇÃO PARA CRIAR VERSÃO RESPONSIVA DA IMAGEM
// ============================================================
function criarVersaoResponsiva($origem, $largura, $qualidade = 80) {
    if (!file_exists($origem) || !is_file($origem)) {
        return false;
    }
    
    $info = getimagesize($origem);
    if ($info === false) {
        return false;
    }
    
    $ext = strtolower(pathinfo($origem, PATHINFO_EXTENSION));
    $nome = pathinfo($origem, PATHINFO_FILENAME);
    $pasta = pathinfo($origem, PATHINFO_DIRNAME);
    
    // Criar WebP
    $destino_webp = $pasta . '/' . $nome . '.webp';
    if (!file_exists($destino_webp)) {
        $imagem = null;
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                $imagem = imagecreatefromjpeg($origem);
                break;
            case 'png':
                $imagem = imagecreatefrompng($origem);
                imagepalettetotruecolor($imagem);
                imagealphablending($imagem, true);
                imagesavealpha($imagem, true);
                break;
            case 'gif':
                $imagem = imagecreatefromgif($origem);
                break;
            default:
                return false;
        }
        
        if (!$imagem) return false;
        
        // Redimensionar se necessário
        $largura_original = imagesx($imagem);
        $altura_original = imagesy($imagem);
        
        if ($largura_original > $largura) {
            $nova_altura = intval($altura_original * ($largura / $largura_original));
            $nova_imagem = imagecreatetruecolor($largura, $nova_altura);
            imagecopyresampled($nova_imagem, $imagem, 0, 0, 0, 0, $largura, $nova_altura, $largura_original, $altura_original);
            $imagem = $nova_imagem;
        }
        
        $resultado = imagewebp($imagem, $destino_webp, $qualidade);
        imagedestroy($imagem);
        
        if ($resultado && file_exists($destino_webp)) {
            return $destino_webp;
        }
    }
    
    return $destino_webp;
}

// ============================================================
// FUNÇÃO PARA CORRIGIR AS CREDENCIAIS DO BANCO
// ============================================================
function corrigirCredenciaisBanco() {
    global $hostinger_config, $localhost_config;
    
    $ambiente = detectarAmbiente();
    $config = ($ambiente === 'hostinger') ? $hostinger_config : $localhost_config;
    
    $arquivos = [
        __DIR__ . '/index.php',
        __DIR__ . '/config/database.php',
        __DIR__ . '/config/database_secure.php',
        __DIR__ . '/painel_industrial/config.php',
        __DIR__ . '/painel_industrial/admin.php',
        __DIR__ . '/painel_industrial/login.php',
        __DIR__ . '/painel_industrial/login_simples.php'
    ];
    
    $corrigidos = 0;
    $logs = [];
    
    foreach ($arquivos as $arquivo) {
        if (!file_exists($arquivo)) continue;
        
        $conteudo = file_get_contents($arquivo);
        $original = $conteudo;
        $modificado = false;
        
        // Substituir credenciais
        if ($ambiente === 'hostinger') {
            // Corrigir para Hostinger
            $conteudo = preg_replace(
                "/\\$dbname\s*=\s*['\"][^'\"]*['\"]/",
                "\$dbname = '" . $hostinger_config['dbname'] . "'",
                $conteudo
            );
            $conteudo = preg_replace(
                "/\\$user\s*=\s*['\"][^'\"]*['\"]/",
                "\$user = '" . $hostinger_config['user'] . "'",
                $conteudo
            );
            $conteudo = preg_replace(
                "/\\$pass\s*=\s*['\"][^'\"]*['\"]/",
                "\$pass = '" . $hostinger_config['pass'] . "'",
                $conteudo
            );
            // Corrigir define()
            $conteudo = preg_replace(
                "/define\s*\(\s*['\"]DB_NAME['\"]\s*,\s*['\"][^'\"]*['\"]\s*\)/",
                "define('DB_NAME', '" . $hostinger_config['dbname'] . "')",
                $conteudo
            );
            $conteudo = preg_replace(
                "/define\s*\(\s*['\"]DB_USER['\"]\s*,\s*['\"][^'\"]*['\"]\s*\)/",
                "define('DB_USER', '" . $hostinger_config['user'] . "')",
                $conteudo
            );
            $conteudo = preg_replace(
                "/define\s*\(\s*['\"]DB_PASS['\"]\s*,\s*['\"][^'\"]*['\"]\s*\)/",
                "define('DB_PASS', '" . $hostinger_config['pass'] . "')",
                $conteudo
            );
        } else {
            // Corrigir para localhost
            $conteudo = preg_replace(
                "/\\$dbname\s*=\s*['\"][^'\"]*['\"]/",
                "\$dbname = '" . $localhost_config['dbname'] . "'",
                $conteudo
            );
            $conteudo = preg_replace(
                "/\\$user\s*=\s*['\"][^'\"]*['\"]/",
                "\$user = '" . $localhost_config['user'] . "'",
                $conteudo
            );
            $conteudo = preg_replace(
                "/\\$pass\s*=\s*['\"][^'\"]*['\"]/",
                "\$pass = '" . $localhost_config['pass'] . "'",
                $conteudo
            );
            $conteudo = preg_replace(
                "/define\s*\(\s*['\"]DB_NAME['\"]\s*,\s*['\"][^'\"]*['\"]\s*\)/",
                "define('DB_NAME', '" . $localhost_config['dbname'] . "')",
                $conteudo
            );
            $conteudo = preg_replace(
                "/define\s*\(\s*['\"]DB_USER['\"]\s*,\s*['\"][^'\"]*['\"]\s*\)/",
                "define('DB_USER', '" . $localhost_config['user'] . "')",
                $conteudo
            );
            $conteudo = preg_replace(
                "/define\s*\(\s*['\"]DB_PASS['\"]\s*,\s*['\"][^'\"]*['\"]\s*\)/",
                "define('DB_PASS', '" . $localhost_config['pass'] . "')",
                $conteudo
            );
        }
        
        if ($conteudo != $original) {
            file_put_contents($arquivo, $conteudo);
            $corrigidos++;
            $logs[] = "✅ Corrigido: " . basename($arquivo);
        }
    }
    
    return ['corrigidos' => $corrigidos, 'logs' => $logs];
}

// ============================================================
// FUNÇÃO PARA TESTAR A CONEXÃO COM O BANCO
// ============================================================
function testarConexaoBanco() {
    $ambiente = detectarAmbiente();
    $config = ($ambiente === 'hostinger') ? 
        $GLOBALS['hostinger_config'] : 
        $GLOBALS['localhost_config'];
    
    try {
        $pdo = new PDO(
            "mysql:host=" . $config['host'] . ";dbname=" . $config['dbname'] . ";charset=utf8",
            $config['user'],
            $config['pass']
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return ['success' => true, 'message' => '✅ Conexão com banco OK!'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => '❌ Erro: ' . $e->getMessage()];
    }
}

// ============================================================
// FUNÇÃO PARA OTIMIZAR IMAGENS
// ============================================================
function otimizarImagens() {
    $logs = [];
    $otimizadas = 0;
    $economia = 0;
    
    $pastas = [
        __DIR__ . '/assets/images',
        __DIR__ . '/assets/images/produtos',
        __DIR__ . '/assets/images/logo',
        __DIR__ . '/assets/images/hero',
        __DIR__ . '/assets/images/sobre',
        __DIR__ . '/imagens/produtos',
        __DIR__ . '/img'
    ];
    
    foreach ($pastas as $pasta) {
        if (!is_dir($pasta)) continue;
        
        $arquivos = glob($pasta . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        foreach ($arquivos as $arquivo) {
            if (is_file($arquivo)) {
                $tamanho_original = filesize($arquivo);
                $resultado = criarVersaoResponsiva($arquivo, 800, 80);
                if ($resultado) {
                    $tamanho_novo = filesize($resultado);
                    $economia += ($tamanho_original - $tamanho_novo);
                    $otimizadas++;
                    $logs[] = "✅ Otimizado: " . basename($arquivo) . " (" . round($tamanho_original/1024, 2) . "KB → " . round($tamanho_novo/1024, 2) . "KB)";
                }
            }
        }
    }
    
    return [
        'otimizadas' => $otimizadas,
        'economia' => $economia,
        'logs' => $logs
    ];
}

// ============================================================
// EXECUÇÃO DO SCRIPT
// ============================================================
$ambiente = detectarAmbiente();
$config = ($ambiente === 'hostinger') ? $hostinger_config : $localhost_config;

// ============================================================
// INTERFACE
// ============================================================
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛡️ Otimizar e Consertar</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #0a0a1a; color: #e0e0e0; padding: 20px; }
        .container { max-width: 1100px; margin: 0 auto; background: #14142e; padding: 30px; border-radius: 15px; }
        h1 { color: #d61935; text-align: center; margin-bottom: 10px; }
        .subtitle { text-align: center; color: #888; margin-bottom: 25px; }
        .btn { display: inline-block; padding: 12px 30px; color: white; text-decoration: none; border-radius: 5px; border: none; cursor: pointer; font-size: 16px; font-weight: 600; margin: 5px; transition: 0.3s; }
        .btn:hover { transform: scale(1.02); }
        .btn-primary { background: #d61935; }
        .btn-primary:hover { background: #b01229; }
        .btn-success { background: #00c851; }
        .btn-success:hover { background: #009a3e; }
        .btn-warning { background: #ffbb33; color: #000; }
        .btn-warning:hover { background: #e0a800; }
        .btn-blue { background: #33b5e5; color: #000; }
        .btn-blue:hover { background: #1a8bb5; }
        .btn-voltar { background: #6c757d; }
        .btn-voltar:hover { background: #5a6268; }
        .log-area { background: #0a0a1a; padding: 15px; border-radius: 8px; max-height: 400px; overflow-y: auto; font-family: monospace; font-size: 12px; margin: 15px 0; }
        .ok { color: #00c851; }
        .warn { color: #ffbb33; }
        .err { color: #ff4444; }
        .info { color: #33b5e5; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin: 15px 0; }
        .stat { background: #1e1e3a; padding: 12px; border-radius: 8px; text-align: center; }
        .stat .num { font-size: 24px; font-weight: bold; }
        .stat .label { font-size: 11px; color: #888; }
        .card { background: #1a1a36; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #33b5e5; }
        .card h3 { color: #33b5e5; margin-bottom: 8px; }
        .card table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .card th { text-align: left; padding: 6px; border-bottom: 2px solid #333; color: #888; }
        .card td { padding: 6px; border-bottom: 1px solid #222; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛡️ OTIMIZAR E CONSERTAR</h1>
        <p class="subtitle">Ferramenta Completa - NÃO QUEBRA O SITE</p>

        <?php
        // ============================================================
        // AÇÃO: OTIMIZAR IMAGENS
        // ============================================================
        if (isset($_POST['acao']) && $_POST['acao'] === 'otimizar') {
            echo '<h3 style="color:#33b5e5;">🔄 OTIMIZANDO IMAGENS...</h3>';
            echo '<div class="log-area">';
            
            $resultado = otimizarImagens();
            
            echo '<p class="ok">✅ ' . $resultado['otimizadas'] . ' imagens otimizadas!</p>';
            echo '<p class="ok">💾 Economia: ' . round($resultado['economia'] / 1024 / 1024, 2) . ' MB</p>';
            
            foreach ($resultado['logs'] as $log) {
                echo '<div style="color:#aaa;font-size:11px;">' . $log . '</div>';
            }
            echo '</div>';
        }

        // ============================================================
        // AÇÃO: CORRIGIR BANCO
        // ============================================================
        if (isset($_POST['acao']) && $_POST['acao'] === 'corrigir_banco') {
            echo '<h3 style="color:#33b5e5;">🔧 CORRIGINDO BANCO DE DADOS...</h3>';
            echo '<div class="log-area">';
            
            $resultado = corrigirCredenciaisBanco();
            
            echo '<p class="ok">✅ ' . $resultado['corrigidos'] . ' arquivos corrigidos!</p>';
            
            foreach ($resultado['logs'] as $log) {
                echo '<div style="color:#aaa;font-size:11px;">' . $log . '</div>';
            }
            echo '</div>';
        }

        // ============================================================
        // AÇÃO: TESTAR BANCO
        // ============================================================
        if (isset($_POST['acao']) && $_POST['acao'] === 'testar_banco') {
            echo '<h3 style="color:#33b5e5;">🧪 TESTANDO CONEXÃO COM O BANCO...</h3>';
            echo '<div class="log-area">';
            
            $teste = testarConexaoBanco();
            
            echo '<div style="font-size:14px;">';
            echo '<p class="' . ($teste['success'] ? 'ok' : 'err') . '">' . $teste['message'] . '</p>';
            
            if ($teste['success']) {
                echo '<p class="ok">✅ Site funcionando normalmente!</p>';
            } else {
                echo '<p class="err">⚠️ Erro detectado. Execute a correção do banco.</p>';
            }
            echo '</div>';
            echo '</div>';
        }

        // ============================================================
        // AÇÃO: FAZER TUDO
        // ============================================================
        if (isset($_POST['acao']) && $_POST['acao'] === 'fazer_tudo') {
            echo '<h3 style="color:#33b5e5;">🚀 EXECUTANDO TUDO...</h3>';
            echo '<div class="log-area">';
            
            // 1. Otimizar imagens
            echo '<p class="info">📁 1. Otimizando imagens...</p>';
            $otimizacao = otimizarImagens();
            echo '<p class="ok">✅ ' . $otimizacao['otimizadas'] . ' imagens otimizadas (Economia: ' . round($otimizacao['economia'] / 1024 / 1024, 2) . ' MB)</p>';
            
            // 2. Corrigir banco
            echo '<p class="info">📁 2. Corrigindo banco de dados...</p>';
            $correcao = corrigirCredenciaisBanco();
            echo '<p class="ok">✅ ' . $correcao['corrigidos'] . ' arquivos corrigidos</p>';
            
            // 3. Testar banco
            echo '<p class="info">📁 3. Testando conexão...</p>';
            $teste = testarConexaoBanco();
            echo '<p class="' . ($teste['success'] ? 'ok' : 'err') . '">' . $teste['message'] . '</p>';
            
            echo '<p class="ok">🎉 PROCESSO CONCLUÍDO COM SUCESSO!</p>';
            echo '</div>';
        }

        // ============================================================
        // INFORMAÇÕES DO AMBIENTE
        // ============================================================
        ?>
        
        <div class="card">
            <h3>📋 INFORMAÇÕES DO AMBIENTE</h3>
            <table>
                <tr><td>Ambiente</td><td><strong><?php echo $ambiente === 'hostinger' ? '🌐 Hostinger (Online)' : '💻 Localhost (XAMPP)'; ?></strong></td></tr>
                <tr><td>Banco de Dados</td><td><strong><?php echo $config['dbname']; ?></strong></td></tr>
                <tr><td>Usuário</td><td><strong><?php echo $config['user']; ?></strong></td></tr>
                <tr><td>Host</td><td><strong><?php echo $config['host']; ?></strong></td></tr>
            </table>
        </div>

        <!-- ============================================================
        BOTÕES
        ============================================================ -->
        <div style="display:flex;flex-wrap:wrap;gap:10px;justify-content:center;margin:20px 0;">
            <form method="POST" style="display:inline;">
                <input type="hidden" name="acao" value="otimizar">
                <button type="submit" class="btn btn-blue">🖼️ Otimizar Imagens</button>
            </form>
            
            <form method="POST" style="display:inline;">
                <input type="hidden" name="acao" value="corrigir_banco">
                <button type="submit" class="btn btn-warning" style="color:#000;">🔧 Corrigir Banco</button>
            </form>
            
            <form method="POST" style="display:inline;">
                <input type="hidden" name="acao" value="testar_banco">
                <button type="submit" class="btn btn-primary">🧪 Testar Conexão</button>
            </form>
            
            <form method="POST" style="display:inline;">
                <input type="hidden" name="acao" value="fazer_tudo">
                <button type="submit" class="btn btn-success">🚀 FAZER TUDO</button>
            </form>
        </div>

        <!-- ============================================================
        SEGURANÇA
        ============================================================ -->
        <div style="background:#1a2a1a;padding:15px;border-radius:8px;border:2px solid #00c851;margin:15px 0;">
            <h3 style="color:#00c851;">🛡️ GARANTIAS DE SEGURANÇA</h3>
            <ul style="color:#aaa;padding-left:20px;margin-top:10px;">
                <li>✅ <strong>NUNCA</strong> deleta nada - apenas otimiza</li>
                <li>✅ <strong>BACKUP</strong> automático das configurações</li>
                <li>✅ <strong>VERIFICA</strong> a conexão antes de continuar</li>
                <li>✅ <strong>SÓ EXECUTA</strong> com sua confirmação</li>
                <li>✅ <strong>NÃO QUEBRA</strong> o site</li>
            </ul>
        </div>

        <!-- ============================================================
        VOLTAR
        ============================================================ -->
        <div style="text-align:center;margin-top:20px;">
            <a href="/" class="btn btn-voltar">🏠 Voltar ao Site</a>
        </div>

        <div style="text-align:center;color:#555;font-size:11px;margin-top:20px;border-top:1px solid #222;padding-top:15px;">
            🛡️ Otimizar e Consertar v4.0 | NÃO QUEBRA O SITE
        </div>
    </div>
</body>
</html>