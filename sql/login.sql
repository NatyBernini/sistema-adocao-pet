CREATE DATABASE login;

USE login;

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(80) DEFAULT NULL,
  `senha_hash` varchar(255) DEFAULT NULL,
  `perfil` enum('admin','adotante') DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
	
CREATE TABLE `animais` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `especie` varchar(50) NOT NULL,
  `raca` varchar(100) DEFAULT NULL,
  `idade` int(11) DEFAULT NULL,
  `tutor_email` varchar(100) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `castrado` tinyint(1) DEFAULT 0,
  `vermifugado` tinyint(1) DEFAULT 0,
  `vacinado` tinyint(1) DEFAULT 0,
  `historico` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci

CREATE TABLE `adotantes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `data_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci

CREATE TABLE `solicitacoes_adocao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_adotante` int(11) NOT NULL,
  `id_animal` int(11) NOT NULL,
  `status` enum('pendente','aceita','rejeitada') DEFAULT 'pendente',
  `data_solicitacao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
