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

// Variáveis
$mensagem = "";

// CREATE
if (isset($_POST['acao']) && $_POST['acao'] === 'cadastrar') {
    $nome = trim($_POST['nome']);
    $especie = trim($_POST['especie']);
    $raca = trim($_POST['raca']);
    $idade = (int) $_POST['idade'];
    $tutor_email = trim($_POST['tutor_email']);

    if ($nome && $especie) {
        $stmt = $pdo->prepare("INSERT INTO animais (nome, especie, raca, idade, tutor_email) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $especie, $raca, $idade, $tutor_email]);
        $mensagem = "Animal cadastrado com sucesso!";
    } else {
        $mensagem = "Preencha pelo menos o nome e a espécie.";
    }
}

// DELETE
if (isset($_GET['excluir'])) {
    $id = (int) $_GET['excluir'];
    $pdo->prepare("DELETE FROM animais WHERE id = ?")->execute([$id]);
    header("Location: animais.php");
    exit;
}

// READ
$stmt = $pdo->query("SELECT * FROM animais ORDER BY id DESC");
$animais = $stmt->fetchAll(PDO::FETCH_ASSOC);

// UPDATE
if (isset($_POST['acao']) && $_POST['acao'] === 'editar') {
    $id = (int) $_POST['id'];
    $nome = trim($_POST['nome']);
    $especie = trim($_POST['especie']);
    $raca = trim($_POST['raca']);
    $idade = (int) $_POST['idade'];
    $tutor_email = trim($_POST['tutor_email']);

    $stmt = $pdo->prepare("UPDATE animais SET nome=?, especie=?, raca=?, idade=?, tutor_email=? WHERE id=?");
    $stmt->execute([$nome, $especie, $raca, $idade, $tutor_email, $id]);
    header("Location: animais.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Animais</title>
    <link rel="stylesheet" href="style/login.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 900px;
            margin: 30px auto;
            background-color: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h2, h3 {
            color: #4CAF50;
        }
        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        form label {
            font-weight: 500;
        }
        form input, form button {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            width: 100%;
        }
        form button {
            grid-column: span 2;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
        }
        table th {
            background-color: #4CAF50;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        table tr:hover {
            background-color: #e0f7e0;
        }
        a {
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
            transition: opacity 0.5s ease;
        }
        .nenhum-animal {
            padding: 15px;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            border-radius: 6px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🐾 Gerenciar Animais</h2>

        <?php if($mensagem): ?>
            <div class="mensagem" id="mensagem"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <!-- Formulário de cadastro -->
        <form method="post">
            <input type="hidden" name="acao" value="cadastrar">
            <div>
                <label>Nome:</label>
                <input type="text" name="nome" required>
            </div>
            <div>
                <label>Espécie:</label>
                <input type="text" name="especie" required>
            </div>
            <div>
                <label>Raça:</label>
                <input type="text" name="raca">
            </div>
            <div>
                <label>Idade:</label>
                <input type="number" name="idade" min="0">
            </div>
            <div>
                <label>Email do Tutor:</label>
                <input type="email" name="tutor_email">
            </div>
            <div>
                <button type="submit">Cadastrar Animal</button>
            </div>
        </form>

        <!-- Lista de animais -->
        <?php if(count($animais) > 0): ?>
            <h3>Lista de Animais</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Espécie</th>
                    <th>Raça</th>
                    <th>Idade</th>
                    <th>Tutor</th>
                    <th>Ações</th>
                </tr>
                <?php foreach ($animais as $a): ?>
                <tr>
                    <td><?= $a['id'] ?></td>
                    <td><?= htmlspecialchars($a['nome']) ?></td>
                    <td><?= htmlspecialchars($a['especie']) ?></td>
                    <td><?= htmlspecialchars($a['raca']) ?></td>
                    <td><?= $a['idade'] ?></td>
                    <td><?= htmlspecialchars($a['tutor_email']) ?></td>
                    <td>
                        <a href="animais_editar.php?id=<?= $a['id'] ?>">Editar</a> |
                        <a href="?excluir=<?= $a['id'] ?>" onclick="return confirm('Excluir este animal?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <div class="nenhum-animal">Nenhum animal cadastrado ainda.</div>
        <?php endif; ?>

        <br>
        <a href="dashboard.php">⬅ Voltar ao Dashboard</a>
    </div>

    <script>
        // Faz a mensagem desaparecer após 3 segundos
        const msg = document.getElementById('mensagem');
        if (msg) {
            setTimeout(() => {
                msg.style.opacity = '0';
                setTimeout(() => { msg.remove(); }, 500);
            }, 3000);
        }
    </script>
</body>
</html>
