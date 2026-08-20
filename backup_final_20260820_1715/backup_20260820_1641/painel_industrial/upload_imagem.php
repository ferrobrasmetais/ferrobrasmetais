<?php
// ============================================
// UPLOAD DE IMAGEM - CAMINHO CORRETO (PASTA imagens/produtos/)
// ============================================

session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'NÃƒÂ£o autorizado']);
    exit;
}

if (!isset($_FILES['imagem'])) {
    echo json_encode(['success' => false, 'message' => 'Nenhuma imagem enviada']);
    exit;
}

$file = $_FILES['imagem'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Erro no upload: ' . $file['error']]);
    exit;
}

$tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file['type'], $tiposPermitidos)) {
    echo json_encode(['success' => false, 'message' => 'Formato nÃƒÂ£o permitido. Use JPG, PNG, GIF ou WEBP.']);
    exit;
}

if ($file['size'] > 2 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'Imagem muito grande. MÃƒÂ¡ximo 2MB.']);
    exit;
}

// ============================================
// SALVAR NA PASTA CORRETA E GERAR CAMINHO SEM "../"
// ============================================
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$nome = uniqid() . '.' . $ext;

// CAMINHO ABSOLUTO FIXO PARA A PASTA imagens/produtos
$pasta_destino = 'C:/xampp/htdocs/site-ferrobras/imagens/produtos/';

if (!file_exists($pasta_destino)) {
    mkdir($pasta_destino, 0777, true);
}

$caminho_fisico = $pasta_destino . $nome;

// CAMINHO RELATIVO SEM O "../" 
$caminho_relativo = 'imagens/produtos/' . $nome;

if (move_uploaded_file($file['tmp_name'], $caminho_fisico)) {
    echo json_encode([
        'success' => true,
        'message' => 'Imagem enviada com sucesso!',
        'path' => $caminho_relativo,
        'filename' => $nome
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao salvar imagem. Verifique permissÃƒÂµes da pasta.'
    ]);
}
?>