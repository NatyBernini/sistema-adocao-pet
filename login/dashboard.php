<?php
require_once 'verifica_sessao.php';

// Tempo de expiração da sessão
$tempo_expiracao = 60 * 60;
$ultima_atividade = $_SESSION['ultima_atividade'] ?? time();
$tempo_restante = $tempo_expiracao - (time() - $ultima_atividade);
if ($tempo_restante < 0) $tempo_restante = 0;

// Conexão com banco de dados
require_once 'conexao.php';

// Resumos para o dashboard
$total_animais = $pdo->query("SELECT COUNT(*) FROM animais")->fetchColumn();
$total_adotantes = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE perfil='adotante'")->fetchColumn();
$total_solicitacoes_pendentes = $pdo->query("SELECT COUNT(*) FROM solicitacoes_adocao WHERE status='pendente'")->fetchColumn();
$total_solicitacoes_usuario = 0;

// Se o usuário for adotante, pegar suas solicitações
if ($_SESSION['perfil'] === 'adotante') {
    $email = $_SESSION['usuario'];

    // Pegar o id do usuário logado
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $usuario_id = $usuario['id'];

        // Contar solicitações do adotante
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
    <link rel="stylesheet" href="style/main.css">

</head>
<body>

    <!-- MENU LATERAL -->
    <div class="sidebar">
        <div class="logo">
            <img src="assets/iconeLogin.svg" alt="Logo">
            <h2>Pet Adote</h2>
        </div>

        <a href="dashboard.php">🏠 Dashboard</a>
       
        <?php if($_SESSION['perfil'] === 'admin'): ?>
            <a href="animais.php">🐕 Animais</a>
            <a href="adotantes.php">👤 Adotantes</a>
            <a href="solicitacoes.php">📋 Solicitações</a>
        <?php endif; ?>
        
        <?php if($_SESSION['perfil'] === 'adotante'): ?>
            <a href="adocao.php">💛 Adoções</a>
        <?php endif; ?>
        
        <a href="#" id="logoutLink">🚪 Logout</a>

        <form id="logoutForm" action="logout.php" method="post" style="display: none;">
            <input type="hidden" name="logout" value="1">
        </form>
    </div>

    <!-- CONTEÚDO PRINCIPAL -->
    <div class="content">
        <p class="title-page">Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']) ?>!</p>

        <div class="container-form">
            <p><strong>Perfil:</strong> <?= htmlspecialchars($_SESSION['perfil']) ?></p>
        </div>

        <?php if($_SESSION['perfil'] === 'admin'): ?>
            <div class="container-form">
                <p><strong>Total de Animais:</strong> <?= $total_animais ?></p>
                <p><strong>Total de Adotantes:</strong> <?= $total_adotantes ?></p>
                <p><strong>Solicitações Pendentes:</strong> <?= $total_solicitacoes_pendentes ?></p>
            </div>
        <?php endif; ?>

        <?php if($_SESSION['perfil'] === 'adotante'): ?>
            <div class="container-form">
                <p><strong>Suas solicitações de adoção:</strong> <?= $total_solicitacoes_usuario ?></p>
            </div>
        <?php endif; ?>

        <footer>&copy; <?= date('Y') ?> Maguila</footer>
    </div>

    <script>
        // Faz o link de logout enviar o formulário escondido
        document.getElementById('logoutLink').addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('logoutForm').submit();
        });

        // Contador de sessão
        let tempoRestante = <?= $tempo_restante ?>;
        const contadorElem = document.getElementById('contador-expiracao');
        const intervalo = setInterval(() => {
            tempoRestante--;
            if (tempoRestante <= 0) {
                clearInterval(intervalo);
                alert('Sua sessão expirou por inatividade.');
                window.location.href = 'index.php';
            } else {
                contadorElem.textContent = tempoRestante;
            }
        }, 1000);
    </script>

</body>
</html>
