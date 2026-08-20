<?php
// ============================================================
// CONFIGURAÇÃO DE LOGS
// ============================================================

// Pasta onde os logs serão salvos
define('LOG_DIR', __DIR__ . '/../logs/');

// Nível de log: DEBUG, INFO, WARNING, ERROR, CRITICAL
define('LOG_LEVEL', 'DEBUG');

// Email para alertas
define('ALERT_EMAIL', 'comercial@ferrobrasmetais.com.br');

// Função para registrar log
function registrarLog($mensagem, $nivel = 'INFO', $detalhes = null) {
    $log_dir = LOG_DIR;
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $arquivo = $log_dir . 'log_' . date('Y-m-d') . '.txt';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $url = $_SERVER['REQUEST_URI'] ?? '/';
    $metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    
    $linha = "[$timestamp] [$nivel] IP: $ip | $metodo $url | $mensagem";
    if ($detalhes) {
        $linha .= " | Detalhes: " . print_r($detalhes, true);
    }
    $linha .= "\n";
    
    file_put_contents($arquivo, $linha, FILE_APPEND);
    
    // Se for erro crítico, enviar email
    if ($nivel === 'CRITICAL' || $nivel === 'ERROR') {
        enviarAlertaEmail($mensagem, $nivel, $detalhes);
    }
}

// Função para enviar alerta por email
function enviarAlertaEmail($mensagem, $nivel, $detalhes = null) {
    $para = ALERT_EMAIL;
    $assunto = "🚨 ALERTA: $nivel no site Ferrobras Metais";
    
    $corpo = "Data: " . date('Y-m-d H:i:s') . "\n";
    $corpo .= "Nível: $nivel\n";
    $corpo .= "Mensagem: $mensagem\n";
    $corpo .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0') . "\n";
    $corpo .= "URL: " . ($_SERVER['REQUEST_URI'] ?? '/') . "\n";
    $corpo .= "Método: " . ($_SERVER['REQUEST_METHOD'] ?? 'GET') . "\n";
    if ($detalhes) {
        $corpo .= "Detalhes: " . print_r($detalhes, true) . "\n";
    }
    $corpo .= "\n---\n";
    $corpo .= "Para mais detalhes, verifique os logs em: https://ferrobrasmetais.com.br/logs/\n";
    
    mail($para, $assunto, $corpo, "From: alertas@ferrobrasmetais.com.br");
}

// Função para log de erro personalizado
function logError($errno, $errstr, $errfile, $errline) {
    $nivel = 'ERROR';
    $mensagem = "$errstr em $errfile na linha $errline";
    registrarLog($mensagem, $nivel, ['errno' => $errno]);
    return true;
}

// Função para log de exceção
function logException($exception) {
    $mensagem = $exception->getMessage();
    $detalhes = [
        'arquivo' => $exception->getFile(),
        'linha' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ];
    registrarLog($mensagem, 'CRITICAL', $detalhes);
}

// Registrar erros do PHP
set_error_handler('logError');
set_exception_handler('logException');

// Registrar erros fatais
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        logError($error['type'], $error['message'], $error['file'], $error['line']);
    }
});

// Função para registrar acesso
function logAcesso() {
    registrarLog('Acesso ao site', 'INFO');
}

// Função para registrar login
function logLogin($usuario, $sucesso) {
    $nivel = $sucesso ? 'INFO' : 'WARNING';
    $mensagem = $sucesso ? "Login bem-sucedido: $usuario" : "Tentativa de login falhou: $usuario";
    registrarLog($mensagem, $nivel);
}

// Função para registrar erro de banco
function logBanco($mensagem, $detalhes = null) {
    registrarLog($mensagem, 'ERROR', $detalhes);
}
?>
