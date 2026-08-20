<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de Peso de Metais | Ferrobras Metais</title>
    <meta name="description" content="Calcule o peso teórico de metais, tubos, chapas e barras. Ferrobras Metais - Caxias do Sul">
    <link rel="icon" type="image/png" href="/assets/images/ferrobrasmetais_logo.webp">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Oswald:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #d61935;
            --primary-dark: #b01229;
            --dark: #121212;
            --gray-light: #f4f6f8;
            --text-main: #333333;
            --text-muted: #666666;
            --border-color: #e0e0e0;
            --transition: all 0.3s ease;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--gray-light); color: var(--text-main); line-height: 1.6; }
        .calc-header { background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 15px 20px; border-bottom: 1px solid var(--border-color); }
        .calc-header .container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .calc-header .logo-area { display: flex; align-items: center; gap: 15px; }
        .calc-header .logo-area img { height: 60px; width: auto; }
        .calc-header .logo-area h1 { font-family: 'Oswald', sans-serif; font-size: 1.5rem; color: var(--dark); }
        .calc-header .logo-area h1 span { color: var(--primary); }
        .btn-back { background: var(--primary); color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: var(--transition); }
        .btn-back:hover { background: var(--primary-dark); transform: translateY(-2px); }
        .btn-whatsapp-calc { background: #25d366; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: var(--transition); }
        .btn-whatsapp-calc:hover { background: #20ba5a; transform: translateY(-2px); }
        .calc-container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .calc-wrapper { background: white; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid var(--border-color); }
        .calc-top-header { padding: 25px 30px 15px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; border-bottom: 1px solid var(--border-color); }
        .calc-top-header .title-area { display: flex; align-items: center; gap: 15px; }
        .calc-top-header .title-area i { font-size: 2rem; color: var(--primary); }
        .calc-top-header .title-area h2 { font-family: 'Oswald', sans-serif; font-size: 1.8rem; text-transform: uppercase; color: var(--dark); }
        .calc-top-header .title-area p { color: var(--text-muted); font-size: 0.9rem; }
        .badge-brand { background: var(--dark); color: white; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
        .calc-tabs { display: flex; background: #f8f9fa; border-bottom: 1px solid var(--border-color); overflow-x: auto; padding: 0 20px; gap: 2px; }
        .calc-tab { padding: 14px 20px; font-weight: 600; font-size: 0.9rem; color: var(--text-muted); background: none; border: none; cursor: pointer; transition: var(--transition); white-space: nowrap; border-bottom: 3px solid transparent; }
        .calc-tab:hover { color: var(--dark); background: rgba(214, 25, 53, 0.05); }
        .calc-tab.active { color: var(--dark); background: white; border-bottom-color: var(--primary); }
        .calc-body { padding: 30px; }
        .calc-item-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 10px; }
        .calc-item-header h3 { font-size: 1.25rem; font-weight: 700; color: var(--dark); }
        .material-badge { background: #e2e8f0; color: #4a5568; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .formula-box { background: #f1f5f9; border-left: 4px solid var(--primary); padding: 12px 18px; font-family: 'Courier New', monospace; font-size: 0.9rem; color: #334155; border-radius: 0 6px 6px 0; margin-bottom: 25px; overflow-x: auto; }
        .calc-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; align-items: flex-end; margin-bottom: 25px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group label { font-weight: 600; font-size: 0.85rem; color: var(--text-main); }
        .form-control { padding: 11px 14px; border: 2px solid var(--border-color); border-radius: 6px; font-family: 'Inter', sans-serif; font-size: 0.95rem; outline: none; transition: var(--transition); background: white; width: 100%; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(214, 25, 53, 0.1); }
        .btn-calcular { background: var(--primary); color: white; border: none; padding: 12px 24px; border-radius: 6px; font-weight: 700; cursor: pointer; transition: var(--transition); height: 46px; text-transform: uppercase; font-size: 0.9rem; white-space: nowrap; width: 100%; }
        .btn-calcular:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 4px 15px rgba(214, 25, 53, 0.3); }
        .calc-result-box { background: #f0fdf4; border: 2px solid #bbf7d0; border-radius: 8px; padding: 20px 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }
        .result-info span { font-size: 1rem; color: var(--text-main); font-weight: 500; }
        .result-value { font-size: 2rem; font-weight: 800; color: #166534; font-family: 'Oswald', sans-serif; }
        .result-unit { font-size: 1rem; font-weight: 600; color: #166534; }
        .result-dens { font-size: 0.85rem; color: var(--text-muted); }
        .calc-footer { text-align: center; margin-top: 25px; padding-top: 15px; border-top: 1px solid var(--border-color); font-size: 0.85rem; color: var(--text-muted); }
        @media (max-width: 768px) { .calc-header .logo-area h1 { font-size: 1.2rem; } .calc-top-header .title-area h2 { font-size: 1.3rem; } .calc-form-grid { grid-template-columns: 1fr; } .calc-tabs { padding: 0 10px; } .calc-tab { padding: 10px 14px; font-size: 0.8rem; } .calc-body { padding: 20px; } .result-value { font-size: 1.5rem; } }
    </style>
</head>
<body>
<header class="calc-header">
    <div class="container">
        <div class="logo-area">
            <img src="/assets/images/ferrobrasmetais_logo.webp" alt="Ferrobras Metais">
            <h1>Ferrobras <span>Metais</span></h1>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="../index.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Voltar ao Site</a>
            <a href="https://wa.me/5554992097850?text=Olá,%20vim%20pela%20calculadora%20e%20gostaria%20de%20um%20orçamento." target="_blank" class="btn-whatsapp-calc"><i class="fa-brands fa-whatsapp"></i> Orçar</a>
        </div>
    </div>
</header>
<div class="calc-container">
    <div class="calc-wrapper">
        <div class="calc-top-header">
            <div class="title-area">
                <i class="fa-solid fa-calculator"></i>
                <div>
                    <h2>Calculadora de Peso Teórico</h2>
                    <p>Calcule o peso estimado do seu material</p>
                </div>
            </div>
            <div class="badge-brand">Ferrobras Metais</div>
        </div>
        <div class="calc-tabs">
            <button class="calc-tab active" data-tab="redondo">Redondo</button>
            <button class="calc-tab" data-tab="chapa">Chapa / Bloco</button>
            <button class="calc-tab" data-tab="sextavado">Sextavado</button>
            <button class="calc-tab" data-tab="tuboRedondo">Tubo Redondo</button>
            <button class="calc-tab" data-tab="tuboQuadrado">Tubo Quadrado</button>
            <button class="calc-tab" data-tab="tuboRetangular">Tubo Retangular</button>
        </div>
        <div class="calc-body">
            <div class="calc-item-header">
                <h3 id="calcTitle">Barra Redonda (maciça)</h3>
                <span class="material-badge" id="materialBadge">Aço Carbono</span>
            </div>
            <div class="formula-box" id="formulaDisplay">Peso (kg) = (π × (D/2)² × C) × ρ / 1000</div>
            <div class="calc-form-grid" id="inputsContainer"></div>
            <button class="btn-calcular" onclick="calcularPeso()"><i class="fa-solid fa-calculator"></i> Calcular</button>
            <div class="calc-result-box" style="margin-top: 20px;">
                <div class="result-info">
                    <span>Peso total:</span>
                    <div>
                        <span class="result-value" id="resultadoPeso">0,000</span>
                        <span class="result-unit">kg</span>
                    </div>
                    <div class="result-dens" id="densInfo">ρ = 7,85 g/cm³</div>
                </div>
                <a id="btnOrcamento" href="#" target="_blank" class="btn-whatsapp-calc" style="background:#25d366;">
                    <i class="fa-brands fa-whatsapp"></i> Orçar no WhatsApp
                </a>
            </div>
            <div class="calc-footer">
                Densidades: Aço Carbono 7,85 | Inox 304 7,90 | Inox 316 7,95 | Alumínio 2,70 | Cobre 8,90 | Bronze 8,40 | Latão 8,60
            </div>
        </div>
    </div>
</div>
<script>
const densidades = {
    "Aço Carbono": 0.00785,
    "Aço Inoxidável": 0.00793,
    "Alumínio": 0.00270,
    "Cobre / Bronze": 0.00896,
    "Latão": 0.00855,
    "Bronze": 0.00880,
    "Nylon": 0.00115,
    "Celeron": 0.00140
};
let currentTab = 'redondo';
const tabConfig = {
    'redondo': {
        title: 'Barra Redonda (maciça)',
        formula: 'Peso (kg) = (π × (D/2)² × C) × ρ / 1000',
        fields: [{ id: 'D', label: 'Diâmetro (mm)', value: 25, step: 'any' }, { id: 'C', label: 'Comprimento (mm)', value: 1000, step: 'any' }, { id: 'Q', label: 'Quantidade (UN)', value: 1, step: 1 }]
    },
    'chapa': {
        title: 'Chapa / Bloco',
        formula: 'Peso (kg) = (E × L × C) × ρ / 1000',
        fields: [{ id: 'E', label: 'Espessura (mm)', value: 10, step: 'any' }, { id: 'L', label: 'Largura (mm)', value: 200, step: 'any' }, { id: 'C', label: 'Comprimento (mm)', value: 500, step: 'any' }, { id: 'Q', label: 'Quantidade (UN)', value: 1, step: 1 }]
    },
    'sextavado': {
        title: 'Barra Sextavada',
        formula: 'Peso (kg) = (1.103 × S² × C) × ρ / 1000',
        fields: [{ id: 'S', label: 'Chave (mm)', value: 30, step: 'any' }, { id: 'C', label: 'Comprimento (mm)', value: 1000, step: 'any' }, { id: 'Q', label: 'Quantidade (UN)', value: 1, step: 1 }]
    },
    'tuboRedondo': {
        title: 'Tubo Redondo (oco)',
        formula: 'Peso (kg) = (π × (D - e) × e × C) × ρ / 1000',
        fields: [{ id: 'D', label: 'Diâmetro Ext. (mm)', value: 60, step: 'any' }, { id: 'E', label: 'Parede (mm)', value: 3, step: 'any' }, { id: 'C', label: 'Comprimento (mm)', value: 1000, step: 'any' }, { id: 'Q', label: 'Quantidade (UN)', value: 1, step: 1 }]
    },
    'tuboQuadrado': {
        title: 'Tubo Quadrado (oco)',
        formula: 'Peso (kg) = (4 × e × (A - e) × C) × ρ / 1000',
        fields: [{ id: 'A', label: 'Lado (mm)', value: 50, step: 'any' }, { id: 'E', label: 'Parede (mm)', value: 2.5, step: 'any' }, { id: 'C', label: 'Comprimento (mm)', value: 1000, step: 'any' }, { id: 'Q', label: 'Quantidade (UN)', value: 1, step: 1 }]
    },
    'tuboRetangular': {
        title: 'Tubo Retangular (oco)',
        formula: 'Peso (kg) = (2 × e × (A + B - 2e) × C) × ρ / 1000',
        fields: [{ id: 'A', label: 'Lado A (mm)', value: 80, step: 'any' }, { id: 'B', label: 'Lado B (mm)', value: 40, step: 'any' }, { id: 'E', label: 'Parede (mm)', value: 3, step: 'any' }, { id: 'C', label: 'Comprimento (mm)', value: 1000, step: 'any' }, { id: 'Q', label: 'Quantidade (UN)', value: 1, step: 1 }]
    }
};
function renderInputs(tab) {
    const config = tabConfig[tab];
    if (!config) return;
    let html = '<div class="form-group"><label>Material</label><select id="matSelect" class="form-control" onchange="calcularPeso()">' +
        Object.keys(densidades).map(m => '<option value="' + m + '">' + m + '</option>').join('') +
        '</select></div>';
    config.fields.forEach(field => {
        const fieldId = 'val_' + field.id;
        html += '<div class="form-group"><label>' + field.label + '</label><input type="number" id="' + fieldId + '" class="form-control" value="' + field.value + '" step="' + field.step + '" oninput="calcularPeso()"></div>';
    });
    document.getElementById('inputsContainer').innerHTML = html;
    document.getElementById('calcTitle').textContent = config.title;
    document.getElementById('formulaDisplay').textContent = config.formula;
}
function switchTab(tab) {
    currentTab = tab;
    document.querySelectorAll('.calc-tab').forEach(t => t.classList.remove('active'));
    document.querySelector('[data-tab="' + tab + '"]').classList.add('active');
    renderInputs(tab);
    calcularPeso();
}
function calcularPeso() {
    const matName = document.getElementById('matSelect')?.value || 'Aço Carbono';
    const rho = (densidades[matName] || 0.00785) * 1000;
    document.getElementById('materialBadge').textContent = matName;
    document.getElementById('densInfo').textContent = 'ρ = ' + (rho / 1000).toFixed(2).replace('.', ',') + ' g/cm³';
    const config = tabConfig[currentTab];
    if (!config) return;
    let pesoUnitario = 0;
    const q = parseFloat(document.getElementById('val_Q')?.value) || 1;
    const getVal = (id) => parseFloat(document.getElementById('val_' + id)?.value) || 0;
    if (currentTab === 'redondo') {
        const D = getVal('D'), C = getVal('C');
        pesoUnitario = (Math.PI * Math.pow(D / 2, 2) * C * rho) / 1000000;
    } else if (currentTab === 'chapa') {
        const E = getVal('E'), L = getVal('L'), C = getVal('C');
        pesoUnitario = (E * L * C * rho) / 1000000;
    } else if (currentTab === 'sextavado') {
        const S = getVal('S'), C = getVal('C');
        pesoUnitario = (1.103 * Math.pow(S, 2) * C * rho) / 1000000;
    } else if (currentTab === 'tuboRedondo') {
        const D = getVal('D'), E = getVal('E'), C = getVal('C');
        pesoUnitario = (Math.PI * (D - E) * E * C * rho) / 1000000;
    } else if (currentTab === 'tuboQuadrado') {
        const A = getVal('A'), E = getVal('E'), C = getVal('C');
        pesoUnitario = (4 * E * (A - E) * C * rho) / 1000000;
    } else if (currentTab === 'tuboRetangular') {
        const A = getVal('A'), B = getVal('B'), E = getVal('E'), C = getVal('C');
        pesoUnitario = (2 * E * (A + B - (2 * E)) * C * rho) / 1000000;
    }
    const pesoTotal = pesoUnitario * q;
    const pesoFormatado = pesoTotal > 0 ? pesoTotal.toFixed(3).replace('.', ',') : '0,000';
    document.getElementById('resultadoPeso').textContent = pesoFormatado;
    const titulo = document.getElementById('calcTitle').textContent;
    const msg = encodeURIComponent('Olá, calculei um item (' + titulo + ' - ' + matName + ') com peso total estimado de ' + pesoFormatado + ' kg. Gostaria de um orçamento.');
    document.getElementById('btnOrcamento').href = 'https://wa.me/5554992097850?text=' + msg;
}
document.querySelectorAll('.calc-tab').forEach(btn => {
    btn.addEventListener('click', function() {
        switchTab(this.dataset.tab);
    });
});
window.onload = function() {
    renderInputs('redondo');
    calcularPeso();
};
</script>
</body>
</html>
