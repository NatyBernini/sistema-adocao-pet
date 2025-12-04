<?php
require_once 'conexao.php';
require_once 'verifica_sessao.php';

if ($_SESSION['perfil'] !== 'admin') {
    die("Acesso negado.");
}

if (!isset($_GET['id'])) {
    die("Adotante não encontrado.");
}

$id = (int) $_GET['id'];

// Buscar adotante
$stmt = $pdo->prepare("SELECT * FROM adotantes WHERE id = ?");
$stmt->execute([$id]);
$adotante = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$adotante) {
    die("Adotante não encontrado.");
}

// Buscar histórico de solicitações
$sql = "
SELECT s.*, a.nome AS animal_nome
FROM solicitacoes_adocao s
JOIN animais a ON a.id = s.id_animal
WHERE s.id_adotante = ?
ORDER BY s.data_solicitacao DESC
";

$stmt2 = $pdo->prepare($sql);
$stmt2->execute([$id]);
$historico = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Adotante - Pet Adote</title>
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
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-top: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .info-box { margin-bottom: 15px; }
        .info-label { font-weight: bold; color: #555; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th {
            background: #3498db;
            color: white;
            padding: 10px;
            text-align: left;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            background: white;
        }

        .nenhum {
            padding: 10px;
            background: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            border-radius: 6px;
            margin-top: 10px;
        }

        .actions a {
            margin-right: 15px;
            text-decoration: none;
            font-weight: 600;
        }
        .voltar { color: #7f8c8d; }
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

    <p class="title-page">👤 Visualizar Adotante</p>

    <div class="card">
        <h2><?= htmlspecialchars($adotante['nome']) ?></h2>

        <div class="info-box">
            <span class="info-label">Email:</span> <?= htmlspecialchars($adotante['email']) ?>
        </div>

        <div class="info-box">
            <span class="info-label">Telefone:</span> <?= htmlspecialchars($adotante['telefone']) ?>
        </div>

        <div class="info-box">
            <span class="info-label">Endereço:</span> <?= htmlspecialchars($adotante['endereco']) ?>
        </div>

        <hr style="margin: 20px 0;">

        <h3>📋 Histórico de Solicitações de Adoção</h3>

        <?php if (count($historico) > 0): ?>
            <table>
                <tr>
                    <th>Animal</th>
                    <th>Status</th>
                    <th>Data</th>
                </tr>

                <?php foreach ($historico as $h): ?>
                    <tr>
                        <td><?= htmlspecialchars($h['animal_nome']) ?></td>
                        <td><?= ucfirst($h['status']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($h['data_solicitacao'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <div class="nenhum">Nenhuma solicitação encontrada.</div>
        <?php endif; ?>

        <div class="actions" style="margin-top: 20px;">
            <a class="voltar" href="adotantes.php">⬅ Voltar</a>
        </div>

    </div>
</div>

</body>
</html>
