<?php
// includes/functions_rga.php - FunÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes do RGA
require_once __DIR__ . '/../config/database_rga.php';

class RGAFunctions {
    private $db;
    
    public function __construct() {
        $this->db = db_rga();
    }
    
    // ========== USUÃƒÆ’Ã†â€™Ãƒâ€šÃ‚ÂRIOS ==========
    public function getUsuario($email, $senha) {
        $usuario = $this->db->fetchOne(
            "SELECT * FROM usuarios WHERE email = ?",
            [$email]
        );
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            return $usuario;
        }
        return false;
    }
    
    public function verificarNivel($nivelMinimo) {
        if (!isset($_SESSION['user_id'])) return false;
        $usuario = $this->db->fetchOne(
            "SELECT nivel FROM usuarios WHERE id = ? AND ativo = 1 AND bloqueado = 0",
            [$_SESSION['user_id']]
        );
        if (!$usuario) return false;
        $niveis = ['visualizador' => 1, 'operador' => 2, 'gerente' => 3, 'admin' => 4];
        $nivelUsuario = $niveis[$usuario['nivel']] ?? 0;
        $nivelRequerido = $niveis[$nivelMinimo] ?? 0;
        return $nivelUsuario >= $nivelRequerido;
    }
    
    public function getNivelUsuario() {
        if (!isset($_SESSION['user_id'])) return null;
        $usuario = $this->db->fetchOne("SELECT nivel FROM usuarios WHERE id = ?", [$_SESSION['user_id']]);
        return $usuario ? $usuario['nivel'] : null;
    }
    
    // ========== CATÃƒÆ’Ã†â€™Ãƒâ€šÃ‚ÂLOGO ==========
    public function getCatalogo() {
        return $this->db->fetchAll("SELECT * FROM catalogo_produtos WHERE ativo = 1 ORDER BY nome");
    }
    
    public function getCatalogoAll() {
        return $this->db->fetchAll("SELECT * FROM catalogo_produtos ORDER BY nome");
    }
    
    public function getCatalogoItem($id) {
        return $this->db->fetchOne("SELECT * FROM catalogo_produtos WHERE id = ?", [$id]);
    }
    
    public function adicionarCatalogo($dados) {
        return $this->db->insert('catalogo_produtos', [
            'nome' => $dados['nome'],
            'categoria' => $dados['categoria'],
            'material' => $dados['material'],
            'especificacao' => $dados['especificacao'] ?? '',
            'acabamento' => $dados['acabamento'] ?? '',
            'formato' => $dados['formato'],
            'tipo_formato' => $dados['tipo_formato'] ?? 'redondo',
            'tipo_produto' => $dados['tipo_produto'] ?? 'maciÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§o',
            'simbolo' => $dados['simbolo'] ?? 'ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒâ€šÃ‚Â¦',
            'densidade_padrao' => $dados['densidade_padrao'] ?? 0.00785,
            'ativo' => isset($dados['ativo']) ? 1 : 0
        ]);
    }
    
    public function atualizarCatalogo($id, $dados) {
        return $this->db->update('catalogo_produtos', [
            'nome' => $dados['nome'],
            'categoria' => $dados['categoria'],
            'material' => $dados['material'],
            'especificacao' => $dados['especificacao'] ?? '',
            'acabamento' => $dados['acabamento'] ?? '',
            'formato' => $dados['formato'],
            'tipo_formato' => $dados['tipo_formato'] ?? 'redondo',
            'tipo_produto' => $dados['tipo_produto'] ?? 'maciÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§o',
            'simbolo' => $dados['simbolo'] ?? 'ÃƒÆ’Ã‚Â°Ãƒâ€¦Ã‚Â¸ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒâ€šÃ‚Â¦',
            'densidade_padrao' => $dados['densidade_padrao'] ?? 0.00785,
            'ativo' => isset($dados['ativo']) ? 1 : 0
        ], 'id = ?', [$id]);
    }
    
    public function excluirCatalogo($id) {
        return $this->db->delete('catalogo_produtos', 'id = ?', [$id]);
    }
    
    // ========== ESTOQUE ==========
    public function getEstoque() {
        return $this->db->fetchAll("
            SELECT e.*, c.nome as produto_nome, c.material, c.densidade_padrao 
            FROM estoque e 
            LEFT JOIN catalogo_produtos c ON e.produto_id = c.id 
            ORDER BY e.id DESC
        ");
    }
    
    public function getEstoqueItem($id) {
        return $this->db->fetchOne("SELECT * FROM estoque WHERE id = ?", [$id]);
    }
    
        
    public function adicionarEstoque($dados) {
        $produto = $this->getCatalogoItem($dados['produto_id']);
        
        // Calcular peso usando a densidade do catÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡logo
        $pesoUnitario = $this->calcularPesoItem(
            $produto['tipo_formato'] ?? $produto['formato'],
            $produto['material'],
            $dados,
            $produto['densidade_padrao'] ?? 0.00785
        );
        
        $pesoTotal = $pesoUnitario * $dados['quantidade'];
        $valorTotal = $pesoTotal * ($dados['preco_kg'] ?? 0);
        
        return $this->db->insert('estoque', [
            'produto_id' => $dados['produto_id'],
            'tipo' => $produto['nome'],
            'material' => $produto['material'],
            'formato' => $produto['formato'],
            'tipo_formato' => $produto['tipo_formato'] ?? 'redondo',
            'dimensao1' => $dados['dimensao1'] ?? 0,
            'dimensao2' => $dados['dimensao2'] ?? 0,
            'altura' => $dados['altura'] ?? 0,
            'largura' => $dados['largura'] ?? 0,
            'diametro' => $dados['diametro'] ?? 0,
            'parede' => $dados['parede'] ?? 0,
            'espessura' => $dados['espessura'] ?? 0,
            'comprimento' => $dados['comprimento'] ?? 0,
            'quantidade' => $dados['quantidade'],
            'peso_unitario' => $pesoUnitario,
            'peso_total' => $pesoTotal,
            'peso_especifico' => $produto['densidade_padrao'] ?? 0.00785,
            'preco_custo' => $valorTotal,
            'preco_kg' => $dados['preco_kg'] ?? 0,
            'data_compra' => $dados['data_compra'] ?? null,
            'status_estoque' => $dados['status_estoque'] ?? 'em_casa',
            'alerta_minimo' => $dados['alerta_minimo'] ?? 5,
            'em_casa' => isset($dados['em_casa']) ? 1 : 0,
            'localizacao' => $dados['localizacao'] ?? 'fabrica',
            'disponivel' => isset($dados['disponivel']) ? 1 : 0,
            'observacao' => $dados['observacao'] ?? ''
        ]);
    }
    
    public function atualizarEstoque($id, $dados) {
        $produto = $this->getCatalogoItem($dados['produto_id']);
        $pesoUnitario = $this->calcularPesoItem(
            $produto['tipo_formato'] ?? $produto['formato'],
            $produto['material'],
            $dados,
            $produto['densidade_padrao'] ?? 0.00785
        );
        $pesoTotal = $pesoUnitario * $dados['quantidade'];
        $valorTotal = $pesoTotal * ($dados['preco_kg'] ?? 0);
        
        return $this->db->update('estoque', [
            'produto_id' => $dados['produto_id'],
            'tipo' => $produto['nome'],
            'material' => $produto['material'],
            'formato' => $produto['formato'],
            'tipo_formato' => $produto['tipo_formato'] ?? 'redondo',
            'dimensao1' => $dados['dimensao1'] ?? 0,
            'dimensao2' => $dados['dimensao2'] ?? 0,
            'altura' => $dados['altura'] ?? 0,
            'largura' => $dados['largura'] ?? 0,
            'diametro' => $dados['diametro'] ?? 0,
            'parede' => $dados['parede'] ?? 0,
            'espessura' => $dados['espessura'] ?? 0,
            'comprimento' => $dados['comprimento'] ?? 0,
            'quantidade' => $dados['quantidade'],
            'peso_unitario' => $pesoUnitario,
            'peso_total' => $pesoTotal,
            'peso_especifico' => $produto['densidade_padrao'] ?? 0.00785,
            'preco_custo' => $valorTotal,
            'preco_kg' => $dados['preco_kg'] ?? 0,
            'data_compra' => $dados['data_compra'] ?? null,
            'status_estoque' => $dados['status_estoque'] ?? 'em_casa',
            'alerta_minimo' => $dados['alerta_minimo'] ?? 5,
            'localizacao' => $dados['localizacao'] ?? 'fabrica',
            'disponivel' => isset($dados['disponivel']) ? 1 : 0,
            'observacao' => $dados['observacao'] ?? ''
        ], 'id = ?', [$id]);
    }
    
    public function excluirEstoque($id) {
        return $this->db->delete('estoque', 'id = ?', [$id]);
    }
    
    // ========== MATERIAIS ==========
    public function getMateriais() {
        return $this->db->fetchAll("SELECT * FROM materiais WHERE ativo = 1 ORDER BY nome");
    }
    
    public function getMateriaisOptions() {
        $materiais = $this->getMateriais();
        $options = [];
        foreach ($materiais as $mat) {
            $options[$mat['nome']] = floatval($mat['densidade']);
        }
        return $options;
    }
    
    // ========== CÃƒÆ’Ã†â€™Ãƒâ€šÃ‚ÂLCULOS ==========
    public function salvarCalculo($tipo_material, $formato, $peso) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        return $this->db->insert('calculos_realizados', [
            'tipo_material' => $tipo_material,
            'formato' => $formato,
            'peso' => $peso,
            'ip' => $ip
        ]);
    }
    
    public function getCalculos() {
        return $this->db->fetchAll("SELECT * FROM calculos_realizados ORDER BY created_at DESC");
    }
    
    // ========== DIMENSÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¢ES ==========
    public function getDimensoesPadrao() {
        return $this->db->fetchAll("SELECT * FROM dimensoes_padrao WHERE ativo = 1 ORDER BY tipo_formato, diametro_mm");
    }
    
    // ========== ACABAMENTOS ==========
    public function getAcabamentos() {
        return $this->db->fetchAll("SELECT * FROM acabamentos WHERE ativo = 1 ORDER BY ordem");
    }
    
    // ========== PESO ==========
    public function calcularPesoItem($formato, $material, $dados, $densidade = null) {
        if ($densidade === null) {
            $mat = $this->db->fetchOne("SELECT densidade FROM materiais WHERE nome = ?", [$material]);
            $rho = $mat ? floatval($mat['densidade']) : 0.00785;
        } else {
            $rho = floatval($densidade);
        }
        
        $rho = $rho / 1000;
        $peso = 0;
        $C = $dados['comprimento'] ?? 0;
        $D = $dados['diametro'] ?? $dados['dimensao1'] ?? 0;
        $E = $dados['parede'] ?? $dados['dimensao2'] ?? 0;
        $A = $dados['altura'] ?? 0;
        $L = $dados['largura'] ?? 0;
        $Esp = $dados['espessura'] ?? 0;
        
        switch ($formato) {
            case 'redondo': $peso = (3.14159 * pow($D / 2, 2) * $C * $rho); break;
            case 'quadrado': $peso = (pow($L, 2) * $C * $rho); break;
            case 'retangular': $peso = ($A * $L * $C * $rho); break;
            case 'sextavado': $peso = (1.103 * pow($D, 2) * $C * $rho); break;
            case 'tuboRedondo': $peso = (3.14159 * ($D - $E) * $E * $C * $rho); break;
            case 'tuboQuadrado': $peso = (4 * $E * ($L - $E) * $C * $rho); break;
            case 'tuboRetangular': $peso = (2 * $E * ($A + $L - (2 * $E)) * $C * $rho); break;
            case 'chapa': $peso = ($Esp * $L * $C * $rho); break;
            default: $peso = 0;
        }
        
        return round($peso, 3);
    }
}

function rga() {
    return new RGAFunctions();
}
?>





