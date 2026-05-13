<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ahorcado Anime - NixoList</title>
    <link rel="stylesheet" href="../../CSS/styles.css">
    <link rel="icon" type="image/png" href="../../Recursos/icono/icononixo.png">

    <link rel="stylesheet" href="../../CSS/juegos.css">
</head>
<body>

<header class="header-main">
    <div class="header-top">
        <div class="logo-container">
            <a href="index.php" class="enlace-logo">
                <h1 class="logo-texto">NixoList</h1>
            </a>
        </div>
        <div class="PerfilContenedor">
            <?php
            if (isset($_SESSION['Usuario'])) {
                $Foto = (!empty($_SESSION['Foto'])) ? $_SESSION['Foto'] : '/Recursos/fotousuario.png';
                echo '
                <a href="listaperfil.php" style="text-decoration: none; color: inherit;">
                    <div class="perfil-horiz">
                        <div class="perfil-info">
                            <p class="perfil-nombre nombre-mio">' . htmlspecialchars($_SESSION['Usuario']) . ' <span class="flecha">▼</span></p>
                        </div>
                        <img src="' . htmlspecialchars($Foto) . '" class="profile-pic foto-mia">
                    </div>
                </a>';
            } else {
                echo '
                <div class="auth-buttons">
                    <a href="../Login/Index.php"><button class="login-btn">Iniciar Sesión</button></a>
                </div>';
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
    <div class="game-container wordle-game">
        
        <div class="wordle-left">
            <h2>Ahorcado Anime</h2>
            <p>Adivina el anime</p>
            <div id="ahorcado-vidas" class="vidas-container"></div>
            <div id="ahorcado-palabra" class="palabra-secreta"></div>
        </div>

        <div class="wordle-right">
            <div id="ahorcado-mensaje" class="mensaje-alerta"></div>
            <div id="ahorcado-keyboard" class="keyboard-container"></div>
            <button id="btn-reset-ahorcado" class="btn-siguiente" onclick="iniciarAhorcado()" style="display:none;">Jugar de nuevo</button>
        </div>

    </div>
</div>

<script>
const diccionarioAnime = [
    "ONE PIECE", "NARUTO", "DRAGON BALL", "DEATH NOTE",
    "EVANGELION", "BLEACH", "FULLMETAL ALCHEMIST",
    "HUNTER X HUNTER", "ATTACK ON TITAN", "TOKYO GHOUL",
    "MY HERO ACADEMIA", "JUJUTSU KAISEN", "DEMON SLAYER",
    "COWBOY BEBOP", "STEINS GATE", "CHAINSAW MAN",
    "VINLAND SAGA", "ONE PUNCH MAN", "CODE GEASS"
];

let palabraObjetivo = "";
let letrasAdivinadas = [];
let errores = 0;
const MAX_ERRORES = 6;
let juegoTerminado = false;

function iniciarAhorcado() {
    palabraObjetivo = diccionarioAnime[Math.floor(Math.random() * diccionarioAnime.length)].toUpperCase();
    letrasAdivinadas = [];
    errores = 0;
    juegoTerminado = false;
    
    document.getElementById('ahorcado-mensaje').innerText = "";
    document.getElementById('btn-reset-ahorcado').style.display = "none";
    
    actualizarVidas();
    dibujarPalabra();
    dibujarTeclado();
}

function actualizarVidas() {
    const vidasRestantes = MAX_ERRORES - errores;
    document.getElementById('ahorcado-vidas').innerText = "Vidas: " + "❤️".repeat(vidasRestantes) + "🖤".repeat(errores);
}

function dibujarPalabra() {
    const contenedor = document.getElementById('ahorcado-palabra');
    let mostrar = "";
    let victoria = true;

    for (let i = 0; i < palabraObjetivo.length; i++) {
        let letra = palabraObjetivo[i];
        
        if (letra === " ") {
            mostrar += " \u00A0 "; 
        } else if (letrasAdivinadas.includes(letra)) {
            mostrar += letra;
        } else {
            mostrar += "_";
            victoria = false; 
        }
    }

    contenedor.innerText = mostrar;

    if (victoria) {
        document.getElementById('ahorcado-mensaje').innerText = "Ganaste!";
        finalizarJuego();
    }
}

function dibujarTeclado() {
    const tecladoContenedor = document.getElementById('ahorcado-keyboard');
    tecladoContenedor.innerHTML = "";
    const filas = ["QWERTYUIOP", "ASDFGHJKL", "ZXCVBNM"];

    filas.forEach(fila => {
        const divFila = document.createElement("div");
        divFila.className = "key-row";
        
        fila.split("").forEach(t => {
            const boton = document.createElement("button");
            boton.id = `tecla-${t}`;
            boton.innerText = t;
            boton.className = "tecla-wordle"; 
            
            boton.onclick = () => manejarEntrada(t);
            divFila.appendChild(boton);
        });
        tecladoContenedor.appendChild(divFila);
    });
}

function manejarEntrada(tecla) {
    if (juegoTerminado || letrasAdivinadas.includes(tecla)) return;

    letrasAdivinadas.push(tecla);
    const botonVirtual = document.getElementById(`tecla-${tecla}`);

    if (palabraObjetivo.includes(tecla)) {
        if (botonVirtual) botonVirtual.classList.add("correct");
        dibujarPalabra();
    } else {
        if (botonVirtual) botonVirtual.classList.add("absent");
        errores++;
        actualizarVidas();

        if (errores >= MAX_ERRORES) {
            document.getElementById('ahorcado-mensaje').innerText = "Perdiste. Era " + palabraObjetivo;
            finalizarJuego();
        }
    }
}

window.addEventListener("keydown", (e) => {
    if (juegoTerminado) return;
    if (e.key.length === 1 && /[a-zA-Z]/.test(e.key)) {
        manejarEntrada(e.key.toUpperCase());
    }
});

function finalizarJuego() {
    juegoTerminado = true;
    document.getElementById('btn-reset-ahorcado').style.display = "block";
    
    const todasLasTeclas = document.querySelectorAll('.tecla-wordle');
    todasLasTeclas.forEach(tecla => {
        if (!tecla.classList.contains('correct') && !tecla.classList.contains('absent')) {
            tecla.style.opacity = "0.5";
            tecla.style.cursor = "not-allowed";
        }
    });
}

window.onload = iniciarAhorcado;
</script>
</body>
</html>