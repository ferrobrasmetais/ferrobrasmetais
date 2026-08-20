<?php
// ============================================
// SISTEMA DE LOG - MONITORAMENTO DO PAINEL
// ============================================

class SystemLog {
    private $arquivoLog;
    
    public function __construct() {
        $this->arquivoLog = __DIR__ . "/../data/system_log.json";
        
        if (!is_dir(dirname($this->arquivoLog))) {
            mkdir(dirname($this->arquivoLog), 0755, true);
        }
    }
    
    public function registrar($acao, $detalhes = "") {
        $logs = $this->getLogs();
        
        $entrada = array(
            "data" => date("Y-m-d H:i:s"),
            "ip" => $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0",
            "usuario" => $_SESSION["admin_usuario"] ?? "desconhecido",
            "acao" => $acao,
            "detalhes" => $detalhes,
            "user_agent" => $_SERVER["HTTP_USER_AGENT"] ?? "desconhecido"
        );
        
        array_unshift($logs, $entrada);
        
        // Manter apenas os últimos 500 registros
        if (count($logs) > 500) {
            $logs = array_slice($logs, 0, 500);
        }
        
        $this->saveLogs($logs);
    }
    
    private function getLogs() {
        if (file_exists($this->arquivoLog)) {
            $content = file_get_contents($this->arquivoLog);
            $decoded = json_decode($content, true);
            return is_array($decoded) ? $decoded : array();
        }
        return array();
    }
    
    private function saveLogs($logs) {
        file_put_contents($this->arquivoLog, json_encode($logs, JSON_PRETTY_PRINT), LOCK_EX);
    }
    
    public function getLogsRecentes($limite = 50) {
        $logs = $this->getLogs();
        return array_slice($logs, 0, $limite);
    }
    
    public function getEstatisticas() {
        $logs = $this->getLogs();
        $total = count($logs);
        
        $contagem = array();
        foreach ($logs as $log) {
            $acao = $log["acao"];
            if (!isset($contagem[$acao])) {
                $contagem[$acao] = 0;
            }
            $contagem[$acao]++;
        }
        
        return array(
            "total" => $total,
            "por_acao" => $contagem,
            "ultimo_acesso" => $total > 0 ? $logs[0]["data"] : "Nenhum registro"
        );
    }
}