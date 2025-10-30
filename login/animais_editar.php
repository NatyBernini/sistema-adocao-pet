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
            background-color: #f4f6f9;
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 600px;
            margin: 30px auto;
            background-color: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h2 {
            color: #4CAF50;
            margin-bottom: 20px;
        }
        form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }
        form label {
            font-weight: 500;
        }
        form input, form button {
            padding: 10px 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            width: 100%;
        }
        form button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        form button:hover {
            background-color: #45a049;
        }
        a {
            display: inline-block;
            margin-top: 15px;
            color: #4CAF50;
            text-decoration: none;
            font-weight: 500;
        }
        a:hover {
            text-decoration: underline;
        }
        .mensagem {
            padding: 10px;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            border-radius: 6px;
            margin-bottom: 15px;
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
