<?php
$host = "localhost"; // Endereço do servidor
$user = "root"; // Usuário do banco de dados (padrão do XAMPP)
$password = "root"; // Senha do banco de dados (em branco no XAMPP)
$database = "saep_db"; // Nome do banco de dados

// Criação da conexão
$conn = new mysqli($host, $user, $password, $database);

// Verifica se há erro na conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>
