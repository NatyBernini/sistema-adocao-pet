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
        #container-cadastro {
            display: none;
        }

        input[readonly] {
            background: #f1f1f1;
        }
        .display-flex {
            display: flex;
        }
        
    </style>
</head>

<body>
    <!-- CABEÇALHO -->
    <div class="logo-top-left">
        <img src="assets/iconeLogin.svg" alt="Logo">
        <span class="logo-text">pet-adote</span>
    </div>

    <!-- LOGIN -->
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

    <!-- CADASTRO -->
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

                <!-- CAMPOS EXTRAS ADOTANTE -->
                <div id="camposAdotante" style="display:none; margin-top: 20px;">

                    <label>Nome</label>
                    <input type="text" name="nome" placeholder="Seu nome completo">

                    <div class="display-flex">
<label>Telefone</label>
                    <input type="text" name="telefone" placeholder="(XX) XXXXX-XXXX">

                    <label>CEP</label>
                    <input type="text" name="cep" id="cep" placeholder="00000-000">
                    </div>
                    

                    <div class="display-flex">
                    <label>Logradouro</label>
                    <input type="text" name="logradouro" id="logradouro" readonly>

                    <label>Bairro</label>
                    <input type="text" name="bairro" id="bairro" readonly>
</div>

                    <div class="display-flex">
                    <label>Cidade</label>
                    <input type="text" name="cidade" id="cidade" readonly>
                    
                    <label>UF</label>
                    <input type="text" name="uf" id="uf" readonly>
    </div>

                    <div class="display-flex">
                    <label>Número</label>
                    <input type="text" name="numero" placeholder="Número da residência">

                    <label>Complemento</label>
                    <input type="text" name="complemento" placeholder="Complemento (opcional)">
                    </div>
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

    <!-- JAVASCRIPT -->
    <script>
        // Mostrar / ocultar senha
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

        // Alternar containers
        document.getElementById('btnMostrarCadastro').addEventListener('click', function() {
            document.getElementById('container-login').style.display = 'none';
            document.getElementById('container-cadastro').style.display = 'flex';
        });

        document.getElementById('btnVoltarLogin').addEventListener('click', function() {
            document.getElementById('container-login').style.display = 'flex';
            document.getElementById('container-cadastro').style.display = 'none';
        });

        // Mostrar campos extras se perfil = adotante
        document.getElementById('perfil').addEventListener('change', function() {
            const campos = document.getElementById('camposAdotante');
            campos.style.display = this.value === 'adotante' ? 'block' : 'none';
        });

        // VIA CEP
        document.getElementById('cep').addEventListener('blur', async function() {
            const cep = this.value.replace(/\D/g, "");

            if (cep.length !== 8) {
                alert("CEP inválido!");
                return;
            }

            try {
                const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const data = await response.json();

                if (data.erro) {
                    alert("CEP não encontrado!");
                    return;
                }

                document.getElementById('logradouro').value = data.logradouro;
                document.getElementById('bairro').value = data.bairro;
                document.getElementById('cidade').value = data.localidade;
                document.getElementById('uf').value = data.uf;

            } catch (error) {
                alert("Erro ao consultar o CEP.");
            }
        });
    </script>

</body>

</html>
