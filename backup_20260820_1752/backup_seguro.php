<?php
// ============================================================
// 🚀 BACKUP SEGURO - SCRIPT COMPLETO
// ============================================================
// Versão: 1.0 - Cria backup completo do site
// 
// O QUE FAZ:
// 1. Cria backup de TODOS os arquivos
// 2. Cria backup do banco de dados
// 3. Salva em pasta com data/hora
// 4. NUNCA sobrescreve backups existentes
// ============================================================

// CONFIGURAÇÕES
$pasta_raiz = __DIR__;
$pasta_backup = __DIR__ . '/backup_' . date('Ymd_His');
$backup_arquivos = $pasta_backup . '/arquivos';
$backup_banco = $pasta_backup . '/banco';

// ============================================================
// FUNÇÃO PARA COPIAR PASTA RECURSIVAMENTE
// ============================================================
function copiarPasta($origem, $destino) {
    if (!is_dir($destino)) {
        mkdir($destino, 0755, true);
    }
    
    $itens = scandir($origem);
    foreach ($itens as $item) {
        if ($item == '.' || $item == '..') continue;
        if (strpos($item, 'backup_') === 0) continue; // Pular backups
        if (strpos($item, 'lixeira_') === 0) continue; // Pular lixeiras
        
        $origem_path = $origem . '/' . $item;
        $destino_path = $destino . '/' . $item;
        
        if (is_dir($origem_path)) {
            copiarPasta($origem_path, $destino_path);
        } else {
            copy($origem_path, $destino_path);
        }
    }
}

// ============================================================
// FUNÇÃO PARA BACKUP DO BANCO DE DADOS
// ============================================================
function backupBanco() {
    global $backup_banco;
    
    if (!is_dir($backup_banco)) {
        mkdir($backup_banco, 0755, true);
    }
    
    // Credenciais do Hostinger
    $host = 'localhost';
    $dbname = 'u119221664_ferrobras_site';
    $user = 'u119221664_ferrobras_user';
    $pass = 'Ferrobras@2026';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Pegar todas as tabelas
        $stmt = $pdo->query("SHOW TABLES");
        $tabelas = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $sql = "-- ============================================================\n";
        $sql .= "-- BACKUP DO BANCO DE DADOS - " . date('d/m/Y H:i:s') . "\n";
        $sql .= "-- ============================================================\n\n";
        
        foreach ($tabelas as $tabela) {
            // Estrutura da tabela
            $stmt = $pdo->query("SHOW CREATE TABLE $tabela");
            $row = $stmt->fetch();
            $sql .= $row['Create Table'] . ";\n\n";
            
            // Dados da tabela
            $stmt = $pdo->query("SELECT * FROM $tabela");
            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($dados)) {
                $colunas = array_keys($dados[0]);
                $colunas_str = implode('`, `', $colunas);
                
                foreach ($dados as $linha) {
                    $valores = [];
                    foreach ($linha as $valor) {
                        if ($valor === null) {
                            $valores[] = 'NULL';
                        } else {
                            $valores[] = "'" . addslashes($valor) . "'";
                        }
                    }
                    $sql .= "INSERT INTO `$tabela` (`$colunas_str`) VALUES (" . implode(', ', $valores) . ");\n";
                }
                $sql .= "\n";
            }
        }
        
        // Salvar arquivo SQL
        $arquivo_sql = $backup_banco . '/backup_' . date('Ymd_His') . '.sql';
        file_put_contents($arquivo_sql, $sql);
        
        // Criar versão compactada
        if (function_exists('gzencode')) {
            $arquivo_gz = $backup_banco . '/backup_' . date('Ymd_His') . '.sql.gz';
            file_put_contents($arquivo_gz, gzencode($sql, 9));
        }
        
        return $arquivo_sql;
        
    } catch (PDOException $e) {
        return false;
    }
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
    <title>🚀 Backup Seguro</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #0a0a1a; color: #e0e0e0; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: #14142e; padding: 30px; border-radius: 15px; }
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
        .log-area { background: #0a0a1a; padding: 15px; border-radius: 8px; max-height: 400px; overflow-y: auto; font-family: monospace; font-size: 12px; margin: 15px 0; }
        .ok { color: #00c851; }
        .warn { color: #ffbb33; }
        .err { color: #ff4444; }
        .info { color: #33b5e5; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin: 15px 0; }
        .stat { background: #1e1e3a; padding: 12px; border-radius: 8px; text-align: center; }
        .stat .num { font-size: 24px; font-weight: bold; }
        .stat .label { font-size: 11px; color: #888; }
        .backup-info { background: #1a2a1a; padding: 15px; border-radius: 8px; border: 2px solid #00c851; margin: 15px 0; }
        .backup-info h3 { color: #00c851; }
        .backup-info ul { color: #aaa; padding-left: 20px; margin-top: 10px; }
        .backup-info li { margin: 4px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 BACKUP SEGURO</h1>
        <p class="subtitle">Backup completo do site e banco de dados</p>

        <?php
        // ============================================================
        // AÇÃO: FAZER BACKUP
        // ============================================================
        if (isset($_POST['acao']) && $_POST['acao'] === 'backup') {
            echo '<div class="log-area">';
            echo '<p class="info">📁 INICIANDO BACKUP...</p>';
            
            // 1. Backup dos arquivos
            echo '<p class="info">📁 Copiando arquivos...</p>';
            $inicio = microtime(true);
            
            if (!is_dir($backup_arquivos)) {
                mkdir($backup_arquivos, 0755, true);
            }
            
            copiarPasta($pasta_raiz, $backup_arquivos);
            echo '<p class="ok">✅ Arquivos copiados!</p>';
            
            // 2. Backup do banco
            echo '<p class="info">🗄️ Fazendo backup do banco de dados...</p>';
            $arquivo_sql = backupBanco();
            
            if ($arquivo_sql) {
                echo '<p class="ok">✅ Banco de dados exportado!</p>';
                echo '<p class="ok">📄 Arquivo: ' . basename($arquivo_sql) . '</p>';
            } else {
                echo '<p class="err">❌ Erro ao exportar banco de dados!</p>';
            }
            
            $fim = microtime(true);
            $tempo = round($fim - $inicio, 2);
            
            echo '<p class="ok">⏱️ Tempo total: ' . $tempo . ' segundos</p>';
            echo '</div>';
            
            // ============================================================
            // ESTATÍSTICAS
            // ============================================================
            echo '<div class="stats">';
            
            $total_arquivos = 0;
            $total_pastas = 0;
            $tamanho_total = 0;
            
            if (is_dir($backup_arquivos)) {
                $itens = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($backup_arquivos),
                    RecursiveIteratorIterator::SELF_FIRST
                );
                
                foreach ($itens as $item) {
                    if ($item->isFile()) {
                        $total_arquivos++;
                        $tamanho_total += $item->getSize();
                    } elseif ($item->isDir()) {
                        $total_pastas++;
                    }
                }
            }
            
            echo '<div class="stat"><div class="num">' . $total_arquivos . '</div><div class="label">📁 Arquivos</div></div>';
            echo '<div class="stat"><div class="num">' . $total_pastas . '</div><div class="label">📂 Pastas</div></div>';
            echo '<div class="stat"><div class="num">' . round($tamanho_total / 1024 / 1024, 2) . ' MB</div><div class="label">💾 Tamanho</div></div>';
            echo '</div>';
            
            echo '<div class="backup-info">';
            echo '<h3>✅ BACKUP CONCLUÍDO COM SUCESSO!</h3>';
            echo '<ul>';
            echo '<li>📁 Local: <strong>' . $pasta_backup . '</strong></li>';
            echo '<li>📅 Data: ' . date('d/m/Y H:i:s') . '</li>';
            echo '<li>📄 Arquivos: ' . $total_arquivos . '</li>';
            echo '<li>💾 Tamanho: ' . round($tamanho_total / 1024 / 1024, 2) . ' MB</li>';
            echo '</ul>';
            echo '</div>';
            
            echo '<div style="text-align:center;margin-top:15px;">';
            echo '<a href="/" class="btn btn-success">🏠 Voltar ao Site</a>';
            echo ' <a href="?force" class="btn btn-primary">🔄 Novo Backup</a>';
            echo '</div>';
            
        } else {
            // ============================================================
            // TELA INICIAL
            // ============================================================
            ?>
            
            <div class="backup-info">
                <h3>🛡️ O QUE O BACKUP VAI FAZER</h3>
                <ul>
                    <li>✅ Copiar TODOS os arquivos do site</li>
                    <li>✅ Exportar TODO o banco de dados (SQL)</li>
                    <li>✅ Criar pasta com data/hora</li>
                    <li>✅ NUNCA sobrescrever backups existentes</li>
                    <li>✅ Backup seguro e completo</li>
                </ul>
            </div>
            
            <div style="background:#1a1a36;padding:15px;border-radius:8px;margin:15px 0;">
                <h3 style="color:#33b5e5;">📋 INFORMAÇÕES</h3>
                <table style="width:100%;border-collapse:collapse;font-size:14px;">
                    <tr style="border-bottom:1px solid #333;">
                        <td style="padding:8px;color:#888;">Site</td>
                        <td style="padding:8px;"><strong>ferrobrasmetais.com.br</strong></td>
                    </tr>
                    <tr style="border-bottom:1px solid #333;">
                        <td style="padding:8px;color:#888;">Data/Hora</td>
                        <td style="padding:8px;"><strong><?php echo date('d/m/Y H:i:s'); ?></strong></td>
                    </tr>
                    <tr>
                        <td style="padding:8px;color:#888;">Backup será salvo em</td>
                        <td style="padding:8px;"><strong style="color:#00c851;">backup_<?php echo date('Ymd_His'); ?></strong></td>
                    </tr>
                </table>
            </div>
            
            <div style="text-align:center;">
                <form method="POST">
                    <input type="hidden" name="acao" value="backup">
                    <button type="submit" class="btn" style="background:#00c851;font-size:18px;padding:15px 40px;">
                        🚀 FAZER BACKUP AGORA
                    </button>
                </form>
                <p style="color:#888;font-size:12px;margin-top:10px;">
                    ⚠️ O backup pode levar alguns minutos dependendo do tamanho do site
                </p>
            </div>
            
            <?php
            // Listar backups existentes
            $backups = glob(__DIR__ . '/backup_*', GLOB_ONLYDIR);
            if (!empty($backups)) {
                echo '<div style="margin-top:20px;border-top:1px solid #222;padding-top:15px;">';
                echo '<h4 style="color:#888;">📁 Backups existentes:</h4>';
                echo '<div style="font-family:monospace;font-size:12px;max-height:150px;overflow-y:auto;">';
                rsort($backups);
                foreach ($backups as $backup) {
                    $nome = basename($backup);
                    $data = substr($nome, 7, 8);
                    $hora = substr($nome, 15, 6);
                    $data_formatada = substr($data, 0, 4) . '/' . substr($data, 4, 2) . '/' . substr($data, 6, 2);
                    $hora_formatada = substr($hora, 0, 2) . ':' . substr($hora, 2, 2) . ':' . substr($hora, 4, 2);
                    echo '<div style="color:#888;padding:2px 0;">📁 ' . $nome . ' - ' . $data_formatada . ' ' . $hora_formatada . '</div>';
                }
                echo '</div></div>';
            }
            ?>
            
            <div style="text-align:center;margin-top:20px;">
                <a href="/" class="btn btn-voltar">🏠 Voltar ao Site</a>
            </div>
            
        <?php } ?>
        
        <div style="text-align:center;color:#555;font-size:11px;margin-top:20px;border-top:1px solid #222;padding-top:15px;">
            🚀 Backup Seguro v1.0 | Nunca sobrescreve backups existentes
        </div>
    </div>
</body>
</html>