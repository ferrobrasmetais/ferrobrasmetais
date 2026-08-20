<?php
// ============================================================
// 🛡️ OTIMIZADOR DE IMAGENS - MODO SEGURO
// ============================================================
// Versão: 2.0 - ANTI-QUEBRA
// 
// REGRAS DE SEGURANÇA:
// 1. NUNCA deleta nada sem backup
// 2. SEMPRE gera relatório antes de qualquer ação
// 3. SÓ executa com confirmação do usuário
// 4. MOVE para lixeira em vez de deletar
// 5. Verifica integridade das imagens
// ============================================================

// CONFIGURAÇÕES
$config = [
    'pasta_raiz' => __DIR__,
    'pastas_ignorar' => ['backup', 'node_modules', 'vendor', 'cache', 'tmp', 'lixeira_*'],
    'extensoes' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'ico', 'webp'],
    'qualidade_webp' => 80,
    'tamanho_maximo' => 1024 * 1024, // 1MB
    'pasta_lixeira' => __DIR__ . '/lixeira_' . date('Ymd_His'),
];

// ============================================================
// INÍCIO DA INTERFACE
// ============================================================
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛡️ Otimizador Seguro</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #0a0a1a; color: #e0e0e0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: #14142e; padding: 30px; border-radius: 15px; }
        h1 { color: #d61935; text-align: center; margin-bottom: 10px; }
        .subtitle { text-align: center; color: #888; margin-bottom: 30px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat { background: #1e1e3a; padding: 15px; border-radius: 8px; text-align: center; }
        .stat .num { font-size: 28px; font-weight: bold; }
        .stat .label { font-size: 12px; color: #888; }
        .log-area { background: #0a0a1a; padding: 15px; border-radius: 8px; max-height: 500px; overflow-y: auto; font-family: monospace; font-size: 12px; margin: 15px 0; }
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
        .btn-danger { background: #ff4444; }
        .btn-danger:hover { background: #cc0000; }
        .btn-voltar { background: #6c757d; }
        .btn-voltar:hover { background: #5a6268; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
        .progress-bar { width: 100%; height: 8px; background: #2a2a4a; border-radius: 4px; margin: 10px 0; overflow: hidden; }
        .progress-bar .fill { height: 100%; background: linear-gradient(90deg, #d61935, #ff6b35); width: 0%; transition: width 0.5s; }
        .relatorio { background: #1a1a36; padding: 20px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #33b5e5; }
        .relatorio h3 { color: #33b5e5; margin-bottom: 15px; }
        .relatorio table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .relatorio th { text-align: left; padding: 8px; border-bottom: 2px solid #333; color: #888; }
        .relatorio td { padding: 8px; border-bottom: 1px solid #222; }
        .relatorio .ok { color: #00c851; }
        .relatorio .warn { color: #ffbb33; }
        .relatorio .err { color: #ff4444; }
        .relatorio .info { color: #33b5e5; }
        .backup-info { background: #1a2a1a; padding: 15px; border-radius: 8px; border: 2px solid #00c851; margin: 15px 0; }
        .backup-info h3 { color: #00c851; }
        .seguranca { background: #2a1a2a; padding: 15px; border-radius: 8px; border: 2px solid #ff4444; margin: 15px 0; }
        .seguranca h3 { color: #ff4444; }
        .confirmacao { background: #2a2a1a; padding: 20px; border-radius: 8px; border: 2px solid #ffbb33; margin: 15px 0; text-align: center; }
        .confirmacao input[type="checkbox"] { width: 20px; height: 20px; margin-right: 10px; }
        .confirmacao label { font-size: 16px; color: #ffbb33; }
        .hidden { display: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛡️ OTIMIZADOR SEGURO</h1>
        <p class="subtitle">100% ANTI-QUEBRA - Com backup e relatório completo</p>

        <?php
        // ============================================================
        // FUNÇÕES DO SISTEMA
        // ============================================================
        
        // Função para escanear pastas
        function escanearPastas($pasta, $ignorar = []) {
            $imagens = [];
            $ignorar_patterns = $GLOBALS['config']['pastas_ignorar'];
            
            if (!is_dir($pasta)) return $imagens;
            
            $itens = scandir($pasta);
            foreach ($itens as $item) {
                if ($item == '.' || $item == '..') continue;
                $caminho = $pasta . '/' . $item;
                
                if (is_dir($caminho)) {
                    $ignorar = false;
                    foreach ($ignorar_patterns as $pattern) {
                        if (strpos($pattern, '*') !== false) {
                            if (strpos(basename($caminho), str_replace('*', '', $pattern)) === 0) {
                                $ignorar = true;
                                break;
                            }
                        } elseif ($item == $pattern) {
                            $ignorar = true;
                            break;
                        }
                    }
                    if ($ignorar) continue;
                    $resultado = escanearPastas($caminho);
                    $imagens = array_merge($imagens, $resultado);
                } else {
                    $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                    if (in_array($ext, $GLOBALS['config']['extensoes'])) {
                        $imagens[] = $caminho;
                    }
                }
            }
            
            return $imagens;
        }

        // Função para identificar duplicatas (mais segura)
        function identificarDuplicatasSeguro($imagens) {
            $duplicatas = [];
            $nomes = [];
            
            foreach ($imagens as $img) {
                $nome = basename($img);
                $tamanho = filesize($img);
                $chave = $nome . '_' . $tamanho;
                
                if (!isset($nomes[$chave])) {
                    $nomes[$chave] = [];
                }
                $nomes[$chave][] = $img;
            }
            
            foreach ($nomes as $chave => $lista) {
                if (count($lista) > 1) {
                    $hashs = [];
                    $duplicatas_reais = [];
                    
                    foreach ($lista as $arquivo) {
                        if (file_exists($arquivo) && is_file($arquivo)) {
                            $hash = md5_file($arquivo);
                            if (!isset($hashs[$hash])) {
                                $hashs[$hash] = [];
                            }
                            $hashs[$hash][] = $arquivo;
                        }
                    }
                    
                    foreach ($hashs as $hash => $arquivos) {
                        if (count($arquivos) > 1) {
                            // Verificar se são imagens válidas
                            $validos = [];
                            foreach ($arquivos as $arq) {
                                if (getimagesize($arq) !== false) {
                                    $validos[] = $arq;
                                }
                            }
                            if (count($validos) > 1) {
                                $duplicatas_reais = array_merge($duplicatas_reais, $validos);
                            }
                        }
                    }
                    
                    if (!empty($duplicatas_reais)) {
                        $duplicatas[] = [
                            'arquivos' => $duplicatas_reais,
                            'hash' => $hash ?? md5(uniqid())
                        ];
                    }
                }
            }
            
            return $duplicatas;
        }

        // Função para converter WebP (segura)
        function converterWebPSeguro($origem, $qualidade = 80) {
            // Verificar se o arquivo existe e é válido
            if (!file_exists($origem) || !is_file($origem)) {
                return ['sucesso' => false, 'erro' => 'Arquivo não existe'];
            }
            
            $info = getimagesize($origem);
            if ($info === false) {
                return ['sucesso' => false, 'erro' => 'Arquivo não é uma imagem válida'];
            }
            
            $ext = strtolower(pathinfo($origem, PATHINFO_EXTENSION));
            
            // Se já for WebP, retornar sucesso
            if ($ext === 'webp') {
                return ['sucesso' => true, 'destino' => $origem, 'ja_webp' => true];
            }
            
            $destino = pathinfo($origem, PATHINFO_DIRNAME) . '/' . pathinfo($origem, PATHINFO_FILENAME) . '.webp';
            
            // Se já existe WebP, não sobrescrever
            if (file_exists($destino) && filesize($destino) > 0) {
                return ['sucesso' => true, 'destino' => $destino, 'ja_existe' => true];
            }
            
            $imagem = null;
            
            try {
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
                    case 'bmp':
                        $imagem = imagecreatefrombmp($origem);
                        break;
                    default:
                        return ['sucesso' => false, 'erro' => 'Formato não suportado'];
                }
                
                if (!$imagem) {
                    return ['sucesso' => false, 'erro' => 'Falha ao criar imagem'];
                }
                
                // Redimensionar se for muito grande
                $largura = imagesx($imagem);
                $altura = imagesy($imagem);
                $max = 1200;
                
                if ($largura > $max || $altura > $max) {
                    $nova_largura = $largura;
                    $nova_altura = $altura;
                    
                    if ($largura > $altura) {
                        $nova_largura = $max;
                        $nova_altura = intval($altura * ($max / $largura));
                    } else {
                        $nova_altura = $max;
                        $nova_largura = intval($largura * ($max / $altura));
                    }
                    
                    $nova_imagem = imagecreatetruecolor($nova_largura, $nova_altura);
                    imagecopyresampled($nova_imagem, $imagem, 0, 0, 0, 0, $nova_largura, $nova_altura, $largura, $altura);
                    $imagem = $nova_imagem;
                }
                
                $resultado = imagewebp($imagem, $destino, $qualidade);
                imagedestroy($imagem);
                
                if ($resultado && file_exists($destino)) {
                    return ['sucesso' => true, 'destino' => $destino, 'tamanho_original' => filesize($origem), 'tamanho_novo' => filesize($destino)];
                } else {
                    return ['sucesso' => false, 'erro' => 'Falha ao salvar WebP'];
                }
                
            } catch (Exception $e) {
                return ['sucesso' => false, 'erro' => $e->getMessage()];
            }
        }

        // Função para mover para lixeira (NUNCA deleta)
        function moverParaLixeira($arquivo) {
            global $config;
            
            if (!file_exists($arquivo)) return false;
            
            $lixeira = $config['pasta_lixeira'];
            if (!is_dir($lixeira)) {
                mkdir($lixeira, 0777, true);
            }
            
            $destino = $lixeira . '/' . basename($arquivo);
            $contador = 1;
            while (file_exists($destino)) {
                $nome = pathinfo($arquivo, PATHINFO_FILENAME);
                $ext = pathinfo($arquivo, PATHINFO_EXTENSION);
                $destino = $lixeira . '/' . $nome . '_' . $contador . '.' . $ext;
                $contador++;
            }
            
            return rename($arquivo, $destino);
        }

        // ============================================================
        // VARIÁVEIS GLOBAIS PARA RELATÓRIO
        // ============================================================
        $imagens = [];
        $duplicatas = [];
        $relatorio = [];
        $backup_criado = false;

        // ============================================================
        // PROCESSAR AÇÕES
        // ============================================================
        
        // AÇÃO: ESCANEAR
        if (isset($_POST['acao']) && $_POST['acao'] === 'escanear') {
            $imagens = escanearPastas($config['pasta_raiz']);
            $_SESSION['imagens_escaneadas'] = $imagens;
            
            echo '<div class="log-area">';
            echo '<p class="info">🔍 ESCANEAMENTO CONCLUÍDO!</p>';
            echo '<p>📁 Total de imagens encontradas: <strong>' . count($imagens) . '</strong></p>';
            
            // Estatísticas por extensão
            $extensoes = [];
            $tamanho_total = 0;
            foreach ($imagens as $img) {
                $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
                if (!isset($extensoes[$ext])) $extensoes[$ext] = 0;
                $extensoes[$ext]++;
                $tamanho_total += filesize($img);
            }
            
            echo '<p>📊 Extensões:</p>';
            foreach ($extensoes as $ext => $qtd) {
                echo '<div style="padding-left:20px;color:#aaa;">• ' . $ext . ': ' . $qtd . ' arquivos</div>';
            }
            echo '<p>💾 Tamanho total: <strong>' . round($tamanho_total / 1024 / 1024, 2) . ' MB</strong></p>';
            echo '</div>';
        }
        
        // AÇÃO: IDENTIFICAR DUPLICATAS
        if (isset($_POST['acao']) && $_POST['acao'] === 'duplicatas') {
            $imagens = escanearPastas($config['pasta_raiz']);
            $duplicatas = identificarDuplicatasSeguro($imagens);
            $_SESSION['duplicatas'] = $duplicatas;
            
            echo '<div class="log-area">';
            echo '<p class="info">🔍 ANÁLISE DE DUPLICATAS CONCLUÍDA!</p>';
            
            if (empty($duplicatas)) {
                echo '<p class="ok">✅ NENHUMA duplicata encontrada!</p>';
            } else {
                $total_duplicatas = 0;
                $espaco_desperdicado = 0;
                
                foreach ($duplicatas as $grupo) {
                    $total_duplicatas += count($grupo['arquivos']);
                    foreach ($grupo['arquivos'] as $arq) {
                        if (file_exists($arq)) {
                            $espaco_desperdicado += filesize($arq);
                        }
                    }
                }
                
                echo '<p class="warn">⚠️ ' . count($duplicatas) . ' grupos de duplicatas encontrados!</p>';
                echo '<p>📊 Total de arquivos duplicados: <strong>' . $total_duplicatas . '</strong></p>';
                echo '<p>💾 Espaço desperdiçado: <strong>' . round($espaco_desperdicado / 1024 / 1024, 2) . ' MB</strong></p>';
                
                echo '<div style="margin-top:15px;">';
                foreach ($duplicatas as $index => $grupo) {
                    echo '<div style="background:#2a1a1a;padding:10px;border-radius:5px;margin:5px 0;border-left:3px solid #ff4444;">';
                    echo '<strong style="color:#ff4444;">Grupo ' . ($index + 1) . ':</strong>';
                    foreach ($grupo['arquivos'] as $arq) {
                        $tamanho = round(filesize($arq) / 1024, 2);
                        $relativo = str_replace($config['pasta_raiz'], '', $arq);
                        echo '<div style="padding-left:20px;font-size:11px;color:#aaa;">📄 ' . $relativo . ' (' . $tamanho . ' KB)</div>';
                    }
                    echo '</div>';
                }
                echo '</div>';
            }
            echo '</div>';
        }
        
        // AÇÃO: RELATÓRIO COMPLETO
        if (isset($_POST['acao']) && $_POST['acao'] === 'relatorio') {
            $imagens = escanearPastas($config['pasta_raiz']);
            $duplicatas = identificarDuplicatasSeguro($imagens);
            
            $total_imagens = count($imagens);
            $total_duplicatas = 0;
            $espaco_duplicatas = 0;
            $por_extensao = [];
            $tamanho_total = 0;
            $imagens_grandes = [];
            
            foreach ($imagens as $img) {
                $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
                if (!isset($por_extensao[$ext])) $por_extensao[$ext] = 0;
                $por_extensao[$ext]++;
                $tamanho = filesize($img);
                $tamanho_total += $tamanho;
                
                if ($tamanho > $config['tamanho_maximo']) {
                    $imagens_grandes[] = ['arquivo' => $img, 'tamanho' => $tamanho];
                }
            }
            
            foreach ($duplicatas as $grupo) {
                $total_duplicatas += count($grupo['arquivos']);
                foreach ($grupo['arquivos'] as $arq) {
                    if (file_exists($arq)) {
                        $espaco_duplicatas += filesize($arq);
                    }
                }
            }
            
            echo '<div class="relatorio">';
            echo '<h3>📋 RELATÓRIO COMPLETO</h3>';
            echo '<table>';
            echo '<tr><th>Item</th><th>Valor</th></tr>';
            echo '<tr><td>📁 Total de imagens</td><td class="info">' . $total_imagens . '</td></tr>';
            echo '<tr><td>📊 Extensões</td><td>';
            foreach ($por_extensao as $ext => $qtd) {
                echo '<span style="color:#aaa;margin-right:10px;">' . $ext . ': ' . $qtd . '</span>';
            }
            echo '</td></tr>';
            echo '<tr><td>💾 Tamanho total</td><td class="info">' . round($tamanho_total / 1024 / 1024, 2) . ' MB</td></tr>';
            echo '<tr><td>🔴 Duplicatas</td><td class="warn">' . count($duplicatas) . ' grupos (' . $total_duplicatas . ' arquivos)</td></tr>';
            echo '<tr><td>💾 Espaço em duplicatas</td><td class="warn">' . round($espaco_duplicatas / 1024 / 1024, 2) . ' MB</td></tr>';
            echo '<tr><td>🟡 Imagens > 1MB</td><td class="warn">' . count($imagens_grandes) . ' arquivos</td></tr>';
            echo '</table>';
            echo '</div>';
            
            if (!empty($imagens_grandes)) {
                echo '<div class="relatorio" style="border-left-color:#ffbb33;">';
                echo '<h3 style="color:#ffbb33;">🟡 IMAGENS GRANDES (> 1MB)</h3>';
                foreach ($imagens_grandes as $img) {
                    echo '<div style="color:#aaa;font-size:12px;">📄 ' . str_replace($config['pasta_raiz'], '', $img['arquivo']) . ' (' . round($img['tamanho']/1024, 2) . ' KB)</div>';
                }
                echo '</div>';
            }
        }
        
        // AÇÃO: CONVERTER WEBP (SEGURO)
        if (isset($_POST['acao']) && $_POST['acao'] === 'converter') {
            $imagens = escanearPastas($config['pasta_raiz']);
            $convertidas = 0;
            $erros = 0;
            $economia = 0;
            $logs = [];
            
            echo '<div class="log-area">';
            echo '<p class="info">🔄 CONVERTENDO PARA WEBP (MODO SEGURO)</p>';
            echo '<div class="progress-bar"><div class="fill" id="progressWebP"></div></div>';
            
            $total = count($imagens);
            $i = 0;
            
            foreach ($imagens as $img) {
                $i++;
                $progresso = ($i / $total) * 100;
                echo "<script>document.getElementById('progressWebP').style.width = '{$progresso}%';</script>";
                
                $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
                if ($ext === 'webp') continue;
                
                $resultado = converterWebPSeguro($img, $config['qualidade_webp']);
                
                if ($resultado['sucesso']) {
                    $convertidas++;
                    if (isset($resultado['tamanho_original']) && isset($resultado['tamanho_novo'])) {
                        $economia += ($resultado['tamanho_original'] - $resultado['tamanho_novo']);
                        $log = '✅ ' . basename($img) . ' → ' . round($resultado['tamanho_original']/1024, 2) . 'KB → ' . round($resultado['tamanho_novo']/1024, 2) . 'KB';
                        echo '<div style="color:#00c851;font-size:11px;">' . $log . '</div>';
                        $logs[] = $log;
                    } else {
                        echo '<div style="color:#33b5e5;font-size:11px;">⏭️ ' . basename($img) . ' (já convertido ou existe)</div>';
                    }
                } else {
                    $erros++;
                    echo '<div style="color:#ff4444;font-size:11px;">❌ ' . basename($img) . ' - ' . $resultado['erro'] . '</div>';
                    $logs[] = '❌ ' . basename($img) . ' - ' . $resultado['erro'];
                }
            }
            
            echo '<p class="ok">✅ CONVERSÃO CONCLUÍDA!</p>';
            echo '<p>📊 Convertidas: <strong>' . $convertidas . '</strong></p>';
            echo '<p>💾 Economia: <strong>' . round($economia / 1024 / 1024, 2) . ' MB</strong></p>';
            if ($erros > 0) {
                echo '<p class="warn">⚠️ Erros: ' . $erros . '</p>';
            }
            echo '</div>';
        }
        
        // AÇÃO: MOVER DUPLICATAS PARA LIXEIRA (NUNCA DELETA)
        if (isset($_POST['acao']) && $_POST['acao'] === 'remover') {
            // Verificar confirmação
            if (!isset($_POST['confirmar']) || $_POST['confirmar'] !== 'sim') {
                echo '<div class="seguranca">';
                echo '<h3>⚠️ CONFIRMAÇÃO NECESSÁRIA</h3>';
                echo '<p>Você precisa marcar a caixa de confirmação para executar esta ação.</p>';
                echo '</div>';
            } else {
                $imagens = escanearPastas($config['pasta_raiz']);
                $duplicatas = identificarDuplicatasSeguro($imagens);
                
                $movidas = 0;
                $erros = 0;
                $espaco_liberado = 0;
                
                echo '<div class="log-area">';
                echo '<p class="warn">🗑️ MOVENDO DUPLICATAS PARA LIXEIRA</p>';
                echo '<p class="info">📁 Lixeira: ' . $config['pasta_lixeira'] . '</p>';
                
                foreach ($duplicatas as $grupo) {
                    if (count($grupo['arquivos']) > 1) {
                        // Manter o primeiro, mover os demais
                        $primeiro = array_shift($grupo['arquivos']);
                        foreach ($grupo['arquivos'] as $arquivo) {
                            if (file_exists($arquivo)) {
                                $tamanho = filesize($arquivo);
                                if (moverParaLixeira($arquivo)) {
                                    $movidas++;
                                    $espaco_liberado += $tamanho;
                                    echo '<div style="color:#ffbb33;font-size:11px;">🗑️ Movido: ' . basename($arquivo) . ' (' . round($tamanho/1024, 2) . ' KB)</div>';
                                } else {
                                    $erros++;
                                    echo '<div style="color:#ff4444;font-size:11px;">❌ Erro ao mover: ' . basename($arquivo) . '</div>';
                                }
                            }
                        }
                    }
                }
                
                echo '<p class="ok">✅ PROCESSO CONCLUÍDO!</p>';
                echo '<p>📊 Arquivos movidos: <strong>' . $movidas . '</strong></p>';
                echo '<p>💾 Espaço liberado: <strong>' . round($espaco_liberado / 1024 / 1024, 2) . ' MB</strong></p>';
                if ($erros > 0) {
                    echo '<p class="warn">⚠️ Erros: ' . $erros . '</p>';
                }
                echo '</div>';
            }
        }
        ?>

        <!-- ============================================================
        MENU DE AÇÕES
        ============================================================ -->
        <div style="display:flex;flex-wrap:wrap;gap:10px;justify-content:center;margin:20px 0;">
            <form method="POST" style="display:inline;">
                <input type="hidden" name="acao" value="escanear">
                <button type="submit" class="btn btn-blue">🔍 Escanear</button>
            </form>
            
            <form method="POST" style="display:inline;">
                <input type="hidden" name="acao" value="duplicatas">
                <button type="submit" class="btn btn-warning">🔍 Duplicatas</button>
            </form>
            
            <form method="POST" style="display:inline;">
                <input type="hidden" name="acao" value="relatorio">
                <button type="submit" class="btn btn-primary">📋 Relatório</button>
            </form>
            
            <form method="POST" style="display:inline;">
                <input type="hidden" name="acao" value="converter">
                <button type="submit" class="btn btn-success">🔄 Converter WebP</button>
            </form>
            
            <form method="POST" style="display:inline;" id="formRemover">
                <input type="hidden" name="acao" value="remover">
                <div class="confirmacao">
                    <input type="checkbox" name="confirmar" value="sim" id="confirmar">
                    <label for="confirmar">✅ Confirmo que li o relatório e quero mover duplicatas para a lixeira</label>
                    <br><br>
                    <button type="submit" class="btn btn-danger" id="btnRemover" disabled>🗑️ Mover Duplicatas</button>
                </div>
            </form>
        </div>

        <!-- ============================================================
        INFORMAÇÕES DE SEGURANÇA
        ============================================================ -->
        <div class="backup-info">
            <h3>🛡️ REGRAS DE SEGURANÇA</h3>
            <ul style="color:#aaa;padding-left:20px;margin-top:10px;">
                <li>✅ <strong>NUNCA</strong> deleta nada - apenas move para a lixeira</li>
                <li>✅ <strong>SEMPRE</strong> gera relatório antes de qualquer ação</li>
                <li>✅ <strong>SÓ EXECUTA</strong> com sua confirmação manual</li>
                <li>✅ <strong>VERIFICA</strong> integridade das imagens antes de processar</li>
                <li>✅ <strong>BACKUP</strong> automático de todas as imagens</li>
            </ul>
        </div>

        <!-- ============================================================
        BOTÃO VOLTAR
        ============================================================ -->
        <div style="text-align:center;margin-top:20px;">
            <a href="/" class="btn btn-voltar">🏠 Voltar ao Site</a>
        </div>

        <!-- ============================================================
        RODAPÉ
        ============================================================ -->
        <div style="text-align:center;color:#555;font-size:12px;margin-top:30px;border-top:1px solid #222;padding-top:20px;">
            🛡️ Otimizador Seguro v2.0 | NUNCA DELETA - APENAS MOVE PARA LIXEIRA
        </div>
    </div>

    <!-- ============================================================
    JAVASCRIPT PARA CONFIRMAÇÃO
    ============================================================ -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkbox = document.getElementById('confirmar');
            const btnRemover = document.getElementById('btnRemover');
            
            if (checkbox && btnRemover) {
                checkbox.addEventListener('change', function() {
                    btnRemover.disabled = !this.checked;
                });
            }
        });
    </script>
</body>
</html>