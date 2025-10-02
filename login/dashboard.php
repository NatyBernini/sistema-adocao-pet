<?php
    $perfil_requerido = 'admin';
    require_once 'verifica_sessao.php';

    $tempo_expiracao = 20;

    // Calcula o tempo restante para expiração
    $ultima_atividade = $_SESSION['ultima_atividade'] ?? time();
    $tempo_restante = $tempo_expiracao - (time() - $ultima_atividade);
    if ($tempo_restante < 0) {
        $tempo_restante = 0;
    }
?>

<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Dashboard</title>
<link rel="stylesheet" href="style/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- CABEÇALHO -->
    <div class="logo-top-left">
        <img src="assets/iconeLogin.svg" alt="Logo">
        <span class="logo-text">pet-adote</span>
    </div>

    <div class="container">
        <p class='title-page'>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']) ?>!</p>
        <div class="container-form">
            <p>Seu perfil: <?= htmlspecialchars($_SESSION['perfil']) ?></p>
            
            <!-- Contador de sessão -->
            <p>Tempo para expirar a sessão: <span id="contador-expiracao"><?= $tempo_restante ?></span> segundos</p>
            
            <form action="logout.php" method="post">
                <button type="submit">Sair</button>
            </form>
        </div>
    </div>

    <!-- RODAPÉ -->
    <footer>
        &copy; <?= date('Y') ?> Maguila
    </footer>

    <script>
        // Pega o tempo restante passado pelo PHP
        let tempoRestante = <?= $tempo_restante ?>;

        const contadorElem = document.getElementById('contador-expiracao');

        // Atualiza o contador a cada segundo
        const intervalo = setInterval(() => {
            tempoRestante--;

            if (tempoRestante <= 0) {
                clearInterval(intervalo);
                // Redireciona para a página de login quando expirar
                alert('Sua sessão expirou por inatividade.');
                window.location.href = 'index.php';
            } else {
                contadorElem.textContent = tempoRestante;
            }
        }, 1000);
    </script>
</body>
</html>
