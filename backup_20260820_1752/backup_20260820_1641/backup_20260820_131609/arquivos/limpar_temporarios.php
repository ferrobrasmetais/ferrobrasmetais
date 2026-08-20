<?php
// ============================================================
// REMOVER ARQUIVOS TEMPORÁRIOS
// ============================================================

echo "<h1>🧹 Removendo arquivos temporários</h1>";

$arquivos_para_remover = [
    'copiar_imagens_faltando.php',
    'mover_imagens.php',
    'organizar_profissional.php',
    'remover_backups.php',
    'analisar_profissional.php',
    'ver_estrutura.php',
    'verificar.php',
    'mapa_site.php',
    'index.php.backup_20260819_144207'
];

$removidos = 0;
foreach ($arquivos_para_remover as $arq) {
    if (file_exists($arq)) {
        if (unlink($arq)) {
            echo "✅ Removido: $arq<br>";
            $removidos++;
        } else {
            echo "❌ Erro ao remover: $arq<br>";
        }
    } else {
        echo "⏭️ Não encontrado: $arq<br>";
    }
}

echo "<hr>";
echo "<h2>📊 RESULTADO</h2>";
echo "<ul>";
echo "<li>✅ Arquivos removidos: $removidos</li>";
echo "</ul>";

echo "<p><a href='/' style='display:inline-block;padding:10px 20px;background:#28a745;color:white;text-decoration:none;border-radius:4px;'>🏠 Ver site</a></p>";
echo "<p><a href='painel_industrial/login_simples.php' style='display:inline-block;padding:10px 20px;background:#d61935;color:white;text-decoration:none;border-radius:4px;'>🔐 Ver painel</a></p>";
?>