<?php
// ============================================================
// 🔧 CORRIGIR IMAGENS - VERSÃO FINAL
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
// BUSCAR PRODUTOS COM O CAMINHO ERRADO
// ============================================================
$stmt = $pdo->query("SELECT id, nome, imagem FROM produtos");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>🔧 CORRIGINDO CAMINHOS</h2>";
echo "<div style='font-family:monospace;'>";

$corrigidos = 0;
$erros = 0;

foreach ($produtos as $produto) {
    $id = $produto['id'];
    $nome = $produto['nome'];
    $imagem_atual = $produto['imagem'];
    
    echo "<p><strong>$nome</strong></p>";
    echo "<span style='color:#888;'>Atual: $imagem_atual</span><br>";
    
    // Extrair apenas o nome do arquivo
    $nome_arquivo = basename($imagem_atual);
    echo "<span style='color:#888;'>Arquivo: $nome_arquivo</span><br>";
    
    // Procurar o arquivo nas pastas
    $pastas = [
        'assets/images/produtos/',
        'imagens/produtos/',
        'img/produtos/',
        'img/'
    ];
    
    $encontrado = false;
    $caminho_novo = '';
    
    foreach ($pastas as $pasta) {
        $caminho_completo = __DIR__ . '/' . $pasta . $nome_arquivo;
        if (file_exists($caminho_completo)) {
            $encontrado = true;
            $caminho_novo = $pasta . $nome_arquivo;
            echo "<span style='color:#00c851;'>✅ Encontrado em: $caminho_novo</span><br>";
            break;
        }
    }
    
    // Se não encontrou, tentar procurar sem extensão
    if (!$encontrado) {
        $nome_sem_ext = pathinfo($nome_arquivo, PATHINFO_FILENAME);
        foreach ($pastas as $pasta) {
            $arquivos = glob(__DIR__ . '/' . $pasta . $nome_sem_ext . '.*');
            if (!empty($arquivos)) {
                $encontrado = true;
                $caminho_novo = $pasta . basename($arquivos[0]);
                echo "<span style='color:#00c851;'>✅ Encontrado (variação): $caminho_novo</span><br>";
                break;
            }
        }
    }
    
    if ($encontrado) {
        // Atualizar o banco
        $stmt_update = $pdo->prepare("UPDATE produtos SET imagem = ? WHERE id = ?");
        if ($stmt_update->execute([$caminho_novo, $id])) {
            echo "<span style='color:#00c851;'>✅ Atualizado!</span><br>";
            $corrigidos++;
        } else {
            echo "<span style='color:#ff4444;'>❌ Erro ao atualizar</span><br>";
            $erros++;
        }
    } else {
        echo "<span style='color:#ff4444;'>❌ Arquivo não encontrado em nenhuma pasta!</span><br>";
        $erros++;
    }
    
    echo "<br>";
}

echo "</div>";

// ============================================================
// VERIFICAR O QUE FOI FEITO
// ============================================================
$stmt = $pdo->query("SELECT id, nome, imagem FROM produtos");
$produtos_atualizados = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>📋 PRODUTOS APÓS CORREÇÃO</h3>";
echo "<div style='font-family:monospace;'>";
foreach ($produtos_atualizados as $p) {
    echo "<p>" . $p['nome'] . " → <span style='color:#00c851;'>" . $p['imagem'] . "</span></p>";
}
echo "</div>";

// ============================================================
// RESUMO
// ============================================================
echo "<div style='margin-top:20px;border-top:1px solid #333;padding-top:15px;'>";
echo "<h3>📊 RESUMO</h3>";
echo "<p style='color:#00c851;'>✅ Corrigidos: $corrigidos</p>";
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