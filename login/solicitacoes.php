<?php
require_once 'conexao.php';
require_once 'verifica_sessao.php';

// === PERMISSÃO DE ACESSO (admin OU ong) ===
if (!in_array($_SESSION['perfil'], ['admin', 'ong'])) {
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

// === FILTRO DE STATUS ===
$statusFiltro = $_GET['status'] ?? 'todas';

$where = "";
$params = [];

if ($statusFiltro !== 'todas') {
    $where = "WHERE s.status = ?";
    $params[] = $statusFiltro;
}

// Consulta principal
$stmt = $pdo->prepare("
    SELECT s.id, s.data_solicitacao, s.status, 
           a.nome AS adotante_nome, 
           an.nome AS animal_nome
    FROM solicitacoes_adocao s
    JOIN adotantes a ON s.id_adotante = a.id
    JOIN animais an ON s.id_animal = an.id
    $where
    ORDER BY s.data_solicitacao DESC
");

$stmt->execute($params);
$solicitacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Solicitações - Pet Adote</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/main.css">

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
.sidebar a:hover { background: #34495e; }

.sidebar a.active {
    background: #1abc9c;
    font-weight: bold;
    color: white;
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

/* SELECT */
.select-status {
    padding: 10px 14px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
    font-family: 'Poppins', sans-serif;
    background: #fff;
    appearance: none;
    cursor: pointer;
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
}
table td {
    padding: 12px;
    background: white;
    border-bottom: 1px solid #eee;
}
table tr:hover td { background: #f0f8ff; }

a.action {
    color: #3498db;
    text-decoration: none;
    font-weight: 500;
}
a.action:hover { text-decoration: underline; }

/* FOOTER */
footer {
    margin-top: 40px;
    text-align: center;
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
    <a href="animais.php">🐕 Animais</a>
    <a href="adotantes.php">👤 Adotantes</a>
    <a class="active" href="solicitacoes.php">📋 Solicitações</a>

    <a href="#" id="logoutLink">🚪 Logout</a>

    <form id="logoutForm" action="logout.php" method="post" style="display:none;">
        <input type="hidden" name="logout" value="1">
    </form>
</div>

<!-- CONTEÚDO -->
<div class="content">

    <p class="title-page">📋 Solicitações de Adoção</p>

    <!-- FILTRO -->
    <form method="GET" style="margin-bottom: 20px;">
        <label for="status" style="font-weight: 600; margin-right: 10px;">Filtrar por status:</label>

        <select name="status" id="status" class="select-status" onchange="this.form.submit()">
            <option value="todas" <?= $statusFiltro === 'todas' ? 'selected' : '' ?>>Todas</option>
            <option value="pendente" <?= $statusFiltro === 'pendente' ? 'selected' : '' ?>>Pendentes</option>
            <option value="aceita" <?= $statusFiltro === 'aceita' ? 'selected' : '' ?>>Aceitas</option>
            <option value="rejeitada" <?= $statusFiltro === 'rejeitada' ? 'selected' : '' ?>>Rejeitadas</option>
        </select>
    </form>

    <?php if(count($solicitacoes) === 0): ?>
        <p>Nenhuma solicitação encontrada.</p>
    <?php else: ?>

    <div class="card">
        <h3 style="margin-bottom: 15px;">Lista de Solicitações</h3>

        <table>
            <tr>
                <th>ID</th>
                <th>Adotante</th>
                <th>Animal</th>
                <th>Data</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>

            <?php foreach ($solicitacoes as $s): ?>
            <tr>
                <td><?= $s['id'] ?></td>
                <td><?= htmlspecialchars($s['adotante_nome']) ?></td>
                <td><?= htmlspecialchars($s['animal_nome']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($s['data_solicitacao'])) ?></td>

                <td>
                    <?php
                        if($s['status'] === 'pendente') echo "<span style='color:#f39c12;font-weight:600;'>Pendente</span>";
                        if($s['status'] === 'aceita') echo "<span style='color:#27ae60;font-weight:600;'>Aceita</span>";
                        if($s['status'] === 'rejeitada') echo "<span style='color:#c0392b;font-weight:600;'>Rejeitada</span>";
                    ?>
                </td>

                <td>
                    <?php if($s['status'] === 'pendente'): ?>
                        <a class="action" href="?acao=aceitar&id=<?= $s['id'] ?>">Aceitar</a> |
                        <a class="action" href="?acao=rejeitar&id=<?= $s['id'] ?>" onclick="return confirm('Deseja realmente rejeitar?')">Rejeitar</a>
                    <?php else: ?>
                        <span style="color:#7f8c8d;">Sem ações</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <?php endif; ?>

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
