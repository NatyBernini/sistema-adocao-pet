CREATE DATABASE login;

USE login;

CREATE TABLE usuarios(
	id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR (80) UNIQUE,
    senha_hash VARCHAR (255),
    perfil ENUM('admin', 'user')
);