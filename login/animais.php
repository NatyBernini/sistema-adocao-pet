<?php
require_once 'conexao.php';

// Limitar ao perfil admin
$perfil_requerido = 'admin';
require_once 'verifica_sessao.php';


$mensagem = "";

/* ===========================
   CREATE - CADASTRAR (com foto, saude, descricao)
   ============================ */
if (isset($_POST['acao']) && $_POST['acao'] === 'cadastrar') {
    $nome = trim($_POST['nome']);
    $especie = trim($_POST['especie']);
    $raca = trim($_POST['raca']);
    $idade = isset($_POST['idade']) && $_POST['idade'] !== '' ? (int) $_POST['idade'] : null;
    $tutor_email = trim($_POST['tutor_email']);
    $castrado = isset($_POST['castrado']) ? 1 : 0;
    $vermifugado = isset($_POST['vermifugado']) ? 1 : 0;
    $vacinado = isset($_POST['vacinado']) ? 1 : 0;
    $historico = trim($_POST['historico']);
    $saude = trim($_POST['saude'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    // Handle file upload
    $foto_nome_final = null;
    if (!empty($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        $arquivo = $_FILES['foto'];
        if ($arquivo['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
            $permitidos = ['jpg','jpeg','png','webp'];
            if (!in_array($ext, $permitidos)) {
                $mensagem = "Formato de imagem inválido. Use: jpg, jpeg, png, webp.";
            } else {
                // cria pasta se não existir
                $uploadDir = __DIR__ . '/uploads/animais/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                // nome único
                $foto_nome_final = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                $destino = $uploadDir . $foto_nome_final;

                if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
                    $mensagem = "Falha ao salvar a foto no servidor.";
                }
            }
        } else {
            $mensagem = "Erro no upload da foto.";
        }
    }

    if (!$mensagem) {
        if ($nome && $especie) {
            $stmt = $pdo->prepare("
                INSERT INTO animais 
                (nome, especie, raca, idade, tutor_email, castrado, vermifugado, vacinado, historico, saude, descricao, foto)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $nome,
                $especie,
                $raca,
                $idade,
                $tutor_email,
                $castrado,
                $vermifugado,
                $vacinado,
                $historico,
                $saude,
                $descricao,
                $foto_nome_final
            ]);
            $mensagem = "Animal cadastrado com sucesso!";
        } else {
            $mensagem = "Preencha pelo menos o nome e a espécie.";
            // se houve upload e deu certo, mas formulário inválido, remover arquivo salvo
            if ($foto_nome_final) {
                @unlink(__DIR__ . '/uploads/animais/' . $foto_nome_final);
            }
        }
    } else {
        // caso tenha mensagem de erro de upload e tenha criado arquivo parcialmente, remova
        if (!empty($foto_nome_final) && file_exists(__DIR__ . '/uploads/animais/' . $foto_nome_final)) {
            @unlink(__DIR__ . '/uploads/animais/' . $foto_nome_final);
        }
    }
}

/* ===========================
   DELETE (remover foto também)
   ============================ */
if (isset($_GET['excluir'])) {
    $id = (int) $_GET['excluir'];

    // pegar nome da foto para apagar
    $stmtSel = $pdo->prepare("SELECT foto FROM animais WHERE id = ?");
    $stmtSel->execute([$id]);
    $row = $stmtSel->fetch(PDO::FETCH_ASSOC);
    if ($row && !empty($row['foto'])) {
        $path = __DIR__ . '/uploads/animais/' . $row['foto'];
        if (file_exists($path)) @unlink($path);
    }

    $pdo->prepare("DELETE FROM animais WHERE id = ?")->execute([$id]);
    header("Location: animais.php");
    exit;
}

/* ===========================
   BUSCA / FILTRO
   ============================ */
$busca = "";
$params = [];

if (!empty($_GET['nome'])) {
    $busca .= " AND nome LIKE ? ";
    $params[] = "%".$_GET['nome']."%";
}

if (!empty($_GET['especie'])) {
    $busca .= " AND especie LIKE ? ";
    $params[] = "%".$_GET['especie']."%";
}

if (!empty($_GET['raca'])) {
    $busca .= " AND raca LIKE ? ";
    $params[] = "%".$_GET['raca']."%";
}

if (isset($_GET['idade']) && $_GET['idade'] !== '') {
    $busca .= " AND idade = ? ";
    $params[] = (int) $_GET['idade'];
}

$sql = "SELECT * FROM animais WHERE 1 $busca ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
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

    /* CONTENT */
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
    form input, form textarea {
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
        width: 100%;
    }

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
        vertical-align: middle;
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

    .btn-add {
        padding: 12px 18px;
        background: #27ae60;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
    }

    /* modal básico (reaproveitado) */
   /* ===== MODAL RESPONSIVO ===== */
.modal {
    display: none;
    position: fixed;
    z-index: 2000;
    inset: 0;
    background: rgba(0, 0, 0, 0.55);
    display: none;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

/* Conteúdo do modal */
.modal-content {
    background: #ffffff;
    width: 100%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.20);
    position: relative;
    animation: fadeInUp .3s ease;
}

/* Animação */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Botão fechar */
.modal-content .close {
    position: absolute;
    right: 15px;
    top: 10px;
    font-size: 26px;
    cursor: pointer;
    color: #444;
    font-weight: bold;
}

.modal-content .close:hover {
    color: #000;
}

/* Layout dos inputs */
.modal-content label {
    display: block;
    margin-top: 12px;
    margin-bottom: 4px;
    font-weight: 600;
    color: #333;
}

.modal-content input,
.modal-content textarea,
.modal-content select {
    width: 100%;
    padding: 10px;
    font-size: 15px;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-sizing: border-box;
}

/* Foto */
.modal-content input[type="file"] {
    padding: 7px;
}

/* Botão */
.btn-submit {
    margin-top: 15px;
    padding: 12px;
    width: 100%;
    background: #3498db;
    border: none;
    color: #fff;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
    font-size: 15px;
}

.btn-submit:hover {
    background: #2980b9;
}

/* Ajuste mobile */
@media (max-width: 480px) {
    .modal-content {
        padding: 18px;
        max-width: 95%;
    }
}

  

    .thumb {
        width:60px;
        height:60px;
        object-fit:cover;
        border-radius:6px;
        border:1px solid #ddd;
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

<!-- CONTENT -->
<div class="content">

    <p class="title-page">🐾 Gerenciar Animais</p>

    <div class="card">

        <h3>Lista de Animais</h3>

        <!-- FORM DE BUSCA -->
        <form method="GET" style="margin-bottom: 20px; display:flex; gap:10px; flex-wrap:wrap;">
            <input type="text" name="nome" placeholder="Nome..." value="<?= htmlspecialchars($_GET['nome'] ?? '') ?>">
            <input type="text" name="especie" placeholder="Espécie..." value="<?= htmlspecialchars($_GET['especie'] ?? '') ?>">
            <input type="text" name="raca" placeholder="Raça..." value="<?= htmlspecialchars($_GET['raca'] ?? '') ?>">
            <input type="number" name="idade" placeholder="Idade..." value="<?= htmlspecialchars($_GET['idade'] ?? '') ?>">

            <button type="submit" style="padding:10px 15px; background:#3498db; border:none; border-radius:8px; color:white;">🔎 Buscar</button>

            <a href="animais.php" style="padding:10px 15px; background:#7f8c8d; border-radius:8px; color:white; text-decoration:none;">❌ Limpar</a>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Foto</th>
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
                <td>
                    <?php if (!empty($a['foto']) && file_exists(__DIR__.'/uploads/animais/'.$a['foto'])): ?>
                        <img src="uploads/animais/<?= htmlspecialchars($a['foto']) ?>" class="thumb" alt="foto">
                    <?php else: ?>
                        <span style="color:#7f8c8d; font-size:13px;">sem foto</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($a['nome']) ?></td>
                <td><?= htmlspecialchars($a['especie']) ?></td>
                <td><?= htmlspecialchars($a['raca']) ?></td>
                <td><?= $a['idade'] ?></td>
                <td><?= $a['castrado'] ? '✅' : '❌' ?></td>
                <td><?= $a['vermifugado'] ? '✅' : '❌' ?></td>
                <td><?= $a['vacinado'] ? '✅' : '❌' ?></td>
                <td>
                    <a class="action" href="animais_ver.php?id=<?= $a['id'] ?>">Ver</a> |
                    <a class="action" href="animais_editar.php?id=<?= $a['id'] ?>">Editar</a> |
                    <a class="action" href="?excluir=<?= $a['id'] ?>" onclick="return confirm('Excluir este animal?')">Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

    </div>

    <div class="card" style="margin-top:16px;">
        <?php if($mensagem): ?>
            <div class="mensagem"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <button class="btn-add" onclick="abrirModal()">➕ Cadastrar Novo Animal</button>
    </div>

</div>

<!-- MODAL -->
<!-- MODAL -->
<div id="modalCadastro" class="modal">
    <div class="modal-content">

        <span class="close" onclick="fecharModal()">&times;</span>

        <h2 style="margin-top: 0;">Cadastrar Novo Animal</h2>

        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="acao" value="cadastrar">

            <label>Foto do Animal:</label>
            <input type="file" name="foto" accept="image/*">

            <label>Nome:</label>
            <input type="text" name="nome" required>

            <label>Espécie:</label>
            <input type="text" name="especie" required>

            <label>Raça:</label>
            <input type="text" name="raca">

            <label>Idade:</label>
            <input type="number" name="idade">

            <label>Email do Tutor:</label>
            <input type="email" name="tutor_email">

            <label>Situação de Saúde:</label>
            <input type="text" name="saude">

            <label>Descrição:</label>
            <textarea name="descricao" rows="4"></textarea>

            <label>Histórico:</label>
            <textarea name="historico" rows="4"></textarea>

            <label>
                <input type="checkbox" name="castrado"> Castrado
            </label>
            <label>
                <input type="checkbox" name="vermifugado"> Vermifugado
            </label>
            <label>
                <input type="checkbox" name="vacinado"> Vacinado
            </label>

            <button class="btn-submit" type="submit">Cadastrar</button>
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
window.onclick = function(e) {
    const modal = document.getElementById("modalCadastro");
    if (e.target === modal) fecharModal();
}
</script>

</body>
</html>
