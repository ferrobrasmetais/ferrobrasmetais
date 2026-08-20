<?php
// ============================================================
// 🚨 MONITOR DE SITE
// ============================================================

// Configurações
$email_alerta = 'comercial@ferrobrasmetais.com.br';
$site_url = 'https://ferrobrasmetais.com.br/';
$tempo_limite = 5; // segundos
$arquivo_log = __DIR__ . '/logs/monitor.txt';

// Verificar se o site está no ar
function verificarSite($url, $timeout = 5) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $start = microtime(true);
    $exec = curl_exec($ch);
    $end = microtime(true);
    $tempo = round(($end - $start) * 1000, 2);
    
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'online' => $code >= 200 && $code < 400,
        'code' => $code,
        'tempo' => $tempo,
        'error' => $error,
        'data' => date('Y-m-d H:i:s')
    ];
}

// Função para enviar alerta
function enviarAlerta($status) {
    global $email_alerta, $site_url;
    
    $assunto = "🚨 SITE FERROBRAS METAIS ESTÁ FORA DO AR!";
    
    $corpo = "⚠️ ALERTA CRÍTICO!\n\n";
    $corpo .= "O site Ferrobras Metais está fora do ar!\n";
    $corpo .= "Data: " . $status['data'] . "\n";
    $corpo .= "Status: " . $status['code'] . "\n";
    $corpo .= "Erro: " . $status['error'] . "\n";
    $corpo .= "URL: $site_url\n\n";
    $corpo .= "Verifique imediatamente!\n";
    $corpo .= "---\n";
    $corpo .= "Monitor automático do site Ferrobras Metais";
    
    mail($email_alerta, $assunto, $corpo, "From: monitor@ferrobrasmetais.com.br");
}

// Função para enviar aviso de recuperação
function enviarRecuperacao($status) {
    global $email_alerta, $site_url;
    
    $assunto = "✅ SITE FERROBRAS METAIS RECUPERADO!";
    
    $corpo = "✅ O site Ferrobras Metais está online novamente!\n\n";
    $corpo .= "Data: " . $status['data'] . "\n";
    $corpo .= "Status: " . $status['code'] . "\n";
    $corpo .= "Tempo de resposta: " . $status['tempo'] . "ms\n";
    $corpo .= "URL: $site_url\n";
    
    mail($email_alerta, $assunto, $corpo, "From: monitor@ferrobrasmetais.com.br");
}

// Executar monitoramento
$resultado = verificarSite($site_url, $tempo_limite);

// Registrar no log
$log_dir = __DIR__ . '/logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

$log = "[" . $resultado['data'] . "] ";
$log .= $resultado['online'] ? "✅ ONLINE" : "❌ OFFLINE";
$log .= " | Código: " . $resultado['code'];
$log .= " | Tempo: " . $resultado['tempo'] . "ms";
$log .= $resultado['error'] ? " | Erro: " . $resultado['error'] : "";
$log .= "\n";

file_put_contents($arquivo_log, $log, FILE_APPEND);

// Verificar estado anterior
$estado_anterior = file_exists($arquivo_log . '.status') ? file_get_contents($arquivo_log . '.status') : 'online';
$estado_atual = $resultado['online'] ? 'online' : 'offline';

if ($estado_atual !== $estado_anterior) {
    if ($estado_atual === 'offline') {
        enviarAlerta($resultado);
    } else {
        enviarRecuperacao($resultado);
    }
    file_put_contents($arquivo_log . '.status', $estado_atual);
}

// Se estiver online, mostrar status
if ($resultado['online']) {
    echo "✅ Site online - Tempo: " . $resultado['tempo'] . "ms\n";
} else {
    echo "❌ Site OFFLINE - Erro: " . $resultado['error'] . "\n";
}
?>
