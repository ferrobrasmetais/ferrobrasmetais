<?php
// ============================================================
// 🔍 VERIFICAR HTML DOS PRODUTOS
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

// Buscar produtos
$stmt = $pdo->query("SELECT id, nome, imagem FROM produtos");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h1>🔍 VERIFICANDO PRODUTOS</h1>";
echo "<div style='font-family:monospace;'>";

foreach ($produtos as $produto) {
    $id = $produto['id'];
    $nome = $produto['nome'];
    $imagem = $produto['imagem'];
    
    echo "<div style='border:1px solid #333;padding:10px;margin:10px 0;'>";
    echo "<h3>$nome (ID: $id)</h3>";
    echo "<p><strong>Banco:</strong> $imagem</p>";
    
    // Verificar se o arquivo existe
    $caminho_arquivo = __DIR__ . '/' . $imagem;
    if (file_exists($caminho_arquivo)) {
        echo "<p style='color:#00c851;'>✅ Arquivo existe: $caminho_arquivo</p>";
        
        // Mostrar caminho relativo para o HTML
        $caminho_relativo = '/' . $imagem;
        echo "<p style='color:#33b5e5;'>📄 Caminho para HTML: $caminho_relativo</p>";
        
        // Tentar criar a tag img
        echo "<p>🖼️ Teste:</p>";
        echo "<img src='$caminho_relativo' alt='$nome' style='max-width:200px;border:1px solid #333;padding:5px;'>";
        echo "<br><span style='color:#888;font-size:11px;'>tag: &lt;img src='$caminho_relativo'&gt;</span>";
    } else {
        echo "<p style='color:#ff4444;'>❌ Arquivo NÃO existe: $caminho_arquivo</p>";
        
        // Procurar em outras pastas
        $pastas = ['assets/images/produtos/', 'imagens/produtos/', 'img/produtos/', 'img/'];
        $encontrado = false;
        $nome_arquivo = basename($imagem);
        
        foreach ($pastas as $pasta) {
            if (file_exists(__DIR__ . '/' . $pasta . $nome_arquivo)) {
                echo "<p style='color:#ffbb33;'>✅ Encontrado em: $pasta</p>";
                $encontrado = true;
                $caminho_relativo = '/' . $pasta . $nome_arquivo;
                echo "<p>🖼️ Teste:</p>";
                echo "<img src='$caminho_relativo' alt='$nome' style='max-width:200px;border:1px solid #333;padding:5px;'>";
                echo "<br><span style='color:#888;font-size:11px;'>tag: &lt;img src='$caminho_relativo'&gt;</span>";
                break;
            }
        }
        
        if (!$encontrado) {
            echo "<p style='color:#ff4444;'>❌ Não encontrado em nenhuma pasta!</p>";
        }
    }
    echo "</div>";
}

echo "</div>";
echo "<div style='margin-top:20px;'>";
echo "<a href='/' style='display:inline-block;padding:10px 20px;background:#d61935;color:white;text-decoration:none;border-radius:5px;'>🏠 Voltar ao Site</a>";
echo "</div>";
?>