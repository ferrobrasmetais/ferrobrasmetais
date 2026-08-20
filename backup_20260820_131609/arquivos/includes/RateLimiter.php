<?php
// ============================================
// RATE LIMITING - Controle de tentativas de login
// ============================================

class RateLimiter {
    private $tentativasMaximas = 5;
    private $tempoBloqueio = 300;
    private $arquivoLog;

    public function __construct() {
        $this->arquivoLog = __DIR__ . '/../data/login_attempts.json';
        
        if (!is_dir(dirname($this->arquivoLog))) {
            mkdir(dirname($this->arquivoLog), 0755, true);
        }
    }

    private function getLogs() {
        if (file_exists($this->arquivoLog)) {
            $content = file_get_contents($this->arquivoLog);
            return json_decode($content, true) ?? [];
        }
        return [];
    }

    private function saveLogs($logs) {
        file_put_contents($this->arquivoLog, json_encode($logs), LOCK_EX);
    }

    public function verificarIP($ip) {
        $logs = $this->getLogs();
        $now = time();

        foreach ($logs as $key => $log) {
            if (($now - $log['timestamp']) > 3600) {
                unset($logs[$key]);
            }
        }

        if (isset($logs[$ip]) && $logs[$ip]['bloqueado_ate'] > $now) {
            $tempoRestante = $logs[$ip]['bloqueado_ate'] - $now;
            return [
                'bloqueado' => true,
                'tempo_restante' => $tempoRestante,
                'mensagem' => "IP bloqueado por " . ceil($tempoRestante / 60) . " minuto(s)"
            ];
        }

        return ['bloqueado' => false];
    }

    public function registrarTentativa($ip, $sucesso = false) {
        $logs = $this->getLogs();
        $now = time();

        if (!isset($logs[$ip])) {
            $logs[$ip] = [
                'tentativas' => 0,
                'timestamp' => $now,
                'bloqueado_ate' => 0
            ];
        }

        if ($sucesso) {
            $logs[$ip]['tentativas'] = 0;
            $logs[$ip]['bloqueado_ate'] = 0;
            $this->saveLogs($logs);
            return;
        }

        $logs[$ip]['tentativas']++;
        $logs[$ip]['timestamp'] = $now;

        if ($logs[$ip]['tentativas'] >= $this->tentativasMaximas) {
            $logs[$ip]['bloqueado_ate'] = $now + $this->tempoBloqueio;
        }

        $this->saveLogs($logs);
    }

    public function getTentativasRestantes($ip) {
        $logs = $this->getLogs();
        if (isset($logs[$ip])) {
            $restantes = $this->tentativasMaximas - $logs[$ip]['tentativas'];
            return $restantes > 0 ? $restantes : 0;
        }
        return $this->tentativasMaximas;
    }
}