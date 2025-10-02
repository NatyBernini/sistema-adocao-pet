<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    $_SESSION['erro_login'] = "Sessão expirada ou acesso negado.";
    header('Location: index.php'); exit;
}

// Se desejar limitar a um perfil específico:
if (isset($perfil_requerido) && $_SESSION['perfil'] !== $perfil_requerido) {
    header('Location: sem_permissao.php'); exit;
}
