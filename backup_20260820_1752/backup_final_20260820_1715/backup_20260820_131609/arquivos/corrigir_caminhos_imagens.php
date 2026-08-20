<?php
// ============================================================
// 🔧 CORRIGIR CAMINHOS DAS IMAGENS NO BANCO
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
// VERIFICAR ONDE AS IMAGENS ESTÃO
// ============================================================
echo "<h2>🔍 VERIFICANDO ARQUIVOS</h2>";
echo "<div style='font-family:monospace;'>";

$pastas = [
    'assets/images/produtos/',
    'imagens/produtos/',
    'img/produtos/',
    'img/'
];

$imagens_encontradas = [];

foreach ($pastas as $pasta) {
    if (is_dir(__DIR__ . '/' . $pasta)) {
        $arquivos = glob(__DIR__ . '/' . $pasta . '*.webp');
        if (!empty($arquivos)) {
            echo "<p style='color:#00c851;'>📁 $pasta: " . count($arquivos) . " arquivos WebP</p>";
            foreach ($arquivos as $arq) {
                $nome = basename($arq);
                $imagens_encontradas[$nome] = $pasta . $nome;
                echo "<span style='color:#888;font-size:11px;padding-left:20px;'>📄 $nome</span><br>";
            }
        }
    }
}

echo "</div>";

// ============================================================
// ATUALIZAR O BANCO COM O CAMINHO COMPLETO
// ============================================================
echo "<h2>🔧 ATUALIZANDO BANCO</h2>";
echo "<div style='font-family:monospace;'>";

// Buscar produtos
$stmt = $pdo->query("SELECT id, nome, imagem FROM produtos");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$atualizados = 0;
$erros = 0;

foreach ($produtos as $produto) {
    $imagem = $produto['imagem'];
    $id = $produto['id'];
    $nome = $produto['nome'];
    
    // Verificar se a imagem existe em alguma pasta
    $caminho_completo = null;
    foreach ($pastas as $pasta) {
        if (file_exists(__DIR__ . '/' . $pasta . $imagem)) {
            $caminho_completo = $pasta . $imagem;
            break;
        }
    }
    
    if ($caminho_completo) {
        // Atualizar com o caminho completo
        $stmt_update = $pdo->prepare("UPDATE produtos SET imagem = ? WHERE id = ?");
        $resultado = $stmt_update->execute([$caminho_completo, $id]);
        
        if ($resultado) {
            echo "<p style='color:#00c851;'>✅ $nome: $imagem → $caminho_completo</p>";
            $atualizados++;
        } else {
            echo "<p style='color:#ff4444;'>❌ Erro ao atualizar: $nome</p>";
            $erros++;
        }
    } else {
        echo "<p style='color:#ffbb33;'>⚠️ Imagem não encontrada: $imagem</p>";
        $erros++;
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
// BOTÕES
// ============================================================
echo "<div style='margin-top:20px;'>";
echo "<a href='/' style='display:inline-block;padding:10px 20px;background:#d61935;color:white;text-decoration:none;border-radius:5px;'>🏠 Voltar ao Site</a>";
echo " <a href='?force' style='display:inline-block;padding:10px 20px;background:#ffbb33;color:#000;text-decoration:none;border-radius:5px;'>🔄 Recarregar</a>";
echo "</div>";
?>