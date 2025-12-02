<?php
require_once 'conexao.php'; // apenas a conexão, sem verificar sessão

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $perfil = $_POST['perfil'];

    // Campos extras se perfil = adotante
    $nome = $_POST['nome'] ?? null;
    $telefone = $_POST['telefone'] ?? null;
    $endereco = $_POST['endereco'] ?? null;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "E-mail inválido.";
    } elseif (strlen($senha) < 8) {
        $mensagem = "A senha deve ter no mínimo 8 caracteres.";
    } elseif (!in_array($perfil, ['admin', 'adotante'])) {
        $mensagem = "Perfil inválido.";
    } elseif ($perfil === 'adotante' && empty(trim($nome))) {
        $mensagem = "O nome é obrigatório para adotantes.";
    } else {

        // Verificar e-mail duplicado
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $mensagem = "Este e-mail já está cadastrado.";
        } else {

            // Criar usuário
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (email, senha_hash, perfil) VALUES (?, ?, ?)");

            if ($stmt->execute([$email, $senha_hash, $perfil])) {

                // ID recém criado
                $usuario_id = $pdo->lastInsertId();

                // Se for adotante, cria também na tabela adotantes
                if ($perfil === 'adotante') {
                    $stmt2 = $pdo->prepare("
                        INSERT INTO adotantes (id, nome, email, telefone, endereco)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmt2->execute([
                        $usuario_id,
                        $nome,
                        $email,
                        $telefone,
                        $endereco
                    ]);
                }

                // Criar sessão e redirecionar
                session_start();
                $_SESSION['usuario'] = $email;
                $_SESSION['perfil'] = $perfil;

                if ($perfil === 'adotante') {
                    $_SESSION['id_adotante'] = $usuario_id;
                }

                header('Location: dashboard.php');
                exit;
            } else {
                $mensagem = "Erro ao cadastrar usuário.";
            }
        }
    }
}

?>
