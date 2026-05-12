<?php
session_start();

function callAPI($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response,true);
}

$seccion = isset($_GET['seccion']) ? $_GET['seccion'] : 'inicio';

// Variable de paginación
$pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($seccion == 'inicio') {
    $topAnime = callAPI("https://api.jikan.moe/v4/top/anime?limit=10");
    $popularAnime = callAPI("https://api.jikan.moe/v4/top/anime?filter=bypopularity&limit=10");
    $upcomingAnime = callAPI("https://api.jikan.moe/v4/seasons/upcoming?limit=10");
    $recommendedAnime = callAPI("https://api.jikan.moe/v4/recommendations/anime"); 
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Anime</title>
<link rel="stylesheet" href="../../CSS/styles.css">

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
            <a href="juegos.php" class="seccion-principal <?php echo (in_array($pagina_actual, ['juegos.php', 'juegoOpeningsAnime.php', 'juegoPersonajesAnime.php', 'juegoWordleAnime.php'])) ? 'active' : ''; ?>">Juegos</a>
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
        <button type="submit" class="search-button">
            <i>🔍</i> 
        </button>
    </div>
</nav>


<div class="container">
<?php if ($seccion == 'inicio') { ?>

<h2>Recomendados</h2>
<div class="carousel">

<?php
foreach(array_slice($recommendedAnime["data"],0,10) as $anime){

    $entry = $anime["entry"][0];

    $title = $entry["title"];
    $img = $entry["images"]["jpg"]["image_url"];
    $id = $entry["mal_id"]; // id de MyAnimeList
    $type = "anime"; // tipo fijo

    echo "
    <div class='card'>
        <a href='media.php?id=$id&type=$type'>
            <img src='$img'>
        </a>
        <p>$title</p>
    </div>
    ";
}
?>

</div>

<h2>Más Populares</h2>
<div class="carousel">

<?php
foreach($popularAnime["data"] as $anime){

    $title = $anime["title"];
    $img = $anime["images"]["jpg"]["image_url"];
    $id = $anime["mal_id"]; // <--- importante
    $type = "anime";

    echo "
    <div class='card'>
        <a href='media.php?id=$id&type=$type'>
            <img src='$img'>
        </a>
        <p>$title</p>
    </div>
    ";
}
?>

</div>

<h2>Top Anime</h2>
<div class="carousel">

<?php
foreach($topAnime["data"] as $anime){
    $title = $anime["title"];
    $img = $anime["images"]["jpg"]["image_url"];
    $id = $anime["mal_id"];
    $type = "anime";

    echo "
    <div class='card'>
        <a href='media.php?id=$id&type=$type'>
            <img src='$img'>
        </a>
        <p>$title</p>
    </div>
    ";
}
?>

</div>

<h2>Próximos Lanzamientos</h2>
<div class="carousel">

<?php
foreach(array_slice($upcomingAnime["data"],0,10) as $anime){
    $title = $anime["title"];
    $img = $anime["images"]["jpg"]["image_url"];
    $id = $anime["mal_id"];
    $type = "anime";

    echo "
    <div class='card'>
        <a href='media.php?id=$id&type=$type'>
            <img src='$img'>
        </a>
        <p>$title</p>
    </div>
    ";
}
?>

</div>

<?php } elseif ($seccion == 'populares' || $seccion == 'top') { 
    // ---- PÁGINAS DE LISTA (POPULARES Y TOP) ----
    
    // 1. Títulos y Endpoints
    $tituloSeccion = ($seccion == 'populares') ? "Animes Más Populares" : "Top Anime de Todos los Tiempos";
    $endpoint = ($seccion == 'populares') ? "top/anime?filter=bypopularity" : "top/anime?";
    // Ojo: Jikan usa un formato de paginación con '&page='
    $conector = ($seccion == 'populares') ? "&" : "";
    
    // 2. Llamada a la API de Jikan con Paginación
    $listaDatos = callAPI("https://api.jikan.moe/v4/".$endpoint.$conector."page=".$pagina."&limit=25");

    // 3. Cabecera con título y paginación superior
    echo "<div class='lista-header'>";
    echo "<h2>$tituloSeccion</h2>";
    echo "<div class='paginacion'>";
    if ($pagina > 1) {
        echo "<a href='anime.php?seccion=$seccion&page=".($pagina - 1)."' class='btn-pag'>&lt; Prev 25</a>";
    }
    echo "<a href='anime.php?seccion=$seccion&page=".($pagina + 1)."' class='btn-pag'>Next 25 &gt;</a>";
    echo "</div>";
    echo "</div>";

    // 4. Base de Datos: Comprobar qué animes tiene el usuario
    $mis_animes = [];
    $mis_estados = [];

    if (isset($_SESSION['id_usuario'])) {
        require_once(__DIR__ . "/../conexion.php");
        
        /* NOTA: Uso 'tmdb_id' porque es el nombre de la columna que tienes en BD, 
           aunque aquí se guarde el ID de MyAnimeList. Si tu BD tiene otro nombre 
           para la columna de IDs de Anime, cámbialo aquí. */
        $stmt_user = $conexion->prepare("
            SELECT m.tmdb_id, mu.status 
            FROM media_usuario mu 
            JOIN media m ON mu.id_media = m.id_media 
            WHERE mu.id_usuario = ? AND m.tmdb_id IS NOT NULL
        ");
        $stmt_user->bind_param("i", $_SESSION['id_usuario']);
        $stmt_user->execute();
        $res_user = $stmt_user->get_result();
        
        while($fila = $res_user->fetch_assoc()) {
            $mis_animes[] = $fila['tmdb_id'];
            $mis_estados[$fila['tmdb_id']] = $fila['status'];
        }
    }

    echo "<table class='tabla-nixolist'>";
    echo "<thead><tr><th style='text-align:center;'>Rank</th><th>Title</th><th style='text-align:center;'>Score</th><th style='text-align:center;'>Status</th></tr></thead>";
    echo "<tbody>";

    $rank = ($pagina - 1) * 25 + 1; 

    // Ojo: Jikan usa ["data"] en vez de ["results"]
    if(isset($listaDatos["data"]) && is_array($listaDatos["data"])) {
        foreach($listaDatos["data"] as $anime){
            $id = $anime["mal_id"];
            $img = $anime["images"]["jpg"]["image_url"];
            $title = $anime["title"];
            $date = isset($anime["year"]) ? $anime["year"] : 'N/A'; // Usamos el año para el Anime
            $score = isset($anime["score"]) ? $anime["score"] : 'N/A';

            echo "<tr>";
            echo "<td class='rank-numero'>$rank</td>";
            echo "<td class='title-cell'>";
            echo "  <img src='$img' alt='Poster' class='lista-img'>";
            echo "  <div class='title-info'>";
            echo "      <a href='media.php?id=$id&type=anime'>$title</a>";
            echo "      <p class='fecha'>$date</p>";
            echo "  </div>";
            echo "</td>";
            echo "<td class='score-cell'>⭐ $score</td>";

            $titulo_seguro = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

            // Comprobamos si ya está en la lista
            $esta_en_lista = in_array($id, $mis_animes);
            $estado_actual = $esta_en_lista ? $mis_estados[$id] : '';

            echo "<td class='status-cell' style='text-align: center;'>";

            $btn_display = $esta_en_lista ? "display: none;" : "display: inline-block;";
            echo "<button class='btn-add-list btn-add-ajax' data-id='$id' data-type='anime' data-title='$titulo_seguro' data-img='$img' style='$btn_display'>Add to My List</button>";

            $select_display = $esta_en_lista ? "display: inline-block;" : "display: none;";
            echo "<select class='status-select-ajax' data-id='$id' data-type='anime' data-title='$titulo_seguro' data-img='$img' style='$select_display background: #222; color: white; border: 1px solid #444; padding: 5px; border-radius: 3px; cursor: pointer;'>";

            $opciones = [
                'planned'   => 'Planteando verlo',
                'watching'  => 'Viendo',
                'completed' => 'Completado',
                'paused'    => 'En espera',
                'dropped'   => 'Dropeado'
            ];

            foreach($opciones as $val => $texto) {
                $sel = ($estado_actual == $val) ? 'selected' : '';
                echo "<option value='$val' $sel>$texto</option>";
            }
            echo "</select>";
            echo "</td>";
            echo "</tr>";
            
            $rank++;
        }
    }
    echo "</tbody></table>";
    
    // Paginación inferior
    echo "<div class='lista-footer'>";
    if ($pagina > 1) {
        echo "<a href='anime.php?seccion=$seccion&page=".($pagina - 1)."' class='btn-pag'>&lt; Prev 25</a>";
    }
    echo "<a href='anime.php?seccion=$seccion&page=".($pagina + 1)."' class='btn-pag'>Next 25 &gt;</a>";
    echo "</div>";

} elseif ($seccion == 'recomendados') {
    // ---- PÁGINA RECOMENDADOS (Se queda en CUADRÍCULA) ----
    echo "<h2>Animes Recomendados</h2>";
    echo "<div class='grid-galeria'>";
    $paginaRecomendados = callAPI("https://api.jikan.moe/v4/recommendations/anime");
    
    foreach(array_slice($paginaRecomendados["data"],0,25) as $anime){
        $entry = $anime["entry"][0];
        $id = $entry["mal_id"];
        $img = $entry["images"]["jpg"]["image_url"];
        echo "<div class='card'><a href='media.php?id=$id&type=anime'><img src='$img'></a><p>{$entry['title']}</p></div>";
    }
    echo "</div>";
}
?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const botonesAdd = document.querySelectorAll('.btn-add-ajax');
    const selectsStatus = document.querySelectorAll('.status-select-ajax');

    // 1. Lógica para el botón "Add to My List"
    botonesAdd.forEach(boton => {
        boton.addEventListener('click', function(e) {
            e.preventDefault();
            const botonActual = this;
            
            const datos = new FormData();
            datos.append('id_api', botonActual.dataset.id);
            datos.append('type', botonActual.dataset.type);
            datos.append('titulo', botonActual.dataset.title);
            datos.append('portada', botonActual.dataset.img);
            datos.append('action', 'add_list');
            datos.append('nuevo_status', 'planned'); // Valor por defecto al añadir

            fetch('../FUNCIONALIDADES/procesar_interaccion.php', {
                method: 'POST',
                body: datos
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success' && res.result === 'added') {
                    // Ocultamos el botón y mostramos el <select>
                    botonActual.style.display = 'none';
                    const selectAsociado = botonActual.nextElementSibling;
                    if(selectAsociado) {
                        selectAsociado.style.display = 'inline-block';
                        selectAsociado.value = 'planned';
                    }
                } else {
                    alert("Hubo un problema al añadir a la lista.");
                }
            })
            .catch(err => console.error("Error:", err));
        });
    });

    // 2. Lógica para actualizar el estado con el <select>
    selectsStatus.forEach(select => {
        select.addEventListener('change', function() {
            const nuevoEstado = this.value;

            const datos = new FormData();
            datos.append('id_api', this.dataset.id);
            datos.append('type', this.dataset.type);
            datos.append('titulo', this.dataset.title);
            datos.append('portada', this.dataset.img);
            datos.append('action', 'update_status');
            datos.append('nuevo_status', nuevoEstado);

            fetch('../FUNCIONALIDADES/procesar_interaccion.php', {
                method: 'POST',
                body: datos
            })
            .then(res => res.json())
            .then(res => {
                if (res.status !== 'success') {
                    alert("Error al actualizar el estado.");
                }
            })
            .catch(err => console.error("Error:", err));
        });
    });
});
</script>

</body>
</html>