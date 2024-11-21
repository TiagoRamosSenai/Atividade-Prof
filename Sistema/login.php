<?php
session_start();  // Make sure session_start() is at the top of the page

include('conexao.php');  // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Prepare the query to check if the email and password exist
    $sql = "SELECT id, nome FROM professores WHERE email = ? AND senha = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $email, $senha);  // Bind email and password to the query
        $stmt->execute();  // Execute the query

        $stmt->store_result();  // Store the result of the query
        if ($stmt->num_rows > 0) {  // If a matching user is found
            $stmt->bind_result($id_professor, $nome);  // Bind the result to variables
            $stmt->fetch();  // Fetch the result
            $_SESSION['id_professor'] = $id_professor;  // Store user id in session
            $_SESSION['nome_professor'] = $nome;  // Store name in session

            // Redirect to the main page
            header("Location: turmas.php");  // Redirect to the main page
            exit();
        } else {
            echo "<script>alert('E-mail ou senha inválidos.');</script>";
        }

        $stmt->close();  // Close the statement
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <form method="POST" action="">
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required><br><br>

        <input type="submit" value="Entrar">
    </form>
</body>
</html>
