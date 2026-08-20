<?php
// ============================================
// INDEX.PHP - FERROBRAS METAIS
// VERSÃO ESTÁVEL - 100% FUNCIONANDO
// ============================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

// CONEXÃO COM O BANCO DE DADOS - HOSTINGER
$host = 'localhost';
$dbname = 'u119221664_ferrobras_site';
$user = 'u119221664_ferrobras_user';
$pass = 'Ferrobras@2026';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT * FROM produtos WHERE ativo = 1 ORDER BY id ASC");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $produtos = [];
    $erro_conexao = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Tubos, Metais e Retalhos em Caxias do Sul e Serra Gaúcha | Ferrobras Metais</title>
    <meta name="description" content="Tubos fora de padrão, metais em pedaços, inox, alumínio, nylon e bronze em Caxias do Sul. Atendimento ágil para toda a Serra Gaúcha.">
    <meta name="keywords" content="tubos, metais, retalhos, inox, aluminio, nylon, bronze, Caxias do Sul, Serra Gaucha, acos, chapas, vigas, perfis, barras, ferragens, serralheria, usinagem, manutencao industrial">
    <meta name="keywords" content="tubos, metais, retalhos, inox, aluminio, nylon, bronze, Caxias do Sul, Serra Gaucha, acos, chapas, vigas, perfis, barras, ferragens, serralheria, usinagem, manutencao industrial">
    <link rel="canonical" href="https://ferrobrasmetais.com.br/">
    
    <meta property="og:locale" content="pt_BR">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Tubos, Metais e Retalhos em Caxias do Sul | Ferrobras Metais">
    <meta property="og:description" content="Encontre tubos, metais em pedaços, inox, alumínio e plásticos de engenharia em Caxias do Sul.">
    <meta property="og:url" content="https://ferrobrasmetais.com.br/">
    <meta property="og:site_name" content="Ferrobras Metais">
    <meta property="og:image" content="https://ferrobrasmetais.com.br/assets/images/logo/ferrobrasmetais_logo.webp">

    <link rel="icon" type="image/png" href="favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="site.webmanifest">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Oswald:wght@500;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --primary: #d61935; --primary-dark: #b01229; --dark: #121212; --gray-light: #f4f6f8; --text-main: #333333; --text-muted: #666666; --border-color: #e0e0e0; --transition: all 0.3s ease; --font-base: 16px; }
        * { box-sizing: border-box; margin: 0; padding: 0; max-width: 100%; }
        html { font-size: var(--font-base); scroll-behavior: smooth; -webkit-text-size-adjust: 100%; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: var(--text-main); background: #ffffff; line-height: 1.6; overflow-x: hidden; }
        img { max-width: 100%; height: auto; display: block; }

        .top-bar { background: var(--dark); color: #fff; font-size: clamp(0.65rem, 1.5vw, 0.85rem); padding: 6px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .top-bar .container { display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto; padding: 0 15px; flex-wrap: wrap; gap: 5px; }
        .top-bar .info { display: flex; flex-wrap: wrap; gap: 8px 15px; }
        .top-bar .info span { font-size: clamp(0.6rem, 1.2vw, 0.8rem); white-space: nowrap; }
        .top-bar i { color: var(--primary); margin-right: 4px; width: 14px; }

        header { background: rgba(255,255,255,0.97); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); box-shadow: 0 2px 15px rgba(0,0,0,0.06); position: sticky; top: 0; z-index: 1000; border-bottom: 1px solid var(--border-color); }
        .navbar { display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto; padding: 8px 15px; gap: 8px; flex-wrap: wrap; }
        .logo-area { flex-shrink: 0; }
        .brand-logo { height: clamp(40px, 8vw, 105px); max-height: 115px; width: auto; object-fit: contain; transition: var(--transition); }
        .brand-logo:hover { transform: scale(1.02); }
        .nav-links { display: flex; list-style: none; gap: 4px; align-items: center; flex-wrap: wrap; justify-content: center; }
        .nav-links a { text-decoration: none; color: var(--dark); font-weight: 500; font-size: clamp(0.65rem, 1.4vw, 0.95rem); padding: 6px 12px; border-radius: 30px; transition: var(--transition); white-space: nowrap; }
        .nav-links a:hover, .nav-links a.active { color: var(--primary); background: rgba(214,25,53,0.06); }
        .nav-links a.btn-calculadora { background: var(--primary); color: #fff; font-size: clamp(0.6rem, 1.2vw, 0.85rem); padding: 4px 12px; }
        .nav-links a.btn-calculadora:hover { background: var(--primary-dark); color: #fff; }

        .btn-whatsapp-header { background: #25d366; color: #fff; padding: 6px 14px; border-radius: 30px; text-decoration: none; font-weight: 600; font-size: clamp(0.6rem, 1.2vw, 0.9rem); display: flex; align-items: center; gap: 6px; transition: var(--transition); box-shadow: 0 2px 8px rgba(37,211,102,0.2); flex-shrink: 0; }
        .btn-whatsapp-header:hover { background: #20ba5a; transform: translateY(-2px); }
        .btn-whatsapp-header i { font-size: clamp(0.8rem, 1.5vw, 1.1rem); }

        .hero { background: linear-gradient(rgba(10,10,10,0.7), rgba(10,10,10,0.7)), url('assets/images/hero/tubos.webp'); background-size: cover; background-position: center; color: #fff; padding: clamp(40px, 10vw, 110px) 15px; text-align: center; min-height: clamp(300px, 50vh, 600px); display: flex; align-items: center; }
        .hero-container { max-width: 800px; margin: 0 auto; width: 100%; }
        .hero h2 { font-family: 'Oswald', sans-serif; font-size: clamp(1.4rem, 5vw, 3rem); margin-bottom: 15px; text-transform: uppercase; letter-spacing: 1px; line-height: 1.2; text-shadow: 0 2px 8px rgba(0,0,0,0.85); }
        .hero h2 span { color: var(--primary); }
        .hero p { font-size: clamp(0.85rem, 2vw, 1.15rem); color: #f8f9fa; margin-bottom: 25px; text-shadow: 0 1px 6px rgba(0,0,0,0.85); max-width: 650px; margin: 0 auto; }
        .hero-buttons { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }
        .btn-primary { background: var(--primary); color: #fff; padding: clamp(10px, 1.8vw, 14px) clamp(20px, 3vw, 28px); border-radius: 4px; text-decoration: none; font-weight: 700; text-transform: uppercase; font-size: clamp(0.7rem, 1.2vw, 0.9rem); transition: var(--transition); box-shadow: 0 4px 10px rgba(0,0,0,0.3); text-align: center; min-width: 140px; }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-2px); }
        .btn-outline { background: rgba(0,0,0,0.4); border: 2px solid #fff; color: #fff; padding: clamp(10px, 1.8vw, 14px) clamp(20px, 3vw, 28px); border-radius: 4px; text-decoration: none; font-weight: 700; text-transform: uppercase; font-size: clamp(0.7rem, 1.2vw, 0.9rem); transition: var(--transition); text-align: center; min-width: 140px; }
        .btn-outline:hover { background: #fff; color: var(--dark); }

        .features-bar { background: var(--primary); color: #fff; padding: clamp(20px, 4vw, 30px) 15px; }
        .features-grid { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: clamp(15px, 3vw, 30px); text-align: center; }
        .feature-item i { font-size: clamp(1.8rem, 4vw, 2.5rem); margin-bottom: 8px; }
        .feature-item h3 { font-size: clamp(0.85rem, 1.5vw, 1.1rem); font-weight: 700; margin-bottom: 4px; text-transform: uppercase; }
        .feature-item p { font-size: clamp(0.75rem, 1.2vw, 0.9rem); color: #fce8e6; }

        .products-section { padding: clamp(40px, 6vw, 80px) 15px; background: var(--gray-light); }
        .section-title { text-align: center; margin-bottom: clamp(30px, 5vw, 50px); }
        .section-title h2 { font-family: 'Oswald', sans-serif; font-size: clamp(1.6rem, 4vw, 2.5rem); text-transform: uppercase; color: var(--dark); margin-bottom: 8px; }
        .section-title p { color: var(--text-muted); font-size: clamp(0.85rem, 1.5vw, 1.05rem); }

        .products-grid { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: clamp(15px, 2.5vw, 25px); }
        .product-card { background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.06); transition: var(--transition); border-top: 4px solid var(--primary); display: flex; flex-direction: column; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 6px 20px rgba(0,0,0,0.1); }
        .product-image { width: 100%; height: clamp(150px, 25vw, 200px); object-fit: cover; background: #f0f2f5; }
        .product-image-placeholder { width: 100%; height: clamp(150px, 25vw, 200px); background: #f0f2f5; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #bbb; font-size: clamp(2rem, 4vw, 3rem); }
        .product-image-placeholder span { font-size: 12px; color: #999; margin-top: 5px; }
        .product-content { padding: clamp(15px, 2vw, 25px); flex: 1; display: flex; flex-direction: column; }
        .product-content h3 { font-size: clamp(1rem, 1.8vw, 1.3rem); font-weight: 700; margin-bottom: 8px; color: var(--dark); }
        .product-content p { color: var(--text-muted); font-size: clamp(0.8rem, 1.2vw, 0.95rem); margin-bottom: 12px; flex: 1; }
        .product-price { font-weight: 700; color: var(--primary); margin: 8px 0 12px; font-size: clamp(0.9rem, 1.4vw, 1.05rem); }
        .product-link { color: var(--primary); text-decoration: none; font-weight: 600; font-size: clamp(0.8rem, 1.2vw, 0.95rem); display: inline-flex; align-items: center; gap: 6px; transition: var(--transition); align-self: flex-start; margin-top: auto; }
        .product-link:hover { color: var(--primary-dark); gap: 10px; }

        .about-section { padding: clamp(40px, 6vw, 80px) 15px; background: #fff; }
        .about-container { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: clamp(25px, 5vw, 50px); align-items: center; }
        .about-text h2 { font-family: 'Oswald', sans-serif; font-size: clamp(1.6rem, 3.5vw, 2.3rem); text-transform: uppercase; margin-bottom: 15px; color: var(--dark); }
        .about-text p { color: var(--text-muted); margin-bottom: 12px; font-size: clamp(0.9rem, 1.2vw, 1.02rem); }
        .about-list { list-style: none; margin-bottom: 25px; }
        .about-list li { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; font-weight: 600; font-size: clamp(0.85rem, 1.2vw, 1rem); color: var(--dark); }
        .about-list i { color: var(--primary); width: 20px; }
        .about-image { background: url('assets/images/sobre/serra.webp'); background-size: cover; background-position: center; height: clamp(200px, 35vw, 400px); border-radius: 8px; box-shadow: 0 6px 25px rgba(0,0,0,0.12); }

        .cta-section { background: var(--dark); color: #fff; padding: clamp(35px, 6vw, 60px) 15px; text-align: center; }
        .cta-container { max-width: 800px; margin: 0 auto; }
        .cta-section h2 { font-family: 'Oswald', sans-serif; font-size: clamp(1.4rem, 3.5vw, 2.5rem); text-transform: uppercase; margin-bottom: 12px; }
        .cta-section p { color: #bbbbbb; margin-bottom: 25px; font-size: clamp(0.9rem, 1.5vw, 1.1rem); }

        footer { background: #0b0b0b; color: #888; padding: clamp(30px, 5vw, 50px) 15px 15px; }
        .footer-container { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: clamp(20px, 3vw, 40px); margin-bottom: 30px; }
        .footer-col h4 { color: #fff; font-size: clamp(0.9rem, 1.5vw, 1.1rem); margin-bottom: 15px; text-transform: uppercase; font-family: 'Oswald', sans-serif; }
        .footer-col p, .footer-col ul { font-size: clamp(0.8rem, 1.2vw, 0.95rem); list-style: none; }
        .footer-col ul li { margin-bottom: 8px; }
        .footer-col ul a { color: #888; text-decoration: none; transition: var(--transition); }
        .footer-col ul a:hover { color: var(--primary); }
        .footer-col p i { width: 20px; color: var(--primary); }
        .footer-bottom { max-width: 1200px; margin: 0 auto; border-top: 1px solid #222; padding-top: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; font-size: clamp(0.7rem, 1vw, 0.85rem); color: #666; gap: 10px; }
        .footer-bottom-left { display: flex; flex-direction: column; gap: 4px; }
        .footer-bottom-left a { color: #888; text-decoration: none; transition: var(--transition); }
        .footer-bottom-left a:hover { color: var(--primary); }
        .lgpd-links a { color: #888; text-decoration: none; margin-left: 12px; transition: var(--transition); font-size: clamp(0.7rem, 1vw, 0.85rem); }
        .lgpd-links a:hover { color: var(--primary); }

        .whatsapp-float { position: fixed; bottom: 25px; right: 25px; width: 60px; height: 60px; background: #25d366; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; box-shadow: 0 4px 15px rgba(37,211,102,0.4); z-index: 999; text-decoration: none; transition: all 0.3s ease; animation: pulse-wpp 2s infinite; }
        .whatsapp-float:hover { transform: scale(1.1); box-shadow: 0 6px 25px rgba(37,211,102,0.6); }
        .whatsapp-float i { color: #fff; }
        @keyframes pulse-wpp { 0% { box-shadow: 0 0 0 0 rgba(37,211,102,0.4); } 70% { box-shadow: 0 0 0 20px rgba(37,211,102,0); } 100% { box-shadow: 0 0 0 0 rgba(37,211,102,0); } }

        .btn-topo { position: fixed; bottom: 100px; right: 25px; width: 50px; height: 50px; background: var(--primary); color: #fff; border: none; border-radius: 50%; font-size: 22px; cursor: pointer; box-shadow: 0 4px 15px rgba(214,25,53,0.3); z-index: 998; transition: all 0.3s ease; opacity: 0; visibility: hidden; transform: translateY(20px); }
        .btn-topo:hover { background: var(--primary-dark); transform: translateY(-3px); box-shadow: 0 6px 20px rgba(214,25,53,0.4); }
        .btn-topo.visible { opacity: 1; visibility: visible; transform: translateY(0); }

        #progressBar { position: fixed; top: 0; left: 0; width: 0%; height: 4px; background: linear-gradient(90deg, #d61935, #ff6b35); z-index: 9999; transition: width 0.1s ease; box-shadow: 0 2px 10px rgba(214,25,53,0.3); }
        #progressBar::after { content: ''; position: absolute; top: 0; right: 0; width: 100px; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4)); animation: shimmer 2s infinite; }
        @keyframes shimmer { 0% { transform: translateX(-100px); } 100% { transform: translateX(100px); } }

        .cookie-banner { position: fixed; bottom: 0; left: 0; width: 100%; background: var(--dark); color: #fff; padding: 12px 15px; display: flex; justify-content: space-between; align-items: center; z-index: 1500; border-top: 3px solid var(--primary); flex-wrap: wrap; gap: 10px; }
        .cookie-banner p { font-size: clamp(0.75rem, 1.2vw, 0.9rem); color: #cccccc; max-width: 900px; }
        .cookie-banner button { background: var(--primary); color: #fff; border: none; padding: 6px 18px; font-weight: 600; border-radius: 4px; cursor: pointer; font-size: clamp(0.75rem, 1vw, 0.9rem); flex-shrink: 0; }

        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 2000; justify-content: center; align-items: center; padding: 15px; }
        .modal-content { background: #fff; color: var(--text-main); max-width: 600px; width: 100%; padding: clamp(20px, 4vw, 30px); border-radius: 8px; max-height: 80vh; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .modal-content h3 { font-family: 'Oswald', sans-serif; font-size: clamp(1.3rem, 2.5vw, 1.8rem); color: var(--dark); margin-bottom: 12px; text-transform: uppercase; }
        .modal-content p { margin-bottom: 10px; font-size: clamp(0.85rem, 1.2vw, 0.95rem); color: var(--text-muted); }
        .close-modal { background: var(--primary); color: #fff; border: none; padding: 8px 20px; font-weight: 600; border-radius: 4px; cursor: pointer; margin-top: 12px; text-transform: uppercase; font-size: clamp(0.8rem, 1vw, 0.9rem); float: right; }

        @media (max-width: 992px) { .about-container { grid-template-columns: 1fr; gap: 25px; } .about-image { height: 250px; } }
        @media (max-width: 768px) { 
            .top-bar .container { flex-direction: column; text-align: center; gap: 4px; }
            .top-bar .info { justify-content: center; }
            .navbar { flex-direction: column; align-items: center; padding: 6px 10px; gap: 6px; }
            .brand-logo { height: 50px; }
            .nav-links a { font-size: 0.7rem; padding: 4px 10px; }
            .hero { padding: 35px 12px; min-height: 280px; }
            .hero h2 { font-size: 1.6rem; }
            .hero-buttons { flex-direction: column; align-items: center; gap: 8px; }
            .btn-primary, .btn-outline { width: 100%; max-width: 250px; padding: 10px 16px; font-size: 0.75rem; min-width: auto; }
            .products-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
            .product-image { height: 120px; }
            .product-content { padding: 12px; }
            .product-content h3 { font-size: 0.9rem; }
            .about-image { height: 180px; }
            .footer-container { grid-template-columns: 1fr 1fr; gap: 15px; }
            .whatsapp-float { width: 55px; height: 55px; font-size: 28px; bottom: 20px; right: 20px; }
            .btn-topo { width: 45px; height: 45px; font-size: 18px; bottom: 90px; right: 20px; }
            #progressBar { height: 3px; }
        }
        @media (max-width: 480px) { 
            .hero h2 { font-size: 1.3rem; }
            .products-grid { grid-template-columns: 1fr; gap: 15px; }
            .features-grid { grid-template-columns: 1fr; gap: 10px; }
            .footer-container { grid-template-columns: 1fr; text-align: center; }
            .brand-logo { height: 42px; }
            .whatsapp-float { width: 50px; height: 50px; font-size: 24px; bottom: 15px; right: 15px; }
            .btn-topo { width: 40px; height: 40px; font-size: 16px; bottom: 80px; right: 15px; }
            .product-image { height: 160px; }
            .about-image { height: 150px; }
        }
        @media (max-width: 360px) { .hero h2 { font-size: 1.1rem; } .brand-logo { height: 36px; } }
    </style>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "Ferrobras Metais",
  "image": "https://ferrobrasmetais.com.br/assets/images/logo/ferrobrasmetais_logo.webp",
  "@id": "https://ferrobrasmetais.com.br/",
  "url": "https://ferrobrasmetais.com.br/",
  "telephone": "+555420240129",
  "priceRange": "$$",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "Caxias do Sul",
    "addressRegion": "RS",
    "addressCountry": "BR"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": -29.1678,
    "longitude": -51.1794
  },
  "openingHours": "Mo-Fr 07:30-12:00,13:00-17:30"
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "Ferrobras Metais",
  "image": "https://ferrobrasmetais.com.br/assets/images/logo/ferrobrasmetais_logo.webp",
  "@id": "https://ferrobrasmetais.com.br/",
  "url": "https://ferrobrasmetais.com.br/",
  "telephone": "+555420240129",
  "priceRange": "$$",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "Caxias do Sul",
    "addressRegion": "RS",
    "addressCountry": "BR"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": -29.1678,
    "longitude": -51.1794
  },
  "openingHours": "Mo-Fr 07:30-12:00,13:00-17:30"
}
</script>
</head>
<body>

    <!-- TOP BAR -->
    <div class="top-bar">
        <div class="container">
            <div class="info">
                <span><i class="fa-solid fa-phone"></i> (54) 2024-0129</span>
                <span><i class="fa-solid fa-envelope"></i> comercial@ferrobrasmetais.com.br</span>
            </div>
        </div>
    </div>

    <!-- HEADER -->
    <header>
        <div class="navbar">
            <div class="logo-area">
                <a href="#inicio" style="display:flex;align-items:center;text-decoration:none;">
                    <img loading="lazy" src="/assets/images/logo/ferrobrasmetais_logo.webp" alt="Ferrobras Metais" class="brand-logo" width="200" height="105" loading="lazy">
                </a>
            </div>
            <ul class="nav-links">
                <li><a href="#inicio">Início</a></li>
                <li><a href="#produtos">Produtos</a></li>
                <li><a href="#sobre">A Empresa</a></li>
                <li><a href="#contato">Contato</a></li>
                <li><a href="calculadora/" class="btn-calculadora" target="_blank"><i class="fa-solid fa-calculator"></i> Calcular peso</a></li>
            </ul>
            <a href="https://wa.me/5554992097850?text=Olá,%20vou%20mandar%20a%20foto%20ou%20a%20medida%20do%20material." target="_blank" rel="noopener" class="btn-whatsapp-header">
                <i class="fa-brands fa-whatsapp"></i> Orçamento Rápido
            </a>
        </div>
    </header>

    <!-- HERO -->
    <section class="hero" id="inicio">
        <div class="hero-container">
            <h2>Tubos, Metais em Pedaços e Materiais Variados em <span>Caxias do Sul</span></h2>
            <p>Temos tubos fora de padrão, pedaços de metal, inox, alumínio, nylon, bronze e outros materiais para serralheria, manutenção, usinagem e pequenos projetos.</p>
            <div class="hero-buttons">
                <a href="#produtos" class="btn-primary">Ver Materiais</a>
                <a href="https://wa.me/5554992097850?text=Olá,%20vou%20mandar%20a%20foto%20ou%20a%20medida%20do%20material." target="_blank" rel="noopener" class="btn-outline">Enviar Foto no WhatsApp</a>
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <div class="features-bar">
        <div class="features-grid">
            <div class="feature-item"><i class="fa-solid fa-boxes-stacked"></i><h3>Material Variado</h3><p>Tubos, barras, chapas e peças avulsas.</p></div>
            <div class="feature-item"><i class="fa-solid fa-scissors"></i><h3>Pedaços e Cortes</h3><p>Ideal para manutenção e pequenos serviços.</p></div>
            <div class="feature-item"><i class="fa-solid fa-comments"></i><h3>Atendimento Direto</h3><p>Mande foto, medida ou desenho no WhatsApp.</p></div>
        </div>
    </div>

    <!-- PRODUTOS -->
    <section class="products-section" id="produtos">
        <div class="section-title">
            <h2>O que você encontra aqui</h2>
            <p>Materiais variados conforme disponibilidade em estoque</p>
        </div>
        <div class="products-grid">
            <?php if(!empty($produtos)): ?>
                <?php foreach($produtos as $produto): ?>
                    <?php 
                    // ============================================================
                    // VERIFICAÇÃO DE IMAGEM - VERSÃO CORRETA
                    // ============================================================
                    $img_encontrada = false;
                    $img_final = '';
                    $nome_imagem = $produto['imagem'];
                    
                    if(!empty($nome_imagem)) {
                        // Verificar se o arquivo existe diretamente
                        $caminho_direto = __DIR__ . '/' . $nome_imagem;
                        if(file_exists($caminho_direto)) {
                            $img_encontrada = true;
                            $img_final = '/' . $nome_imagem;
                        } else {
                            // Tentar com barra no início
                            $caminho_com_barra = __DIR__ . '/' . ltrim($nome_imagem, '/');
                            if(file_exists($caminho_com_barra)) {
                                $img_encontrada = true;
                                $img_final = '/' . ltrim($nome_imagem, '/');
                            }
                        }
                        
                        // Se não encontrou, procurar nas pastas
                        if(!$img_encontrada) {
                            $pastas = [
                                'assets/images/produtos/',
                                'assets/images/',
                                'imagens/produtos/',
                                'img/produtos/',
                                'img/'
                            ];
                            $nome_arquivo = basename($nome_imagem);
                            
                            foreach($pastas as $pasta) {
                                $caminho = $pasta . $nome_arquivo;
                                if(file_exists(__DIR__ . '/' . $caminho)) {
                                    $img_encontrada = true;
                                    $img_final = '/' . $caminho;
                                    break;
                                }
                            }
                        }
                    }
                    ?>
                    <div class="product-card">
                        <?php if($img_encontrada): ?>
                            <img loading="lazy" src="<?php echo $img_final; ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="product-image" loading="lazy" width="400" height="200">
                        <?php else: ?>
                            <div class="product-image-placeholder">
                                <i class="fa-solid fa-image"></i>
                                <span>Sem imagem</span>
                            </div>
                        <?php endif; ?>
                        <div class="product-content">
                            <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
                            <p><?php echo htmlspecialchars($produto['descricao']); ?></p>
                            <a href="https://wa.me/5554992097850?text=Olá,%20gostaria%20de%20consultar%20sobre%20<?php echo urlencode($produto['nome']); ?>" target="_blank" rel="noopener" class="product-link">
                                Solicitar Cotação <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column:1/-1;text-align:center;padding:40px 20px;color:#999;">
                    <i class="fa-solid fa-box-open" style="font-size:3rem;display:block;margin-bottom:15px;"></i>
                    <p style="font-size:1.1rem;">Nenhum produto cadastrado no momento.</p>
                    <?php if(isset($erro_conexao)): ?>
                        <p style="color:#d61935;font-size:0.9rem;margin-top:10px;">⚠️ Erro de conexão: <?php echo htmlspecialchars($erro_conexao); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- SOBRE -->
    <section class="about-section" id="sobre">
        <div class="about-container">
            <div class="about-text">
                <h2>Quem Somos</h2>
                <p>Com mais de 15 anos de atuação no setor industrial, a Ferrobras está retomando suas atividades com um novo foco. Oferecemos tubos fora de padrão, metais em pedaços, inox, alumínio, nylon, bronze e outros materiais para serralheria, manutenção e usinagem em Caxias do Sul e Serra Gaúcha.</p>
                <ul class="about-list">
                    <li><i class="fa-solid fa-check"></i> Materiais variados</li>
                    <li><i class="fa-solid fa-check"></i> Tubos fora de padrão</li>
                    <li><i class="fa-solid fa-check"></i> Pedaços e sobras úteis</li>
                    <li><i class="fa-solid fa-check"></i> Atendimento rápido pelo WhatsApp</li>
                    <li><i class="fa-solid fa-check"></i> Cobertura em toda a Serra Gaúcha</li>
                </ul>
                <p style="margin-top:15px;"><i class="fa-solid fa-location-dot" style="color:var(--primary);"></i> <strong>R. José Michelon, 273 - Nossa Sra. de Fátima, Caxias do Sul - RS, 95041-310</strong></p>
                <a href="https://wa.me/5554992097850?text=Olá,%20vim%20pelo%20site%20e%20quero%20falar%20com%20um%20especialista." target="_blank" rel="noopener" class="btn-primary" style="background-color:#25d366;display:inline-flex;align-items:center;gap:8px;margin-top:10px;"><i class="fa-brands fa-whatsapp"></i> Chamar no WhatsApp</a>
            </div>
            <div class="about-image" role="img" aria-label="Imagem da Serra Gaúcha"></div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section" id="contato">
        <div class="cta-container">
            <h2>Mande a foto ou a medida do material</h2>
            <p>Respondemos pelo WhatsApp e informamos se temos a peça disponível, o tamanho e o valor.<br><br><strong>Sem burocracia.</strong></p>
            <a href="https://wa.me/5554992097850?text=Olá,%20vou%20mandar%20a%20foto%20ou%20a%20medida%20do%20material." target="_blank" rel="noopener" class="btn-primary" style="background-color:#25d366;display:inline-flex;align-items:center;gap:8px;"><i class="fa-brands fa-whatsapp"></i> Chamar no WhatsApp</a>
        </div>
    </section>

    <!-- FOOTER -->
<!-- ============================================================
     ONDE ESTAMOS 
     ============================================================ --> 
<section class="onde-estamos" style="padding:40px 15px;background:#f4f6f8;text-align:center;"> 
    <div class="container" style="max-width:800px;margin:0 auto;"> 
        <h2 style="font-family:"Oswald",sans-serif;font-size:clamp(1.6rem,3vw,2.2rem);text-transform:uppercase;color:#121212;margin-bottom:15px;">📍 Onde estamos</h2> 
        <p style="color:#666;font-size:clamp(0.9rem,1.2vw,1.05rem);margin-bottom:12px;">Estamos localizados em <strong>Caxias do Sul</strong>, atendendo também a região de <strong>Farroupilha</strong> e imediações com agilidade e atendimento personalizado.</p> 
        <p style="color:#666;font-size:clamp(0.9rem,1.2vw,1.05rem);">Atendimento <strong>presencial</strong> em nossa loja na R. José Michelon, 273 - Nossa Sra. de Fátima, Caxias do Sul - RS, 95041-310.</p> 
        <div style="margin-top:15px;"> 
            <a href="https://maps.app.goo.gl/Q2p1dDGUzgeUJAB19" target="_blank" style="display:inline-block;background:#d61935;color:#fff;padding:10px 25px;border-radius:5px;text-decoration:none;font-weight:600;">Ver no Google Maps</a> 
        </div> 
    </div> 
</section>
    <footer>
        <div class="footer-container">
            <div class="footer-col">
                <h4>Sobre Nós</h4>
                <p>Tubos, metais em pedaços e materiais variados em Caxias do Sul. Atendimento rápido para serralheria, manutenção, usinagem e projetos em toda a Serra Gaúcha.</p>
            </div>
            <div class="footer-col">
                <h4>Links Rápidos</h4>
                <ul>
                    <li><a href="#inicio">Início</a></li>
                    <li><a href="#produtos">Produtos</a></li>
                    <li><a href="#sobre">A Empresa</a></li>
                    <li><a href="#contato">Contato</a></li>
                    <li><a href="calculadora/" target="_blank">Calcular peso</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Principais Produtos</h4>
                <ul>
                    <li>Tubos fora de padrão</li>
                    <li>Metais em pedaços</li>
                    <li>Inox e alumínio</li>
                    <li>Nylon e celeron</li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Contato</h4>
                <p><i class="fa-solid fa-phone"></i> (54) 2024-0129</p>
                <p><i class="fa-brands fa-whatsapp"></i> (54) 99209-7850</p>
                <p><i class="fa-solid fa-envelope"></i> comercial@ferrobrasmetais.com.br</p>
                <p style="margin-top:10px;">
                    <a href="https://maps.app.goo.gl/Q2p1dDGUzgeUJAB19" target="_blank" rel="noopener" style="color:#fff;text-decoration:none;">
                        <i class="fa-solid fa-location-dot" style="color:#d61935;"></i> Ver localização no Maps
                    </a>
                </p>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-bottom-left">
                <p>&copy; 2026 Ferrobras Metais - Caxias do Sul - RS</p>
                <a href="https://www.rgconsultoriaindustrial.com.br" target="_blank" rel="noopener">rgconsultoriaindustrial</a>
            </div>
            <div class="lgpd-links">
                <a href="javascript:void(0)" onclick="openModal('privacyModal'); return false;">Política de Privacidade</a>
                <a href="javascript:void(0)" onclick="openModal('termsModal'); return false;">Termos de Uso</a>
            </div>
        </div>
    </footer>

    <!-- COOKIE BANNER -->
    <div id="cookieBanner" class="cookie-banner">
        <p>Utilizamos cookies essenciais para melhorar sua experiência. Ao continuar, você concorda com nossa <a href="javascript:void(0)" onclick="openModal('privacyModal'); return false;" style="color:var(--primary);text-decoration:underline;">Política de Privacidade</a>.</p>
        <button onclick="acceptCookies()">Entendido</button>
    </div>

    <!-- MODAIS -->
    <div id="privacyModal" class="modal-overlay">
        <div class="modal-content">
            <h3>Política de Privacidade (LGPD)</h3>
            <p>A <strong>Ferrobras Metais</strong> respeita sua privacidade e está em conformidade com a LGPD.</p>
            <p>Seus dados não são vendidos ou repassados a terceiros.</p>
            <button class="close-modal" onclick="closeModal('privacyModal')">Fechar</button>
        </div>
    </div>

    <div id="termsModal" class="modal-overlay">
        <div class="modal-content">
            <h3>Termos de Uso</h3>
            <p>Os materiais anunciados estão sujeitos à disponibilidade em estoque.</p>
            <p>As cotações enviadas via WhatsApp possuem validade conforme o estoque do dia.</p>
            <button class="close-modal" onclick="closeModal('termsModal')">Fechar</button>
        </div>
    </div>

    <!-- WHATSAPP FLUTUANTE -->
    <a href="https://wa.me/5554992097850?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20mais%20informações!" target="_blank" rel="noopener" class="whatsapp-float" aria-label="WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <!-- BOTÃO VOLTAR AO TOPO -->
    <button onclick="voltarAoTopo()" id="btnTopo" class="btn-topo" aria-label="Voltar ao topo">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

    <!-- BARRA DE PROGRESSO -->
    <div id="progressBar"></div>

    <!-- JAVASCRIPT -->
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        document.querySelectorAll('.modal-overlay').forEach(function(m) {
            m.addEventListener('click', function(e) { if(e.target === this) closeModal(this.id); });
        });
        function acceptCookies() {
            document.getElementById('cookieBanner').style.display = 'none';
            localStorage.setItem('ferrobras_cookies_accepted', 'true');
        }
        if (localStorage.getItem('ferrobras_cookies_accepted') === 'true') {
            document.getElementById('cookieBanner').style.display = 'none';
        }

        // Voltar ao Topo
        window.addEventListener('scroll', function() {
            var btn = document.getElementById('btnTopo');
            if (window.scrollY > 400) {
                btn.classList.add('visible');
            } else {
                btn.classList.remove('visible');
            }
        });
        function voltarAoTopo() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // WhatsApp Flutuante
        (function() {
            var wpp = document.querySelector('.whatsapp-float');
            if (wpp) {
                wpp.style.opacity = '0';
                wpp.style.transform = 'translateY(30px)';
                wpp.style.transition = 'all 0.5s ease';
            }
            setTimeout(function() {
                if (wpp) {
                    wpp.style.opacity = '1';
                    wpp.style.transform = 'translateY(0)';
                }
            }, 3000);
            window.addEventListener('scroll', function() {
                if (wpp && window.scrollY > 300) {
                    wpp.style.opacity = '1';
                    wpp.style.transform = 'translateY(0)';
                }
            });
        })();

        // Barra de Progresso
        (function() {
            var bar = document.getElementById('progressBar');
            function update() {
                var scrollTop = window.scrollY;
                var docHeight = document.documentElement.scrollHeight - window.innerHeight;
                if (docHeight > 0) {
                    bar.style.width = (scrollTop / docHeight * 100) + '%';
                }
            }
            window.addEventListener('scroll', update);
            window.addEventListener('resize', update);
            window.addEventListener('load', update);
        })();
    </script>
    <!-- 
============================================================
         GOOGLE ANALYTICS (GA4)
         
============================================================ 
-->
    <!-- Google tag (gtag.js) -->
    <script async 
src="https://www.googletagmanager.com/gtag/js?id=G-LJRKBE47Y2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-LJRKBE47Y2');
    </script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "Ferrobras Metais",
  "image": "https://ferrobrasmetais.com.br/assets/images/logo/ferrobrasmetais_logo.webp",
  "@id": "https://ferrobrasmetais.com.br/",
  "url": "https://ferrobrasmetais.com.br/",
  "telephone": "+555420240129",
  "priceRange": "$$",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "Caxias do Sul",
    "addressRegion": "RS",
    "addressCountry": "BR"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": -29.1678,
    "longitude": -51.1794
  },
  "openingHours": "Mo-Fr 07:30-12:00,13:00-17:30"
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "Ferrobras Metais",
  "image": "https://ferrobrasmetais.com.br/assets/images/logo/ferrobrasmetais_logo.webp",
  "@id": "https://ferrobrasmetais.com.br/",
  "url": "https://ferrobrasmetais.com.br/",
  "telephone": "+555420240129",
  "priceRange": "$$",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "Caxias do Sul",
    "addressRegion": "RS",
    "addressCountry": "BR"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": -29.1678,
    "longitude": -51.1794
  },
  "openingHours": "Mo-Fr 07:30-12:00,13:00-17:30"
}
</script>
</head>
    <!-- ============================================================
         PWA - SERVICE WORKER 
         ============================================================ --> 
    <script> 
        // Registrar Service Worker 
                .then(function(reg) { 
                    console.log("✅ Service Worker registrado!"); 
                }) 
                .catch(function(err) { 
                    console.log("❌ Erro ao registrar Service Worker:", err); 
                }); 
        } 
    </script>
    <script> 
                .then(function(reg) { 
                    console.log("✅ Service Worker registrado!"); 
                }) 
                .catch(function(err) { 
                    console.log("❌ Erro:", err); 
                }); 
        } 
    </script>
<script>
    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.register("/sw.js")
            .then(function(reg) {
                console.log("✅ Service Worker registrado!");
            })
            .catch(function(err) {
                console.log("❌ Erro:", err);
            });
    }
</script>
<!-- ============================================================
     BOTÕES SOCIAIS FLUTUANTES 
     ============================================================ --> 

<!-- INSTAGRAM --> 
<a href="https://www.instagram.com/ferrobrasmetais" 
   target="_blank" 
   rel="noopener" 
   class="instagram-float" 
   aria-label="Instagram"> 
    <i class="fa-brands fa-instagram"></i> 
</a> 

<!-- LINKEDIN --> 
<a href="https://www.linkedin.com/company/ferrobrasmetais" 
   target="_blank" 
   rel="noopener" 
   class="linkedin-float" 
   aria-label="LinkedIn"> 
    <i class="fa-brands fa-linkedin-in"></i> 
</a> 

<style> 
    /* Instagram */ 
    .instagram-float { 
        position: fixed; 
        bottom: 180px; 
        left: 25px; 
        width: 60px; 
        height: 60px; 
        background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%); 
        color: white; 
        border-radius: 50%; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-size: 32px; 
        box-shadow: 0 4px 15px rgba(214,36,159,0.4); 
        z-index: 999; 
        text-decoration: none; 
        transition: all 0.3s ease; 
        animation: pulse-instagram 2s infinite; 
    } 
    .instagram-float:hover { 
        transform: scale(1.1); 
        box-shadow: 0 6px 25px rgba(214,36,159,0.6); 
    } 
    .instagram-float i { 
        color: white; 
    } 
    @keyframes pulse-instagram { 
        0% { box-shadow: 0 0 0 0 rgba(214,36,159,0.4); } 
        70% { box-shadow: 0 0 0 20px rgba(214,36,159,0); } 
        100% { box-shadow: 0 0 0 0 rgba(214,36,159,0); } 
    } 

    /* LinkedIn */ 
    .linkedin-float { 
        position: fixed; 
        bottom: 100px; 
        left: 25px; 
        width: 60px; 
        height: 60px; 
        background: #0A66C2; 
        color: white; 
        border-radius: 50%; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-size: 32px; 
        box-shadow: 0 4px 15px rgba(10,102,194,0.4); 
        z-index: 999; 
        text-decoration: none; 
        transition: all 0.3s ease; 
        animation: pulse-linkedin 2s infinite; 
    } 
    .linkedin-float:hover { 
        transform: scale(1.1); 
        box-shadow: 0 6px 25px rgba(10,102,194,0.6); 
    } 
    .linkedin-float i { 
        color: white; 
    } 
    @keyframes pulse-linkedin { 
        0% { box-shadow: 0 0 0 0 rgba(10,102,194,0.4); } 
        70% { box-shadow: 0 0 0 20px rgba(10,102,194,0); } 
        100% { box-shadow: 0 0 0 0 rgba(10,102,194,0); } 
    } 

    /* Responsivo */ 
    @media (max-width: 768px) { 
        .instagram-float { 
            width: 50px; 
            height: 50px; 
            font-size: 26px; 
            bottom: 160px; 
            left: 15px; 
        } 
        .linkedin-float { 
            width: 50px; 
            height: 50px; 
            font-size: 26px; 
            bottom: 90px; 
            left: 15px; 
        } 
    } 
    @media (max-width: 480px) { 
        .instagram-float { 
            width: 45px; 
            height: 45px; 
            font-size: 22px; 
            bottom: 145px; 
            left: 12px; 
        } 
        .linkedin-float { 
            width: 45px; 
            height: 45px; 
            font-size: 22px; 
            bottom: 80px; 
            left: 12px; 
        } 
    } 
</style>
</body>
</html>
<!-- ============================================================
     ============================================================ -->
</button>
<!-- ============================================================
     ============================================================ -->
</button>

<!-- ============================================================
     INSTALAÇÃO AUTOMÁTICA DO APP
     ============================================================ -->
<script>
    // ============================================================
    // INSTALAR APP AUTOMATICAMENTE
    // ============================================================
    
    let isAppInstalled = false;

    // Verificar se já está instalado
    window.addEventListener('appinstalled', function() {
        isAppInstalled = true;
        console.log('✅ App instalado com sucesso!');
        // Mostrar mensagem de sucesso
        mostrarToast('✅ App instalado com sucesso!');
    });

    // Capturar o evento de instalação
        e.preventDefault();
        console.log('📱 App disponível para instalação');
        
        // Mostrar botão de instalação
        mostrarBotaoInstalacao();
    });

    // Função para mostrar o botão de instalação
    function mostrarBotaoInstalacao() {
        // Verificar se o botão já existe
            return;
        }
        
        // Criar botão de instalação
        const btn = document.createElement('button');
        btn.style.cssText = `

            position: fixed;

            bottom: 180px;

            right: 25px;

            background: #25d366;

            color: #fff;

            border: none;

            padding: 12px 20px;

            border-radius: 50px;

            font-size: 14px;

            font-weight: 600;

            cursor: pointer;

            box-shadow: 0 4px 15px rgba(37,211,102,0.4);

            z-index: 9999;

            animation: pulse-green 2s infinite;

            display: flex;

            align-items: center;

            gap: 8px;

        `;
        btn.onclick = function() {
            instalarApp();
        };
        
        // Adicionar estilo de pulsação
        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }
        `;
        document.head.appendChild(style);
        
        document.body.appendChild(btn);
        
        // Mostrar toast informativo
        setTimeout(function() {
        }, 2000);
    }

    // Função para instalar o app
    function instalarApp() {
                if (choiceResult.outcome === 'accepted') {
                    console.log('✅ Usuário aceitou instalar');
                    isAppInstalled = true;
                    mostrarToast('✅ App instalado com sucesso!');
                    // Remover o botão após instalar
                    if (btn) btn.remove();
                } else {
                    console.log('❌ Usuário recusou instalar');
                    mostrarToast('❌ Instalação cancelada');
                }
            });
        } else {
            mostrarToast('⚠️ App não disponível para instalação');
        }
    }

    // Verificar se já está instalado (pelo PWA)
    if (window.matchMedia('(display-mode: standalone)').matches) {
        isAppInstalled = true;
        console.log('✅ App já está instalado');
        // Remover o botão se já estiver instalado
        document.addEventListener('DOMContentLoaded', function() {
            if (btn) btn.remove();
        });
    }

    console.log('📱 Sistema de instalação automática carregado!');
</script>

<!-- Estilos adicionais -->
<style>
    /* Animação do botão */
    @keyframes pulse {
        0% { transform: scale(1); box-shadow: 0 4px 15px rgba(37,211,102,0.4); }
        50% { transform: scale(1.05); box-shadow: 0 6px 25px rgba(37,211,102,0.6); }
        100% { transform: scale(1); box-shadow: 0 4px 15px rgba(37,211,102,0.4); }
    }
    
    /* Toast de notificação */
    .toast-install {
        position: fixed;
        bottom: 250px;
        left: 50%;
        transform: translateX(-50%);
        background: #1a1a2e;
        color: #fff;
        padding: 12px 25px;
        border-radius: 8px;
        font-size: 14px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        z-index: 10000;
        border-left: 4px solid #d61935;
        max-width: 90%;
        text-align: center;
    }
</style>

<!-- ============================================================
     BOTÃO DE INSTALAÇÃO FIXO (SEMPRE VISÍVEL)
     ============================================================ -->
</button>

<style>
        @keyframes pulse-green {

            0% { transform: scale(1); box-shadow: 0 4px 15px rgba(37,211,102,0.4); }

            50% { transform: scale(1.05); box-shadow: 0 6px 25px rgba(37,211,102,0.6); }

            100% { transform: scale(1); box-shadow: 0 4px 15px rgba(37,211,102,0.4); }

        }
        50% { transform: scale(1.05); box-shadow: 0 6px 25px rgba(37,211,102,0.6); }
        100% { transform: scale(1); box-shadow: 0 4px 15px rgba(37,211,102,0.4); }
    }
</style>

<script>
    // Esconder o botão fixo se já estiver instalado
    if (window.matchMedia('(display-mode: standalone)').matches) {
        document.addEventListener('DOMContentLoaded', function() {
            if (btn) btn.style.display = 'none';
        });
    }
</script>

<script>
    // ============================================================
    // FUNÇÃO ALTERNATIVA DE INSTALAÇÃO
    // ============================================================
    
    function instalarAppAlternativo() {
        console.log('📱 Tentando instalar app...');
        
        // Verificar se já está instalado
        if (window.matchMedia('(display-mode: standalone)').matches) {
            mostrarToast('✅ App já está instalado!');
            return;
        }
        
                if (choiceResult.outcome === 'accepted') {
                    mostrarToast('✅ App instalado com sucesso!');
                } else {
                    mostrarToast('❌ Instalação cancelada');
                }
            });
            return;
        }
        
        mostrarToast('📱 Clique nos 3 pontos ⋮ e selecione "Adicionar à tela inicial"');
        
        // Abrir o menu de instalação do navegador (se disponível)
        if (navigator.share) {
            navigator.share({
                title: 'Ferrobras Metais',
                text: 'Instale o app Ferrobras Metais!',
                url: window.location.href
            });
        }
    }
    
    // Substituir a função original
    window.instalarApp = instalarAppAlternativo;
    
    console.log('✅ Função de instalação alternativa carregada!');
</script>

<!-- ============================================================
     INSTALAÇÃO AUTOMÁTICA DO APP (VERSÃO QUE FUNCIONAVA)
     ============================================================ -->
<script>
    let deferredPrompt;
    let isAppInstalled = false;

    window.addEventListener('appinstalled', function() {
        isAppInstalled = true;
        console.log('✅ App instalado com sucesso!');
        mostrarToast('✅ App instalado com sucesso!');
    });

    window.addEventListener('beforeinstallprompt', function(e) {
        e.preventDefault();
        deferredPrompt = e;
        console.log('📱 App disponível para instalação');
        mostrarBotaoInstalacao();
    });

    function mostrarBotaoInstalacao() {
        if (document.getElementById('btnInstalarApp')) {
            return;
        }
        
        const btn = document.createElement('button');
        btn.id = 'btnInstalarApp';
        btn.innerHTML = '📲 Instalar App';
        btn.style.cssText = `

            position: fixed;

            bottom: 180px;

            right: 25px;

            background: #25d366;

            color: #fff;

            border: none;

            padding: 12px 20px;

            border-radius: 50px;

            font-size: 14px;

            font-weight: 600;

            cursor: pointer;

            box-shadow: 0 4px 15px rgba(37,211,102,0.4);

            z-index: 9999;

            animation: pulse-green 2s infinite;

            display: flex;

            align-items: center;

            gap: 8px;

        `;
        btn.onclick = function() {
            instalarApp();
        };
        
        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse {
                0% { transform: scale(1); box-shadow: 0 4px 15px rgba(37,211,102,0.4); }
                50% { transform: scale(1.05); box-shadow: 0 6px 25px rgba(37,211,102,0.6); }
                100% { transform: scale(1); box-shadow: 0 4px 15px rgba(37,211,102,0.4); }
            }
        `;
        document.head.appendChild(style);
        document.body.appendChild(btn);
        
        setTimeout(function() {
            mostrarToast('📱 Clique em "Instalar App" para instalar!');
        }, 2000);
    }

    function instalarApp() {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(function(choiceResult) {
                if (choiceResult.outcome === 'accepted') {
                    console.log('✅ Usuário aceitou instalar');
                    isAppInstalled = true;
                    mostrarToast('✅ App instalado com sucesso!');
                    const btn = document.getElementById('btnInstalarApp');
                    if (btn) btn.remove();
                } else {
                    console.log('❌ Usuário recusou instalar');
                    mostrarToast('❌ Instalação cancelada');
                }
                deferredPrompt = null;
            });
        } else {
            mostrarToast('⚠️ App não disponível para instalação');
        }
    }

    if (window.matchMedia('(display-mode: standalone)').matches) {
        isAppInstalled = true;
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('btnInstalarApp');
            if (btn) btn.remove();
        });
    }

    // Função toast
    if (typeof mostrarToast === 'undefined') {
        window.mostrarToast = function(mensagem) {
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed;
                bottom: 250px;
                left: 50%;
                transform: translateX(-50%);
                background: #1a1a2e;
                color: #fff;
                padding: 12px 25px;
                border-radius: 8px;
                font-size: 14px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.3);
                z-index: 10000;
                border-left: 4px solid #d61935;
                max-width: 90%;
                text-align: center;
                transition: all 0.3s ease;
                opacity: 0;
            `;
            toast.textContent = mensagem;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(-50%)';
            }, 100);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        };
    }

    console.log('📱 Sistema de instalação carregado!');
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "Ferrobras Metais",
  "image": "https://ferrobrasmetais.com.br/assets/images/logo/ferrobrasmetais_logo.webp",
  "@id": "https://ferrobrasmetais.com.br/",
  "url": "https://ferrobrasmetais.com.br/",
  "telephone": "+555420240129",
  "priceRange": "$$",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "Caxias do Sul",
    "addressRegion": "RS",
    "addressCountry": "BR"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": -29.1678,
    "longitude": -51.1794
  },
  "openingHours": "Mo-Fr 07:30-12:00,13:00-17:30"
}
</script>
