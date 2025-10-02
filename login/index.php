<?php
    session_start();
    $erro = $_SESSION['erro_login'] ?? '';
    unset($_SESSION['erro_login']);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Login</title>
    <link rel="stylesheet" href="style/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- CABEÇALHO -->
    <div class="logo-top-left">
        <img src="assets/iconeLogin.svg" alt="Logo">
        <span class="logo-text">pet-adote</span>
    </div>

    <!-- FORMULÁRIO -->
    <div class="container">
        <p class="title-page">Bem-vindo de volta AUmigo</p>
        <?php if ($erro): ?>
        <p><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>
        <div class="container-form">
            <form method="POST" action="autentica.php">
                <label>E-mail</label>
                <input type="email" name="email" placeholder="e-mail" required minlength="8" maxlength="100">

                <label>Senha</label>
                <div class="password-wrapper">
                    <input type="password" id="senha" name="senha" placeholder="********" required minlength="8"
                        maxlength="20">
                    <span class="toggle-password" onclick="togglePassword()">🙉</span>
                </div>
                <button type="submit">Entrar</button>
            </form>
        </div>
    </div>

    <!-- RODAPÉ -->
    <footer>
        &copy; <?= date('Y') ?> Maguila
    </footer>

    <script>
        // Função para mostrar ou ocultar a senha
        function togglePassword() {
            const input = document.getElementById('senha');
            const toggle = document.querySelector('.toggle-password');
            if (input.type === "password") {
                input.type = "text";
                toggle.textContent = "🙈";
            } else {
                input.type = "password";
                toggle.textContent = "🙉";
            }
        }
    </script>

</body>

</html>