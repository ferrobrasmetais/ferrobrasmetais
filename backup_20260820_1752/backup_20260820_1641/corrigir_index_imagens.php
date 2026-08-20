<?php
// ============================================================
// 🔧 CORRIGIR INDEX.PHP - CAMINHOS DAS IMAGENS
// ============================================================

$indexPath = __DIR__ . '/index.php';

if (!file_exists($indexPath)) {
    die("❌ index.php não encontrado!");
}

// Ler o conteúdo
$conteudo = file_get_contents($indexPath);
$original = $conteudo;

// ============================================================
// CORRIGIR: Adicionar barra no início dos caminhos das imagens
// ============================================================

// Padrão: src="assets/images/... ou src='assets/images/...
$conteudo = preg_replace(
    '/src=["\']assets\/images\//',
    'src="/assets/images/',
    $conteudo
);

// Padrão: src="img/... ou src='img/...
$conteudo = preg_replace(
    '/src=["\']img\//',
    'src="/img/',
    $conteudo
);

// Padrão: src="imagens/... ou src='imagens/...
$conteudo = preg_replace(
    '/src=["\']imagens\//',
    'src="/imagens/',
    $conteudo
);

// Também corrigir se já tiver o caminho completo sem barra
$conteudo = preg_replace(
    '/src=["\']assets\/images\/produtos\//',
    'src="/assets/images/produtos/',
    $conteudo
);

// ============================================================
// CORRIGIR: O código PHP que gera a imagem
// ============================================================

// Procurar por echo $produto['imagem'] ou similar
$conteudo = preg_replace(
    '/echo\s*\$produto\[\'imagem\'\]/',
    "echo '/' . \$produto['imagem']",
    $conteudo
);

$conteudo = preg_replace(
    '/echo\s*\$produto\["imagem"\]/',
    "echo '/' . \$produto['imagem']",
    $conteudo
);

// ============================================================
// SALVAR
// ============================================================
if ($conteudo != $original) {
    // Backup
    $backup = $indexPath . '.backup_' . date('Ymd_His');
    copy($indexPath, $backup);
    echo "<p style='color:#33b5e5;'>📁 Backup criado: " . basename($backup) . "</p>";
    
    // Salvar
    file_put_contents($indexPath, $conteudo);
    echo "<p style='color:#00c851;'>✅ index.php corrigido!</p>";
} else {
    echo "<p style='color:#ffbb33;'>⚠️ Nenhuma alteração necessária.</p>";
}

// ============================================================
// MOSTRAR O QUE FOI CORRIGIDO
// ============================================================
echo "<h3>📋 CORREÇÕES APLICADAS:</h3>";
echo "<div style='font-family:monospace;background:#0a0a1a;padding:15px;border-radius:8px;'>";

echo "<p style='color:#00c851;'>✅ Adicionada barra (/) no início dos caminhos das imagens</p>";
echo "<p style='color:#00c851;'>✅ Corrigido: src='assets/images/...' → src='/assets/images/...'</p>";
echo "<p style='color:#00c851;'>✅ Corrigido: echo \$produto['imagem'] → echo '/' . \$produto['imagem']</p>";

echo "</div>";

// ============================================================
// BOTÕES
// ============================================================
echo "<div style='margin-top:20px;'>";
echo "<a href='/' style='display:inline-block;padding:10px 20px;background:#d61935;color:white;text-decoration:none;border-radius:5px;'>🏠 Ver Site</a>";
echo "<a href='?force' style='display:inline-block;padding:10px 20px;background:#ffbb33;color:#000;text-decoration:none;border-radius:5px;margin-left:10px;'>🔄 Recarregar</a>";
echo "</div>";
?>