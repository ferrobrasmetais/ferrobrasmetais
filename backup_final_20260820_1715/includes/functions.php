<?php
// includes/functions.php

require_once __DIR__ . '/../config/database.php';

class SiteFunctions {
    private $db;

    public function __construct() {
        $this->db = db();
    }

    public function getProdutos() {
        $sql = "SELECT * FROM produtos WHERE ativo = 1 ORDER BY id";
        return fetchAll($sql);
    }

    public function getGaleria() {
        $sql = "SELECT * FROM galeria WHERE ativo = 1 ORDER BY ordem";
        return fetchAll($sql);
    }

    public function getMateriais() {
        $sql = "SELECT * FROM materiais WHERE ativo = 1 ORDER BY nome";
        return fetchAll($sql);
    }

    public function salvarCalculo($tipo_material, $formato, $peso) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $sql = "INSERT INTO calculos_realizados (tipo_material, formato, peso, ip) VALUES ('$tipo_material', '$formato', '$peso', '$ip')";
        return query($sql);
    }

    public function getMateriaisOptions() {
        $materiais = $this->getMateriais();
        $options = [];
        foreach ($materiais as $mat) {
            $options[$mat['nome']] = floatval($mat['densidade']);
        }
        return $options;
    }

    // ========== FUNÃƒâ€¡Ãƒâ€¢ES DE CATÃƒÂLOGO ==========
    
    public function getEstoque() {
        return fetchAll("SELECT * FROM estoque ORDER BY id");
    }

    public function importarEstoqueCSV($arquivo, $usuario) {
        $handle = fopen($arquivo, 'r');
        $count = 0;
        while (($data = fgetcsv($handle, 1000, ';')) !== false) {
            $sql = "INSERT INTO estoque (produto_id, tipo_formato, altura, largura, diametro, parede, comprimento, quantidade, localizacao, observacao, disponivel) 
                    VALUES ('$data[0]', '$data[1]', '$data[2]', '$data[3]', '$data[4]', '$data[5]', '$data[6]', '$data[7]', '$data[8]', '$data[9]', '$data[10]')";
            query($sql);
            $count++;
        }
        fclose($handle);

        $sql = "INSERT INTO historico_estoque (total_itens, arquivo, usuario, observacao) 
                VALUES ('$count', '" . basename($arquivo) . "', '$usuario', 'ImportaÃƒÂ§ÃƒÂ£o via CSV')";
        query($sql);

        return $count;
    }

    public function exportarEstoqueCSV() {
        $itens = $this->getEstoque();
        $handle = fopen('php://memory', 'r+');

        fputcsv($handle, ['ID Produto', 'Tipo', 'Altura(mm)', 'Largura(mm)', 'DiÃƒÂ¢metro(mm)', 'Parede(mm)', 'Comprimento(mm)', 'Quantidade', 'LocalizaÃƒÂ§ÃƒÂ£o', 'ObservaÃƒÂ§ÃƒÂ£o', 'DisponÃƒÂ­vel'], ';');

        foreach ($itens as $item) {
            fputcsv($handle, [
                $item['produto_id'],
                $item['tipo_formato'] ?? $item['formato'],
                $item['altura'],
                $item['largura'],
                $item['diametro'],
                $item['parede'],
                $item['comprimento'],
                $item['quantidade'],
                $item['localizacao'],
                $item['observacao'],
                $item['disponivel'] ? 'sim' : 'nÃƒÂ£o'
            ], ';');
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}

function site() {
    return new SiteFunctions();
}
