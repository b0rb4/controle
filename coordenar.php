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
<title>Coordenar Transmissão</title>
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

    /* Sidebar - Copiado do index.php */
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

    /* Main content - Adaptado */
    #main-content {
        flex-grow: 1;
        padding: clamp(15px, 4vw, 40px);
        overflow-y: auto;
        width: 0; /* Força o flex-grow */
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        width: 100%;
    }

    h1 {
        color: #81ecec;
        margin-bottom: 20px;
        text-align: center;
        font-size: clamp(1.5rem, 4vw, 2.5rem);
        margin-top: 0;
    }

    #hora-jogo {
        color: rgb(255, 255, 255);
        font-weight: 700;
        font-size: clamp(2rem, 4vw, 2rem);
        margin-bottom: 20px;
        text-align: center;
        word-break: break-word;
        hyphens: auto;
        line-height: 1.3;
        padding: 0 10px;
    }

    #relogio-brasilia {
        text-align: center;
        font-size: clamp(2rem, 5vw, 2rem);
        color: rgb(255, 0, 0);
        margin-top: -10px;
        margin-bottom: 20px;
        font-weight: bold;
        user-select: none;
    }

    /* Container evento atual e tempo */
    #evento-atual-container {
        margin-bottom: 20px;
        text-align: center;
        user-select: none;
        padding: 0 10px;
    }

    #texto-evento-atual {
        font-weight: 700;
        font-size: clamp(0.9rem, 2.5vw, 1.2rem);
        margin-bottom: 10px;
        word-break: break-word;
        hyphens: auto;
        line-height: 1.3;
    }

    #tempo-restante {
        font-weight: 900;
        font-size: clamp(2rem, 8vw, 4rem);
        color: red;
        font-family: 'Courier New', Courier, monospace;
        user-select: none;
        line-height: 1;
        animation: piscar-verde-vermelho 1s infinite alternate;
        margin: 10px 0;
    }

    @keyframes piscar-verde-vermelho {
        0% { color: red; }
        100% { color: limegreen; }
    }
    
    ul#lista-eventos {
        list-style: none;
        padding: 0;
        margin: 0 0 20px 0;
        height: 300px; /* Altura fixa para mostrar aproximadamente 6 eventos */
        overflow-y: auto;
        border: 1px solid #6c5ce7;
        border-radius: 8px;
        background-color: #3a3a59;
        font-size: clamp(0.8rem, 2vw, 1rem);
        user-select: none;
        -webkit-overflow-scrolling: touch;
    }

    ul#lista-eventos li {
        padding: clamp(8px, 2vw, 15px);
        border-bottom: 1px solid #5a4ccc;
        cursor: pointer;
        user-select: none;
        transition: background-color 0.3s ease;
        word-break: break-word;
        hyphens: auto;
        line-height: 1.4;
        min-height: 44px; /* Touch target mínimo */
        display: flex;
        align-items: center;
    }

    ul#lista-eventos li:last-child {
        border-bottom: none;
    }

    ul#lista-eventos li.evento-ativo {
        background-color: #6c5ce7;
        color: white;
        font-weight: bold;
    }

    ul#lista-eventos li:hover:not(.evento-ativo) {
        background-color: #5757a1;
    }

    .botoes-controle {
        display: grid;
        gap: clamp(8px, 2vw, 15px);
        margin-bottom: 20px;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        max-width: 100%;
    }

    button {
        padding: clamp(10px, 2.5vw, 15px);
        font-size: clamp(0.9rem, 2.2vw, 1.1rem);
        font-weight: 700;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        color: white;
        transition: all 0.3s ease;
        user-select: none;
        white-space: nowrap;
        min-height: 44px; /* Touch target mínimo */
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    button:active {
        transform: scale(0.95);
    }

    button#btn-iniciar {
        background-color: #2ecc71;
    }
    button#btn-iniciar:hover {
        background-color: #27ae60;
    }

    button#btn-pausar {
        background-color: #f1c40f;
        color: black;
    }
    button#btn-pausar:hover {
        background-color: #d4ac0d;
    }

    button#btn-parar {
        background-color: #e74c3c;
    }
    button#btn-parar:hover {
        background-color: #c0392b;
    }

    button#btn-anterior, button#btn-proximo {
        background-color: #0984e3;
    }
    button#btn-anterior:hover, button#btn-proximo:hover {
        background-color: #065aab;
    }

    #status {
        text-align: center;
        margin-top: 10px;
        font-size: clamp(0.8rem, 2vw, 1rem);
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
        display: none;
        align-items: center;
        justify-content: center;
    }

    .mobile-toggle:hover {
        background-color: #5a4ccc;
    }

    /* Breakpoints específicos */
    
    /* Tablets */
    @media (max-width: 768px) {
        #main-content {
            padding: clamp(15px, 3vw, 25px);
        }
        
        .container {
            padding: 0 5px;
        }
        
        #hora-jogo {
            margin-bottom: 15px;
        }
        
        #relogio-brasilia {
            margin-bottom: 30px;
        }
        
        #evento-atual-container {
            margin-bottom: 15px;
        }
        
        .botoes-controle {
            grid-template-columns: repeat(2, 1fr);
        }
        
        ul#lista-eventos {
            height: 280px; /* Mantém altura consistente no tablet */
        }

        #sidebar ul li {
            padding: 12px 15px;
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

        .mobile-toggle {
            display: flex;
        }
        
        .container {
            padding: 0;
        }
        
        #hora-jogo {
            font-size: clamp(1rem, 5vw, 1.4rem);
            margin-bottom: 10px;
            line-height: 1.2;
        }
        
        #relogio-brasilia {
            font-size: clamp(0.9rem, 10vw, 1.2rem);
            margin-bottom: 10px;
        }
        
        #tempo-restante {
            font-size: clamp(1.8rem, 10vw, 3rem);
        }
        
        #texto-evento-atual {
            font-size: clamp(0.8rem, 3vw, 1rem);
        }
        
        .botoes-controle {
            grid-template-columns: 1fr;
            gap: 8px;
        }
        
        ul#lista-eventos {
            height: 250px; /* Altura ajustada para smartphones */
            font-size: clamp(0.75rem, 2.5vw, 0.9rem);
        }
        
        ul#lista-eventos li {
            padding: 10px;
            min-height: 40px;
        }
        
        button {
            font-size: clamp(0.85rem, 3vw, 1rem);
            padding: 12px;
            min-height: 48px;
        }

        h1 {
            margin-top: 10px;
            text-align: center;
        }
    }

    /* Telas muito pequenas */
    @media (max-width: 320px) {
        #tempo-restante {
            font-size: clamp(1.5rem, 12vw, 2.5rem);
        }
        
        ul#lista-eventos {
            height: 220px; /* Altura para telas muito pequenas */
            font-size: 0.7rem;
        }
        
        ul#lista-eventos li {
            padding: 8px;
            min-height: 36px;
        }
        
        button {
            font-size: 0.8rem;
            padding: 10px;
            min-height: 44px;
        }
        
        .botoes-controle {
            gap: 6px;
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
        ul#lista-eventos {
            height: 180px; /* Altura reduzida para landscape */
        }
        
        #tempo-restante {
            font-size: clamp(1.5rem, 6vh, 2.5rem);
        }
        
        #main-content {
            padding: 5px 15px;
        }
        
        #evento-atual-container {
            margin-bottom: 10px;
        }
        
        .botoes-controle {
            grid-template-columns: repeat(5, 1fr);
            gap: 5px;
        }
        
        button {
            font-size: clamp(0.7rem, 2vh, 0.9rem);
            padding: 8px;
            min-height: 40px;
        }

        #sidebar {
            height: 100vh;
        }
    }

    /* Telas grandes */
    @media (min-width: 1200px) {
        #main-content {
            padding: 40px 60px;
            max-width: 1200px;
        }

        .container {
            max-width: 1000px;
        }
        
        .botoes-controle {
            grid-template-columns: repeat(5, 1fr);
            max-width: 800px;
            margin: 0 auto 20px auto;
        }
        
        ul#lista-eventos {
            max-width: 800px;
            margin: 0 auto 20px auto;
        }
    }

    /* Estados de foco para acessibilidade */
    button:focus,
    ul#lista-eventos li:focus {
        outline: 2px solid #81ecec;
        outline-offset: 2px;
    }

    /* Estados para melhor acessibilidade */
    #sidebar ul li {
        outline: none;
    }

    /* Melhorar scrollbar em webkit */
    ul#lista-eventos::-webkit-scrollbar {
        width: 8px;
    }

    ul#lista-eventos::-webkit-scrollbar-track {
        background: #2d2d44;
        border-radius: 4px;
    }

    ul#lista-eventos::-webkit-scrollbar-thumb {
        background: #6c5ce7;
        border-radius: 4px;
    }

    ul#lista-eventos::-webkit-scrollbar-thumb:hover {
        background: #5a4ccc;
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
<button class="mobile-toggle" onclick="toggleMobileSidebar()">☰</button>

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
    <div class="container">
        <h1>Coordenar Transmissão</h1>
        
        <?php if ($jogo): ?>
            <div id="hora-jogo">
                <?=htmlspecialchars($jogo['time_casa'])?> 🆚 <?=htmlspecialchars($jogo['time_fora'])?> - <?=date('d/m/Y', strtotime($jogo['data_jogo']))?> <?=date('H:i', strtotime($jogo['hora_jogo']))?>
            </div>
            <div id="relogio-brasilia">Horário de Brasília: --:--:--</div>
        <?php else: ?>
            <p style="text-align: center; font-size: clamp(1rem, 3vw, 1.2rem);">Não há jogo do dia ou próximo para coordenar.</p>
        <?php endif; ?>

        <?php if ($jogo): ?>
            <div id="evento-atual-container">
                <div id="texto-evento-atual">Evento atual: --</div>
                <div id="tempo-restante">00:00:00</div>
            </div>

            <ul id="lista-eventos"></ul>

            <div class="botoes-controle">
                <button id="btn-iniciar">Iniciar Evento</button>
                <button id="btn-pausar">Pausar Evento</button>
                <button id="btn-parar">Parar Evento</button>
                <button id="btn-anterior">Anterior</button>
                <button id="btn-proximo">Próximo</button>
            </div>

            <div id="status"></div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Funções do sidebar - Copiadas do index.php
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

    // Event listeners do sidebar
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

    // Código do cronômetro - Original do coordenar.php
    <?php if ($jogo): ?>

    const horaJogoStr = '<?=date('H:i:s', strtotime($jogo['hora_jogo']))?>';

    function segundosParaHMS(segundos) {
        const s = Math.abs(segundos);
        const h = Math.floor(s / 3600);
        const m = Math.floor((s % 3600) / 60);
        const sec = s % 60;
        return (segundos < 0 ? '-' : '') +
            String(h).padStart(2,'0') + ':' +
            String(m).padStart(2,'0') + ':' +
            String(sec).padStart(2,'0');
    }

    const eventos = [
        {offset: -3600, duracao: 300, descricao: "Cinta (chegadada+vestiario+entrevistar[casa+visitante])"},
        {offset: -3300, duracao: 2100, descricao: "Girar Câmeras"},
        {offset: -1200, duracao: 30, descricao: "Camera 01"},
        {offset: -1170, duracao: 30, descricao: "Cartela de Abertura"},
        {offset: -1140, duracao: 30, descricao: "Camera 01"},
        {offset: -1110, duracao: 90, descricao: "Girar Câmeras"},
        {offset: -1020, duracao: 60, descricao: "Clipe Vestiario + Chegada Time Casa"},
        {offset: -960, duracao: 30, descricao: "Clipe Destaque Time Casa"},
        {offset: -930, duracao: 60, descricao: "Clipe Entrevista Técnico Time Casa"},
        {offset: -870, duracao: 30, descricao: "Camera 01"},
        {offset: -840, duracao: 60, descricao: "Clipe Vestiário+ Chegada Time Visitante"},
        {offset: -780, duracao: 30, descricao: "Clipe Destaque Time Visitante"},
        {offset: -750, duracao: 60, descricao: "Clipe Entrevista Técnico Time Visitante"},
        {offset: -690, duracao: 60, descricao: "Camera 01"},
        {offset: -630, duracao: 60, descricao: "Girar Cameras"},
        {offset: -570, duracao: 60, descricao: "Camera 01 + cartela de abertura"},
        {offset: -510, duracao: 30, descricao: "clipe de aquecimento"},
        {offset: -480, duracao: 210, descricao: "protocolo entrada e hino"},
        {offset: -270, duracao: 45, descricao: "escalação time casa"},
        {offset: -235, duracao: 45, descricao: "escalação time visitante"},
        {offset: -195, duracao: 20, descricao: "escalação arbitragem"},
        {offset: -175, duracao: 10, descricao: "camera sala var"},
        {offset: -165, duracao: 15, descricao: "camera + gc tecnico casa"},
        {offset: -150, duracao: 15, descricao: "camera + gc tecnico visitante"},
        {offset: -135, duracao: 135, descricao: "girar cameras"},
        {offset: 0, duracao: 2700, descricao: "partida rolando"},
        {offset: 120, duracao: 20, descricao: "cartela reservas time casa"},
        {offset: 140, duracao: 20, descricao: "cartela reservas time visitante"},
    ];

    const ulEventos = document.getElementById('lista-eventos');
    const btnIniciar = document.getElementById('btn-iniciar');
    const btnPausar = document.getElementById('btn-pausar');
    const btnParar = document.getElementById('btn-parar');
    const btnAnterior = document.getElementById('btn-anterior');
    const btnProximo = document.getElementById('btn-proximo');
    const textoEventoAtual = document.getElementById('texto-evento-atual');
    const tempoRestanteElem = document.getElementById('tempo-restante');

    let eventoIndexAtual = 0;
    let cronometro = null;
    let tempoRestante = eventos[eventoIndexAtual].duracao;
    let rodando = false;

    function montarListaEventos() {
        ulEventos.innerHTML = '';
        eventos.forEach((ev, idx) => {
            const offsetFormatado = segundosParaHMS(ev.offset);
            const duracaoFormatada = segundosParaHMS(ev.duracao);
            const li = document.createElement('li');
            li.textContent = `[${offsetFormatado}] ${ev.descricao} (${duracaoFormatada})`;
            li.dataset.index = idx;
            li.setAttribute('tabindex', '0'); // Torna focável para acessibilidade
            if (idx === eventoIndexAtual) li.classList.add('evento-ativo');
            ulEventos.appendChild(li);
        });
    }

    function atualizarStatus() {
        const ev = eventos[eventoIndexAtual];
        textoEventoAtual.textContent = `Evento atual: ${ev.descricao}`;
        tempoRestanteElem.textContent = segundosParaHMS(tempoRestante);
    }

    function iniciarEvento() {
        if (rodando) return;
        rodando = true;
        atualizarStatus();
        cronometro = setInterval(() => {
            if (tempoRestante > 0) {
                tempoRestante--;
                atualizarStatus();
            } else {
                pararEvento();
                proximoEvento();
            }
        }, 1000);
    }

    function pausarEvento() {
        if (!rodando) return;
        rodando = false;
        clearInterval(cronometro);
        atualizarStatus();
    }

    function pararEvento() {
        rodando = false;
        clearInterval(cronometro);
        tempoRestante = eventos[eventoIndexAtual].duracao;
        atualizarStatus();
    }

    function proximoEvento() {
        pararEvento();
        if (eventoIndexAtual < eventos.length - 1) {
            eventoIndexAtual++;
            tempoRestante = eventos[eventoIndexAtual].duracao;
            montarListaEventos();
            atualizarStatus();
        }
    }

    function eventoAnterior() {
        pararEvento();
        if (eventoIndexAtual > 0) {
            eventoIndexAtual--;
            tempoRestante = eventos[eventoIndexAtual].duracao;
            montarListaEventos();
            atualizarStatus();
        }
    }

    // Event listeners com suporte a touch e teclado
    ulEventos.addEventListener('click', e => {
        if (e.target.tagName === 'LI') {
            pararEvento();
            eventoIndexAtual = parseInt(e.target.dataset.index);
            tempoRestante = eventos[eventoIndexAtual].duracao;
            montarListaEventos();
            atualizarStatus();
        }
    });

    // Suporte a navegação por teclado na lista
    ulEventos.addEventListener('keydown', e => {
        if (e.target.tagName === 'LI' && (e.key === 'Enter' || e.key === ' ')) {
            e.preventDefault();
            pararEvento();
            eventoIndexAtual = parseInt(e.target.dataset.index);
            tempoRestante = eventos[eventoIndexAtual].duracao;
            montarListaEventos();
            atualizarStatus();
        }
    });

    btnIniciar.addEventListener('click', iniciarEvento);
    btnPausar.addEventListener('click', pausarEvento);
    btnParar.addEventListener('click', pararEvento);
    btnProximo.addEventListener('click', proximoEvento);
    btnAnterior.addEventListener('click', eventoAnterior);

    // Atalhos de teclado
    document.addEventListener('keydown', e => {
        if (e.target.tagName === 'BUTTON' || e.target.tagName === 'LI') return; // Evita conflito
        
        switch(e.key) {
            case ' ': // Espaço para iniciar/pausar
                e.preventDefault();
                if (rodando) {
                    pausarEvento();
                } else {
                    iniciarEvento();
                }
                break;
            case 'Escape': // ESC para parar
                pararEvento();
                break;
            case 'ArrowLeft': // Seta esquerda para anterior
                eventoAnterior();
                break;
            case 'ArrowRight': // Seta direita para próximo
                proximoEvento();
                break;
        }
    });

    montarListaEventos();
    atualizarStatus();

    <?php endif; ?>

    function atualizarRelogioBrasilia() {
        const agora = new Date();
        const offsetBrasilia = -3 * 60; // UTC-3
        const localUTC = agora.getTime() + (agora.getTimezoneOffset() * 60000);
        const horarioBrasilia = new Date(localUTC + offsetBrasilia * 60000);

        const horas = String(horarioBrasilia.getHours()).padStart(2, '0');
        const minutos = String(horarioBrasilia.getMinutes()).padStart(2, '0');
        const segundos = String(horarioBrasilia.getSeconds()).padStart(2, '0');

        const relogio = document.getElementById('relogio-brasilia');
        if (relogio) {
            relogio.textContent = `Horário de Brasília: ${horas}:${minutos}:${segundos}`;
        }
    }

    setInterval(atualizarRelogioBrasilia, 1000);
    atualizarRelogioBrasilia(); // chama uma vez ao iniciar
</script>

</body>
</html>