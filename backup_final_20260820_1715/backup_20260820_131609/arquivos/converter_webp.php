<?php
// ============================================================
// CONVERTER IMAGENS PARA WEBP (COM SEGURANÇA)
// ============================================================

echo "<h1>🔄 Converter para WebP</h1>";
echo "<p>Este script APENAS CONVERTE, não apaga nada.</p>";
echo "<p>As imagens originais são MANTIDAS.</p>";
echo "<hr>";

// ============================================================
// 1. VERIFICAR SE A EXTENSÃO GD ESTÁ INSTALADA
// ============================================================
if (!extension_loaded('gd')) {
    die("❌ Extensão GD não está instalada.");
}
echo "✅ Extensão GD disponível!<br>";

// ============================================================
// 2. DEFINIR PASTAS PARA ESCANEAR
// ============================================================
$pastas = [
    'assets/images/logo/',
    'assets/images/produtos/',
    'assets/images/hero/',
    'assets/images/sobre/'
];

$total_convertidas = 0;
$total_puladas = 0;
$total_erros = 0;

// ============================================================
// 3. FUNÇÃO PARA CONVERTER
// ============================================================
function converter_para_webp($caminho_origem, $qualidade = 80) {
    if (!file_exists($caminho_origem)) {
        return ['status' => 'erro', 'msg' => 'Arquivo não encontrado'];
    }
    
    $webp_caminho = pathinfo($caminho_origem, PATHINFO_FILENAME) . '.webp';
    $webp_completo = dirname($caminho_origem) . '/' . $webp_caminho;
    
    if (file_exists($webp_completo)) {
        return ['status' => 'pular', 'msg' => 'WebP já existe'];
    }
    
    $ext = strtolower(pathinfo($caminho_origem, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
        return ['status' => 'pular', 'msg' => 'Formato não suportado'];
    }
    
    $imagem = null;
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            $imagem = imagecreatefromjpeg($caminho_origem);
            break;
        case 'png':
            $imagem = imagecreatefrompng($caminho_origem);
            imagepalettetotruecolor($imagem);
            imagealphablending($imagem, true);
            imagesavealpha($imagem, true);
            break;
    }
    
    if (!$imagem) {
        return ['status' => 'erro', 'msg' => 'Erro ao carregar'];
    }
    
    if (imagewebp($imagem, $webp_completo, $qualidade)) {
        imagedestroy($imagem);
        $original = round(filesize($caminho_origem) / 1024, 2);
        $webp = round(filesize($webp_completo) / 1024, 2);
        $reducao = round((1 - ($webp / $original)) * 100, 2);
        
        return [
            'status' => 'ok',
            'original' => $original,
            'webp' => $webp,
            'reducao' => $reducao
        ];
    }
    
    imagedestroy($imagem);
    return ['status' => 'erro', 'msg' => 'Erro ao salvar'];
}

// ============================================================
// 4. PROCESSAR PASTAS
// ============================================================
foreach ($pastas as $pasta) {
    if (!is_dir($pasta)) {
        echo "❌ Pasta não encontrada: $pasta<br>";
        continue;
    }
    
    echo "<h2>📁 $pasta</h2>";
    
    $arquivos = scandir($pasta);
    foreach ($arquivos as $arq) {
        if ($arq == '.' || $arq == '..') continue;
        
        $ext = strtolower(pathinfo($arq, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) continue;
        
        $caminho = $pasta . $arq;
        echo "🔄 $arq ... ";
        
        $resultado = converter_para_webp($caminho);
        
        if ($resultado['status'] == 'ok') {
            echo "✅ " . $resultado['original'] . " KB → " . $resultado['webp'] . " KB (" . $resultado['reducao'] . "% redução)<br>";
            $total_convertidas++;
        } elseif ($resultado['status'] == 'pular') {
            echo "⏭️ " . $resultado['msg'] . "<br>";
            $total_puladas++;
        } else {
            echo "❌ " . $resultado['msg'] . "<br>";
            $total_erros++;
        }
    }
}

// ============================================================
// 5. RESUMO
// ============================================================
echo "<hr>";
echo "<h2>📊 RESUMO</h2>";
echo "<ul>";
echo "<li>✅ Convertidas: $total_convertidas</li>";
echo "<li>⏭️ Puladas: $total_puladas</li>";
echo "<li>❌ Erros: $total_erros</li>";
echo "</ul>";

if ($total_convertidas > 0) {
    echo "<div style='background:#d4edda;padding:15px;border-radius:4px;'>";
    echo "🎉 <strong>$total_convertidas imagens convertidas para WebP!</strong><br>";
    echo "📁 As imagens originais foram MANTIDAS.<br>";
    echo "🔗 O site continua usando as imagens originais (não vai quebrar).";
    echo "</div>";
}

echo "<p><a href='/' style='display:inline-block;padding:10px 20px;background:#28a745;color:white;text-decoration:none;border-radius:4px;'>🏠 Ver site</a></p>";
echo "<p><a href='ver_estrutura.php' style='display:inline-block;padding:10px 20px;background:#d61935;color:white;text-decoration:none;border-radius:4px;'>📁 Ver estrutura</a></p>";
?>