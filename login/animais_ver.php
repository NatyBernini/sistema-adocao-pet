<?php
require_once 'conexao.php';
require_once 'verifica_sessao.php';

// Só admins podem ver esta página
if ($_SESSION['perfil'] !== 'admin') {
    die("Acesso negado.");
}

// Verifica se o ID foi passado
if (!isset($_GET['id'])) {
    die("Animal não encontrado.");
}

$id = (int) $_GET['id'];

// Buscar dados do animal
$stmt = $pdo->prepare("SELECT * FROM animais WHERE id = ?");
$stmt->execute([$id]);
$animal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$animal) {
    die("Animal não encontrado.");
}

// Buscar histórico de solicitações
$stmt2 = $pdo->prepare("
    SELECT s.*, a.nome AS adotante_nome
    FROM solicitacoes_adocao s
    JOIN adotantes a ON a.id = s.id_adotante
    WHERE s.id_animal = ?
    ORDER BY s.data_solicitacao DESC
");
$stmt2->execute([$id]);
$historico = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// caminho da foto (se existir)
$foto_path = null;
if (!empty($animal['foto'])) {
    $possible = __DIR__ . '/uploads/animais/' . $animal['foto'];
    if (file_exists($possible)) {
        $foto_path = 'uploads/animais/' . $animal['foto'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Animal - Pet Adote</title>
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
        .sidebar .logo { text-align: center; margin-bottom: 40px; }
        .sidebar .logo img { width: 60px; }
        .sidebar .logo h2 { margin-top: 10px; font-size: 20px; font-weight: 600; }
        .sidebar a { padding: 15px 20px; display: block; text-decoration: none; color: #ecf0f1; font-size: 15px; transition: .3s; }
        .sidebar a:hover { background: #34495e; }

        /* CONTEÚDO */
        .content {
            margin-left: 250px;
            padding: 40px;
            width: calc(100% - 250px);
        }

        .title-page { font-size: 26px; font-weight: 600; color: #333; }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-top: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .flex-row { display:flex; gap:20px; align-items:flex-start; }
        .col-left { width: 220px; }
        .col-right { flex:1; }

        .thumb-large {
            width: 220px;
            height: 220px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e6e6e6;
        }

        .info-box { margin-bottom: 12px; }
        .info-label { font-weight: 700; color: #555; margin-right: 6px; display:inline-block; min-width:130px; }

        .meta { color: #7f8c8d; font-size: 14px; margin-bottom:8px; }

        .actions a { margin-right: 15px; text-decoration: none; font-weight: 600; }
        .voltar { color: #7f8c8d; }
        .editar { color: #2980b9; }
        .excluir { color: #c0392b; }
        .actions a:hover { text-decoration: underline; }

        table { width:100%; border-collapse: collapse; margin-top: 15px; }
        table th, table td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        table th { background:#3498db; color:#fff; }

        .status-pendente { color: #e67e22; font-weight: 700; }
        .status-aceita { color: #27ae60; font-weight: 700; }
        .status-rejeitada { color: #c0392b; font-weight: 700; }

        pre.descricao { background:#f7f9fb; padding:12px; border-radius:6px; white-space:pre-wrap; }
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

    <p class="title-page">🐾 Visualizar Animal</p>

    <div class="card">
        <div class="flex-row">
            <div class="col-left">
                <?php if ($foto_path): ?>
                    <img src="<?= htmlspecialchars($foto_path) ?>" alt="Foto de <?= htmlspecialchars($animal['nome']) ?>" class="thumb-large">
                <?php else: ?>
                    <div style="width:220px;height:220px;border-radius:8px;border:1px dashed #ddd;display:flex;align-items:center;justify-content:center;color:#7f8c8d;">
                        Sem foto
                    </div>
                <?php endif; ?>
                <div class="meta" style="margin-top:10px;">
                    ID: <?= $animal['id'] ?><br>
                    Cadastrado: <?= !empty($animal['criado_em']) ? date('d/m/Y H:i', strtotime($animal['criado_em'])) : '—' ?>
                </div>
            </div>

            <div class="col-right">
                <h2 style="margin-top:0;"><?= htmlspecialchars($animal['nome']) ?></h2>

                <div class="info-box"><span class="info-label">Espécie:</span> <?= htmlspecialchars($animal['especie'] ?: '—') ?></div>
                <div class="info-box"><span class="info-label">Raça:</span> <?= htmlspecialchars($animal['raca'] ?: '—') ?></div>
                <div class="info-box"><span class="info-label">Idade:</span> <?= ($animal['idade'] !== null && $animal['idade'] !== '') ? htmlspecialchars($animal['idade']) . ' anos' : '—' ?></div>
                <div class="info-box"><span class="info-label">Tutor (e-mail):</span> <?= htmlspecialchars($animal['tutor_email'] ?: '—') ?></div>

                <div class="info-box"><span class="info-label">Castrado:</span> <?= $animal['castrado'] ? 'Sim' : 'Não' ?></div>
                <div class="info-box"><span class="info-label">Vermifugado:</span> <?= $animal['vermifugado'] ? 'Sim' : 'Não' ?></div>
                <div class="info-box"><span class="info-label">Vacinado:</span> <?= $animal['vacinado'] ? 'Sim' : 'Não' ?></div>

                <div class="info-box"><span class="info-label">Situação de Saúde:</span> <?= htmlspecialchars($animal['saude'] ?: '—') ?></div>

                <div class="info-box">
                    <span class="info-label">Descrição:</span>
                    <?php if (!empty($animal['descricao'])): ?>
                        <pre class="descricao"><?= htmlspecialchars($animal['descricao']) ?></pre>
                    <?php else: ?>
                        <span>—</span>
                    <?php endif; ?>
                </div>

                <div class="info-box">
                    <span class="info-label">Histórico:</span>
                    <?php if (!empty($animal['historico'])): ?>
                        <div style="white-space:pre-wrap; color:#444;"><?= nl2br(htmlspecialchars($animal['historico'])) ?></div>
                    <?php else: ?>
                        <span>—</span>
                    <?php endif; ?>
                </div>

                <div class="actions" style="margin-top:14px;">
                    <a class="voltar" href="animais.php">⬅ Voltar</a>
                    <a class="editar" href="animais_editar.php?id=<?= $animal['id'] ?>">✏ Editar</a>
                    <a class="excluir" href="animais.php?excluir=<?= $animal['id'] ?>" onclick="return confirm('Excluir este animal?')">🗑 Excluir</a>
                </div>
            </div>
        </div>
    </div>

    <!-- HISTÓRICO DE ADOÇÕES -->
    <div class="card">
        <h2 style="margin-top:0;">📜 Histórico de Solicitações de Adoção</h2>

        <?php if (count($historico) === 0): ?>
            <p>Este animal ainda não recebeu solicitações.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Adotante</th>
                        <th>Status</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historico as $h): ?>
                        <?php
                            $statusClass = 'status-pendente';
                            if ($h['status'] === 'aceita') $statusClass = 'status-aceita';
                            if ($h['status'] === 'rejeitada') $statusClass = 'status-rejeitada';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($h['adotante_nome']) ?></td>
                            <td><span class="<?= $statusClass ?>"><?= ucfirst($h['status']) ?></span></td>
                            <td><?= !empty($h['data_solicitacao']) ? date('d/m/Y H:i', strtotime($h['data_solicitacao'])) : '—' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
