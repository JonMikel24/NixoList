<?php
session_start();
require_once("../conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../Login/Index.php");
    exit;
}

if (!isset($conexion)) {
    die("Error: Database connection not established.");
}

$mi_id = $_SESSION['id_usuario'];

$sql_req = "SELECT a.id_usuario, u.username, u.avatar 
            FROM amigos a 
            JOIN usuarios u ON a.id_usuario = u.id_usuario 
            WHERE a.id_amigo_usuario = ? AND a.status = 'pendiente'";
$stmt_req = $conexion->prepare($sql_req);
$stmt_req->bind_param("i", $mi_id);
$stmt_req->execute();
$solicitudes = $stmt_req->get_result();


$sql_amigos = "SELECT u.id_usuario, u.username, u.avatar 
               FROM amigos a 
               JOIN usuarios u ON (a.id_amigo_usuario = u.id_usuario OR a.id_usuario = u.id_usuario)
               WHERE (a.id_usuario = ? OR a.id_amigo_usuario = ?) 
               AND a.status = 'aceptado' AND u.id_usuario != ?";
$stmt_ami = $conexion->prepare($sql_amigos);
$stmt_ami->bind_param("iii", $mi_id, $mi_id, $mi_id);
$stmt_ami->execute();
$mis_amigos = $stmt_ami->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Anime</title>
<link rel="stylesheet" href="../../CSS/styles.css">
<link rel="stylesheet" href="../../CSS/listaperfil.css">

</head>
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
<body>
    <?php
    
    $pagina_actual = basename($_SERVER['PHP_SELF']);
    ?>

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
                <option value="all">All</option>
                <option value="anime">Anime</option>
                <option value="manga">Manga</option>
                <option value="movie">Películas</option>
                <option value="tv">Series</option>
            </select>
            <input type="text" id="search-input" placeholder="Search..." class="search-input" autocomplete="off">
            <button type="submit" class="search-button"><i>🔍</i></button>
        </div>
        <div id="search-results" class="search-results-dropdown"></div>
    </div>
</nav>
<body>

<div class="main-wrapper friends-page-layout">
    
    <div class="friends-main">
        <div class="friends-section">
            <h2 class="section-title">Mis Amigos</h2>
            
            <?php if ($mis_amigos->num_rows > 0): ?>
                <div class="friends-grid">
                    <?php while($amigo = $mis_amigos->fetch_assoc()): ?>
                        <a href="listaperfil.php?id=<?php echo $amigo['id_usuario']; ?>" class="friend-item">
                            <img src="<?php echo $amigo['avatar'] ?: '../../Recursos/fotousuario.png'; ?>" alt="<?php echo htmlspecialchars($amigo['username']); ?>">
                            <div class="friend-overlay">
                                <span class="friend-name"><?php echo htmlspecialchars($amigo['username']); ?></span>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p style="color:#888; font-style: italic;">Aún no tienes amigos agregados.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="friends-sidebar">
        <div class="search-section">
            <h2 class="section-title">Buscar Usuarios</h2>
            <input type="text" id="search-user" class="search-box" placeholder="Ej: NarutoFan99...">
            <div id="search-results"></div>
        </div>

        <?php if ($solicitudes->num_rows > 0): ?>
        <div class="requests-section">
            <h2 class="section-title">Solicitudes</h2>
            
            <?php while($sol = $solicitudes->fetch_assoc()): ?>
                <div class="request-card" id="req-<?php echo $sol['id_usuario']; ?>">
                    <div class="request-info">
                        <img src="<?php echo $sol['avatar'] ?: '../../Recursos/fotos_perfil/fotousuario.png'; ?>" alt="Avatar">
                        <span><?php echo htmlspecialchars($sol['username']); ?></span>
                    </div>
                    <div class="req-buttons">
                        <button class="btn-accept" onclick="responderSolicitud(<?php echo $sol['id_usuario']; ?>, 'aceptar')">✓</button>
                        <button class="btn-reject" onclick="responderSolicitud(<?php echo $sol['id_usuario']; ?>, 'rechazar')">✕</button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
    </div>

</div>
<script>
// BUSCADOR
document.getElementById('search-user').addEventListener('input', function() {
    const query = this.value;
    if (query.length < 2) { document.getElementById('search-results').innerHTML = ''; return; }
    fetch('../funcionalidades/procesar_amigos.php?q=' + query) 
    .then(res => res.text())
    .then(data => { document.getElementById('search-results').innerHTML = data; });
});

// AÑADIR
function gestionarAmigo(idAmigo, boton) {
    const datos = new FormData();
    datos.append('id_amigo', idAmigo);
    datos.append('accion', 'enviar_solicitud');
    fetch('../funcionalidades/procesar_amigos.php', { method: 'POST', body: datos })
    .then(res => res.json())
    .then(res => {
        if(res.status === 'success') {
            boton.innerText = 'Pendiente';
            boton.classList.add('pendiente');
            boton.disabled = true;
        }
    });
}

// RESPONDER
function responderSolicitud(idAmigo, tipo) {
    const datos = new FormData();
    datos.append('id_amigo', idAmigo);
    datos.append('accion', tipo);
    fetch('../funcionalidades/procesar_amigos.php', { method: 'POST', body: datos })
    .then(res => res.json())
    .then(res => {
        if(res.status === 'success') {
            location.reload(); 
        }
    });
}
</script>
</body>
</html>