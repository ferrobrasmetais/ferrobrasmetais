<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login_simples.php');
    exit;
}

// Criar arquivo de log se não existir
$logFile = '../data/system_log.json';
if (!file_exists($logFile)) {
    file_put_contents($logFile, '[]');
}

$logs = json_decode(file_get_contents($logFile), true);
$logs = is_array($logs) ? $logs : [];
$total = count($logs);
$ultimo_acesso = $total > 0 ? $logs[0]['data'] : 'Nenhum registro';

// Tipos de ação
$tipos = [];
foreach ($logs as $log) {
    $acao = $log['acao'] ?? 'desconhecido';
    if (!isset($tipos[$acao])) {
        $tipos[$acao] = 0;
    }
    $tipos[$acao]++;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Log do Sistema</title>
    <link rel="icon" type="image/png" href="../img/ferrobrasmetais_logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; background: #f4f6f8; }
        .header { background: #1a1a1a; color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; }
        .header h1 { color: #d61935; }
        .btn { display: inline-block; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold; border: none; cursor: pointer; }
        .btn-voltar { background: #6c757d; color: white; }
        .btn-voltar:hover { background: #5a6268; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .container { max-width: 1200px; margin: 30px auto; padding: 20px; background: white; border-radius: 10px; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px; }
        .stat-box { background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center; }
        .stat-box .number { font-size: 32px; font-weight: 700; color: #d61935; }
        .stat-box .label { color: #666; font-size: 14px; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; padding: 10px; text-align: left; border-bottom: 2px solid #ddd; }
        td { padding: 10px; border-bottom: 1px solid #eee; font-size: 13px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        @media (max-width: 600px) { .stats-grid { grid-template-columns: 1fr; } }
        .logout { background: #dc3545; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; }
        .logout:hover { background: #c82333; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Log do Sistema</h1>
        <div>
            <a href="painel.php" class="btn btn-voltar">← Voltar</a>
            <a href="logout.php" class="logout">Sair</a>
        </div>
    </div>
    <div class="container">
        <div class="stats-grid">
            <div class="stat-box"><div class="number"><?php echo $total; ?></div><div class="label">Total de Registros</div></div>
            <div class="stat-box"><div class="number"><?php echo $ultimo_acesso; ?></div><div class="label">Último Acesso</div></div>
            <div class="stat-box"><div class="number"><?php echo count($tipos); ?></div><div class="label">Tipos de Ação</div></div>
        </div>

        <h2>📋 Últimos Registros</h2>
        <div style="overflow-x:auto;">
            <table>
                <thead><tr><th>Data/Hora</th><th>IP</th><th>Usuário</th><th>Ação</th><th>Detalhes</th></tr></thead>
                <tbody>
                    <?php if (count($logs) > 0): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?php echo $log['data'] ?? '-'; ?></td>
                                <td><?php echo $log['ip'] ?? '-'; ?></td>
                                <td><?php echo $log['usuario'] ?? '-'; ?></td>
                                <td>
                                    <?php
                                    $badge = 'badge-info';
                                    if (strpos($log['acao'] ?? '', 'SUCESSO') !== false) $badge = 'badge-success';
                                    if (strpos($log['acao'] ?? '', 'FALHA') !== false) $badge = 'badge-danger';
                                    ?>
                                    <span class="badge <?php echo $badge; ?>"><?php echo $log['acao'] ?? '-'; ?></span>
                                </td>
                                <td><?php echo $log['detalhes'] ?? '-'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center;color:#888;padding:20px;">Nenhum registro encontrado</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
