<?php
// ============================================================
// 🔧 ATUALIZAR IMAGENS PARA WEBP NO BANCO
// ============================================================

// Credenciais do banco
$host = 'localhost';
$dbname = 'u119221664_ferrobras_site';
$user = 'u119221664_ferrobras_user';
$pass = 'Ferrobras@2026';

// Conectar ao banco
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:#00c851;'>✅ Conectado ao banco!</p>";
} catch (PDOException $e) {
    die("<p style='color:#ff4444;'>❌ Erro: " . $e->getMessage() . "</p>");
}

// ============================================================
// BUSCAR PRODUTOS
// ============================================================
$stmt = $pdo->query("SELECT id, nome, imagem FROM produtos WHERE imagem IS NOT NULL AND imagem != ''");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>📋 Produtos encontrados: " . count($produtos) . "</h2>";
echo "<div style='font-family:monospace;'>";

$atualizados = 0;
$erros = 0;

foreach ($produtos as $produto) {
    $imagem_atual = $produto['imagem'];
    $id = $produto['id'];
    $nome = $produto['nome'];
    
    // Verificar se a imagem é JPG/PNG e se existe versão WebP
    $extensao = strtolower(pathinfo($imagem_atual, PATHINFO_EXTENSION));
    $nome_sem_ext = pathinfo($imagem_atual, PATHINFO_FILENAME);
    
    if (in_array($extensao, ['jpg', 'jpeg', 'png', 'gif'])) {
        // Procurar a versão WebP
        $webp_caminhos = [
            'assets/images/produtos/' . $nome_sem_ext . '.webp',
            'assets/images/produtos/' . $imagem_atual . '.webp',
            'assets/images/produtos/' . $nome_sem_ext . '.webp',
            'imagens/produtos/' . $nome_sem_ext . '.webp',
            'imagens/produtos/' . $imagem_atual . '.webp',
            'img/produtos/' . $nome_sem_ext . '.webp',
            'img/' . $nome_sem_ext . '.webp',
        ];
        
        $webp_encontrado = false;
        foreach ($webp_caminhos as $caminho) {
            if (file_exists(__DIR__ . '/' . $caminho)) {
                $webp_encontrado = $caminho;
                break;
            }
        }
        
        if ($webp_encontrado) {
            // Atualizar o banco
            $stmt_update = $pdo->prepare("UPDATE produtos SET imagem = ? WHERE id = ?");
            $resultado = $stmt_update->execute([basename($webp_encontrado), $id]);
            
            if ($resultado) {
                echo "<p style='color:#00c851;'>✅ $nome: $imagem_atual → " . basename($webp_encontrado) . "</p>";
                $atualizados++;
            } else {
                echo "<p style='color:#ff4444;'>❌ Erro ao atualizar: $nome</p>";
                $erros++;
            }
        } else {
            echo "<p style='color:#ffbb33;'>⚠️ WebP não encontrado para: $imagem_atual</p>";
            $erros++;
        }
    } else {
        echo "<p style='color:#33b5e5;'>ℹ️ $nome: já está em WebP ($imagem_atual)</p>";
    }
}

echo "</div>";

// ============================================================
// RESUMO
// ============================================================
echo "<div style='margin-top:20px;border-top:1px solid #333;padding-top:15px;'>";
echo "<h3>📊 RESUMO</h3>";
echo "<p style='color:#00c851;'>✅ Atualizados: $atualizados</p>";
echo "<p style='color:#ff4444;'>❌ Erros: $erros</p>";
echo "</div>";

// ============================================================
// VERIFICAR SE AS IMAGENS EXISTEM
// ============================================================
echo "<div style='margin-top:20px;'>";
echo "<h3>🔍 Verificando arquivos WebP</h3>";
echo "<div style='font-family:monospace;'>";

$pastas = [
    'assets/images/produtos/',
    'imagens/produtos/',
    'img/produtos/',
    'img/'
];

foreach ($pastas as $pasta) {
    if (is_dir(__DIR__ . '/' . $pasta)) {
        $arquivos = glob(__DIR__ . '/' . $pasta . '*.webp');
        if (!empty($arquivos)) {
            echo "<p style='color:#00c851;'>📁 $pasta: " . count($arquivos) . " arquivos WebP</p>";
            foreach (array_slice($arquivos, 0, 5) as $arq) {
                echo "<span style='color:#888;font-size:11px;padding-left:20px;'>📄 " . basename($arq) . "</span><br>";
            }
        }
    }
}

echo "</div></div>";

// ============================================================
// BOTÃO VOLTAR
// ============================================================
echo "<div style='margin-top:20px;'>";
echo "<a href='/' style='display:inline-block;padding:10px 20px;background:#d61935;color:white;text-decoration:none;border-radius:5px;'>🏠 Voltar ao Site</a>";
echo "</div>";
?>