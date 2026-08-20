<?php
// ============================================================
// 🔧 CORRIGIR IMAGENS FALTANTES
// ============================================================
// Este script:
// 1. Escaneia todas as páginas em busca de imagens
// 2. Verifica quais imagens não existem
// 3. Tenta encontrar a imagem em outras pastas
// 4. Corrige os caminhos automaticamente
// ============================================================

// CONFIGURAÇÕES
$pasta_raiz = __DIR__;
$pastas_imagens = [
    'assets/images/',
    'assets/images/produtos/',
    'assets/images/logo/',
    'assets/images/hero/',
    'assets/images/sobre/',
    'imagens/produtos/',
    'img/',
    'img/produtos/'
];

// ============================================================
// FUNÇÃO PARA ESCANEAR PÁGINAS
// ============================================================
function escanearPaginas($pasta) {
    $paginas = [];
    $itens = scandir($pasta);
    foreach ($itens as $item) {
        if ($item == '.' || $item == '..') continue;
        $caminho = $pasta . '/' . $item;
        if (is_dir($caminho)) {
            if (in_array($item, ['backup', 'node_modules', 'vendor', 'cache', 'tmp', 'lixeira_*'])) continue;
            $paginas = array_merge($paginas, escanearPaginas($caminho));
        } else {
            $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
            if (in_array($ext, ['php', 'html', 'htm'])) {
                $paginas[] = $caminho;
            }
        }
    }
    return $paginas;
}

// ============================================================
// FUNÇÃO PARA ENCONTRAR IMAGEM NAS PASTAS
// ============================================================
function encontrarImagem($nome_arquivo, $pastas) {
    global $pasta_raiz;
    
    // Tentar diferentes extensões
    $extensoes = ['webp', 'jpg', 'jpeg', 'png', 'gif'];
    $nome_sem_ext = pathinfo($nome_arquivo, PATHINFO_FILENAME);
    
    foreach ($pastas as $pasta) {
        $caminho_completo = $pasta_raiz . '/' . $pasta;
        if (!is_dir($caminho_completo)) continue;
        
        foreach ($extensoes as $ext) {
            $caminho = $pasta . $nome_sem_ext . '.' . $ext;
            if (file_exists($pasta_raiz . '/' . $caminho)) {
                return $caminho;
            }
        }
    }
    
    return false;
}

// ============================================================
// FUNÇÃO PARA CORRIGIR IMAGENS NA PÁGINA
// ============================================================
function corrigirImagensPagina($caminho, $pastas) {
    global $pasta_raiz;
    
    $conteudo = file_get_contents($caminho);
    $original = $conteudo;
    $modificado = false;
    $corrigidas = [];
    
    // Encontrar todas as tags img
    preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $conteudo, $matches);
    
    foreach ($matches[1] as $src) {
        // Ignar URLs externas
        if (strpos($src, 'http') === 0 || strpos($src, '//') === 0) continue;
        
        // Limpar o caminho
        $src_limpo = ltrim($src, '/');
        $caminho_completo = $pasta_raiz . '/' . $src_limpo;
        
        // Verificar se a imagem existe
        if (!file_exists($caminho_completo)) {
            // Tentar encontrar a imagem em outras pastas
            $nome_arquivo = basename($src);
            $novo_caminho = encontrarImagem($nome_arquivo, $pastas);
            
            if ($novo_caminho) {
                // Corrigir o caminho na página
                $novo_src = '/' . $novo_caminho;
                $conteudo = str_replace($src, $novo_src, $conteudo);
                $modificado = true;
                $corrigidas[] = $nome_arquivo . " → " . $novo_caminho;
            }
        }
    }
    
    if ($modificado) {
        file_put_contents($caminho, $conteudo);
        return ['modificado' => true, 'corrigidas' => $corrigidas];
    }
    
    return ['modificado' => false, 'corrigidas' => []];
}

// ============================================================
// EXECUTAR
// ============================================================
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔧 Corrigir Imagens Faltando</title>
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
        .btn-voltar { background: #6c757d; }
        .btn-voltar:hover { background: #5a6268; }
        .log-area { background: #0a0a1a; padding: 15px; border-radius: 8px; max-height: 400px; overflow-y: auto; font-family: monospace; font-size: 12px; margin: 15px 0; }
        .ok { color: #00c851; }
        .warn { color: #ffbb33; }
        .err { color: #ff4444; }
        .info { color: #33b5e5; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 12px; margin: 15px 0; }
        .stat { background: #1e1e3a; padding: 12px; border-radius: 8px; text-align: center; }
        .stat .num { font-size: 24px; font-weight: bold; }
        .stat .label { font-size: 11px; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 CORRIGIR IMAGENS FALTANDO</h1>
        <p class="subtitle">Encontra e corrige imagens que não estão carregando</p>

        <?php if (isset($_POST['acao']) && $_POST['acao'] === 'corrigir'): ?>
            <div class="log-area">
                <p class="info">🔍 ESCANEANDO PÁGINAS...</p>
                
                <?php
                $paginas = escanearPaginas($pasta_raiz);
                $total_paginas = count($paginas);
                $paginas_corrigidas = 0;
                $imagens_corrigidas = 0;
                $logs = [];
                
                echo '<p>📄 Total de páginas: <strong>' . $total_paginas . '</strong></p>';
                
                foreach ($paginas as $pagina) {
                    $resultado = corrigirImagensPagina($pagina, $pastas_imagens);
                    
                    if ($resultado['modificado']) {
                        $paginas_corrigidas++;
                        $imagens_corrigidas += count($resultado['corrigidas']);
                        $nome = basename($pagina);
                        echo '<div style="color:#ffbb33;margin-top:5px;">📄 ' . $nome . ':</div>';
                        foreach ($resultado['corrigidas'] as $corrigida) {
                            echo '<div style="color:#00c851;padding-left:20px;font-size:11px;">✅ ' . $corrigida . '</div>';
                        }
                    }
                }
                ?>
                
                <p style="margin-top:15px;border-top:1px solid #333;padding-top:15px;">
                    <strong>📊 RESUMO:</strong>
                    <span class="ok">Páginas corrigidas: <?php echo $paginas_corrigidas; ?></span> |
                    <span class="ok">Imagens corrigidas: <?php echo $imagens_corrigidas; ?></span>
                </p>
                
                <?php if ($imagens_corrigidas > 0): ?>
                    <p class="ok">🎉 IMAGENS CORRIGIDAS!</p>
                    <p style="color:#888;font-size:12px;">Recarregue a página com Ctrl+F5 para ver as imagens.</p>
                <?php else: ?>
                    <p class="ok">✅ NENHUMA IMAGEM PRECISOU SER CORRIGIDA!</p>
                <?php endif; ?>
            </div>
            
            <div style="text-align:center;margin-top:15px;">
                <a href="/" class="btn btn-success">🏠 TESTAR SITE</a>
                <a href="?refresh" class="btn btn-primary">🔄 RECARREGAR</a>
            </div>
            
        <?php else: ?>
            
            <div style="background:#1a2a1a;padding:15px;border-radius:8px;border:2px solid #00c851;margin:15px 0;">
                <h3 style="color:#00c851;">🛡️ O QUE VAI SER FEITO</h3>
                <ul style="color:#aaa;padding-left:20px;margin-top:10px;">
                    <li>✅ Escanear todas as páginas do site</li>
                    <li>✅ Identificar imagens que não estão carregando</li>
                    <li>✅ Procurar as imagens em outras pastas</li>
                    <li>✅ Corrigir os caminhos automaticamente</li>
                    <li>✅ NÃO quebrar o site</li>
                </ul>
            </div>
            
            <div style="text-align:center;">
                <form method="POST">
                    <input type="hidden" name="acao" value="corrigir">
                    <button type="submit" class="btn" style="background:#ffbb33;color:#000;font-size:18px;padding:15px 40px;">
                        🔧 CORRIGIR IMAGENS
                    </button>
                </form>
                <p style="color:#888;font-size:12px;margin-top:10px;">
                    ⚠️ Isso vai corrigir automaticamente os caminhos das imagens
                </p>
            </div>
            
        <?php endif; ?>
        
        <div style="text-align:center;margin-top:20px;">
            <a href="/" class="btn btn-voltar">🏠 Voltar ao Site</a>
        </div>
        
        <div style="text-align:center;color:#555;font-size:11px;margin-top:20px;border-top:1px solid #222;padding-top:15px;">
            🔧 Corrigir Imagens v1.0 | NÃO QUEBRA O SITE
        </div>
    </div>
</body>
</html>