<?php
require_once 'conexao.php';
session_start();

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// Tentativas
$_SESSION['tentativas'] = $_SESSION['tentativas'] ?? 0;
if ($_SESSION['tentativas'] >= 5) {
    $_SESSION['erro_login'] = "Muitas tentativas. Tente novamente mais tarde.";
    header('Location: index.php');
    exit;
}

if (!$email || !$senha) {
    $_SESSION['erro_login'] = "Preencha todos os campos.";
    header('Location: index.php');
    exit;
}

// Buscar usuário
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// ✅ Comparar senha usando password_verify
if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
    session_regenerate_id(true);
    $_SESSION['usuario'] = $usuario['email'];
    $_SESSION['perfil'] = $usuario['perfil'];
    $_SESSION['tentativas'] = 0;
    header('Location: dashboard.php');
    exit;
} else {
    $_SESSION['tentativas']++;
    $_SESSION['erro_login'] = "Credenciais inválidas.";
    header('Location: index.php');
    exit;
}
