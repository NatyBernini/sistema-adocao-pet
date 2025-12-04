<?php
require_once 'conexao.php';
require_once 'verifica_sessao.php';

if (!isset($_GET['id'])) {
    header("Location: adotantes.php");
    exit;
}

$id = (int) $_GET['id'];

// BUSCAR ADOTANTE
$stmt = $pdo->prepare("SELECT * FROM adotantes WHERE id = ?");
$stmt->execute([$id]);
$adotante = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$adotante) {
    echo "Adotante não encontrado!";
    exit;
}

$mensagem = "";

// SALVAR EDIÇÃO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $endereco = trim($_POST['endereco']);

    if ($nome && $email) {
        $update = $pdo->prepare("
            UPDATE adotantes 
            SET nome = ?, email = ?, telefone = ?, endereco = ?
            WHERE id = ?
        ");

        $update->execute([$nome, $email, $telefone, $endereco, $id]);

        $mensagem = "Adotante atualizado com sucesso!";
        
        // Atualiza os dados carregados
        $adotante['nome'] = $nome;
        $adotante['email'] = $email;
        $adotante['telefone'] = $telefone;
        $adotante['endereco'] = $endereco;
    } else {
        $mensagem = "Nome e e-mail são obrigatórios!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Adotante</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f6f8fa;
            display: flex;
        }
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
        .sidebar a:hover { background: #34495e; }

        .content {
            margin-left: 250px;
            padding: 40px;
            width: calc(100% - 250px);
        }

        .title-page {
            font-size: 26px;
            font-weight: 600;
            color: #333;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-top: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .mensagem {
            padding: 12px;
            background: #d4edda;
            color: #155724;
            border-left: 5px solid #28a745;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        form {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        form input {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            width: 100%;
        }
        form button {
            grid-column: span 2;
            padding: 12px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: .3s;
        }
        form button:hover {
            background: #2980b9;
        }

        .back {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            background: #95a5a6;
            color: white;
            border-radius: 6px;
            text-decoration: none;
        }
        .back:hover { background: #7f8c8d; }

    </style>
</head>

<body>

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

    <p class="title-page">✏ Editar Adotante</p>

    <div class="card">

        <?php if ($mensagem): ?>
            <div class="mensagem"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <form method="post">

            <div>
                <label>Nome:</label>
                <input type="text" name="nome" value="<?= htmlspecialchars($adotante['nome']) ?>" required>
            </div>

            <div>
                <label>E-mail:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($adotante['email']) ?>" required>
            </div>

            <div>
                <label>Telefone:</label>
                <input type="text" name="telefone" value="<?= htmlspecialchars($adotante['telefone']) ?>">
            </div>

            <div>
                <label>Endereço:</label>
                <input type="text" name="endereco" value="<?= htmlspecialchars($adotante['endereco']) ?>">
            </div>

            <button type="submit">Salvar Alterações</button>
        </form>

        <a href="adotantes.php" class="back">⬅ Voltar</a>

    </div>

</div>

</body>
</html>
