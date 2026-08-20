-- ============================================================
-- BACKUP DO BANCO DE DADOS - 20/08/2026 13:16:09
-- ============================================================

CREATE TABLE `admin_usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expira` datetime DEFAULT NULL,
  `tentativas` int(11) DEFAULT 0,
  `bloqueado_ate` datetime DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin_usuarios` (`id`, `nome`, `email`, `senha`, `reset_token`, `reset_expira`, `tentativas`, `bloqueado_ate`, `ativo`, `created_at`, `updated_at`) VALUES ('8', 'Administrador', 'ferrobrasmetais@gmail.com', '$2y$10$GkV3vQzD96S1Xo9bGORbT.4YuRdZFdfC/h51DZZIK5KTXLwDrjyJe', NULL, NULL, '0', NULL, '1', '2026-07-31 16:14:55', '2026-08-19 14:08:05');
INSERT INTO `admin_usuarios` (`id`, `nome`, `email`, `senha`, `reset_token`, `reset_expira`, `tentativas`, `bloqueado_ate`, `ativo`, `created_at`, `updated_at`) VALUES ('10', 'Administrador', 'admin@ferrobrasmetais.com.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '0', NULL, '1', '2026-08-14 16:20:24', '2026-08-14 16:20:24');

CREATE TABLE `banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) NOT NULL,
  `subtitulo` varchar(200) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `galeria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `imagem` varchar(255) NOT NULL,
  `ordem` int(11) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `galeria` (`id`, `titulo`, `descricao`, `imagem`, `ordem`, `ativo`, `created_at`) VALUES ('11', 'Estoque de Tubos Ferrobras', 'Tubos e Perfis Diversos', 'img/tubos.png', '1', '1', '2026-07-31 16:40:08');
INSERT INTO `galeria` (`id`, `titulo`, `descricao`, `imagem`, `ordem`, `ativo`, `created_at`) VALUES ('12', 'Pátio e Organização Serra Gaúcha', 'Organização e Variedade', 'img/serra.png', '2', '1', '2026-07-31 16:40:09');
INSERT INTO `galeria` (`id`, `titulo`, `descricao`, `imagem`, `ordem`, `ativo`, `created_at`) VALUES ('13', 'Retalhos para Manutenção', 'Retalhos para Manutenção', 'img/pecas_divercas.png', '3', '1', '2026-07-31 16:40:09');

CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) DEFAULT NULL,
  `acao` varchar(100) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `detalhes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria` varchar(50) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `icone` varchar(50) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `preco` varchar(50) DEFAULT 'Sob consulta',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `produtos` (`id`, `categoria`, `nome`, `descricao`, `icone`, `imagem`, `ativo`, `created_at`, `updated_at`, `preco`) VALUES ('2', 'barras', 'Barras, Chapas e Pedacos', 'Barras, chapas, cantoneiras e pedaços de metal para reaproveitamento.', 'fa-solid fa-cube', 'assets/images/produtos/1786734274_barras_chapas_pedacos.webp', '1', '2026-07-31 15:33:52', '2026-08-20 12:57:35', 'Sob consulta');
INSERT INTO `produtos` (`id`, `categoria`, `nome`, `descricao`, `icone`, `imagem`, `ativo`, `created_at`, `updated_at`, `preco`) VALUES ('4', 'aluminio', 'Aluminio', 'Perfis, chapas, cantoneiras e cortes sob consulta.', 'fa-solid fa-sheet-plastic', 'assets/images/produtos/1786734254_aluminio.webp', '1', '2026-07-31 15:33:52', '2026-08-20 12:57:35', 'Sob consulta');
INSERT INTO `produtos` (`id`, `categoria`, `nome`, `descricao`, `icone`, `imagem`, `ativo`, `created_at`, `updated_at`, `preco`) VALUES ('7', 'inox', 'Aco Inoxidavel (Inox)', 'Tubos, chapas e peças avulsas em inox conforme o estoque disponível.', 'fa-solid fa-layer-group', 'assets/images/produtos/1786734236_inox.webp', '1', '2026-07-31 16:24:59', '2026-08-20 12:57:35', 'Sob consulta');
INSERT INTO `produtos` (`id`, `categoria`, `nome`, `descricao`, `icone`, `imagem`, `ativo`, `created_at`, `updated_at`, `preco`) VALUES ('9', 'nylon', 'Nylon e Celeron', 'Tarugos, buchas e materiais de engenharia para manutenção e usinagem.', 'fa-solid fa-shield-halved', 'assets/images/produtos/1786734871_nylon_ferrobras.webp', '1', '2026-07-31 16:24:59', '2026-08-20 12:57:35', 'Sob consulta');
INSERT INTO `produtos` (`id`, `categoria`, `nome`, `descricao`, `icone`, `imagem`, `ativo`, `created_at`, `updated_at`, `preco`) VALUES ('16', 'metais', 'Cobre, Latao e Bronze', 'Peþas, barras e pedaþos de metais nÒo ferrosos conforme disponibilidade.', 'fa-solid fa-fire', 'assets/images/produtos/1786476957_cobre_latao_bronze.webp', '1', '2026-07-31 16:45:13', '2026-08-20 12:57:35', 'Sob consulta');

CREATE TABLE `site_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chave` varchar(50) NOT NULL,
  `valor` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `chave` (`chave`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `site_config` (`id`, `chave`, `valor`, `created_at`) VALUES ('1', 'site_nome', 'Ferrobras Metais', '2026-08-14 19:47:59');
INSERT INTO `site_config` (`id`, `chave`, `valor`, `created_at`) VALUES ('2', 'site_email', 'comercial@ferrobrasmetais.com.br', '2026-08-14 19:47:59');
INSERT INTO `site_config` (`id`, `chave`, `valor`, `created_at`) VALUES ('3', 'site_telefone', '(54) 2024-0129', '2026-08-14 19:47:59');
INSERT INTO `site_config` (`id`, `chave`, `valor`, `created_at`) VALUES ('4', 'site_whatsapp', '(54) 99209-7850', '2026-08-14 19:47:59');
INSERT INTO `site_config` (`id`, `chave`, `valor`, `created_at`) VALUES ('5', 'site_endereco', 'Caxias do Sul - RS', '2026-08-14 19:47:59');
INSERT INTO `site_config` (`id`, `chave`, `valor`, `created_at`) VALUES ('6', 'site_descricao', 'Tubos, metais e retalhos em Caxias do Sul e Serra Gaúcha', '2026-08-14 19:47:59');

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nivel` varchar(20) NOT NULL DEFAULT 'visualizador',
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `bloqueado` tinyint(1) NOT NULL DEFAULT 0,
  `nome` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

INSERT INTO `usuarios` (`id`, `email`, `senha`, `nivel`, `ativo`, `bloqueado`, `nome`, `created_at`) VALUES ('1', 'admin@ferrobrasmetais.com.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '1', '0', 'Administrador', '2026-08-11 18:00:20');
INSERT INTO `usuarios` (`id`, `email`, `senha`, `nivel`, `ativo`, `bloqueado`, `nome`, `created_at`) VALUES ('7', 'ferrobrasmetais@gmail.com', '$2y$10$GkV3vQzD96S1Xo9bGORbT.4YuRdZFdfC/h51DZZIK5KTXLwDrjyJe', 'admin', '1', '0', 'Administrador', '2026-08-11 20:06:54');
INSERT INTO `usuarios` (`id`, `email`, `senha`, `nivel`, `ativo`, `bloqueado`, `nome`, `created_at`) VALUES ('9', 'admin@site.com', '$2y$10$CL3yZiwu3WdGPcFeDsopbuvA6HOPo2H3CwBNgvKbzYcdNQC.LZuVG', 'admin', '1', '0', 'Administrador', '2026-08-13 18:01:57');

