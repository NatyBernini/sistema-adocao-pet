CREATE DATABASE login;

USE login;

CREATE TABLE usuarios(
	id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR (80) UNIQUE,
    senha_hash VARCHAR (255),
    perfil ENUM('admin', 'user')
);

CREATE TABLE animais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    especie VARCHAR(50) NOT NULL,
    raca VARCHAR(100),
    idade INT,
    tutor_email VARCHAR(100),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
