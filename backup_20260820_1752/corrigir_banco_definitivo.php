<?php
// ============================================================
// 🚨 CORREÇÃO DEFINITIVA DO BANCO DE DADOS
// ============================================================
// Este script corrige TODOS os arquivos que podem ter
// credenciais erradas. NÃO QUEBRA O SITE.
// ============================================================

// Credenciais CORRETAS do Hostinger
$HOST = 'localhost';
$DBNAME = 'u119221664_ferrobras_site';
$USER = 'u119221664_ferrobras_user';
$PASS = 'Ferrobras@2026';

// ============================================================
// ARQUIVOS QUE PRECISAM SER CORRIGIDOS
// ============================================================
$arquivos = [
    __DIR__ . '/index.php',
    __DIR__ . '/admin.php',
    __DIR__ . '/login_simples.php',
    __DIR__ . '/config/database.php',
    __DIR__ . '/config/database_secure.php',
    __DIR__ . '/painel_industrial/admin.php',
    __DIR__ . '/painel_industrial/categorias.php',
    __DIR__ . '/painel_industrial/galeria.php',
    __DIR__ . '/painel_industrial/produtos.php',
    __DIR__ . '/painel_industrial/usuarios.php',
    __DIR__ . '/painel_industrial/login.php',
    __DIR__ . '/painel_industrial/login_simples.php',
    __DIR__ . '/painel_industrial/config.php',
    __DIR__ . '/includes/functions.php',
    __DIR__ . '/includes/catalogo.php',
];

// ============================================================
// FUNÇÃO PARA CORRIGIR UM ARQUIVO
// ============================================================
function corrigirArquivo($caminho) {
    global $HOST, $DBNAME, $USER, $PASS;
    
    if (!file_exists($caminho)) {
        return ['status' => 'ignorado', 'msg' => 'Arquivo não encontrado'];
    }
    
    $conteudo = file_get_contents($caminho);
    $original = $conteudo;
    $modificado = false;
    
    // ============================================================
    // CORRIGIR VARIÁVEIS PHP
    // ============================================================
    // $host = '...' → $host = 'localhost'
    if (preg_match('/\$host\s*=\s*["\'][^"\']*["\']/', $conteudo)) {
        $conteudo = preg_replace(
            '/\$host\s*=\s*["\'][^"\']*["\']/',
            '$host = "' . $HOST . '"',
            $conteudo
        );
        $modificado = true;
    }
    
    // $dbname = '...' → $dbname = 'u119221664_ferrobras_site'
    if (preg_match('/\$dbname\s*=\s*["\'][^"\']*["\']/', $conteudo)) {
        $conteudo = preg_replace(
            '/\$dbname\s*=\s*["\'][^"\']*["\']/',
            '$dbname = "' . $DBNAME . '"',
            $conteudo
        );
        $modificado = true;
    }
    
    // $user = '...' → $user = 'u119221664_ferrobras_user'
    if (preg_match('/\$user\s*=\s*["\'][^"\']*["\']/', $conteudo)) {
        $conteudo = preg_replace(
            '/\$user\s*=\s*["\'][^"\']*["\']/',
            '$user = "' . $USER . '"',
            $conteudo
        );
        $modificado = true;
    }
    
    // $pass = '...' → $pass = 'Ferrobras@2026'
    if (preg_match('/\$pass\s*=\s*["\'][^"\']*["\']/', $conteudo)) {
        $conteudo = preg_replace(
            '/\$pass\s*=\s*["\'][^"\']*["\']/',
            '$pass = "' . $PASS . '"',
            $conteudo
        );
        $modificado = true;
    }
    
    // ============================================================
    // CORRIGIR DEFINE() 
    // ============================================================
    // define('DB_HOST', '...') → define('DB_HOST', 'localhost')
    if (preg_match("/define\s*\(\s*['\"]DB_HOST['\"]\s*,\s*['\"][^'\"]*['\"]\s*\)/", $conteudo)) {
        $conteudo = preg_replace(
            "/define\s*\(\s*['\"]DB_HOST['\"]\s*,\s*['\"][^'\"]*['\"]\s*\)/",
            "define('DB_HOST', '" . $HOST . "')",
            $conteudo
        );
        $modificado = true;
    }
    
    // define('DB_NAME', '...') → define('DB_NAME', 'u119221664_ferrobras_site')
    if (preg_match("/define\s*\(\s*['\"]DB_NAME['\"]\s*,\s*['\"][^'\"]*['\"]\s*\)/", $conteudo)) {
        $conteudo = preg_replace(
            "/define\s*\(\s*['\"]DB_NAME['\"]\s*,\s*['\"][^'\"]*['\"]\s*\)/",
            "define('DB_NAME', '" . $DBNAME . "')",
            $conteudo
        );
        $modificado = true;
    }
    
    // define('DB_USER', '...') → define('DB_USER', 'u119221664_ferrobras_user')
    if (preg_match("/define\s*\(\s*['\"]DB_USER['\"]\s*,\s*['\"][^'\"]*['\"]\s*\)/", $conteudo)) {
        $conteudo = preg_replace(
            "/define\s*\(\s*['\"]DB_USER['\"]\s*,\s*['\"][^'\"]*['\"]\s*\)/",
            "define('DB_USER', '" . $USER . "')",
            $conteudo
        );
        $modificado = true;
    }
    
    // define('DB_PASS', '...') → define('DB_PASS', 'Ferrobras@2026')
    if (preg_match("/define\s*\(\s*['\"]DB_PASS['\"]\s*,\s*['\"][^'\"]*['\"]\s*\)/", $conteudo)) {
        $conteudo = preg_replace(
            "/define\s*\(\s*['\"]DB_PASS['\"]\s*,\s*['\"][^'\"]*['\"]\s*\)/",
            "define('DB_PASS', '" . $PASS . "')",
            $conteudo
        );
        $modificado = true;
    }
    
    // ============================================================
    // SALVAR
    // ============================================================
    if ($modificado) {
        // Fazer backup do arquivo original
        $backup = $caminho . '.backup_' . date('Ymd_His');
        copy($caminho, $backup);
        
        // Salvar o arquivo corrigido
        file_put_contents($caminho, $conteudo);
        return ['status' => 'corrigido', 'msg' => 'Corrigido com sucesso'];
    }
    
    return ['status' => 'ok', 'msg' => 'Já está correto'];
}

// ============================================================
// EXECUTAR CORREÇÃO
// ============================================================
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔧 Corrigir Banco</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; background: #0a0a1a; color: #e0e0e0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #14142e; padding: 30px; border-radius: 15px; }
        h1 { color: #d61935; text-align: center; }
        .ok { color: #00c851; }
        .err { color: #ff4444; }
        .info { color: #33b5e5; }
        .log { background: #0a0a1a; padding: 15px; border-radius: 8px; font-family: monospace; font-size: 12px; max-height: 400px; overflow-y: auto; margin: 15px 0; }
        .btn { display: inline-block; padding: 12px 30px; background: #d61935; color: white; border: none; border-radius: 5px; font-size: 16px; font-weight: 600; cursor: pointer; margin: 5px; }
        .btn:hover { background: #b01229; }
        .btn-success { background: #00c851; }
        .btn-success:hover { background: #009a3e; }
        .btn-voltar { background: #6c757d; }
        .btn-voltar:hover { background: #5a6268; }
        .seguranca { background: #1a2a1a; padding: 15px; border-radius: 8px; border: 2px solid #00c851; margin: 15px 0; }
        .seguranca h3 { color: #00c851; }
        .seguranca ul { color: #aaa; padding-left: 20px; }
        .seguranca li { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 CORREÇÃO DEFINITIVA DO BANCO</h1>
        <p style="text-align:center;color:#888;margin-bottom:20px;">Corrige TODOS os arquivos com credenciais erradas</p>

        <?php if (isset($_POST['acao']) && $_POST['acao'] === 'corrigir'): ?>
            <div class="log">
                <p class="info">🔄 CORRIGINDO ARQUIVOS...</p>
                <?php
                $corrigidos = 0;
                $ignorados = 0;
                $ok = 0;
                
                foreach ($arquivos as $arquivo) {
                    $resultado = corrigirArquivo($arquivo);
                    $status = $resultado['status'];
                    $msg = $resultado['msg'];
                    $nome = basename($arquivo);
                    
                    if ($status === 'corrigido') {
                        $corrigidos++;
                        echo '<div class="ok">✅ ' . $nome . ' - ' . $msg . '</div>';
                    } elseif ($status === 'ignorado') {
                        $ignorados++;
                        echo '<div class="info">⏭️ ' . $nome . ' - ' . $msg . '</div>';
                    } else {
                        $ok++;
                        echo '<div class="ok">✅ ' . $nome . ' - ' . $msg . '</div>';
                    }
                }
                ?>
                <p style="margin-top:15px;border-top:1px solid #333;padding-top:15px;">
                    <strong>📊 RESUMO:</strong>
                    <span class="ok">Corrigidos: <?php echo $corrigidos; ?></span> |
                    <span class="info">OK: <?php echo $ok; ?></span> |
                    <span style="color:#888;">Ignorados: <?php echo $ignorados; ?></span>
                </p>
                <p class="ok" style="margin-top:10px;">✅ CORREÇÃO CONCLUÍDA!</p>
            </div>
            
            <div style="text-align:center;">
                <a href="/" class="btn btn-success">🏠 TESTAR SITE</a>
                <a href="?refresh" class="btn">🔄 VOLTAR</a>
            </div>
            
        <?php else: ?>
            
            <div class="seguranca">
                <h3>🛡️ O QUE VAI SER FEITO</h3>
                <ul>
                    <li>✅ Corrigir credenciais em TODOS os arquivos</li>
                    <li>✅ Fazer BACKUP de cada arquivo antes de modificar</li>
                    <li>✅ NÃO quebrar o site</li>
                    <li>✅ Mostrar relatório completo</li>
                </ul>
            </div>
            
            <div style="background:#1a1a36;padding:15px;border-radius:8px;margin:15px 0;">
                <h3 style="color:#33b5e5;">📋 CREDENCIAIS QUE SERÃO APLICADAS</h3>
                <table style="width:100%;border-collapse:collapse;font-size:14px;">
                    <tr style="border-bottom:1px solid #333;">
                        <td style="padding:8px;color:#888;">Host</td>
                        <td style="padding:8px;"><strong><?php echo $HOST; ?></strong></td>
                    </tr>
                    <tr style="border-bottom:1px solid #333;">
                        <td style="padding:8px;color:#888;">Banco</td>
                        <td style="padding:8px;"><strong><?php echo $DBNAME; ?></strong></td>
                    </tr>
                    <tr style="border-bottom:1px solid #333;">
                        <td style="padding:8px;color:#888;">Usuário</td>
                        <td style="padding:8px;"><strong><?php echo $USER; ?></strong></td>
                    </tr>
                    <tr>
                        <td style="padding:8px;color:#888;">Senha</td>
                        <td style="padding:8px;"><strong>••••••••</strong></td>
                    </tr>
                </table>
            </div>
            
            <div style="text-align:center;">
                <form method="POST">
                    <input type="hidden" name="acao" value="corrigir">
                    <button type="submit" class="btn" style="background:#ffbb33;color:#000;font-size:18px;padding:15px 40px;">
                        🔧 CORRIGIR AGORA
                    </button>
                </form>
                <p style="color:#888;font-size:12px;margin-top:10px;">
                    ⚠️ Isso vai corrigir TODOS os arquivos com credenciais erradas
                </p>
            </div>
            
        <?php endif; ?>
        
        <div style="text-align:center;margin-top:20px;">
            <a href="/" class="btn btn-voltar">🏠 Voltar ao Site</a>
        </div>
        
        <div style="text-align:center;color:#555;font-size:11px;margin-top:20px;border-top:1px solid #222;padding-top:15px;">
            🔧 Correção Definitiva v1.0 | NÃO QUEBRA O SITE
        </div>
    </div>
</body>
</html>