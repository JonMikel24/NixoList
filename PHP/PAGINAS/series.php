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

$tmdb_key="0537b412710df9a2b7790cada44e494e";

$seccion = isset($_GET['seccion']) ? $_GET['seccion'] : 'inicio';

$pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($seccion == 'inicio') {
    $popular = callAPI("https://api.themoviedb.org/3/tv/popular?api_key=".$tmdb_key);
    $topRated = callAPI("https://api.themoviedb.org/3/tv/top_rated?api_key=".$tmdb_key);
    $onAir = callAPI("https://api.themoviedb.org/3/tv/on_the_air?api_key=".$tmdb_key);
    $trending = callAPI("https://api.themoviedb.org/3/trending/tv/week?api_key=".$tmdb_key);
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Series</title>
<link rel="stylesheet" href="../../CSS/styles.css">
<link rel="icon" type="image/png" href="../../Recursos/icono/icononixo.png">

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
    // Obtenemos el nombre del archivo actual (ej: anime.php)
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


<div class="container">

<?php if ($seccion == 'inicio') { ?>

<h2>Trending</h2>
<div class="carousel">
<?php
foreach(array_slice($trending["results"],0,10) as $s){
    $title = $s["name"];
    $img = "https://image.tmdb.org/t/p/w500".$s["poster_path"];
    $id = $s["id"];
    $type = "tv";

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
foreach(array_slice($popular["results"],0,10) as $s){
    $title = $s["name"];
    $img = "https://image.tmdb.org/t/p/w500".$s["poster_path"];
    $id = $s["id"];
    $type = "tv";

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

<h2>Top Rated</h2>
<div class="carousel">
<?php
foreach(array_slice($topRated["results"],0,10) as $s){
    $title = $s["name"];
    $img = "https://image.tmdb.org/t/p/w500".$s["poster_path"];
    $id = $s["id"];
    $type = "tv";

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
    
    // 1. Definimos el título y la URL según la sección
    $tituloSeccion = ($seccion == 'populares') ? "Series Más Populares" : "Top Series de Todos los Tiempos";
    $endpoint = ($seccion == 'populares') ? "tv/popular" : "tv/top_rated";
    
    // 2. Llamada a la API con la página actual
    $listaDatos = callAPI("https://api.themoviedb.org/3/".$endpoint."?api_key=".$tmdb_key."&page=".$pagina);

    // 3. Cabecera con título y paginación superior
    echo "<div class='lista-header'>";
    echo "<h2>$tituloSeccion</h2>";
    echo "<div class='paginacion'>";
    if ($pagina > 1) {
        echo "<a href='series.php?seccion=$seccion&page=".($pagina - 1)."' class='btn-pag'>&lt; Prev 20</a>";
    }
    echo "<a href='series.php?seccion=$seccion&page=".($pagina + 1)."' class='btn-pag'>Next 20 &gt;</a>";
    echo "</div>";
    echo "</div>";

    // 4. Estructura de la Tabla Adaptada
    // Comprobar qué series tiene ya el usuario en su lista
$mis_series = [];
$mis_estados = [];

    if (isset($_SESSION['id_usuario'])) {
        require_once(__DIR__ . "/../conexion.php"); // Asegúrate de que la ruta sea correcta
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
            $mis_series[] = $fila['tmdb_id'];
            $mis_estados[$fila['tmdb_id']] = $fila['status'];
        }
    }

    echo "<table class='tabla-nixolist'>";
    echo "<thead><tr><th style='text-align:center;'>Rank</th><th>Title</th><th style='text-align:center;'>Score</th><th style='text-align:center;'>Status</th></tr></thead>";
    echo "<tbody>";

    $rank = ($pagina - 1) * 20 + 1; 

    foreach($listaDatos["results"] as $s){
        $id = $s["id"];
        $img = "https://image.tmdb.org/t/p/w92" . $s["poster_path"]; 
        $title = $s["name"];
        $date = isset($s["first_air_date"]) ? $s["first_air_date"] : 'N/A';
        $score = round($s["vote_average"], 2);

        echo "<tr>";
        echo "<td class='rank-numero'>$rank</td>";
        echo "<td class='title-cell'>";
        echo "  <img src='$img' alt='Poster' class='lista-img'>";
        echo "  <div class='title-info'>";
        echo "      <a href='media.php?id=$id&type=tv'>$title</a>";
        echo "      <p class='fecha'>$date</p>";
        echo "  </div>";
        echo "</td>";
        echo "<td class='score-cell'>⭐ $score</td>";

        // Nos aseguramos de que las comillas del título no rompan el HTML
        $titulo_seguro = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        // Comprobamos si esta serie (por su ID) ya está en nuestro array de la BD
        $esta_en_lista = in_array($id, $mis_series);
        $estado_actual = $esta_en_lista ? $mis_estados[$id] : '';
        $titulo_seguro = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        echo "<td class='status-cell' style='text-align: center;'>";

        // 1. El botón de añadir (Se oculta si ya está en la lista)
        $btn_display = $esta_en_lista ? "display: none;" : "display: inline-block;";
        echo "<button class='btn-add-list btn-add-ajax' data-id='$id' data-type='tv' data-title='$titulo_seguro' data-img='$img' style='$btn_display'>Add to My List</button>";

        // 2. El selector de estado (Se muestra si ya está en la lista)
        $select_display = $esta_en_lista ? "display: inline-block;" : "display: none;";
        echo "<select class='status-select-ajax' data-id='$id' data-type='tv' data-title='$titulo_seguro' data-img='$img' style='$select_display background: #222; color: white; border: 1px solid #444; padding: 5px; border-radius: 3px; cursor: pointer;'>";

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
    echo "</tbody></table>";
    
    // 5. Paginación inferior
    echo "<div class='lista-footer'>";
    if ($pagina > 1) {
        echo "<a href='series.php?seccion=$seccion&page=".($pagina - 1)."' class='btn-pag'>&lt; Prev 20</a>";
    }
    echo "<a href='series.php?seccion=$seccion&page=".($pagina + 1)."' class='btn-pag'>Next 20 &gt;</a>";
    echo "</div>";

} elseif ($seccion == 'trending') {
    // ---- PÁGINA TRENDING (Se queda en CUADRÍCULA si quieres) ----
    echo "<h2>Series en Tendencia</h2>";
    echo "<div class='grid-galeria'>";
    $paginaTrending = callAPI("https://api.themoviedb.org/3/trending/tv/week?api_key=".$tmdb_key);
    
    foreach($paginaTrending["results"] as $s){
        $id = $s["id"];
        $img = "https://image.tmdb.org/t/p/w500" . $s["poster_path"];
        $title = $s["name"];
        echo "<div class='card'><a href='media.php?id=$id&type=tv'><img src='$img'></a><p>$title</p></div>";
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
                    // Ocultamos el botón y mostramos el <select> asociado
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
document.getElementById('search-input').addEventListener('input', function() {
    let query = this.value;
    let type = document.getElementById('search-type').value;
    let resultsContainer = document.getElementById('search-results');

    if (query.length >= 3) {
        fetch(`buscar_sugerencias.php?q=${query}&type=${type}`)
            .then(response => response.text())
            .then(data => {
                resultsContainer.innerHTML = data;
                resultsContainer.style.display = 'block'; 
            });
    } else {
        resultsContainer.style.display = 'none'; 
    }
});
</script>

</body>

</html>