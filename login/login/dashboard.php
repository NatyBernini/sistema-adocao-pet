<?php
    $perfil_requerido = 'admin'; // Troque conforme necessário
    require_once 'verifica_sessao.php';
?>

<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Dashboard</title></head>
<body>
    <h1>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']) ?>!</h1>
    <p>Seu perfil: <?= htmlspecialchars($_SESSION['perfil']) ?></p>
    <p><a href="logout.php">Sair</a></p>
</body>
</html>
