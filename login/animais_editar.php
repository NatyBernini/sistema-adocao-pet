<?php
require_once 'conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM animais WHERE id = ?");
$stmt->execute([$id]);
$animal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$animal) {
    die("<div style='padding:20px; background:#f8d7da; color:#721c24; border-radius:8px;'>Animal não encontrado.</div>");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Animal</title>
    <link rel="stylesheet" href="style/login.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #eef2f5;
            color: #333;
        }

        .container {
            width: 90%;
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.12);
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            color: #4CAF50;
            margin-bottom: 18px;
            font-size: 26px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 18px;
            margin-top: 10px;
        }

        form label {
            font-weight: 600;
            margin-bottom: -5px;
        }

        form input {
            padding: 12px 14px;
            border-radius: 8px;
            border: 1px solid #cfcfcf;
            font-size: 15px;
            transition: border 0.3s;
        }

        form input:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, .3);
        }

        form button {
            padding: 12px;
            border-radius: 8px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 700;
            transition: 0.3s;
        }

        form button:hover {
            background-color: #43a047;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        a {
            display: inline-block;
            margin-top: 18px;
            color: #4CAF50;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
        }

        a:hover {
            text-decoration: underline;
            transform: translateX(-3px);
        }

        .mensagem {
            padding: 12px;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            border-radius: 6px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>✏️ Editar Animal</h2>

        <form method="post" action="animais.php">
            <input type="hidden" name="acao" value="editar">
            <input type="hidden" name="id" value="<?= $animal['id'] ?>">

            <label>Nome:</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($animal['nome']) ?>" required>

            <label>Espécie:</label>
            <input type="text" name="especie" value="<?= htmlspecialchars($animal['especie']) ?>" required>

            <label>Raça:</label>
            <input type="text" name="raca" value="<?= htmlspecialchars($animal['raca']) ?>">

            <label>Idade:</label>
            <input type="number" name="idade" value="<?= $animal['idade'] ?>" min="0">

            <label>Email do Tutor:</label>
            <input type="email" name="tutor_email" value="<?= htmlspecialchars($animal['tutor_email']) ?>">

            <button type="submit">Salvar Alterações</button>
        </form>

        <a href="animais.php">⬅ Voltar</a>
    </div>
</body>
</html>
