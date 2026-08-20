<?php
// config/admin_link.php - Link do painel administrativo
// Inclua este arquivo no header do site
?>
<div class="admin-link">
    <a href="/site-ferrobras/painel_industrial/login_simples.php" 
       class="admin-btn" 
       title="Área Administrativa">
        <i class="fas fa-crown"></i> 
        <span>Admin</span>
    </a>
</div>

<style>
.admin-link {
    display: inline-block;
    margin-left: 15px;
}
.admin-btn {
    background: linear-gradient(135deg, #d61935, #b01229);
    color: white !important;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: none;
    cursor: pointer;
}
.admin-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(214, 25, 53, 0.3);
}
.admin-btn i {
    font-size: 16px;
}
</style>
