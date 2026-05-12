<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Adivina el Personaje - NixoList</title>
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
            <a href="anime.php" class="seccion-principal">Anime</a>
            <div class="sub-menu">
                <a href="anime.php">Inicio Anime</a>
                <a href="anime.php?seccion=recomendados">Recomendados</a>
                <a href="anime.php?seccion=populares">Más Populares</a>
                <a href="anime.php?seccion=top">Top Anime</a>
            </div>
        </div>

        <div class="menu-desplegable">
            <a href="peliculas.php" class="seccion-principal">Películas</a>
            <div class="sub-menu">
                <a href="peliculas.php">Inicio Películas</a>
                <a href="peliculas.php?seccion=recomendadas">Recomendadas</a>
                <a href="peliculas.php?seccion=populares">Más Populares</a>
                <a href="peliculas.php?seccion=top">Top Rated</a>
            </div>
        </div>

        <div class="menu-desplegable">
            <a href="series.php" class="seccion-principal">Series</a>
            <div class="sub-menu">
                <a href="series.php">Inicio Series</a>
                <a href="series.php?seccion=trending">Trending</a>
                <a href="series.php?seccion=populares">Más Populares</a>
                <a href="series.php?seccion=top">Top Rated</a>
            </div>
        </div>

        <div class="menu-desplegable">
            <a href="juegos.php" class="seccion-principal">Juegos</a>
            <div class="sub-menu">
                <a href="juegos.php">Inicio Juegos</a>
                <a href="juegoOpeningsAnime.php">Adivina el Opening</a>
                <a href="juegoPersonajesAnime.php">Adivina el Personaje</a>
                <a href="juegoWordleAnime.php">Wordle Anime</a>
            </div>
        </div>
    </div>

    <div class="search-container">
        <select class="search-select">
            <option value="all">All</option>
            <option value="anime">Anime</option>
            <option value="manga">Manga</option>
        </select>
        <input type="text" placeholder="Search Anime, Manga, and more..." class="search-input">
        <button type="submit" class="search-button"><i>🔍</i></button>
    </div>
</nav>

<div class="container">
    <div class="game-container">
        <h2>👤 ¿Quién es este Personaje? 👤</h2>

        <div id="marcador" style="display: flex; justify-content: center; gap: 20px; margin-bottom: 30px; font-weight: bold; font-size: 1.2rem;">
            <span style="color: #007bff;">Ronda: <span id="ronda-actual">0</span>/10</span>
            <span style="color: #28a745;">Aciertos: <span id="puntos-aciertos">0</span></span>
            <span style="color: #dc3545;">Fallos: <span id="puntos-fallos">0</span></span>
        </div>

        <div id="area-juego" style="display: flex; gap: 60px; align-items: center; justify-content: center; flex-wrap: wrap;">
            
            <div style="display: flex; justify-content: center;">
                <div class="character-img-container" id="contenedor-imagen" style="margin: 0;">
                    <img id="personaje-foto" src="" class="character-img" alt="Cargando personaje...">
                </div>
            </div>

            <div style="width: 100%; max-width: 350px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                
                <div class="opciones-game" id="contenedor-opciones" style="width: 100%;">
                    </div>

                <p id="resultado-texto" style="margin-top: 20px; font-weight: bold; font-size: 1.2rem; text-align: center;">Conectando con MyAnimeList...</p>
                <button id="btn-siguiente" class="btn-siguiente" style="display:none; margin-top: 15px; width: 100%;" onclick="proximaRonda()">Siguiente Personaje ➔</button>
            </div>
        </div>

        <div id="pantalla-final" style="display: none;"></div>

    </div>
</div>

<script>
let personajesBase = [];
let personajesDisponibles = [];
let personajeCorrecto = null;
let aciertos = 0;
let fallos = 0;
let ronda = 0;
const MAX_RONDAS = 10;

async function cargarPersonajesAPI() {
    const textoResultado = document.getElementById('resultado-texto');
    
    // Elegimos una página al azar entre la 1 y la 20 (Top 500 personajes)
    let paginaAleatoria = Math.floor(Math.random() * 20) + 1;
    
    textoResultado.innerText = "Reclutando personajes de todo el mundo... 🌍";
    textoResultado.style.color = "#ccc";
    document.getElementById('contenedor-imagen').style.display = "none"; 
    
    try {
        const response = await fetch(`https://api.jikan.moe/v4/top/characters?page=${paginaAleatoria}`);
        const data = await response.json();
        
        personajesBase = data.data.map(p => ({
            nombre: p.name,
            imagen: p.images.jpg.image_url
        }));

        iniciarJuego(); 
    } catch (error) {
        textoResultado.innerText = "⚠️ Error al cargar los personajes. Comprueba tu conexión.";
        textoResultado.style.color = "#dc3545";
        console.error("Error en la API:", error);
    }
}

function iniciarJuego() {
    aciertos = 0;
    fallos = 0;
    ronda = 0;
    
    // Mostramos las dos columnas y ocultamos la pantalla final
    document.getElementById('area-juego').style.display = "flex";
    document.getElementById('pantalla-final').style.display = "none";
    
    document.getElementById('contenedor-imagen').style.display = "flex";
    
    personajesDisponibles = [...personajesBase].sort(() => 0.5 - Math.random());
    
    proximaRonda();
}

function finalizarPartida() {
    const areaJuego = document.getElementById('area-juego');
    const pantallaFinal = document.getElementById('pantalla-final');
    
    // Ocultamos las dos columnas del juego
    areaJuego.style.display = "none";

    // Mostramos la pantalla final centrada
    pantallaFinal.style.display = "block";
    pantallaFinal.innerHTML = `
        <div style="background: rgba(0,0,0,0.2); padding: 30px; border-radius: 15px; width: 100%; max-width: 500px; margin: 0 auto; text-align: center;">
            <h3 style="color: #fff; margin-bottom: 20px; font-size: 1.8rem;">¡Partida Terminada!</h3>
            <p style="font-size: 1.5rem; color: #28a745; margin-bottom: 10px;">✅ Aciertos: ${aciertos}</p>
            <p style="font-size: 1.5rem; color: #dc3545; margin-bottom: 20px;">❌ Fallos: ${fallos}</p>
            <p style="color: #ccc; font-size: 1.2rem; margin-bottom: 30px;">Tu nota final: <strong>${(aciertos / MAX_RONDAS) * 10}/10</strong></p>
            <button class="btn-siguiente" onclick="cargarPersonajesAPI()" style="margin: 0 auto; display: block;">Jugar de Nuevo 🔄</button>
        </div>
    `;
}

function proximaRonda() {
    if (ronda >= MAX_RONDAS) {
        finalizarPartida();
        return;
    }

    ronda++;
    actualizarMarcador();

    document.getElementById('btn-siguiente').style.display = "none";
    document.getElementById('resultado-texto').innerText = "¿Adivinas quién es?";
    document.getElementById('resultado-texto').style.color = "#ccc";

    personajeCorrecto = personajesDisponibles.pop();

    let incorrectos = personajesBase
        .filter(p => p.nombre !== personajeCorrecto.nombre) 
        .sort(() => 0.5 - Math.random())                    
        .slice(0, 3);                                       

    let opciones = [personajeCorrecto, ...incorrectos].sort(() => 0.5 - Math.random());

    document.getElementById('personaje-foto').src = personajeCorrecto.imagen;

    const contenedorBotones = document.getElementById('contenedor-opciones');
    contenedorBotones.innerHTML = ""; 
    
    opciones.forEach(personaje => {
        let btn = document.createElement('button');
        btn.className = 'btn-opcion'; 
        btn.innerText = personaje.nombre;
        btn.onclick = () => verificarRespuesta(btn, personaje.nombre);
        contenedorBotones.appendChild(btn);
    });
}

function verificarRespuesta(botonElegido, nombreElegido) {
    const botones = document.querySelectorAll('.btn-opcion');
    const textoResultado = document.getElementById('resultado-texto');

    botones.forEach(btn => {
        btn.disabled = true;
        if (btn.innerText === personajeCorrecto.nombre) {
            btn.classList.add('correct'); 
        }
    });

    if (nombreElegido === personajeCorrecto.nombre) {
        aciertos++;
        textoResultado.innerText = "¡CORRECTO! 🎉";
        textoResultado.style.color = "#28a745";
    } else {
        fallos++;
        botonElegido.classList.add('wrong'); 
        textoResultado.innerText = "¡FALLASTE! ❌ Era " + personajeCorrecto.nombre;
        textoResultado.style.color = "#dc3545";
    }

    actualizarMarcador();
    
    const btnSiguiente = document.getElementById('btn-siguiente');
    btnSiguiente.innerText = (ronda === MAX_RONDAS) ? "Ver Resultados Finales ➔" : "Siguiente Personaje ➔";
    btnSiguiente.style.display = "block";
}

function actualizarMarcador() {
    document.getElementById('ronda-actual').innerText = ronda;
    document.getElementById('puntos-aciertos').innerText = aciertos;
    document.getElementById('puntos-fallos').innerText = fallos;
}

window.onload = cargarPersonajesAPI;
</script>

</body>
</html>