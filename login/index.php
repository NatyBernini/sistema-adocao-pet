<?php
  require_once 'conexao.php';
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
            <select name="perfil" id="perfil" required>
                <option value="">Selecione...</option>
                <option value="adotante">Adotante</option>
                <option value="admin">Administrador</option>
            </select>

            <!-- CAMPOS EXTRAS SOMENTE PARA ADOTANTE -->
            <div id="camposAdotante" style="display:none; margin-top: 15px;">

                <label>Nome</label>
                <input type="text" name="nome" placeholder="Seu nome completo">

                <label>Telefone</label>
                <input type="text" name="telefone" placeholder="(XX) XXXXX-XXXX">

                <label>Endereço</label>
                <input type="text" name="endereco" placeholder="Rua, número, bairro">

            </div>

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

    // MOSTRAR CAMPOS EXTRAS SOMENTE SE PERFIL = ADOTANTE
    document.getElementById('perfil').addEventListener('change', function () {
        const campos = document.getElementById('camposAdotante');
        campos.style.display = this.value === 'adotante' ? 'block' : 'none';
    });
</script>


</body>

</html>
