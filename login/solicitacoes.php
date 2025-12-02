<?php
require_once 'conexao.php';
require_once 'verifica_sessao.php';

// Limitar ao perfil admin
if ($_SESSION['perfil'] !== 'admin') {
    die("Acesso negado.");
}

// Aceitar ou rejeitar solicitação
if (isset($_GET['acao'], $_GET['id'])) {
    $id = (int) $_GET['id'];

    if ($_GET['acao'] === 'aceitar') {
        $pdo->prepare("UPDATE solicitacoes_adocao SET status='aceita' WHERE id=?")->execute([$id]);
    } elseif ($_GET['acao'] === 'rejeitar') {
        $pdo->prepare("UPDATE solicitacoes_adocao SET status='rejeitada' WHERE id=?")->execute([$id]);
    }
}

// Lista solicitações pendentes
$stmt = $pdo->query("
    SELECT s.id, s.data_solicitacao, a.nome AS adotante_nome, an.nome AS animal_nome
    FROM solicitacoes_adocao s
    JOIN adotantes a ON s.id_adotante = a.id
    JOIN animais an ON s.id_animal = an.id
    WHERE s.status = 'pendente'
    ORDER BY s.data_solicitacao DESC
");
$solicitacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Solicitações - Pet Adote</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/main.css">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">
        <img src="assets/iconeLogin.svg" alt="Logo">
        <h2>Pet Adote</h2>
    </div>

    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="animais.php">🐕 Animais</a>
    <a href="adotantes.php">👤 Adotantes</a>
    <a class="active" href="solicitacoes.php">📋 Solicitações</a>

    <a href="#" id="logoutLink">🚪 Logout</a>

    <form id="logoutForm" action="logout.php" method="post" style="display: none;">
        <input type="hidden" name="logout" value="1">
    </form>
</div>

<!-- CONTEÚDO -->
<div class="content">

    <p class="title-page">📋 Solicitações de Adoção Pendentes</p>

    <div class="container-form">
        <?php if(count($solicitacoes) === 0): ?>
            <p>Nenhuma solicitação pendente.</p>
        <?php else: ?>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Adotante</th>
                    <th>Animal</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($solicitacoes as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['adotante_nome']) ?></td>
                    <td><?= htmlspecialchars($s['animal_nome']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($s['data_solicitacao'])) ?></td>
                    <td>
                        <a class="btn-aceitar" href="?acao=aceitar&id=<?= $s['id'] ?>">Aceitar</a>
                        <a class="btn-rejeitar" href="?acao=rejeitar&id=<?= $s['id'] ?>" onclick="return confirm('Deseja realmente rejeitar?')">Rejeitar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <footer>&copy; <?= date('Y') ?> Maguila</footer>
</div>

<script>
    document.getElementById('logoutLink').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('logoutForm').submit();
    });
</script>

</body>
</html>
