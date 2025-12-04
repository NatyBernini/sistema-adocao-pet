<?php
require_once 'verifica_sessao.php';

// Tempo de expiração da sessão
$tempo_expiracao = 60 * 60;
$ultima_atividade = $_SESSION['ultima_atividade'] ?? time();
$tempo_restante = $tempo_expiracao - (time() - $ultima_atividade);
if ($tempo_restante < 0) $tempo_restante = 0;

require_once 'conexao.php';

// Resumos para o dashboard
$total_animais = $pdo->query("SELECT COUNT(*) FROM animais")->fetchColumn();
$total_adotantes = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE perfil='adotante'")->fetchColumn();
$total_solicitacoes_pendentes = $pdo->query("SELECT COUNT(*) FROM solicitacoes_adocao WHERE status='pendente'")->fetchColumn();
$total_solicitacoes_usuario = 0;

// Solicitações do adotante logado
if ($_SESSION['perfil'] === 'adotante') {
    $email = $_SESSION['usuario'];

    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $usuario_id = $usuario['id'];

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM solicitacoes_adocao WHERE id_adotante = ?");
        $stmt->execute([$usuario_id]);
        $total_solicitacoes_usuario = $stmt->fetchColumn();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<title>Dashboard - Pet Adote</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        display: flex;
        background: #f4f6f9;
    }

    /* Sidebar */
    .sidebar {
        width: 260px;
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
    .sidebar img { width: 60px; }
    .sidebar h2 { font-size: 22px; margin-top: 10px; }

    .sidebar a {
        padding: 15px 20px;
        color: white;
        text-decoration: none;
        display: block;
        font-size: 15px;
        transition: .3s;
    }
    .sidebar a:hover {
        background: #34495e;
    }

    /* Conteúdo */
    .content {
        margin-left: 260px;
        padding: 35px;
        width: calc(100% - 260px);
    }
    .title-page {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 20px;
    }

    /* Cards */
    .cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        text-align: center;
    }
    .card h3 {
        margin: 0;
        font-size: 20px;
        color: #555;
    }
    .card p {
        margin: 10px 0 0;
        font-size: 32px;
        font-weight: bold;
        color: #3498db;
    }

    footer {
        margin-top: 40px;
        text-align: center;
        color: #777;
    }
</style>

</head>
<body>

<!-- MENU LATERAL -->
<div class="sidebar">
    <div class="logo">
        <img src="assets/iconeLogin.svg" alt="logo">
        <h2>Pet Adote</h2>
    </div>

    <a href="dashboard.php">🏠 Dashboard</a>

    <?php if($_SESSION['perfil'] === 'admin'): ?>
        <a href="animais.php">🐕 Animais</a>
        <a href="adotantes.php">👤 Adotantes</a>
        <a href="solicitacoes.php">📋 Solicitações</a>
    <?php endif; ?>

    <?php if($_SESSION['perfil'] === 'adotante'): ?>
        <a href="adocao.php">💛 Minhas Adoções</a>
    <?php endif; ?>

    <a href="#" id="logoutLink">🚪 Logout</a>

    <form id="logoutForm" action="logout.php" method="post" style="display: none;">
        <input type="hidden" name="logout" value="1">
    </form>
</div>

<!-- CONTEÚDO -->
<div class="content">
    <p class="title-page">Bem-vindo(a), <?= htmlspecialchars($_SESSION['usuario']) ?>!</p>

    <h3>Seu Perfil: <?= ucfirst($_SESSION['perfil']) ?></h3>

    <div class="cards">

        <?php if($_SESSION['perfil'] === 'admin'): ?>

            <div class="card">
                <h3>Total de Animais</h3>
                <p><?= $total_animais ?></p>
            </div>

            <div class="card">
                <h3>Total de Adotantes</h3>
                <p><?= $total_adotantes ?></p>
            </div>

            <div class="card" style="color:#e67e22;">
                <h3>Solicitações Pendentes</h3>
                <p><?= $total_solicitacoes_pendentes ?></p>
            </div>

        <?php endif; ?>

        <?php if($_SESSION['perfil'] === 'adotante'): ?>
            
            <div class="card">
                <h3>Suas Solicitações de Adoção</h3>
                <p><?= $total_solicitacoes_usuario ?></p>
            </div>

        <?php endif; ?>

    </div>

    <footer>&copy; <?= date('Y') ?> Maguila</footer>
</div>

<script>
    // Logout
    document.getElementById('logoutLink').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('logoutForm').submit();
    });

    // Contador de sessão
    let tempoRestante = <?= $tempo_restante ?>;
    setInterval(() => {
        tempoRestante--;
        if (tempoRestante <= 0) {
            alert('Sua sessão expirou.');
            window.location.href = 'index.php';
        }
    }, 1000);
</script>

</body>
</html>
