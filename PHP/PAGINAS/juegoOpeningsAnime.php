<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Adivina el Opening - NixoList</title>
<link rel="stylesheet" href="../../CSS/juegos.css">
<link rel="stylesheet" href="../../CSS/styles.css">
</head>

<body>

<header class="header-main">
    <div class="header-top">
        <div class="logo-container">
            <a href="index.php" class="enlace-logo">
                <h1 class="logo-texto">NixoList</h1>
            </a>
        </div>
        <div class="PerfilContenedor"> <?php
            if (isset($_SESSION['Usuario'])) {
                $Foto = (!empty($_SESSION['Foto'])) ? $_SESSION['Foto'] : '/Recursos/fotousuario.png';
                echo '
                <a href="listaperfil.php" style="text-decoration: none; color: inherit;">
                    <div class="perfil-horiz">
                        <div class="perfil-info">
                            <p class="perfil-nombre nombre-mio">' . htmlspecialchars($_SESSION['Usuario']) . ' <span class="flecha">▼</span></p>
                        </div>
                        <img src="' . htmlspecialchars($Foto) . '" class="profile-pic foto-mia" id="perfilImagen">
                    </div>
                </a>
                ';
            } else {
                echo '
                <div class="auth-buttons">
                    <a href="../Login/Index.php"><button class="login-btn">Iniciar Sesión</button></a>
                    <a href="../Login/registrarse.php"><button class="register-btn">Registrarse</button></a>
                </div>
                ';
            }
            ?>
        </div>
    </div>
</header>

<?php $pagina_actual = basename($_SERVER['PHP_SELF']); ?>

<nav class="navbar">
    <div class="nav-links">
        
        <a href="index.php" class="<?php echo ($pagina_actual == 'index.php') ? 'active' : ''; ?>">Inicio</a>

        <div class="menu-desplegable">
            <a href="anime.php" class="seccion-principal <?php echo ($pagina_actual == 'anime.php') ? 'active' : ''; ?>">Anime</a>
            <div class="sub-menu">
                <a href="anime.php">Inicio Anime</a>
                <a href="anime.php?seccion=recomendados">Recomendados</a>
                <a href="anime.php?seccion=populares">Más Populares</a>
                <a href="anime.php?seccion=top">Top Anime</a>
            </div>
        </div>

        <div class="menu-desplegable">
            <a href="manga.php" class="seccion-principal <?php echo ($pagina_actual == 'manga.php') ? 'active' : ''; ?>">Manga</a>
            <div class="sub-menu">
                <a href="manga.php">Inicio Manga</a>
                <a href="manga.php?seccion=recomendados">Recomendados</a>
                <a href="manga.php?seccion=populares">Más Populares</a>
                <a href="manga.php?seccion=top">Top Manga</a>
            </div>
        </div>

        <div class="menu-desplegable">
            <a href="peliculas.php" class="seccion-principal <?php echo ($pagina_actual == 'peliculas.php') ? 'active' : ''; ?>">Películas</a>
            <div class="sub-menu">
                <a href="peliculas.php">Inicio Películas</a>
                <a href="peliculas.php?seccion=recomendadas">Recomendadas</a>
                <a href="peliculas.php?seccion=populares">Más Populares</a>
                <a href="peliculas.php?seccion=top">Top Rated</a>
            </div>
        </div>

        <div class="menu-desplegable">
            <a href="series.php" class="seccion-principal <?php echo ($pagina_actual == 'series.php') ? 'active' : ''; ?>">Series</a>
            <div class="sub-menu">
                <a href="series.php">Inicio Series</a>
                <a href="series.php?seccion=trending">Trending</a>
                <a href="series.php?seccion=populares">Más Populares</a>
                <a href="series.php?seccion=top">Top Rated</a>
            </div>
        </div>

        <div class="menu-desplegable">
            <a href="juegos.php" class="seccion-principal <?php echo (in_array($pagina_actual, ['juegos.php', 'juegoOpeningsAnime.php', 'juegoPersonajesAnime.php', 'juegoWordleAnime.php', 'juegoAhorcadoAnime.php'])) ? 'active' : ''; ?>">Juegos</a>
            <div class="sub-menu">
                <a href="juegos.php">Inicio Juegos</a>
                <a href="juegoOpeningsAnime.php">Adivina el Opening</a>
                <a href="juegoPersonajesAnime.php">Adivina el Personaje</a>
                <a href="juegoWordleAnime.php">Wordle Anime</a>
                <a href="juegoAhorcadoAnime.php">Ahorcado Anime</a>
            </div>
        </div>
    </div>

    <div class="search-wrapper" style="position: relative;"> 
        <div class="search-container">
            <select class="search-select" id="search-type">
                <option value="all">Todos</option>
                <option value="anime">Anime</option>
                <option value="manga">Manga</option>
                <option value="movie">Películas</option>
                <option value="tv">Series</option>
            </select>
            <input type="text" id="search-input" placeholder="Buscar..." class="search-input" autocomplete="off">
            <button type="submit" class="search-button"><i>🔍</i></button>
        </div>
        <div id="search-results" class="search-results-dropdown"></div>
    </div>
</nav>

<div class="container">
    <div class="game-container">
        <h2>¿De qué Anime es este Opening?</h2>

        <div id="pantalla-inicio">
            <button class="btn-empezar" onclick="empezarJuegoReal()">Empezar Partida</button>
        </div>

        <div id="zona-juego" style="display: none;">
            <div id="marcador" style="display: flex; justify-content: center; gap: 20px; margin-bottom: 20px; font-weight: bold; font-size: 1.2rem;">
                <span style="color: #007bff;">Progreso: <span id="ronda-actual">0</span>/10</span>
                <span style="color: #28a745;">Aciertos: <span id="puntos-aciertos">0</span></span>
                <span style="color: #dc3545;">Fallos: <span id="puntos-fallos">0</span></span>
            </div>

            <!-- El audio real queda oculto; el reproductor-custom es lo que ve el jugador -->
            <audio id="reproductor" style="display:none;">
                Tu navegador no soporta el audio.
            </audio>

            <div id="reproductor-custom">
                <span id="icono-musica">🎵</span>
                <div id="barra-contenedor">
                    <div id="barra-progreso"></div>
                </div>
                <span id="tiempo-restante">30s</span>
            </div>

            <div class="opciones-game" id="contenedor-opciones">
            </div>

            <p id="resultado-texto"></p>
            <button id="btn-siguiente" class="btn-siguiente" onclick="iniciarRonda()">Siguiente Canción ➔</button>
        </div>
    </div>
</div>

<script>
const baseDatosOpenings = [
    { nombre: "Naruto", audio: "https://v.animethemes.moe/Naruto-OP2.webm" },
    { nombre: "Death Note", audio: "https://v.animethemes.moe/DeathNote-OP1.webm" },
    { nombre: "Attack on Titan", audio: "https://v.animethemes.moe/ShingekiNoKyojin-OP1.webm" },
    { nombre: "Tokyo Ghoul", audio: "https://v.animethemes.moe/TokyoGhoul-OP1.webm" },
    { nombre: "Evangelion", audio: "https://v.animethemes.moe/NeonGenesisEvangelion-OP1.webm" },
    { nombre: "One Punch Man", audio: "https://v.animethemes.moe/OnePunchMan-OP1.webm" },
    { nombre: "Jujutsu Kaisen", audio: "https://v.animethemes.moe/JujutsuKaisen-OP1.webm" },
    { nombre: "Demon Slayer", audio: "https://v.animethemes.moe/KimetsuNoYaiba-OP1.webm" },
    { nombre: "My Hero Academia", audio: "https://v.animethemes.moe/BokuNoHeroAcademia-OP1.webm" },
    { nombre: "Fullmetal Alchemist: Brotherhood", audio: "https://v.animethemes.moe/FullmetalAlchemistBrotherhood-OP1.webm" },
    { nombre: "Sword Art Online", audio: "https://v.animethemes.moe/SwordArtOnline-OP1.webm" },
    { nombre: "Cowboy Bebop", audio: "https://v.animethemes.moe/CowboyBebop-OP1.webm" },
    { nombre: "Bleach", audio: "https://v.animethemes.moe/Bleach-OP13.webm" },
    { nombre: "Steins;Gate", audio: "https://v.animethemes.moe/SteinsGate-OP1.webm" },
    { nombre: "Code Geass", audio: "https://v.animethemes.moe/CodeGeass-OP1.webm" },
    { nombre: "No Game No Life", audio: "https://v.animethemes.moe/NoGameNoLife-OP1.webm" },
    { nombre: "Hunter x Hunter", audio: "https://v.animethemes.moe/HunterHunter2011-OP1.webm" },
    { nombre: "Dragon Ball Z", audio: "https://v.animethemes.moe/DragonBallZ-OP1.webm" },
    { nombre: "Fairy Tail", audio: "https://v.animethemes.moe/FairyTail-OP1.webm" },
    { nombre: "JoJo's Bizarre Adventure", audio: "https://v.animethemes.moe/JojoNoKimyouNaBouken-OP2.webm" },
    { nombre: "Chainsaw Man", audio: "https://v.animethemes.moe/ChainsawMan-OP1.webm" },
    { nombre: "Black Clover", audio: "https://v.animethemes.moe/BlackClover-OP3.webm" },
    { nombre: "Vinland Saga", audio: "https://v.animethemes.moe/VinlandSaga-OP1.webm" },
    { nombre: "Fire Force", audio: "https://v.animethemes.moe/EnenNoShouboutai-OP1.webm" }
];

let animeCorrecto = "";
let aciertos = 0;
let fallos = 0;
let contadorRondas = 0;
const MAX_RONDAS = 10;
const TIEMPO_LIMITE = 30; // Segundos

let openingsDisponibles = [];
let cancionesJugadas = [];
let intervaloTemporizador = null;
let tiempoRestante = TIEMPO_LIMITE;

function mezclarArray(array) {
    let arrayMezclado = [...array];
    for (let i = arrayMezclado.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [arrayMezclado[i], arrayMezclado[j]] = [arrayMezclado[j], arrayMezclado[i]];
    }
    return arrayMezclado;
}

function actualizarMarcador() {
    document.getElementById('puntos-aciertos').innerText = aciertos;
    document.getElementById('puntos-fallos').innerText = fallos;
    document.getElementById('ronda-actual').innerText = contadorRondas;
}

function detenerTemporizador() {
    if (intervaloTemporizador) clearInterval(intervaloTemporizador);
}

function iniciarTemporizador() {
    detenerTemporizador();
    tiempoRestante = TIEMPO_LIMITE;
    
    const barra = document.getElementById('barra-progreso');
    const tiempoLabel = document.getElementById('tiempo-restante');
    const resultadoTexto = document.getElementById('resultado-texto');

    // Reset visual
    barra.style.width = '0%';
    tiempoLabel.innerText = tiempoRestante + 's';
    resultadoTexto.innerText = "¿Adivinas el anime?";

    intervaloTemporizador = setInterval(() => {
        tiempoRestante--;
        
        const porcentaje = ((TIEMPO_LIMITE - tiempoRestante) / TIEMPO_LIMITE) * 100;
        barra.style.width = porcentaje + '%';
        tiempoLabel.innerText = tiempoRestante + 's';

        if (tiempoRestante <= 0) {
            detenerTemporizador();
            document.getElementById('reproductor').pause();
            resultadoTexto.innerText = "Se acabó el tiempo! Responde ahora.";
            resultadoTexto.style.color = "#ffc107";
        }
    }, 1000);
}

function iniciarRonda() {
    if (contadorRondas >= MAX_RONDAS) {
        mostrarPantallaFinal();
        return;
    }

    detenerTemporizador();
    contadorRondas++;
    actualizarMarcador();

    const resultadoTexto = document.getElementById('resultado-texto');
    const btnSiguiente = document.getElementById('btn-siguiente');
    const contenedor = document.getElementById('contenedor-opciones');
    const reproductor = document.getElementById('reproductor');

    resultadoTexto.innerText = "Cargando opening...";
    resultadoTexto.style.color = "#ccc";
    btnSiguiente.style.display = 'none';
    contenedor.innerHTML = '';

    document.getElementById('barra-progreso').style.width = '0%';
    document.getElementById('tiempo-restante').innerText = '30s';

    if (openingsDisponibles.length === 0) {
        let opcionesRestantes = baseDatosOpenings.filter(anime => !cancionesJugadas.includes(anime.nombre));
        if (opcionesRestantes.length === 0) {
            cancionesJugadas = [];
            opcionesRestantes = [...baseDatosOpenings];
        }
        openingsDisponibles = mezclarArray(opcionesRestantes);
    }

    let animeGanador = openingsDisponibles.pop();
    animeCorrecto = animeGanador.nombre;
    cancionesJugadas.push(animeCorrecto);

    let opcionesIncorrectas = baseDatosOpenings.filter(anime => anime.nombre !== animeCorrecto);
    opcionesIncorrectas = mezclarArray(opcionesIncorrectas).slice(0, 3);
    let opcionesMezcladas = mezclarArray([animeGanador, ...opcionesIncorrectas]);

    let enlaceRoto = false;

    reproductor.src = animeGanador.audio;
    reproductor.volume = 0.3;

    // EVENTO CLAVE: Solo empieza el tiempo cuando el audio suena
    reproductor.onplaying = () => {
        if (!enlaceRoto) iniciarTemporizador();
    };

    reproductor.onerror = function() {
        enlaceRoto = true;
        detenerTemporizador();
        resultadoTexto.innerText = "Servidor lento. Cambiando opening...";
        contadorRondas--;
        setTimeout(iniciarRonda, 1000); 
    };

    reproductor.play()

    opcionesMezcladas.forEach(anime => {
        let btn = document.createElement('button');
        btn.className = 'btn-opcion';
        btn.innerText = anime.nombre;
        btn.onclick = (e) => verificarRespuesta(e.target, anime.nombre);
        contenedor.appendChild(btn);
    });
}

function verificarRespuesta(botonClicado, animeElegido) {
    detenerTemporizador();
    const reproductor = document.getElementById('reproductor');
    reproductor.pause();

    const resultadoTexto = document.getElementById('resultado-texto');
    const botones = document.querySelectorAll('.btn-opcion');
    const btnSiguiente = document.getElementById('btn-siguiente');

    botones.forEach(btn => {
        btn.disabled = true;
        if (btn.innerText === animeCorrecto) btn.classList.add('correct');
    });

    if (animeElegido === animeCorrecto) {
        aciertos++;
        resultadoTexto.innerText = "CORRECTO!";
        resultadoTexto.style.color = "#28a745";
    } else {
        fallos++;
        botonClicado.classList.add('wrong');
        resultadoTexto.innerText = "FALLASTE! Era " + animeCorrecto;
        resultadoTexto.style.color = "#dc3545";
    }

    actualizarMarcador();

    if (contadorRondas === MAX_RONDAS) {
        btnSiguiente.innerText = "Ver Resultados Finales ➔";
        btnSiguiente.onclick = mostrarPantallaFinal;
    } else {
        btnSiguiente.innerText = "Siguiente Canción ➔";
        btnSiguiente.onclick = iniciarRonda;
    }
    btnSiguiente.style.display = 'block';
}

function mostrarPantallaFinal() {
    detenerTemporizador();

    const contenedor = document.getElementById('contenedor-opciones');
    const resultadoTexto = document.getElementById('resultado-texto');
    const reproductor = document.getElementById('reproductor');
    const btnSiguiente = document.getElementById('btn-siguiente');

    reproductor.pause();
    document.getElementById('reproductor-custom').style.display = 'none';
    contenedor.innerHTML = "";

    resultadoTexto.innerHTML = `
        <div style="background: rgba(0,0,0,0.2); padding: 20px; border-radius: 15px;">
            <h3 style="color: #fff; margin-bottom: 10px;">¡Partida Terminada!</h3>
            <p style="font-size: 1.5rem; color: #28a745;">Aciertos: ${aciertos}</p>
            <p style="font-size: 1.5rem; color: #dc3545;">Fallos: ${fallos}</p>
            <p style="margin-top: 10px; color: #ccc;">Tu nota: <strong>${(aciertos / MAX_RONDAS) * 10}/10</strong></p>
        </div>
    `;

    btnSiguiente.innerText = "Jugar de nuevo";
    btnSiguiente.style.display = 'block';

    btnSiguiente.onclick = function() {
        aciertos = 0;
        fallos = 0;
        contadorRondas = 0;
        reintentosAudio = 0;
        cargandoRonda = false;
        esperandoRespuesta = false;
        cancionesJugadas = [];
        document.getElementById('reproductor-custom').style.display = 'flex';
        openingsDisponibles = mezclarArray([...baseDatosOpenings]);
        iniciarRonda();
    };
}

function empezarJuegoReal() {
    // Ocultamos el botón de inicio y mostramos la zona de juego
    document.getElementById('pantalla-inicio').style.display = 'none';
    document.getElementById('zona-juego').style.display = 'block';
    
    // Ahora sí, inicializamos la lógica
    openingsDisponibles = mezclarArray([...baseDatosOpenings]);
    iniciarRonda();
}

// IMPORTANTE: Cambiamos el window.onload para que NO inicie la ronda solo
window.onload = function() {
    // Aquí podrías precargar algo si quisieras, 
    // pero dejamos que el usuario pulse el botón para empezar.
};
</script>

</body>
</html>