<?php
// ============================================================
// 🔧 CORRIGIR IMAGENS SEM QUEBRAR O SITE
// ============================================================

// Credenciais do banco
$host = 'localhost';
$dbname = 'u119221664_ferrobras_site';
$user = 'u119221664_ferrobras_user';
$pass = 'Ferrobras@2026';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erro: " . $e->getMessage());
}

// ============================================================
// GERAR O CÓDIGO CORRETO PARA AS IMAGENS
// ============================================================
$stmt = $pdo->query("SELECT id, nome, imagem FROM produtos");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h1>🔧 CÓDIGO CORRETO PARA AS IMAGENS</h1>";
echo "<div style='background:#0a0a1a;padding:20px;border-radius:8px;font-family:monospace;font-size:13px;'>";

echo "<p style='color:#33b5e5;'>// Substitua a parte de verificação de imagem no index.php por:</p>";
echo "<br>";

echo "<pre style='color:#00c851;'>";
echo "<?php 
// ============================================================
// VERIFICAÇÃO DE IMAGEM - VERSÃO CORRETA
// ============================================================
\$img_encontrada = false;
\$img_final = '';
\$nome_imagem = \$produto['imagem'];

if(!empty(\$nome_imagem)) {
    // Verificar se o arquivo existe diretamente
    if(file_exists(__DIR__ . '/' . \$nome_imagem)) {
        \$img_encontrada = true;
        \$img_final = '/' . \$nome_imagem;
    } else {
        // Tentar com barra no início
        if(file_exists(__DIR__ . '/' . ltrim(\$nome_imagem, '/'))) {
            \$img_encontrada = true;
            \$img_final = '/' . ltrim(\$nome_imagem, '/');
        }
    }
    
    // Se não encontrou, procurar nas pastas
    if(!\$img_encontrada) {
        \$pastas = [
            'assets/images/produtos/',
            'assets/images/',
            'imagens/produtos/',
            'img/produtos/',
            'img/'
        ];
        \$nome_arquivo = basename(\$nome_imagem);
        
        foreach(\$pastas as \$pasta) {
            \$caminho = \$pasta . \$nome_arquivo;
            if(file_exists(__DIR__ . '/' . \$caminho)) {
                \$img_encontrada = true;
                \$img_final = '/' . \$caminho;
                break;
            }
        }
    }
}
?>";
echo "</pre>";

echo "</div>";

// ============================================================
// LISTA DE PRODUTOS COM CAMINHOS CORRETOS
// ============================================================
echo "<h3>📋 PRODUTOS E SEUS CAMINHOS CORRETOS</h3>";
echo "<div style='font-family:monospace;'>";

foreach ($produtos as $produto) {
    $imagem = $produto['imagem'];
    $nome = $produto['nome'];
    
    // Verificar se o arquivo existe
    $caminhos = [
        __DIR__ . '/' . $imagem,
        __DIR__ . '/assets/images/produtos/' . basename($imagem),
        __DIR__ . '/assets/images/' . basename($imagem),
        __DIR__ . '/imagens/produtos/' . basename($imagem),
        __DIR__ . '/img/produtos/' . basename($imagem),
        __DIR__ . '/img/' . basename($imagem)
    ];
    
    $encontrado = false;
    foreach ($caminhos as $caminho) {
        if (file_exists($caminho)) {
            $relativo = str_replace(__DIR__, '', $caminho);
            echo "<p style='color:#00c851;'>✅ $nome → $relativo</p>";
            $encontrado = true;
            break;
        }
    }
    
    if (!$encontrado) {
        echo "<p style='color:#ff4444;'>❌ $nome → IMAGEM NÃO ENCONTRADA!</p>";
    }
}

echo "</div>";

// ============================================================
// BOTÕES
// ============================================================
echo "<div style='margin-top:20px;'>";
echo "<a href='/' style='display:inline-block;padding:10px 20px;background:#d61935;color:white;text-decoration:none;border-radius:5px;'>🏠 Voltar ao Site</a>";
echo "</div>";
?>