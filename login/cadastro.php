<?php
require_once 'conexao.php';
session_start();

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $perfil = $_POST['perfil'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "E-mail inválido.";
    } elseif (strlen($senha) < 8) {
        $mensagem = "A senha deve ter no mínimo 8 caracteres.";
    } elseif (!in_array($perfil, ['admin', 'user'])) {
        $mensagem = "Perfil inválido.";
    } else {
        // Verificar se o e-mail já existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $mensagem = "Este e-mail já está cadastrado.";
        } else {
            // Inserir novo usuário
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (email, senha_hash, perfil) VALUES (?, ?, ?)");
            if ($stmt->execute([$email, $senha_hash, $perfil])) {
                // Cadastro ok, criar sessão e redirecionar
                $_SESSION['usuario'] = $email;
                $_SESSION['perfil'] = $perfil;

                header('Location: dashboard.php');
                exit;
            } else {
                $mensagem = "Erro ao cadastrar usuário.";
            }
        }
    }
}
?>
