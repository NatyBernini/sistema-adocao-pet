<?php
require_once 'conexao.php';
require_once 'verifica_sessao.php';

if ($_SESSION['perfil'] !== 'adotante') {
    die("Acesso negado.");
}

$mensagem = "";

if (isset($_GET['adotar'])) {
    $id_animal = (int) $_GET['adotar'];
    $id_adotante = $_SESSION['id_adotante'];

    $stmt = $pdo->prepare("SELECT * FROM solicitacoes_adocao 
                           WHERE id_adotante = ? AND id_animal = ? AND status = 'pendente'");
    $stmt->execute([$id_adotante, $id_animal]);

    if ($stmt->rowCount() > 0) {
        $mensagem = "Você já enviou uma solicitação para este animal.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO solicitacoes_adocao (id_adotante, id_animal) VALUES (?, ?)");
        $stmt->execute([$id_adotante, $id_animal]);
        $mensagem = "Solicitação enviada com sucesso!";
    }
}

$stmt = $pdo->query("
    SELECT a.*, s.id AS solicitacao_id 
    FROM animais a
    LEFT JOIN solicitacoes_adocao s ON a.id = s.id_animal AND s.status = 'aceita'
    WHERE s.id IS NULL
    ORDER BY a.id DESC
");
$animais = $stmt->fetchAll(PDO::FETCH_ASSOC);

$id_adotante = $_SESSION['id_adotante'];

$stmt2 = $pdo->prepare("
    SELECT sa.*, a.nome AS nome_animal, a.especie, a.raca, a.idade
    FROM solicitacoes_adocao sa
    INNER JOIN animais a ON sa.id_animal = a.id
    WHERE sa.id_adotante = ?
    ORDER BY sa.data_solicitacao DESC
");
$stmt2->execute([$id_adotante]);
$solicitacoes = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Adoções - Pet Adote</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/main.css">

    <style>
        .content h3 {
            margin-bottom: 12px;
            font-size: 20px;
            color: #444;
        }

        .card-section {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            margin-bottom: 35px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .table th {
            background: #f1f1f1;
            padding: 10px;
            text-align: left;
            font-weight: 600;
        }

        .table td {
            padding: 10px;
            border-bottom: 1px solid #e4e4e4;
        }

        .table tr:hover {
            background: #fafafa;
        }

        .btn-small {
            padding: 6px 12px;
            border-radius: 6px;
            background: #ffbb00;
            color: #333;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-small:hover {
            background: #e0a800;
        }

        .alert {
            padding: 12px;
            background: #fff3cd;
            border-left: 4px solid #ffcc00;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        footer {
            margin-top: 30px;
            text-align: center;
            font-size: 13px;
            color: #777;
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="logo">
            <img src="assets/iconeLogin.svg" alt="Logo">
            <h2>Pet Adote</h2>
        </div>

        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="adocao.php" class="active">💛 Adoções</a>
        <a href="#" id="logoutLink">🚪 Logout</a>

        <form id="logoutForm" action="logout.php" method="post" style="display: none;">
            <input type="hidden" name="logout" value="1">
        </form>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <p class="title-page">Adoção de Animais</p>

        <?php if ($mensagem): ?>
            <div class="alert"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <!-- ANIMAIS DISPONÍVEIS -->
        <div class="card-section">
            <h3>Animais Disponíveis para Adoção</h3>

            <table class="table">
                <tr>
                    <th>Nome</th>
                    <th>Espécie</th>
                    <th>Raça</th>
                    <th>Idade</th>
                    <th>Ações</th>
                </tr>

                <?php foreach($animais as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['nome']) ?></td>
                    <td><?= htmlspecialchars($a['especie']) ?></td>
                    <td><?= htmlspecialchars($a['raca']) ?></td>
                    <td><?= $a['idade'] ?></td>
                    <td>
                        <a class="btn-small" 
                           href="?adotar=<?= $a['id'] ?>"
                           onclick="return confirm('Deseja enviar solicitação de adoção?')">
                           💛 Solicitar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>

            </table>
        </div>

        <!-- MINHAS SOLICITAÇÕES -->
        <div class="card-section">
            <h3>Minhas Solicitações</h3>

            <?php if(count($solicitacoes) === 0): ?>
                <p>Você ainda não enviou nenhuma solicitação.</p>
            <?php else: ?>

            <table class="table">
                <tr>
                    <th>Animal</th>
                    <th>Espécie</th>
                    <th>Raça</th>
                    <th>Idade</th>
                    <th>Status</th>
                    <th>Data</th>
                </tr>

                <?php foreach($solicitacoes as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['nome_animal']) ?></td>
                    <td><?= htmlspecialchars($s['especie']) ?></td>
                    <td><?= htmlspecialchars($s['raca']) ?></td>
                    <td><?= $s['idade'] ?></td>
                    <td><strong><?= ucfirst($s['status']) ?></strong></td>
                    <td><?= date('d/m/Y H:i', strtotime($s['data_solicitacao'])) ?></td>
                </tr>
                <?php endforeach; ?>

            </table>

            <?php endif; ?>
        </div>

        <footer>&copy; <?= date('Y') ?> Maguila</footer>
    </div>

    <script>
        document.getElementById('logoutLink').addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('logoutForm').submit();
        });
    </script>

</body>
</html>
