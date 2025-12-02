<?php
// Inicia a sessão apenas se ainda não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    // Configurações de segurança de sessão
    ini_set('session.cookie_httponly', 1);
    if (!empty($_SERVER['HTTPS'])) {
        ini_set('session.cookie_secure', 1);
    }

    session_start();
}

// Tempo de expiração em segundos
$tempo_expiracao = 20;

// Verifica se existe o timestamp da última atividade
if (isset($_SESSION['ultima_atividade'])) {
    $tempo_inativo = time() - $_SESSION['ultima_atividade'];
    if ($tempo_inativo > $tempo_expiracao) {
        // Tempo excedido, destrói a sessão
        session_unset();
        session_destroy();
        // Redirecionar para login ou outra página
        header('Location: index.php');
        exit;
    }
}

// Atualiza o timestamp da última atividade
$_SESSION['ultima_atividade'] = time();

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'login');
define('DB_USER', 'root');
define('DB_PASS', ''); // Coloque a senha do MySQL se houver

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}
?>
