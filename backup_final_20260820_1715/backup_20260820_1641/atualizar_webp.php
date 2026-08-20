<?php
// ============================================================
// ATUALIZAR INDEX.PHP PARA USAR WEBP
// ============================================================

echo "<h1>🔄 Atualizando site para usar WebP</h1>";
echo "<p>Data: " . date('d/m/Y H:i:s') . "</p>";
echo "<hr>";

// 1. VERIFICAR SE O INDEX.PHP EXISTE
if (!file_exists('index.php')) {
    die("❌ index.php não encontrado!");
}
echo "✅ index.php encontrado!<br>";

// 2. FAZER BACKUP
$backup = 'index.php.backup_' . date('Ymd_His');
copy('index.php', $backup);
echo "✅ Backup criado: $backup<br>";

// 3. LER O CONTEÚDO
$conteudo = file_get_contents('index.php');

// 4. SUBSTITUIR IMAGENS POR WEBP (SE EXISTIR)
$substituicoes = [
    // Logo
    [
        'buscar' => 'assets/images/logo/ferrobrasmetais_logo.png',
        'webp' => 'assets/images/logo/ferrobrasmetais_logo.webp'
    ],
    [
        'buscar' => 'assets/images/logo/logo.png',
        'webp' => 'assets/images/logo/logo.webp'
    ],
    // Hero
    [
        'buscar' => 'assets/images/hero/tubos.jpg',
        'webp' => 'assets/images/hero/tubos.webp'
    ],
    [
        'buscar' => 'assets/images/hero/tubos.png',
        'webp' => 'assets/images/hero/tubos.webp'
    ],
    // Sobre
    [
        'buscar' => 'assets/images/sobre/serra.jpg',
        'webp' => 'assets/images/sobre/serra.webp'
    ],
    [
        'buscar' => 'assets/images/sobre/serra.png',
        'webp' => 'assets/images/sobre/serra.webp'
    ]
];

$substituicoes_feitas = 0;

foreach ($substituicoes as $sub) {
    if (file_exists($sub['webp'])) {
        // Substituir no conteúdo
        $conteudo = str_replace($sub['buscar'], $sub['webp'], $conteudo);
        echo "✅ Substituído: " . basename($sub['buscar']) . " → " . basename($sub['webp']) . "<br>";
        $substituicoes_feitas++;
    } else {
        echo "⏭️ WebP não encontrado: " . basename($sub['webp']) . "<br>";
    }
}

// 5. SALVAR O ARQUIVO
if (file_put_contents('index.php', $conteudo)) {
    echo "<h2>✅ index.php atualizado com sucesso!</h2>";
    echo "<p>📊 Substituições feitas: $substituicoes_feitas</p>";
} else {
    echo "<h2>❌ Erro ao salvar index.php</h2>";
}

echo "<hr>";
echo "<p><a href='/' style='display:inline-block;padding:10px 20px;background:#28a745;color:white;text-decoration:none;border-radius:4px;'>🏠 Ver site</a></p>";
echo "<p><a href='ver_estrutura.php' style='display:inline-block;padding:10px 20px;background:#d61935;color:white;text-decoration:none;border-radius:4px;'>📁 Ver estrutura</a></p>";
?>