<?php
// Conexão com o banco
$mysqli = new mysqli("localhost", "manager", "102030", "manager");
if ($mysqli->connect_error) {
    die("Falha na conexão: " . $mysqli->connect_error);
}

// Obter jogo do dia ou próximo
$hoje = date('Y-m-d');
$sql = "
    SELECT * FROM jogos 
    WHERE data_jogo >= ? 
    ORDER BY data_jogo ASC, hora_jogo ASC 
    LIMIT 1";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $hoje);
$stmt->execute();
$result = $stmt->get_result();
$jogo = $result->fetch_assoc();

$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Página Inicial</title>
<style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #2d2d44;
        color: #ddd;
        display: flex;
        min-height: 100vh;
        line-height: 1.4;
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
        position: relative;
        z-index: 1000;
    }

    #sidebar.collapsed {
        width: 70px;
    }

    #sidebar .toggle-btn {
        background-color: transparent;
        border: none;
        color: #6c5ce7;
        font-size: clamp(22px, 4vw, 26px);
        cursor: pointer;
        padding: 10px 15px;
        text-align: left;
        outline: none;
        transition: color 0.3s ease;
        min-height: 44px;
        display: flex;
        align-items: center;
    }

    #sidebar .toggle-btn:hover {
        color: #81ecec;
    }

    #sidebar .toggle-btn:focus {
        outline: 2px solid #81ecec;
        outline-offset: 2px;
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
        padding: clamp(10px, 2vw, 12px) 20px;
        cursor: pointer;
        color: #ddd;
        user-select: none;
        transition: background-color 0.2s ease;
        min-height: 44px;
        white-space: nowrap;
    }

    #sidebar ul li:hover {
        background-color: #6c5ce7;
        color: white;
    }

    #sidebar ul li:focus {
        outline: 2px solid #81ecec;
        outline-offset: -2px;
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
        font-size: clamp(16px, 3vw, 18px);
        flex-shrink: 0;
    }

    #sidebar.collapsed ul li span {
        display: none;
    }

    #sidebar ul li span {
        font-size: clamp(14px, 2.5vw, 16px);
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Mobile sidebar overlay */
    #sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }

    /* Main content */
    #main-content {
        flex-grow: 1;
        padding: clamp(15px, 4vw, 40px);
        overflow-y: auto;
        width: 0; /* Força o flex-grow */
    }

    h1 {
        margin-top: 0;
        color: #81ecec;
        font-size: clamp(1.8rem, 5vw, 2.5rem);
        margin-bottom: clamp(20px, 4vw, 30px);
    }

    .jogo-destaque {
        background-color: #3a3a59;
        border-radius: 10px;
        padding: clamp(20px, 4vw, 25px);
        box-shadow: 0 0 15px #6c5ce7aa;
        max-width: 100%;
        width: 100%;
        margin: 0 auto;
    }

    .jogo-destaque h2 {
        margin-top: 0;
        color: #dfe6e9;
        font-size: clamp(1.3rem, 4vw, 1.8rem);
        margin-bottom: clamp(15px, 3vw, 20px);
        word-break: break-word;
        hyphens: auto;
        line-height: 1.3;
    }

    .jogo-info {
        font-size: clamp(16px, 3vw, 18px);
        margin: clamp(8px, 2vw, 10px) 0;
        color: #b2bec3;
        word-break: break-word;
    }

    .jogo-info strong {
        color: #dfe6e9;
        font-weight: 600;
    }

    /* Link estilo botão */
    .btn-form {
        margin-top: clamp(20px, 4vw, 25px);
    }

    a.btn-link, button.btn-link {
        display: inline-block;
        background-color: #6c5ce7;
        padding: clamp(10px, 2.5vw, 12px) clamp(20px, 4vw, 25px);
        border-radius: 7px;
        color: white;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-size: clamp(14px, 3vw, 16px);
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        width: 100%;
        max-width: 300px;
    }

    a.btn-link:hover, button.btn-link:hover {
        background-color: #5a4ccc;
        transform: translateY(-1px);
    }

    a.btn-link:active, button.btn-link:active {
        transform: translateY(0);
    }

    a.btn-link:focus, button.btn-link:focus {
        outline: 2px solid #81ecec;
        outline-offset: 2px;
    }

    .no-game-message {
        text-align: center;
        font-size: clamp(16px, 3vw, 18px);
        color: #b2bec3;
        padding: clamp(20px, 4vw, 40px);
        background-color: #3a3a59;
        border-radius: 10px;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Breakpoints específicos */

    /* Tablets */
    @media (max-width: 768px) {
        #main-content {
            padding: clamp(15px, 3vw, 25px);
        }

        .jogo-destaque {
            padding: clamp(15px, 3vw, 20px);
        }

        #sidebar ul li {
            padding: 12px 15px;
        }

        a.btn-link, button.btn-link {
            max-width: 250px;
        }
    }

    /* Smartphones */
    @media (max-width: 480px) {
        body {
            flex-direction: column;
        }

        #sidebar {
            position: fixed;
            top: 0;
            left: -230px;
            height: 100vh;
            z-index: 1001;
            transition: left 0.3s ease;
            width: 230px !important;
        }

        #sidebar.mobile-open {
            left: 0;
        }

        #sidebar.collapsed {
            width: 230px !important;
        }

        #sidebar.collapsed ul li span {
            display: inline;
        }

        #sidebar-overlay.active {
            display: block;
        }

        #main-content {
            width: 100%;
            padding: 10px 15px;
            padding-top: 60px;
        }

        /* Botão toggle mobile no topo */
        .mobile-toggle {
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1002;
            background-color: #6c5ce7;
            border: none;
            color: white;
            font-size: 20px;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            min-height: 40px;
            min-width: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mobile-toggle:hover {
            background-color: #5a4ccc;
        }

        .jogo-destaque {
            margin-top: 10px;
        }

        h1 {
            margin-top: 10px;
            text-align: center;
        }

        a.btn-link, button.btn-link {
            max-width: 100%;
            width: 100%;
        }
    }

    /* Telas muito pequenas */
    @media (max-width: 320px) {
        .jogo-destaque {
            padding: 15px;
        }

        #main-content {
            padding: 8px 10px;
            padding-top: 55px;
        }

        .mobile-toggle {
            top: 8px;
            left: 8px;
            font-size: 18px;
            padding: 6px 10px;
        }
    }

    /* Landscape em dispositivos móveis */
    @media (max-height: 500px) and (orientation: landscape) {
        #sidebar {
            height: 100vh;
        }

        #main-content {
            padding: 10px 15px;
        }

        h1 {
            margin-bottom: 15px;
        }

        .jogo-destaque {
            padding: 15px;
        }
    }

    /* Telas grandes */
    @media (min-width: 1200px) {
        #main-content {
            padding: 40px 60px;
            max-width: 1200px;
        }

        .jogo-destaque {
            max-width: 700px;
        }
    }

    /* Estados para melhor acessibilidade */
    #sidebar ul li {
        outline: none;
    }

    /* Animações suaves */
    * {
        transition-property: background-color, color, transform;
        transition-duration: 0.2s;
        transition-timing-function: ease;
    }

</style>
</head>
<body>

<!-- Botão toggle mobile -->
<button class="mobile-toggle" onclick="toggleMobileSidebar()" style="display: none;">☰</button>

<!-- Overlay para mobile -->
<div id="sidebar-overlay" onclick="closeMobileSidebar()"></div>

<div id="sidebar">
    <button class="toggle-btn" title="Mostrar/Ocultar Menu" onclick="toggleSidebar()">☰</button>
    <ul>
        <li tabindex="0" onclick="navigateTo('index.php')" onkeydown="handleKeyNavigation(event, 'index.php')">
            <i>🏠</i><span>Página Inicial</span>
        </li>
        <li tabindex="0" onclick="navigateTo('cadastrar_jogo.php')" onkeydown="handleKeyNavigation(event, 'cadastrar_jogo.php')">
            <i>✏️</i><span>Cadastrar Jogo</span>
        </li>
        <li tabindex="0" onclick="navigateTo('manager.php')" onkeydown="handleKeyNavigation(event, 'manager.php')">
            <i>📅</i><span>Gerenciador de Eventos</span>
        </li>
    </ul>
</div>

<div id="main-content">
    <h1>Jogo do Dia / Próximo Jogo</h1>

    <?php if ($jogo): ?>
        <div class="jogo-destaque">
            <h2><?=htmlspecialchars($jogo['time_casa'])?> 🆚 <?=htmlspecialchars($jogo['time_fora'])?></h2>
            <div class="jogo-info"><strong>Data:</strong> <?=date('d/m/Y', strtotime($jogo['data_jogo']))?></div>
            <div class="jogo-info"><strong>Hora:</strong> <?=date('H:i', strtotime($jogo['hora_jogo']))?></div>
            <div class="jogo-info"><strong>Local:</strong> <?=htmlspecialchars($jogo['local_jogo'])?></div>

            <form action="coordenar.php" method="get" class="btn-form">
                <input type="hidden" name="jogo_id" value="<?=htmlspecialchars($jogo['id'])?>">
                <button type="submit" class="btn-link">Coordenar Transmissão</button>
            </form>
        </div>
    <?php else: ?>
        <div class="no-game-message">
            <p>Não há jogos cadastrados para hoje ou próximos dias.</p>
        </div>
    <?php endif; ?>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        if (window.innerWidth <= 480) {
            // Mobile: usar overlay
            toggleMobileSidebar();
        } else {
            // Desktop/Tablet: collapse normal
            sidebar.classList.toggle('collapsed');
        }
    }

    function toggleMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        
        sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('active');
    }

    function closeMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    }

    function navigateTo(url) {
        window.location.href = url;
    }

    function handleKeyNavigation(event, url) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            navigateTo(url);
        }
    }

    // Mostrar/ocultar botão mobile baseado no tamanho da tela
    function updateMobileToggleVisibility() {
        const mobileToggle = document.querySelector('.mobile-toggle');
        if (window.innerWidth <= 480) {
            mobileToggle.style.display = 'flex';
        } else {
            mobileToggle.style.display = 'none';
            closeMobileSidebar();
        }
    }

    // Event listeners
    window.addEventListener('resize', updateMobileToggleVisibility);
    window.addEventListener('load', updateMobileToggleVisibility);

    // Fechar sidebar mobile ao pressionar ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeMobileSidebar();
        }
    });

    // Navegação por teclado no sidebar
    const sidebarItems = document.querySelectorAll('#sidebar ul li');
    sidebarItems.forEach((item, index) => {
        item.addEventListener('keydown', function(event) {
            let targetIndex = index;
            
            switch(event.key) {
                case 'ArrowDown':
                    event.preventDefault();
                    targetIndex = (index + 1) % sidebarItems.length;
                    sidebarItems[targetIndex].focus();
                    break;
                case 'ArrowUp':
                    event.preventDefault();
                    targetIndex = (index - 1 + sidebarItems.length) % sidebarItems.length;
                    sidebarItems[targetIndex].focus();
                    break;
            }
        });
    });
</script>

</body>
</html>