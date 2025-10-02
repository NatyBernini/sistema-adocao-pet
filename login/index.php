<?php
    session_start();
    $erro = $_SESSION['erro_login'] ?? '';
    unset($_SESSION['erro_login']);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Login / Cadastro</title>
    <link rel="stylesheet" href="style/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Para esconder o container de cadastro inicialmente */
        #container-cadastro {
            display: none;
        }
    </style>
</head>

<body>
    <!-- CABEÇALHO -->
    <div class="logo-top-left">
        <img src="assets/iconeLogin.svg" alt="Logo">
        <span class="logo-text">pet-adote</span>
    </div>

    <!-- CONTAINER LOGIN -->
    <div class="container" id="container-login">
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
                    <input type="password" id="senha" name="senha" placeholder="********" required minlength="8" maxlength="20">
                    <span class="toggle-password" onclick="togglePassword()">🙉</span>
                </div>

                <button type="submit">Entrar</button>
            </form>
            <button type="button" id="btnMostrarCadastro" style="margin-top: 15px;">Cadastrar</button>
        </div>
    </div>

    <!-- CONTAINER CADASTRO -->
    <div class="container" id="container-cadastro">
        <p class="title-page">Cadastro de Usuário</p>
        <div class="container-form">
            <form method="POST" action="cadastro.php">
                <label>E-mail</label>
                <input type="email" name="email" placeholder="e-mail" required maxlength="100">

                <label>Senha</label>
                <input type="password" name="senha" placeholder="********" required minlength="8" maxlength="20">

                <label>Perfil</label>
                <select name="perfil" required>
                    <option value="">Selecione...</option>
                    <option value="user">Usuário</option>
                    <option value="admin">Administrador</option>
                </select>

                <button type="submit">Cadastrar</button>
            </form>
            <button type="button" id="btnVoltarLogin" style="margin-top: 15px;">Voltar ao Login</button>
        </div>
    </div>

    <!-- RODAPÉ -->
    <footer>
        &copy; <?= date('Y') ?> Maguila
    </footer>

    <script>
        // Mostrar / ocultar senha no login
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

        // Alternar containers login / cadastro
        document.getElementById('btnMostrarCadastro').addEventListener('click', function() {
            document.getElementById('container-login').style.display = 'none';
            document.getElementById('container-cadastro').style.display = 'flex';
        });

        document.getElementById('btnVoltarLogin').addEventListener('click', function() {
            document.getElementById('container-cadastro').style.display = 'none';
            document.getElementById('container-login').style.display = 'flex';
        });
    </script>

</body>

</html>
