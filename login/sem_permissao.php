<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Acesso Negado</title>

<link rel="stylesheet" href="style/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"></head>
<body>
    <!-- CABEÇALHO -->
    <div class="logo-top-left">
        <img src="assets/iconeLogin.svg" alt="Logo">
        <span class="logo-text">pet-adote</span>
    </div>

    <div class="container">
    <h2>Acesso Negado</h2>
        <div class="container-form">    
    <p>Você não tem permissão para acessar esta página.</p>
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
