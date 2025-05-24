<?php
// Conexão MySQL
$mysqli = new mysqli("localhost", "manager", "102030", "manager");

if ($mysqli->connect_error) {
    $msg = "Falha na conexão: " . $mysqli->connect_error;
    $success = false;
} else {
    // Recebe dados do formulário
    $time_casa = $_POST['time_casa'] ?? '';
    $time_fora = $_POST['time_fora'] ?? '';
    $data_jogo = $_POST['data_jogo'] ?? '';
    $hora_jogo = $_POST['hora_jogo'] ?? '';
    $local_jogo = $_POST['local_jogo'] ?? '';

    // Validação simples
    if (!$time_casa || !$time_fora || !$data_jogo || !$hora_jogo || !$local_jogo) {
        $msg = "Por favor, preencha todos os campos obrigatórios.";
        $success = false;
    } else {
        // Prepara e executa o insert
        $stmt = $mysqli->prepare("INSERT INTO jogos (time_casa, time_fora, data_jogo, hora_jogo, local_jogo) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $time_casa, $time_fora, $data_jogo, $hora_jogo, $local_jogo);

        if ($stmt->execute()) {
            $msg = "Jogo cadastrado com sucesso!";
            $success = true;
        } else {
            $msg = "Erro ao cadastrar: " . $stmt->error;
            $success = false;
        }
        $stmt->close();
    }
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Resultado do Cadastro</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #2d2d44;
            color: #ddd;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
            flex-direction: column;
            text-align: center;
        }
        .message-box {
            background-color: #3a3a59;
            padding: 25px 30px;
            border-radius: 10px;
            max-width: 400px;
            box-shadow: 0 0 12px rgba(108, 92, 231, 0.7);
        }
        .success {
            color: #81ecec;
            font-weight: 700;
            font-size: 20px;
            margin-bottom: 15px;
        }
        .error {
            color: #ff7675;
            font-weight: 700;
            font-size: 20px;
            margin-bottom: 15px;
        }
        button {
            background-color: #6c5ce7;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            color: white;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #5a4ccc;
        }
        a {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body>
    <div class="message-box">
        <div class="<?= $success ? 'success' : 'error' ?>">
            <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>
        </div>
        <button onclick="window.location.href='cadastrar_jogo.php'">← Voltar ao Cadastro</button>
    </div>
</body>
</html>
