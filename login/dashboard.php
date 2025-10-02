<?php
    $perfil_requerido = 'admin'; // Troque conforme necessário
    require_once 'verifica_sessao.php';
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
        <p class='title-page'>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']) ?>!</p class='title-page'>
        <div class="container-form">    
            <p>Seu perfil: <?= htmlspecialchars($_SESSION['perfil']) ?></p>
            <form action="logout.php" method="post">
                <button type="submit">Sair</button>
            </form>
        </div>

    </div>
    <!-- RODAPÉ -->
    <footer>
        &copy; <?= date('Y') ?> Maguila
    </footer>
</body>
</html>
