<?php
session_start();
include('conexao.php');

// Obter o nome do professor
$nome_professor = $_SESSION['nome_professor'] ?? 'Professor';

// Verificando se o professor está logado
if (!isset($_SESSION['id_professor'])) {
    header('Location: login.php');
    exit();
}

// Verificar se a turma foi excluída
if (isset($_GET['excluir'])) {
    $id_turma = $_GET['excluir'];

    // Verificar se a turma tem atividades cadastradas
    $sql = "SELECT COUNT(*) FROM atividades WHERE id_turma = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id_turma);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            echo "<script>alert('Você não pode excluir uma turma com atividades cadastradas.');</script>";
        } else {
            // Excluir a turma
            $sql_delete = "DELETE FROM turmas WHERE id = ?";
            if ($stmt_delete = $conn->prepare($sql_delete)) {
                $stmt_delete->bind_param("i", $id_turma);
                $stmt_delete->execute();
                $stmt_delete->close();
                echo "<script>alert('Turma excluída com sucesso!');</script>";
            } else {
                echo "<script>alert('Erro ao excluir a turma.');</script>";
            }
        }
    } else {
        echo "<script>alert('Erro ao verificar atividades.');</script>";
    }
}

// Listar turmas do professor
$sql = "SELECT * FROM turmas WHERE id_professor = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $_SESSION['id_professor']);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    echo "<script>alert('Erro ao carregar turmas.');</script>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turmas</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <div class="top-bar">
        <span>Nome do Professor: <?= htmlspecialchars($nome_professor); ?></span>
        <a href="sair.php" class="logout-button">Sair</a>
    </div>
    <div class="container">
        <div class="header">
            <h1>Turmas</h1>
            <a href="cadastro_turma.php" class="button">Cadastrar turma</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Nome</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $counter = 1;
                while ($row = $result->fetch_assoc()): 
                ?>
                    <tr>
                        <td><?= $counter++; ?></td>
                        <td><?= htmlspecialchars($row['nome']); ?></td>
                        <td>
                            <a href="?excluir=<?= $row['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?')" class="action-button delete">Excluir</a>
                            <a href="atividades.php?id_turma=<?= $row['id']; ?>" class="action-button view">Visualizar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

