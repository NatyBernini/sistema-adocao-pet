<?php
require_once 'conexao.php';
require_once 'verifica_sessao.php';

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

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f6f8fa;
            display: flex;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            height: 100vh;
            position: fixed;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
        }
        .sidebar .logo {
            text-align: center;
            margin-bottom: 40px;
        }
        .sidebar .logo img { width: 60px; }
        .sidebar .logo h2 { margin-top: 10px; font-size: 20px; font-weight: 600; }
        .sidebar a {
            padding: 15px 20px;
            display: block;
            text-decoration: none;
            color: #ecf0f1;
            font-size: 15px;
            transition: .3s;
        }
        .sidebar a:hover {
            background: #34495e;
        }

        /* CONTEÚDO */
        .content {
            margin-left: 250px;
            padding: 40px;
            width: calc(100% - 250px);
        }

        .title-page {
            font-size: 26px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 650px;
        }

        form {
            display: grid;
            gap: 15px;
        }

        form label {
            font-weight: 600;
            font-size: 14px;
        }

        form input, form textarea {
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
        }

        form textarea {
            resize: vertical;
            min-height: 80px;
        }

        form button {
            padding: 12px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: .3s;
            margin-top: 10px;
        }

        form button:hover {
            background: #2980b9;
        }

        .animal-foto {
            width: 150px;
            border-radius: 10px;
            margin-bottom: 10px;
            border: 2px solid #ddd;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            font-weight: 600;
            color: #3498db;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">
        <img src="assets/iconeLogin.svg" alt="logo">
        <h2>Pet Adote</h2>
    </div>

    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="animais.php">🐕 Animais</a>
    <a href="adotantes.php">👤 Adotantes</a>
    <a href="solicitacoes.php">📋 Solicitações</a>
    <a href="logout.php">🚪 Logout</a>
</div>


<div class="content">

    <p class="title-page">✏️ Editar Animal</p>

    <div class="card">

        <!-- mostrar foto atual -->
        <?php if (!empty($animal['foto'])): ?>
            <img src="uploads/animais/<?= $animal['foto'] ?>" class="animal-foto">
        <?php endif; ?>

        <form method="post" action="animais.php" enctype="multipart/form-data">
            <input type="hidden" name="acao" value="editar">
            <input type="hidden" name="id" value="<?= $animal['id'] ?>">

            <label>Nome:</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($animal['nome']) ?>" required>

            <label>Espécie:</label>
            <input type="text" name="especie" value="<?= htmlspecialchars($animal['especie']) ?>" required>

            <label>Raça:</label>
            <input type="text" name="raca" value="<?= htmlspecialchars($animal['raca']) ?>">

            <label>Idade:</label>
            <input type="number" name="idade" min="0" value="<?= $animal['idade'] ?>">

            <label>Tutor (e-mail):</label>
            <input type="email" name="tutor_email" value="<?= htmlspecialchars($animal['tutor_email']) ?>">

            <label>Situação de Saúde:</label>
            <input type="text" name="saude" value="<?= htmlspecialchars($animal['saude'] ?? '') ?>">

            <label>Descrição:</label>
            <textarea name="descricao"><?= htmlspecialchars($animal['descricao'] ?? '') ?></textarea>

            <label>Foto do Animal:</label>
            <input type="file" name="foto">

            <button type="submit">Salvar Alterações</button>
        </form>

        <a href="animais.php" class="back-link">⬅ Voltar</a>

    </div>

</div>

</body>
</html>
