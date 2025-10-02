<?php
session_start();

$tempo_expiracao = 20;

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    $_SESSION['erro_login'] = "Sessão expirada ou acesso negado.";
    header('Location: index.php');
    exit;
}

// Verifica tempo de inatividade
if (isset($_SESSION['ultima_atividade'])) {
    $tempo_inativo = time() - $_SESSION['ultima_atividade'];
    if ($tempo_inativo > $tempo_expiracao) {
        // Expirou por inatividade
        session_unset();
        session_destroy();
        session_start(); // iniciar sessão para definir mensagem
        $_SESSION['erro_login'] = "Sessão expirada por inatividade.";
        header('Location: index.php');
        exit;
    }
}
// Atualiza o timestamp da última atividade
$_SESSION['ultima_atividade'] = time();

// Se desejar limitar a um perfil específico:
if (isset($perfil_requerido) && $_SESSION['perfil'] !== $perfil_requerido) {
    header('Location: sem_permissao.php');
    exit;
}
?>
