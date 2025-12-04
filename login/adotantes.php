<?php
require_once 'conexao.php';
require_once 'verifica_sessao.php';

$mensagem = "";

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'cadastrar') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $endereco = trim($_POST['endereco']);

    if ($nome && $email) {
        $stmt = $pdo->prepare("INSERT INTO adotantes (nome, email, telefone, endereco) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $telefone, $endereco]);
        $mensagem = "Adotante cadastrado com sucesso!";
    } else {
        $mensagem = "Preencha pelo menos o nome e o e-mail.";
    }
}


// DELETE
if (isset($_GET['excluir'])) {
    $id = (int) $_GET['excluir'];
    $pdo->prepare("DELETE FROM adotantes WHERE id = ?")->execute([$id]);
    header("Location: adotantes.php");
    exit;
}

// READ
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

if ($busca !== '') {
    $stmt = $pdo->prepare("SELECT * FROM adotantes WHERE nome LIKE ? ORDER BY id DESC");
    $stmt->execute(["%$busca%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM adotantes ORDER BY id DESC");
}
$adotantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Adotantes</title>
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

        /* CARD */
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

        /* FORM */
        form {
            display: grid;
            grid-template-columns: repeat(2,1fr);
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

        /* TABELA */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th {
            background: #3498db;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 15px;
        }
        table td {
            padding: 12px;
            background: white;
            border-bottom: 1px solid #eee;
        }
        table tr:hover td {
            background: #f0f8ff;
        }

        .nenhum {
            padding: 15px;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            border-radius: 6px;
            margin-top: 15px;
        }

        a.action {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }
        a.action:hover {
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

<!-- CONTEÚDO -->
<div class="content">

    <p class="title-page">👤 Gerenciar Adotantes</p>

    <!-- <div class="card">

        <?php if(isset($mensagem) && $mensagem): ?>
            <div class="mensagem"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?> -->

        <!-- FORMULÁRIO -->
        <!-- <form method="post">
            <input type="hidden" name="acao" value="cadastrar">

            <div>
                <label>Nome:</label>
                <input type="text" name="nome" required>
            </div>

            <div>
                <label>E-mail:</label>
                <input type="email" name="email" required>
            </div>

            <div>
                <label>Telefone:</label>
                <input type="text" name="telefone" placeholder="(XX) XXXXX-XXXX">
            </div>

            <div>
                <label>Endereço:</label>
                <input type="text" name="endereco">
            </div>

            <button type="submit">Cadastrar Adotante</button>
        </form> -->
    <!-- </div> -->

    <!-- LISTAGEM -->
    <div class="card" style="margin-top: 30px;">

        <?php if(count($adotantes) > 0): ?>

            <h3 style="margin-bottom: 15px;">Lista de Adotantes</h3>
            <form method="get" style="margin-bottom: 20px; display:flex; gap:10px;">
    <input 
        type="text" 
        name="busca" 
        placeholder="Buscar por nome..." 
        value="<?= isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : '' ?>"
        style="flex:1; padding:10px; border-radius:6px; border:1px solid #ccc;"
    >

    <button 
        type="submit" 
        style="padding:10px 20px; background:#3498db; color:white; border:none; border-radius:6px; cursor:pointer;">
        Buscar
    </button>

    <a 
        href="adotantes.php"
        style="padding:10px 20px; background:#95a5a6; color:white; border-radius:6px; text-decoration:none;">
        Limpar
    </a>
</form>


            <table>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Endereço</th>
                    <th>Data Registro</th>
                    <th>Ações</th>
                </tr>

                <?php foreach ($adotantes as $a): ?>
                <tr>
                    <td><?= $a['id'] ?></td>
                    <td><?= htmlspecialchars($a['nome']) ?></td>
                    <td><?= htmlspecialchars($a['email']) ?></td>
                    <td><?= htmlspecialchars($a['telefone']) ?></td>
                    <td><?= htmlspecialchars($a['endereco']) ?></td>
                    <td><?= date('d/m/Y', strtotime($a['data_registro'])) ?></td>
                    <td>
                    <a class="action" href="adotantes_ver.php?id=<?= $a['id'] ?>">Ver</a> |
                    <a class="action" href="adotantes_editar.php?id=<?= $a['id'] ?>">Editar</a> |
                    <a class="action" href="?excluir=<?= $a['id'] ?>" onclick="return confirm('Excluir este adotante?')">Excluir</a>
                </td>
                </tr>
                <?php endforeach; ?>
            </table>

        <?php else: ?>

            <div class="nenhum">Nenhum adotante cadastrado ainda.</div>

        <?php endif; ?>
    </div>

</div>

</body>
</html>

