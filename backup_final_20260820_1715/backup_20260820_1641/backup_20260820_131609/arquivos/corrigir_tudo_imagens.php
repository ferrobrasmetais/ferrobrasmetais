<?php
// ============================================================
// 🔧 CORRIGIR TUDO - IMAGENS DOS PRODUTOS
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
} catch (PDOException $e) {
    die("❌ Erro de conexão: " . $e->getMessage());
}

// Lista de produtos com seus nomes e imagens corretas
$produtos = [
    ['nome' => 'Barras, Chapas e Pedacos', 'imagem' => '1786734274_barras_chapas_pedacos.jpg'],
    ['nome' => 'Aluminio', 'imagem' => '1786734254_aluminio.jpg'],
    ['nome' => 'Aco Inoxidavel (Inox)', 'imagem' => '1786734236_inox.jpg'],
    ['nome' => 'Nylon e Celeron', 'imagem' => '1786734871_nylon_ferrobras.jpg'],
];

// Atualizar no banco
$atualizados = 0;
foreach ($produtos as $produto) {
    $stmt = $pdo->prepare("UPDATE produtos SET imagem = ? WHERE nome LIKE ?");
    $resultado = $stmt->execute([$produto['imagem'], '%' . $produto['nome'] . '%']);
    if ($resultado) {
        $atualizados++;
        echo "<p style='color:#00c851;'>✅ Atualizado: " . $produto['nome'] . " → " . $produto['imagem'] . "</p>";
    }
}

echo "<p style='color:#00c851;'>📊 Total atualizados: $atualizados</p>";
echo "<a href='/' style='display:inline-block;padding:10px 20px;background:#d61935;color:white;text-decoration:none;border-radius:5px;margin-top:15px;'>🏠 Voltar ao Site</a>";
?>