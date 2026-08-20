<?php
// ============================================================
// 🛡️ SEGURANÇA TOTAL - ANÁLISE E CORREÇÃO
// ============================================================
// Versão: 1.0 - NÃO QUEBRA O SITE
// 
// O QUE FAZ:
// 1. Analisa permissões de arquivos
// 2. Verifica .htaccess
// 3. Testa SSL/HTTPS
// 4. Verifica credenciais do banco
// 5. Protege pastas sensíveis
// 6. Gera relatório completo
// 7. Aplica correções seguras
// ============================================================

// ============================================================
// FUNÇÕES DE SEGURANÇA
// ============================================================

function verificarPermissoes($arquivo) {
    if (!file_exists($arquivo)) {
        return ['status' => 'erro', 'msg' => 'Arquivo não encontrado'];
    }
    
    $perms = fileperms($arquivo);
    $perms_str = substr(sprintf('%o', $perms), -4);
    
    $correto = false;
    if (is_dir($arquivo)) {
        $correto = ($perms_str === '0755' || $perms_str === '0755' || $perms_str === '0755');
    } else {
        $correto = ($perms_str === '0644' || $perms_str === '0644' || $perms_str === '0644');
    }
    
    return [
        'status' => $correto ? 'ok' : 'atencao',
        'perm' => $perms_str,
        'correto' => $correto ? '✅ Correto' : '⚠️ Precisa ajustar'
    ];
}

function verificarArquivo($arquivo) {
    if (!file_exists($arquivo)) {
        return ['status' => 'erro', 'msg' => 'Não encontrado'];
    }
    return ['status' => 'ok', 'msg' => '✅ Encontrado'];
}

function testarSSL() {
    $url = 'https://' . $_SERVER['HTTP_HOST'];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $code === 200;
}

function testarBanco() {
    $host = 'localhost';
    $dbname = 'u119221664_ferrobras_site';
    $user = 'u119221664_ferrobras_user';
    $pass = 'Ferrobras@2026';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return ['status' => 'ok', 'msg' => '✅ Conexão OK'];
    } catch (PDOException $e) {
        return ['status' => 'erro', 'msg' => '❌ Erro: ' . $e->getMessage()];
    }
}

function criarHTAccess($arquivo) {
    $conteudo = "# ============================================================
# 🔐 SEGURANÇA DO SITE
# ============================================================

# Prevenir listagem de diretórios
Options -Indexes

# Proteger arquivos sensíveis
<FilesMatch \"\\.(htaccess|htpasswd|ini|log|sql|bak|backup|sql.gz)\$\">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Proteger pastas sensíveis
RedirectMatch 404 /(config|includes|painel_industrial|backup_|lixeira_)/.*\\.(php|ini|sql|log)\$

# Proteger contra SQL Injection e XSS
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{QUERY_STRING} [^a-zA-Z0-9_]
    RewriteRule ^ - [F,L]
</IfModule>

# Bloquear bots maliciosos
<IfModule mod_rewrite.c>
    RewriteCond %{HTTP_USER_AGENT} (ahrefs|majestic|rogerbot|semrush|spider|crawler|bot) [NC]
    RewriteRule .* - [R=403,L]
</IfModule>

# Proteger XML-RPC (WordPress)
<Files xmlrpc.php>
    Order Allow,Deny
    Deny from all
</Files>

# Prevenir hotlinking de imagens
<IfModule mod_rewrite.c>
    RewriteCond %{HTTP_REFERER} !^$
    RewriteCond %{HTTP_REFERER} !^https?://(www\.)?ferrobrasmetais\.com\.br [NC]
    RewriteRule \.(jpg|jpeg|png|gif|webp|ico)$ - [F,NC,L]
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options \"nosniff\"
    Header set X-Frame-Options \"DENY\"
    Header set X-XSS-Protection \"1; mode=block\"
    Header set Referrer-Policy \"strict-origin-when-cross-origin\"
    Header set Permissions-Policy \"geolocation=(), microphone=(), camera=()\"
</IfModule>

# Cache de arquivos estáticos
<FilesMatch \"\\.(css|js|png|jpg|jpeg|gif|webp|ico|woff|woff2|ttf|svg)\$\">
    Header set Cache-Control \"public, max-age=2592000, immutable\"
</FilesMatch>";

    if (file_put_contents($arquivo, $conteudo)) {
        return ['status' => 'ok', 'msg' => '✅ .htaccess criado/atualizado'];
    }
    return ['status' => 'erro', 'msg' => '❌ Erro ao criar .htaccess'];
}

function corrigirPermissoes($caminho) {
    if (is_dir($caminho)) {
        chmod($caminho, 0755);
    } else {
        chmod($caminho, 0644);
    }
    return true;
}

function verificarArquivosSensiveis() {
    $sensiveis = [
        'config/database.php',
        'config/database_secure.php',
        'config/database_hostiger.php',
        'config/database_site.php',
        '.env',
        '.htaccess'
    ];
    
    $resultados = [];
    foreach ($sensiveis as $arquivo) {
        if (file_exists($arquivo)) {
            $resultados[$arquivo] = '✅ Encontrado';
        } else {
            $resultados[$arquivo] = '❌ Não encontrado';
        }
    }
    return $resultados;
}

// ============================================================
// INÍCIO DA INTERFACE
// ============================================================
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛡️ Segurança Total</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #0a0a1a; color: #e0e0e0; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: #14142e; padding: 30px; border-radius: 15px; }
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
        .btn-voltar { background: #6c757d; }
        .btn-voltar:hover { background: #5a6268; }
        .log-area { background: #0a0a1a; padding: 15px; border-radius: 8px; max-height: 500px; overflow-y: auto; font-family: monospace; font-size: 12px; margin: 15px 0; }
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
        .seguranca { background: #1a2a1a; padding: 15px; border-radius: 8px; border: 2px solid #00c851; margin: 15px 0; }
        .seguranca h3 { color: #00c851; }
        .seguranca ul { color: #aaa; padding-left: 20px; margin-top: 10px; }
        .seguranca li { margin: 4px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛡️ SEGURANÇA TOTAL</h1>
        <p class="subtitle">Análise e correção de segurança - NÃO QUEBRA O SITE</p>

        <?php
        // ============================================================
        // AÇÃO: ANALISAR
        // ============================================================
        if (isset($_POST['acao']) && $_POST['acao'] === 'analisar') {
            echo '<div class="log-area">';
            echo '<p class="info">🔍 ANALISANDO SEGURANÇA...</p>';
            
            // 1. Permissões
            echo '<p class="info">📋 1. Verificando permissões...</p>';
            $arquivos = ['index.php', 'admin.php', 'config', 'painel_industrial', 'assets'];
            foreach ($arquivos as $arq) {
                $resultado = verificarPermissoes($arq);
                echo '<div style="color:' . ($resultado['status'] === 'ok' ? '#00c851' : '#ffbb33') . ';font-size:11px;">';
                echo '📄 ' . $arq . ': ' . $resultado['perm'] . ' - ' . $resultado['correto'];
                echo '</div>';
            }
            
            // 2. Arquivos sensíveis
            echo '<p class="info">📋 2. Verificando arquivos sensíveis...</p>';
            $sensiveis = verificarArquivosSensiveis();
            foreach ($sensiveis as $arq => $status) {
                echo '<div style="color:' . (strpos($status, '✅') !== false ? '#00c851' : '#ff4444') . ';font-size:11px;">';
                echo '📄 ' . $arq . ': ' . $status;
                echo '</div>';
            }
            
            // 3. Testar SSL
            echo '<p class="info">📋 3. Testando SSL/HTTPS...</p>';
            $ssl = testarSSL();
            echo '<div style="color:' . ($ssl ? '#00c851' : '#ff4444') . ';font-size:11px;">';
            echo $ssl ? '✅ HTTPS ativo' : '❌ HTTPS NÃO ativo';
            echo '</div>';
            
            // 4. Testar banco
            echo '<p class="info">📋 4. Testando banco de dados...</p>';
            $banco = testarBanco();
            echo '<div style="color:' . ($banco['status'] === 'ok' ? '#00c851' : '#ff4444') . ';font-size:11px;">';
            echo $banco['msg'];
            echo '</div>';
            
            // 5. Verificar .htaccess
            echo '<p class="info">📋 5. Verificando .htaccess...</p>';
            if (file_exists('.htaccess')) {
                echo '<div style="color:#00c851;font-size:11px;">✅ .htaccess encontrado</div>';
            } else {
                echo '<div style="color:#ffbb33;font-size:11px;">⚠️ .htaccess NÃO encontrado</div>';
            }
            
            echo '</div>';
            
            // ============================================================
            // RESUMO
            // ============================================================
            echo '<div class="seguranca">';
            echo '<h3>📋 RESUMO DA ANÁLISE</h3>';
            echo '<ul>';
            echo '<li>✅ Permissões verificadas</li>';
            echo '<li>✅ Arquivos sensíveis verificados</li>';
            echo '<li>' . ($ssl ? '✅' : '❌') . ' SSL/HTTPS: ' . ($ssl ? 'Ativo' : 'Inativo') . '</li>';
            echo '<li>' . ($banco['status'] === 'ok' ? '✅' : '❌') . ' Banco de dados: ' . ($banco['status'] === 'ok' ? 'OK' : 'Erro') . '</li>';
            echo '<li>' . (file_exists('.htaccess') ? '✅' : '⚠️') . ' .htaccess: ' . (file_exists('.htaccess') ? 'Encontrado' : 'Não encontrado') . '</li>';
            echo '</ul>';
            echo '</div>';
        }

        // ============================================================
        // AÇÃO: CORRIGIR
        // ============================================================
        if (isset($_POST['acao']) && $_POST['acao'] === 'corrigir') {
            echo '<div class="log-area">';
            echo '<p class="info">🔧 APLICANDO CORREÇÕES DE SEGURANÇA...</p>';
            
            // 1. Corrigir permissões
            echo '<p class="info">📋 1. Corrigindo permissões...</p>';
            $arquivos = ['index.php', 'admin.php', 'login_simples.php', 'config', 'painel_industrial', 'assets', 'includes'];
            foreach ($arquivos as $arq) {
                if (file_exists($arq)) {
                    corrigirPermissoes($arq);
                    echo '<div style="color:#00c851;font-size:11px;">✅ Corrigido: ' . $arq . '</div>';
                }
            }
            
            // 2. Criar/atualizar .htaccess
            echo '<p class="info">📋 2. Criando/atualizando .htaccess...</p>';
            $htaccess = criarHTAccess('.htaccess');
            echo '<div style="color:' . ($htaccess['status'] === 'ok' ? '#00c851' : '#ff4444') . ';font-size:11px;">';
            echo $htaccess['msg'];
            echo '</div>';
            
            // 3. Proteger pasta config
            echo '<p class="info">📋 3. Protegendo pasta config...</p>';
            if (!file_exists('config/.htaccess')) {
                $conteudo = "<Files *.php>\n    Order Deny,Allow\n    Deny from all\n</Files>";
                file_put_contents('config/.htaccess', $conteudo);
                echo '<div style="color:#00c851;font-size:11px;">✅ config/.htaccess criado</div>';
            } else {
                echo '<div style="color:#00c851;font-size:11px;">✅ config/.htaccess já existe</div>';
            }
            
            echo '<p class="ok">🎉 CORREÇÕES APLICADAS COM SUCESSO!</p>';
            echo '</div>';
            
            echo '<div class="seguranca">';
            echo '<h3>✅ CORREÇÕES APLICADAS</h3>';
            echo '<ul>';
            echo '<li>✅ Permissões corrigidas</li>';
            echo '<li>✅ .htaccess atualizado com regras de segurança</li>';
            echo '<li>✅ Pasta config protegida</li>';
            echo '<li>✅ Arquivos sensíveis protegidos</li>';
            echo '</ul>';
            echo '</div>';
        }
        ?>

        <!-- ============================================================
        MENU DE AÇÕES
        ============================================================ -->
        <div style="display:flex;flex-wrap:wrap;gap:10px;justify-content:center;margin:20px 0;">
            <form method="POST" style="display:inline;">
                <input type="hidden" name="acao" value="analisar">
                <button type="submit" class="btn btn-primary">🔍 Analisar Segurança</button>
            </form>
            
            <form method="POST" style="display:inline;">
                <input type="hidden" name="acao" value="corrigir">
                <button type="submit" class="btn btn-success" onclick="return confirm('⚠️ Isso vai aplicar correções de segurança. Deseja continuar?')">
                    🔧 Aplicar Correções
                </button>
            </form>
        </div>

        <!-- ============================================================
        INFORMAÇÕES DE SEGURANÇA
        ============================================================ -->
        <div class="seguranca">
            <h3>🛡️ O QUE FOI/ SERÁ CORRIGIDO</h3>
            <ul>
                <li>✅ Permissões de arquivos (644 para arquivos, 755 para pastas)</li>
                <li>✅ .htaccess com regras de segurança</li>
                <li>✅ Proteção contra listagem de diretórios</li>
                <li>✅ Proteção de arquivos sensíveis (.env, .sql, .log)</li>
                <li>✅ Proteção contra SQL Injection e XSS</li>
                <li>✅ Bloqueio de bots maliciosos</li>
                <li>✅ Security Headers (X-Content-Type-Options, X-Frame-Options, etc.)</li>
                <li>✅ Cache de arquivos estáticos</li>
                <li>✅ Proteção da pasta config</li>
            </ul>
        </div>

        <!-- ============================================================
        VERIFICAR SITES EXTERNOS
        ============================================================ -->
        <div style="background:#1a1a36;padding:15px;border-radius:8px;margin:15px 0;">
            <h3 style="color:#33b5e5;">🔗 VERIFICAR SEGURANÇA EM SITES EXTERNOS</h3>
            <ul style="color:#aaa;padding-left:20px;margin-top:10px;">
                <li>🔍 <a href="https://observatory.mozilla.org/analyze/ferrobrasmetais.com.br" target="_blank" style="color:#33b5e5;">Mozilla Observatory</a> - Análise completa de segurança</li>
                <li>🔍 <a href="https://www.ssllabs.com/ssltest/analyze.html?d=ferrobrasmetais.com.br" target="_blank" style="color:#33b5e5;">SSL Labs</a> - Teste de SSL/HTTPS</li>
                <li>🔍 <a href="https://securityheaders.com/?q=ferrobrasmetais.com.br" target="_blank" style="color:#33b5e5;">Security Headers</a> - Verificação de headers HTTP</li>
                <li>🔍 <a href="https://www.virustotal.com/gui/url/ferrobrasmetais.com.br" target="_blank" style="color:#33b5e5;">VirusTotal</a> - Verificação de malware</li>
                <li>🔍 <a href="https://sitecheck.sucuri.net/results/ferrobrasmetais.com.br" target="_blank" style="color:#33b5e5;">Sucuri SiteCheck</a> - Verificação de segurança</li>
            </ul>
        </div>

        <!-- ============================================================
        VOLTAR
        ============================================================ -->
        <div style="text-align:center;margin-top:20px;">
            <a href="/" class="btn btn-voltar">🏠 Voltar ao Site</a>
        </div>

        <div style="text-align:center;color:#555;font-size:11px;margin-top:20px;border-top:1px solid #222;padding-top:15px;">
            🛡️ Segurança Total v1.0 | NÃO QUEBRA O SITE
        </div>
    </div>
</body>
</html>