<?php
    session_start();
    $erro = $_SESSION['erro_login'] ?? '';
    unset($_SESSION['erro_login']);
?>

<!DOCTYPE html>
<html>

<head><meta charset="utf-8"><title>Login</title></head>

<body>
    <h2>Login</h2>
    <?php if ($erro): ?><p style="color:red;"><?= htmlspecialchars($erro) ?></p><?php endif; ?>
    <form method="POST" action="autentica.php">
        <label>Email:</label><br>
        <input type="email" name="email" placeholder="e-mail" required minlength="8" maxlength="100"><br><br>

        <label>Senha:</label><br>
        <input type="password" name="senha" placeholder="********" required minlength="8" maxlength="20"><br><br>

        <button type="submit">Entrar</button>
    </form>
</body>

</html>
