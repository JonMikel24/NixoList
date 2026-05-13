<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Wordle Anime - NixoList</title>
    <link rel="stylesheet" href="../../CSS/styles.css">
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
            <h2>Wordle Anime</h2>
            <p>Adivina el personaje de anime (6 letras)</p>
            <div id="wordle-board" class="board"></div>
        </div>

        <div class="wordle-right">
            <div id="wordle-mensaje" class="mensaje-alerta"></div>
            <div id="wordle-keyboard" class="keyboard-container"></div>
            <button id="btn-reset-wordle" class="btn-siguiente" onclick="reiniciarWordle()">Jugar de nuevo</button>
        </div>

    </div>
</div>

<script>
// Diccionario estricto de 6 letras
const diccionarioAnime = [
    "NARUTO", "SASUKE", "VEGETA", "ITACHI", "SAKURA",
    "KANEKI", "ICHIGO", "MIKASA", "EDWARD", "NEZUKO",
    "SHINJI", "MAKIMA", "JOTARO", "KILLUA", "HISOKA"
];

let palabraObjetivo = "";
let intentoActual = 0;
let letraActual = 0;
let juegoTerminado = false;
const MAX_INTENTOS = 6;
const LONGITUD_PALABRA = 6;

function iniciarWordle() {
    palabraObjetivo = diccionarioAnime[Math.floor(Math.random() * diccionarioAnime.length)].toUpperCase();
    intentoActual = 0;
    letraActual = 0;
    juegoTerminado = false;
    document.getElementById('wordle-mensaje').innerText = "";
    document.getElementById('btn-reset-wordle').style.display = "none";
    
    dibujarTablero();
    dibujarTeclado();
}

function dibujarTablero() {
    const contenedor = document.getElementById('wordle-board');
    contenedor.innerHTML = "";
    for (let i = 0; i < MAX_INTENTOS; i++) {
        let fila = document.createElement("div");
        fila.className = "board-row";
        for (let j = 0; j < LONGITUD_PALABRA; j++) {
            let cuadro = document.createElement("div");
            cuadro.id = `cuadro-${i}-${j}`;
            cuadro.className = "cuadro-wordle";
            fila.appendChild(cuadro);
        }
        contenedor.appendChild(fila);
    }
}

function dibujarTeclado() {
    const tecladoContenedor = document.getElementById('wordle-keyboard');
    tecladoContenedor.innerHTML = "";
    const filas = ["QWERTYUIOP", "ASDFGHJKL", ["ENTER", "ZXCVBNM", "←"]];

    filas.forEach(fila => {
        const divFila = document.createElement("div");
        divFila.className = "key-row";
        
        const teclas = Array.isArray(fila) ? [fila[0], ...fila[1].split(""), fila[2]] : fila.split("");
        
        teclas.forEach(t => {
            const boton = document.createElement("button");
            // ASIGNAMOS ID A CADA TECLA PARA PODER PINTARLA LUEGO
            boton.id = `tecla-${t}`;
            boton.innerText = t;
            boton.className = "tecla-wordle";
            
            if(t === "ENTER" || t === "←") {
                boton.classList.add("tecla-especial");
            }
            
            boton.onclick = () => manejarEntrada(t);
            divFila.appendChild(boton);
        });
        tecladoContenedor.appendChild(divFila);
    });
}

function manejarEntrada(tecla) {
    if (juegoTerminado) return;
    
    if (tecla === "ENTER") {
        verificarPalabra();
    } else if (tecla === "←") {
        borrarLetra();
    } else {
        insertarLetra(tecla.toUpperCase());
    }
}

// Soporte para teclado de PC
window.addEventListener("keydown", (e) => {
    if (juegoTerminado) return;
    if (e.key === "Enter") manejarEntrada("ENTER");
    else if (e.key === "Backspace") manejarEntrada("←");
    else if (e.key.length === 1 && /[a-zA-Z]/.test(e.key)) manejarEntrada(e.key.toUpperCase());
});

function insertarLetra(letra) {
    if (letraActual < LONGITUD_PALABRA && intentoActual < MAX_INTENTOS) {
        const cuadro = document.getElementById(`cuadro-${intentoActual}-${letraActual}`);
        cuadro.innerText = letra;
        cuadro.classList.add("animacion-pop");
        
        // Quitamos la clase de animacion rapido para poder reutilizarla
        setTimeout(() => cuadro.classList.remove("animacion-pop"), 150);
        
        letraActual++;
    }
}

function borrarLetra() {
    if (letraActual > 0) {
        letraActual--;
        const cuadro = document.getElementById(`cuadro-${intentoActual}-${letraActual}`);
        cuadro.innerText = "";
    }
}

function verificarPalabra() {
    if (letraActual !== LONGITUD_PALABRA) {
        document.getElementById('wordle-mensaje').innerText = "Faltan letras";
        setTimeout(() => { document.getElementById('wordle-mensaje').innerText = ""; }, 1500);
        return;
    }

    let palabraIntento = "";
    for (let i = 0; i < LONGITUD_PALABRA; i++) {
        palabraIntento += document.getElementById(`cuadro-${intentoActual}-${i}`).innerText;
    }

    let tempObjetivo = palabraObjetivo.split("");
    let filaColores = new Array(LONGITUD_PALABRA).fill("absent"); // Adaptado a la nueva longitud

    // 1. Verdes (correct)
    for (let i = 0; i < LONGITUD_PALABRA; i++) {
        if (palabraIntento[i] === tempObjetivo[i]) {
            filaColores[i] = "correct";
            tempObjetivo[i] = null;
        }
    }

    // 2. Amarillos (present)
    for (let i = 0; i < LONGITUD_PALABRA; i++) {
        if (filaColores[i] === "absent" && tempObjetivo.includes(palabraIntento[i])) {
            filaColores[i] = "present";
            tempObjetivo[tempObjetivo.indexOf(palabraIntento[i])] = null;
        }
    }

    // Aplicar clases al Tablero y al Teclado
    for (let i = 0; i < LONGITUD_PALABRA; i++) {
        // Tablero
        const cuadro = document.getElementById(`cuadro-${intentoActual}-${i}`);
        cuadro.classList.add(filaColores[i]);

        // Teclado Virtual
        let letra = palabraIntento[i];
        let claseColor = filaColores[i];
        let teclaVirtual = document.getElementById(`tecla-${letra}`);

        if (teclaVirtual) {
            // Evitar pisar una letra que ya era verde con un color inferior
            if (teclaVirtual.classList.contains("correct")) {
                continue;
            }
            if (teclaVirtual.classList.contains("present") && claseColor === "absent") {
                continue;
            }

            teclaVirtual.classList.remove("absent", "present", "correct");
            teclaVirtual.classList.add(claseColor);
        }
    }

    // Evaluar victoria o derrota
    if (palabraIntento === palabraObjetivo) {
        document.getElementById('wordle-mensaje').innerText = "Ganaste!";
        finalizarJuego();
    } else if (intentoActual === MAX_INTENTOS - 1) {
        document.getElementById('wordle-mensaje').innerText = "Perdiste. Era: " + palabraObjetivo;
        finalizarJuego();
    } else {
        intentoActual++;
        letraActual = 0;
    }
}

function finalizarJuego() {
    juegoTerminado = true;
    document.getElementById('btn-reset-wordle').style.display = "block";
}

function reiniciarWordle() {
    iniciarWordle();
}

window.onload = iniciarWordle;
</script>
</body>
</html>