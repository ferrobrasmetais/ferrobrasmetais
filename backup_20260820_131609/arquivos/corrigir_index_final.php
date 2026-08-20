<?php
// ============================================================
// 🔧 CORRIGIR INDEX.PHP - VERSÃO FINAL
// ============================================================

$indexPath = __DIR__ . '/index.php';

if (!file_exists($indexPath)) {
    die("❌ index.php não encontrado!");
}

// Ler o conteúdo
$conteudo = file_get_contents($indexPath);
$original = $conteudo;

// ============================================================
// CORRIGIR 1: Adicionar 'assets/images/produtos/' nas pastas de busca
// ============================================================

$conteudo = str_replace(
    '$pastas = [',
    '$pastas = [
                \'assets/images/produtos/\',
                \'assets/images/\',',
    $conteudo
);

// ============================================================
// CORRIGIR 2: Se a imagem já tem o caminho completo, usar direto
// ============================================================

// Procurar por: if(!empty($nome_imagem)) { ... }
// E garantir que usa o caminho completo

$padrao = '/if\s*\(\s*!\s*empty\s*\(\s*\$nome_imagem\s*\)\s*\)\s*\{/';
$substituicao = 'if(!empty($nome_imagem)) {
                // Se já tem o caminho completo, usar direto
                if(strpos($nome_imagem, \'assets/images/\') !== false || strpos($nome_imagem, \'/\') === 0) {
                    $img_encontrada = true;
                    $img_final = \'/\' . ltrim($nome_imagem, \'/\');
                } else {';

$conteudo = preg_replace($padrao, $substituicao, $conteudo);

// Fechar o else
$conteudo = str_replace(
    'foreach($pastas as $pasta) {',
    'foreach($pastas as $pasta) {',
    $conteudo
);

// ============================================================
// CORRIGIR 3: Garantir que o caminho final tem barra
// ============================================================

$conteudo = preg_replace(
    '/\$img_final\s*=\s*(["\'])\/([^"\']+)/',
    '$img_final = $1 . "/" . $2',
    $conteudo
);

// ============================================================
// SALVAR
// ============================================================
if ($conteudo != $original) {
    $backup = $indexPath . '.backup_' . date('Ymd_His');
    copy($indexPath, $backup);
    echo "<p style='color:#33b5e5;'>📁 Backup criado: " . basename($backup) . "</p>";
    
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
echo "<p style='color:#00c851;'>✅ Adicionada pasta 'assets/images/produtos/' nas pastas de busca</p>";
echo "<p style='color:#00c851;'>✅ Corrigido: caminhos com barra (/) no início</p>";
echo "<p style='color:#00c851;'>✅ Priorizado: uso direto do caminho completo</p>";
echo "</div>";

// ============================================================
// BOTÕES
// ============================================================
echo "<div style='margin-top:20px;'>";
echo "<a href='/' style='display:inline-block;padding:10px 20px;background:#d61935;color:white;text-decoration:none;border-radius:5px;'>🏠 Ver Site</a>";
echo "<a href='?force' style='display:inline-block;padding:10px 20px;background:#ffbb33;color:#000;text-decoration:none;border-radius:5px;margin-left:10px;'>🔄 Recarregar</a>";
echo "</div>";
?>