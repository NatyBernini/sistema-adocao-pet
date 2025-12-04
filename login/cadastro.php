<?php
require_once 'conexao.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $perfil = $_POST['perfil'];

    // Campos do adotante
    $nome = $_POST['nome'] ?? null;
    $telefone = $_POST['telefone'] ?? null;
    $cep = preg_replace('/\D/', '', $_POST['cep'] ?? '');
    $rua = $_POST['rua'] ?? '';
    $bairro = $_POST['bairro'] ?? '';
    $cidade = $_POST['cidade'] ?? '';
    $estado = $_POST['estado'] ?? '';

    // Monta endereço completo
    $endereco = "$rua, $bairro - $cidade / $estado (CEP: $cep)";

    // ---------------- VALIDAÇÕES -----------------
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "E-mail inválido.";
    } elseif (strlen($senha) < 8) {
        $mensagem = "A senha deve ter no mínimo 8 caracteres.";
    } elseif (!in_array($perfil, ['admin', 'adotante'])) {
        $mensagem = "Perfil inválido.";
    } elseif ($perfil === 'adotante' && empty(trim($nome))) {
        $mensagem = "O nome é obrigatório para adotantes.";
    } elseif ($perfil === 'adotante' && strlen($cep) !== 8) {
        $mensagem = "CEP inválido.";
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

                $usuario_id = $pdo->lastInsertId();

                // Se for adotante → cria registro extra
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

