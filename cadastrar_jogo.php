<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>📅 Cadastrar Jogo</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #2d2d44;
            color: #ddd;
            display: flex;
            height: 100vh;
        }

        /* Sidebar */
        #sidebar {
            background-color: #3a3a59;
            width: 230px;
            transition: width 0.3s ease;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding-top: 20px;
        }

        #sidebar.collapsed {
            width: 70px;
        }

        #sidebar .toggle-btn {
            background-color: transparent;
            border: none;
            color: #6c5ce7;
            font-size: 26px;
            cursor: pointer;
            padding: 10px 15px;
            text-align: left;
            outline: none;
        }

        #sidebar ul {
            list-style: none;
            padding: 0;
            margin: 20px 0 0 0;
            flex-grow: 1;
        }

        #sidebar ul li {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            cursor: pointer;
            color: #ddd;
            user-select: none;
            transition: background-color 0.2s ease;
        }

        #sidebar ul li:hover {
            background-color: #6c5ce7;
            color: white;
        }

        #sidebar ul li i {
            font-style: normal;
            display: inline-block;
            width: 25px;
            font-weight: 700;
            text-align: center;
            margin-right: 15px;
            font-size: 18px;
        }

        #sidebar.collapsed ul li span {
            display: none;
        }

        /* Main content */
        #main-content {
            flex-grow: 1;
            padding: 30px 40px;
            overflow-y: auto;
        }

        h1 {
            color: #a29bfe;
            margin-bottom: 25px;
        }

        form {
            background-color: #3a3a59;
            padding: 20px;
            border-radius: 8px;
            max-width: 400px;
        }

        label {
            display: block;
            margin-bottom: 15px;
            font-weight: 600;
        }

        input[type="text"],
        input[type="date"],
        input[type="time"] {
            width: 100%;
            padding: 8px 10px;
            border-radius: 5px;
            border: none;
            font-size: 16px;
            background-color: #272746;
            color: #eee;
            box-sizing: border-box;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        input[type="time"]:focus {
            outline: none;
            box-shadow: 0 0 8px #6c5ce7;
            background-color: #3a3a59;
        }

        button[type="submit"] {
            background-color: #6c5ce7;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #5a4ccc;
        }
    </style>
</head>
<body>
    <div id="sidebar">
        <button class="toggle-btn" title="Mostrar/Ocultar Menu" onclick="toggleSidebar()">☰</button>
        <ul>
            <li onclick="window.location.href='index.php'">
                <i>🏠</i><span>Página Inicial</span>
            </li>
            <li onclick="window.location.href='cadastrar_jogo.php'">
                <i>✏️</i><span>Cadastrar Jogo</span>
            </li>
            <li onclick="window.location.href='manager.php'">
                <i>📅</i><span>Gerenciador de Eventos</span>
            </li>
        </ul>
    </div>

    <div id="main-content">
        <h1>📅 Cadastrar Jogo</h1>
        <form action="salvar_jogo.php" method="POST">
            <label for="time_casa">Time Casa:
                <input type="text" name="time_casa" id="time_casa" required />
            </label>

            <label for="time_fora">Time Fora:
                <input type="text" name="time_fora" id="time_fora" required />
            </label>

            <label for="data_jogo">Data do Jogo:
                <input type="date" name="data_jogo" id="data_jogo" required />
            </label>

            <label for="hora_jogo">Hora do Jogo:
                <input type="time" name="hora_jogo" id="hora_jogo" required />
            </label>

            <label for="local_jogo">Local:
                <input type="text" name="local_jogo" id="local_jogo" />
            </label>

            <button type="submit">💾 Salvar</button>
        </form>
    </div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('collapsed');
    }
</script>

</body>
</html>
