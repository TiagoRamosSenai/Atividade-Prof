<?php
session_start();
include('conexao.php');

// Obter o nome do professor
$nome_professor = $_SESSION['nome_professor'] ?? 'Professor';


// Verificar se o professor está logado
if (!isset($_SESSION['id_professor'])) {
    header('Location: login.php');
    exit();
}

// Processar o cadastro de turma
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_turma = $_POST['nome_turma'];

    // Verificar se o nome da turma foi preenchido
    if (!empty($nome_turma)) {
        // Inserir a turma no banco de dados
        $sql = "INSERT INTO turmas (nome, id_professor) VALUES (?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("si", $nome_turma, $_SESSION['id_professor']);
            $stmt->execute();
            $stmt->close();

            echo "<script>alert('Turma cadastrada com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao cadastrar turma.');</script>";
        }
    } else {
        echo "<script>alert('Por favor, insira o nome da turma.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Turma</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <div class="top-bar">
        <span>Nome do Professor: <?= htmlspecialchars($nome_professor); ?></span>
        <a href="sair.php" class="logout-button">Sair</a>
    </div>
    <h1>Cadastrar Turma</h1>
    <form method="POST" action="">
        <label for="nome_turma">Nome da Turma:</label>
        <input type="text" id="nome_turma" name="nome_turma" required><br><br>

        <input type="submit" value="Cadastrar">
    </form>
    <a href="turmas.php">Voltar para a lista de turmas</a>
</body>
</html>
