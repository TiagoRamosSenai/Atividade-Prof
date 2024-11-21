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

// Verificar se o ID da turma foi passado
if (!isset($_GET['id_turma'])) {
    echo "ID da turma não especificado.";
    exit();
}

$id_turma = $_GET['id_turma'];

// Obter o nome da turma
$sql = "SELECT nome FROM turmas WHERE id = ? AND id_professor = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_turma, $_SESSION['id_professor']);
$stmt->execute();
$stmt->bind_result($nome_turma);
$stmt->fetch();
$stmt->close();

// Verificar se a turma pertence ao professor
if (!$nome_turma) {
    echo "Turma não encontrada ou não pertence a você.";
    exit();
}

// Processar o cadastro de nova atividade
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['descricao'])) {
    $descricao_atividade = $_POST['descricao'];

    if (!empty($descricao_atividade)) {
        $sql = "INSERT INTO atividades (descricao, id_turma) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $descricao_atividade, $id_turma);
        if ($stmt->execute()) {
            echo "<script>alert('Atividade cadastrada com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao cadastrar atividade.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Por favor, insira a descrição da atividade.');</script>";
    }
}

// Processar a exclusão de atividade
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_atividade'])) {
    $id_atividade = $_POST['delete_atividade'];
    
    $sql = "DELETE FROM atividades WHERE id = ? AND id_turma = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_atividade, $id_turma);
    if ($stmt->execute()) {
        echo "<script>alert('Atividade excluída com sucesso!');</script>";
    } else {
        echo "<script>alert('Erro ao excluir atividade.');</script>";
    }
    $stmt->close();
}

// Listar atividades da turma
$sql = "SELECT id, descricao FROM atividades WHERE id_turma = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_turma);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atividades da Turma</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="top-bar">
        <span>Nome do Professor: <?= htmlspecialchars($nome_professor); ?></span>
        <a href="sair.php" class="logout-button">Sair</a>
    </div>
    <div class="container">
        <h1>Atividades da Turma: <?php echo htmlspecialchars($nome_turma); ?></h1>

        <!-- Formulário para cadastrar nova atividade -->
        <form method="POST" action="">
            <label for="descricao">Descrição da Atividade:</label>
            <input type="text" id="descricao" name="descricao" required>
            <input type="submit" value="Cadastrar Atividade">
        </form>

        <!-- Lista de atividades -->
        <h2>Lista de Atividades</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descrição</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['descricao']) . "</td>";
                    echo "<td>";
                    echo "<form method='POST' action='' style='display:inline;'>";
                    echo "<input type='hidden' name='delete_atividade' value='" . $row['id'] . "'>";
                    echo "<input type='submit' value='Excluir' class='action-button delete' onclick='return confirm(\"Tem certeza que deseja excluir esta atividade?\")'>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Botão para voltar -->
        <br>
        <a href="turmas.php" class="button">Voltar para as Turmas</a>
    </div>
</body>
</html>

