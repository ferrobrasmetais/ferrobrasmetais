<?php
// ============================================================
// VER ESTRUTURA - FERROBRAS METAIS
// ============================================================

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>📁 Ver Estrutura - Ferrobras</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; background: #f4f6f8; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: #1a1a1a; color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .header h1 { color: #d61935; }
        .card { background: white; padding: 20px; border-radius: 8px; margin-bottom: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .card h2 { color: #333; border-bottom: 2px solid #d61935; padding-bottom: 10px; margin-bottom: 15px; }
        .ok { background: #d4edda; padding: 10px; border-left: 4px solid #28a745; }
        .erro { background: #f8d7da; padding: 10px; border-left: 4px solid #dc3545; }
        .aviso { background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; }
        .info { background: #d1ecf1; padding: 10px; border-left: 4px solid #17a2b8; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-ok { background: #28a745; color: white; }
        .badge-erro { background: #dc3545; color: white; }
        .badge-aviso { background: #ffc107; color: #333; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-size: 13px; }
        .pasta { background: #f8f9fa; padding: 10px; border-radius: 4px; margin: 3px 0; }
        .pasta-nome { color: #d61935; font-weight: bold; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .footer { text-align: center; color: #999; padding: 20px; font-size: 13px; }
        .btn { display: inline-block; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: bold; border: none; cursor: pointer; }
        .btn-primary { background: #d61935; color: white; }
        .btn-primary:hover { background: #b01229; }
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #218838; }
        @media (max-width: 768px) { .grid-2 { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class='container'>";

echo "<div class='header'>";
echo "<h1>📁 Ver Estrutura - Ferrobras Metais</h1>";
echo "<p>Data: " . date('d/m/Y H:i:s') . " | Servidor: " . $_SERVER['HTTP_HOST'] . "</p>";
echo "</div>";

// ============================================================
// 1. VERIFICAR PASTAS PRINCIPAIS
// ============================================================
echo "<div class='card'>";
echo "<h2>📊 1. STATUS DAS PASTAS</h2>";

$pastas_verificar = [
    'assets/images' => '📁 assets/images/ (Nova estrutura)',
    'assets/images/logo' => '📁 assets/images/logo/',
    'assets/images/produtos' => '📁 assets/images/produtos/',
    'assets/images/hero' => '📁 assets/images/hero/',
    'assets/images/sobre' => '📁 assets/images/sobre/',
    'assets/images/banners' => '📁 assets/images/banners/',
    'img' => '📁 img/ (Compatibilidade)',
    'imagens' => '📁 imagens/ (Compatibilidade)'
];

echo "<table style='width:100%;'>";
echo "<tr><th>Pasta</th><th>Status</th><th>Conteúdo</th></tr>";

foreach ($pastas_verificar as $pasta => $desc) {
    if (is_dir($pasta)) {
        $arquivos = scandir($pasta);
        $total = count(array_filter($arquivos, function($a) { return $a != '.' && $a != '..'; }));
        $status = $total > 0 ? '✅ OK' : '⚠️ Vazia';
        $cor = $total > 0 ? '#d4edda' : '#fff3cd';
        $conteudo = $total > 0 ? "$total arquivos" : 'Vazia';
        
        echo "<tr style='background:$cor;'>";
        echo "<td><code>$desc</code></td>";
        echo "<td><span class='badge " . ($total > 0 ? 'badge-ok' : 'badge-aviso') . "'>$status</span></td>";
        echo "<td>$conteudo</td>";
        echo "</tr>";
    } else {
        echo "<tr style='background:#f8d7da;'>";
        echo "<td><code>$desc</code></td>";
        echo "<td><span class='badge badge-erro'>❌ FALTANDO</span></td>";
        echo "<td>-</td>";
        echo "</tr>";
    }
}
echo "</table>";
echo "</div>";

// ============================================================
// 2. TESTE DE ACESSO ÀS IMAGENS
// ============================================================
echo "<div class='card'>";
echo "<h2>🖼️ 2. TESTE DE ACESSO ÀS IMAGENS</h2>";

$imagens_teste = [
    'assets/images/logo/ferrobrasmetais_logo.png' => 'Logo (assets)',
    'assets/images/hero/tubos.jpg' => 'Hero (assets)',
    'assets/images/sobre/serra.jpg' => 'Sobre (assets)',
    'assets/images/produtos/1786476957_cobre_latao_bronze.jpg' => 'Produto (assets)',
    'img/ferrobrasmetais_logo.png' => 'Logo (img)',
    'imagens/ferrobrasmetais_logo.png' => 'Logo (imagens)'
];

echo "<table style='width:100%;'>";
echo "<tr><th>Imagem</th><th>Status</th><th>URL</th></tr>";

foreach ($imagens_teste as $caminho => $desc) {
    $url = 'https://' . $_SERVER['HTTP_HOST'] . '/' . $caminho;
    
    if (file_exists($caminho)) {
        echo "<tr style='background:#d4edda;'>";
        echo "<td><strong>$desc</strong><br><code style='font-size:11px;'>$caminho</code></td>";
        echo "<td><span class='badge badge-ok'>✅ OK</span></td>";
        echo "<td><a href='$url' target='_blank' style='color:#28a745;'>🔗 Ver</a></td>";
        echo "</tr>";
    } else {
        echo "<tr style='background:#f8d7da;'>";
        echo "<td><strong>$desc</strong><br><code style='font-size:11px;'>$caminho</code></td>";
        echo "<td><span class='badge badge-erro'>❌ FALTANDO</span></td>";
        echo "<td>Não encontrado</td>";
        echo "</tr>";
    }
}
echo "</table>";
echo "</div>";

// ============================================================
// 3. AÇÕES RÁPIDAS
// ============================================================
echo "<div class='card'>";
echo "<h2>🚀 3. AÇÕES RÁPIDAS</h2>";

echo "<div style='display:flex;gap:10px;flex-wrap:wrap;'>";
echo "<a href='/' target='_blank' class='btn btn-success'>🏠 Ver Site</a>";
echo "<a href='painel_industrial/login_simples.php' target='_blank' class='btn btn-primary'>🔐 Ver Painel</a>";
echo "<a href='" . $_SERVER['PHP_SELF'] . "' class='btn btn-primary'>🔄 Atualizar</a>";
echo "</div>";
echo "</div>";

// ============================================================
// FOOTER
// ============================================================
echo "<div class='footer'>";
echo "<p>📁 Estrutura verificada em " . date('d/m/Y H:i:s') . "</p>";
echo "</div>";

echo "</div></body></html>";
?>