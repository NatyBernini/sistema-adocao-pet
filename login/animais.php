<?php
require_once 'conexao.php';
require_once 'verifica_sessao.php';

// Limitar ao perfil admin
$perfil_requerido = 'admin';
require 'verifica_sessao.php';

$mensagem = "";

// CREATE
if (isset($_POST['acao']) && $_POST['acao'] === 'cadastrar') {
    $nome = trim($_POST['nome']);
    $especie = trim($_POST['especie']);
    $raca = trim($_POST['raca']);
    $idade = (int) $_POST['idade'];
    $tutor_email = trim($_POST['tutor_email']);
    $castrado = isset($_POST['castrado']) ? 1 : 0;
    $vermifugado = isset($_POST['vermifugado']) ? 1 : 0;
    $vacinado = isset($_POST['vacinado']) ? 1 : 0;
    $historico = trim($_POST['historico']);

    if ($nome && $especie) {
        $stmt = $pdo->prepare("INSERT INTO animais 
            (nome, especie, raca, idade, tutor_email, castrado, vermifugado, vacinado, historico) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $especie, $raca, $idade, $tutor_email, $castrado, $vermifugado, $vacinado, $historico]);
        $mensagem = "Animal cadastrado com sucesso!";
    } else {
        $mensagem = "Preencha pelo menos o nome e a espécie.";
    }
}

// DELETE
if (isset($_GET['excluir'])) {
    $id = (int) $_GET['excluir'];
    $pdo->prepare("DELETE FROM animais WHERE id = ?")->execute([$id]);
    header("Location: animais.php");
    exit;
}

// READ
$stmt = $pdo->query("SELECT * FROM animais ORDER BY id DESC");
$animais = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<title>Animais - Pet Adote</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: #f6f8fa;
        display: flex;
    }

    /* SIDEBAR */
    .sidebar {
        width: 250px;
        background: #2c3e50;
        color: white;
        height: 100vh;
        position: fixed;
        padding: 20px 0;
        display: flex;
        flex-direction: column;
    }
    .sidebar .logo {
        text-align: center;
        margin-bottom: 40px;
    }
    .sidebar .logo img { width: 60px; }
    .sidebar .logo h2 { margin-top: 10px; font-size: 20px; font-weight: 600; }
    .sidebar a {
        padding: 15px 20px;
        display: block;
        text-decoration: none;
        color: #ecf0f1;
        font-size: 15px;
        transition: .3s;
    }
    .sidebar a:hover {
        background: #34495e;
    }

    /* CONTEÚDO */
    .content {
        margin-left: 250px;
        padding: 40px;
        width: calc(100% - 250px);
    }

    .title-page {
        font-size: 26px;
        font-weight: 600;
        color: #333;
    }

    .card {
        background: white;
        padding: 25px;
        border-radius: 10px;
        margin-top: 25px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .mensagem {
        padding: 12px;
        background: #d4edda;
        color: #155724;
        border-left: 5px solid #28a745;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    /* FORM */
    form {
        display: grid;
        grid-template-columns: repeat(2,1fr);
        gap: 15px;
    }
    form input, form textarea {
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
        width: 100%;
    }
    textarea {
        grid-column: span 2;
        resize: vertical;
    }
    form button {
        grid-column: span 2;
        padding: 12px;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: .3s;
    }
    form button:hover {
        background: #2980b9;
    }

    /* TABELA */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    table th {
        background: #3498db;
        color: white;
        padding: 12px;
        text-align: left;
        font-size: 15px;
    }
    table td {
        padding: 12px;
        background: white;
        border-bottom: 1px solid #eee;
    }
    table tr:hover td {
        background: #f0f8ff;
    }

    a.action {
        color: #3498db;
        text-decoration: none;
        font-weight: 500;
    }
    a.action:hover {
        text-decoration: underline;
    }
    /* BOTÃO */
.btn-add {
    padding: 12px 18px;
    background: #27ae60;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: .3s;
}
.btn-add:hover {
    background: #1f8c4d;
}

/* MODAL */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.55);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background: white;
    width: 500px;
    padding: 30px;
    border-radius: 10px;
    animation: fadeIn .3s;
}

.close {
    float: right;
    font-size: 28px;
    cursor: pointer;
}

.btn-submit {
    grid-column: span 2;
    padding: 12px;
    background: #3498db;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
}
.btn-submit:hover {
    background: #2980b9;
}

/* ANIMAÇÃO */
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(-10px);}
    to   {opacity: 1; transform: translateY(0);}
}

</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">
        <img src="assets/iconeLogin.svg" alt="logo">
        <h2>Pet Adote</h2>
    </div>

    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="animais.php">🐕 Animais</a>

    <?php if($_SESSION['perfil'] === 'admin'): ?>
        <a href="adotantes.php">👤 Adotantes</a>
        <a href="solicitacoes.php">📋 Solicitações</a>
    <?php endif; ?>

    <?php if($_SESSION['perfil'] === 'adotante'): ?>
        <a href="adocao.php">💛 Adoções</a>
    <?php endif; ?>

    <a href="logout.php">🚪 Logout</a>
</div>

<!-- CONTEÚDO -->
<div class="content">

    <p class="title-page">🐾 Gerenciar Animais</p>

 

    <div class="card" style="margin-top: 30px;">
        <h3 style="margin-bottom: 15px;">Lista de Animais</h3>

        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Espécie</th>
                <th>Raça</th>
                <th>Idade</th>
                <th>Castrado</th>
                <th>Vermifugado</th>
                <th>Vacinado</th>
                <th>Ações</th>
            </tr>

            <?php foreach ($animais as $a): ?>
            <tr>
                <td><?= $a['id'] ?></td>
                <td><?= htmlspecialchars($a['nome']) ?></td>
                <td><?= htmlspecialchars($a['especie']) ?></td>
                <td><?= htmlspecialchars($a['raca']) ?></td>
                <td><?= $a['idade'] ?></td>
                <td><?= $a['castrado'] ? '✅' : '❌' ?></td>
                <td><?= $a['vermifugado'] ? '✅' : '❌' ?></td>
                <td><?= $a['vacinado'] ? '✅' : '❌' ?></td>
                <td>
                    <a class="action" href="animais_editar.php?id=<?= $a['id'] ?>">Editar</a> |
                    <a class="action" href="?excluir=<?= $a['id'] ?>" onclick="return confirm('Excluir este animal?')">Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
  <div class="card">
    <?php if($mensagem): ?>
        <div class="mensagem"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <button class="btn-add" onclick="abrirModal()">➕ Cadastrar Novo Animal</button>
</div>

</div>

<!-- MODAL DE CADASTRO -->
<div id="modalCadastro" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fecharModal()">&times;</span>

        <h2>Cadastrar Novo Animal</h2>

        <form method="post">
            <input type="hidden" name="acao" value="cadastrar">

            <div><label>Nome:</label><input type="text" name="nome" required></div>
            <div><label>Espécie:</label><input type="text" name="especie" required></div>
            <div><label>Raça:</label><input type="text" name="raca"></div>
            <div><label>Idade:</label><input type="number" name="idade"></div>
            <div><label>Email do Tutor:</label><input type="email" name="tutor_email"></div>

            <div><label><input type="checkbox" name="castrado"> Castrado</label></div>
            <div><label><input type="checkbox" name="vermifugado"> Vermifugado</label></div>
            <div><label><input type="checkbox" name="vacinado"> Vacinado</label></div>

            <div>
                <label>Histórico:</label>
                <textarea name="historico" rows="3"></textarea>
            </div>

            <button type="submit" class="btn-submit">Salvar</button>
        </form>
    </div>
</div>

<script>
function abrirModal() {
    document.getElementById("modalCadastro").style.display = "flex";
}

function fecharModal() {
    document.getElementById("modalCadastro").style.display = "none";
}

window.onclick = function(event) {
    const modal = document.getElementById("modalCadastro");
    if (event.target === modal) {
        modal.style.display = "none";
    }
}
</script>

</body>
</html>
