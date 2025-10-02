<?php
// Configurações sessão
ini_set('session.cookie_httponly', 1);
if (isset($_SERVER['HTTPS'])) {
    ini_set('session.cookie_secure', 1);
}

session_start();

// Configurações banco
define('DB_HOST', 'localhost');
define('DB_NAME', 'login');
define('DB_USER', 'root');
define('DB_PASS', ''); // coloque a senha do MySQL se houver

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}
?>
