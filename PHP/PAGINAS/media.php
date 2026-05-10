<?php
session_start();

function callAPI($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'NixoList/1.0');
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response,true);
}

$tmdb_key = "0537b412710df9a2b7790cada44e494e";
$id = $_GET['id'] ?? 0;
$type = $_GET['type'] ?? 'movie'; 
$view = $_GET['view'] ?? 'details'; 

$title = ""; $img = ""; $desc = ""; $score = ""; $status = "N/A"; $genres = []; $extra_info = [];

// 1. OBTENER DATOS PRINCIPALES
if($type == "anime"){
    $media = callAPI("https://api.jikan.moe/v4/anime/".$id);
    $data = $media["data"] ?? null;
    if($data){
        $title = $data["title"];
        $img = $data["images"]["webp"]["large_image_url"];
        $desc = $data["synopsis"];
        $score = $data["score"] ?? "N/A";
        $status = $data["status"];
        $genres = $data["genres"];
        $trailer_url = $data["trailer"]["embed_url"] ?? "";
        $extra_info = [
            "Tipo" => $data["type"], 
            "Emitido" => $data["aired"]["string"], 
            "Estudio" => $data["studios"][0]["name"] ?? "N/A", 
            "Duración" => $data["duration"]
        ];
    }
} elseif ($type == "manga") {
    // NUEVA LÓGICA PARA MANGA
    $media = callAPI("https://api.jikan.moe/v4/manga/".$id);
    $data = $media["data"] ?? null;
    if($data){
        $title = $data["title"];
        $img = $data["images"]["webp"]["large_image_url"];
        $desc = $data["synopsis"];
        $score = $data["score"] ?? "N/A";
        $status = $data["status"];
        $genres = $data["genres"];
        // Los mangas no tienen trailers ni estudios, sino autores, capítulos y volúmenes
        $trailer_url = ""; 
        $extra_info = [
            "Tipo" => $data["type"] ?? "Manga", 
            "Publicado" => $data["published"]["string"] ?? "N/A", 
            "Autor" => $data["authors"][0]["name"] ?? "N/A", 
            "Capítulos" => $data["chapters"] ?? "N/A",
            "Volúmenes" => $data["volumes"] ?? "N/A"
        ];
    }
} else {
    $media = callAPI("https://api.themoviedb.org/3/".$type."/".$id."?api_key=".$tmdb_key."&language=es-ES");
    if($media){
        $title = $media["title"] ?? $media["name"];
        $img = "https://image.tmdb.org/t/p/w500".$media["poster_path"];
        $desc = $media["overview"];
        $score = number_format($media["vote_average"], 1);
        $status = $media["status"];
        $genres = $media["genres"] ?? [];
    }
}

$mi_nota = 0;
$en_lista = false;
$es_fav = false;

if (isset($_SESSION['id_usuario'])) {
    require_once("../conexion.php"); // Asegúrate de que la ruta a tu conexión es correcta
    $stmt_user = $conexion->prepare("SELECT mu.puntuacion, mu.status, mu.es_favorito 
                                    FROM media_usuario mu 
                                    JOIN media m ON mu.id_media = m.id_media 
                                    WHERE mu.id_usuario = ? AND (m.tmdb_id = ? OR m.mal_id = ?)");
    $stmt_user->bind_param("iss", $_SESSION['id_usuario'], $id, $id);
    $stmt_user->execute();
    $res_user = $stmt_user->get_result();
    if($fila = $res_user->fetch_assoc()) {
        $mi_nota = $fila['puntuacion'] ?? 0;
        $en_lista = !empty($fila['status']);
        $es_fav = ($fila['es_favorito'] == 1);
    }
}

// 2. LOGICA ESPECIFICA DE CADA PESTAÑA
$characters = []; $episodes_list = []; $videos_list = []; $reviews_list = []; $stats = [];

switch($view) {
    case 'characters':
        if($type == 'anime') $characters = callAPI("https://api.jikan.moe/v4/anime/".$id."/characters")["data"] ?? [];
        elseif($type == 'manga') $characters = callAPI("https://api.jikan.moe/v4/manga/".$id."/characters")["data"] ?? [];
        else $characters = callAPI("https://api.themoviedb.org/3/".$type."/".$id."/credits?api_key=".$tmdb_key)["cast"] ?? [];
        break;
    case 'episodes':
        if($type == 'anime') $episodes_list = callAPI("https://api.jikan.moe/v4/anime/".$id."/episodes")["data"] ?? [];
        // Jikan no tiene listado de capítulos para manga, así que se queda vacío
        break;
    case 'videos':
        if($type == 'anime') {
            $res = callAPI("https://api.jikan.moe/v4/anime/".$id."/videos");
            $videos_list = $res["data"]["promo"] ?? []; 
        } elseif ($type == 'manga') {
            // Los mangas no tienen trailers en la API
            $videos_list = [];
        } else {
            $videos_list = callAPI("https://api.themoviedb.org/3/".$type."/".$id."/videos?api_key=".$tmdb_key)["results"] ?? [];
        }
        break;
    case 'stats':
        if($type == 'anime') $stats = callAPI("https://api.jikan.moe/v4/anime/".$id."/statistics")["data"] ?? [];
        elseif($type == 'manga') $stats = callAPI("https://api.jikan.moe/v4/manga/".$id."/statistics")["data"] ?? [];
        break;
    case 'reviews':
        if($type == 'anime') $reviews_list = callAPI("https://api.jikan.moe/v4/anime/".$id."/reviews")["data"] ?? [];
        elseif($type == 'manga') $reviews_list = callAPI("https://api.jikan.moe/v4/manga/".$id."/reviews")["data"] ?? [];
        else $reviews_list = callAPI("https://api.themoviedb.org/3/".$type."/".$id."/reviews?api_key=".$tmdb_key)["results"] ?? [];
        break;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="../../CSS/styles.css">
    <link rel="stylesheet" href="../../CSS/media.css">
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
                $Foto = (!empty($_SESSION['Foto'])) ? $_SESSION['Foto']  : '../../Recursos/fotos_perfil/fotousuario.png';
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

<nav class="navbar">
    <div class="nav-links">
        
        <a href="index.php" class="<?php echo ($pagina_actual == 'index.php') ? 'active' : ''; ?>">Inicio</a>

        <div class="menu-desplegable">
            <a href="anime.php" class="seccion-principal <?php echo ($pagina_actual == 'anime.php') ? 'active' : ''; ?>">Anime</a>
            <div class="sub-menu">
                <a href="anime.php">Inicio Anime</a>
                <a href="anime.php#recomendados">Recomendados</a>
                <a href="anime.php#populares">Más Populares</a>
                <a href="anime.php#top">Top Anime</a>
            </div>
        </div>
                <div class="menu-desplegable">
            <a href="manga.php" class="seccion-principal <?php echo ($pagina_actual == 'manga.php') ? 'active' : ''; ?>">Manga</a>
            <div class="sub-menu">
                <a href="manga.php">Inicio Manga</a>
                <a href="manga.php#recomendados">Recomendados</a>
                <a href="manga.php#populares">Más Populares</a>
                <a href="manga.php#top">Top Manga</a>
            </div>
        </div>


        <div class="menu-desplegable">
            <a href="peliculas.php" class="seccion-principal <?php echo ($pagina_actual == 'peliculas.php') ? 'active' : ''; ?>">Películas</a>
            <div class="sub-menu">
                <a href="peliculas.php">Inicio Películas</a>
                <a href="peliculas.php#recomendadas">Recomendadas</a>
                <a href="peliculas.php#populares">Más Populares</a>
                <a href="peliculas.php#top">Top Rated</a>
            </div>
        </div>

        <div class="menu-desplegable">
            <a href="series.php" class="seccion-principal <?php echo ($pagina_actual == 'series.php') ? 'active' : ''; ?>">Series</a>
            <div class="sub-menu">
                <a href="series.php">Inicio Series</a>
                <a href="series.php#trending">Trending</a>
                <a href="series.php#populares">Más Populares</a>
                <a href="series.php#top">Top Rated</a>
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

<div class="main-wrapper">
    <aside class="side-info">
        <img src="<?php echo $img; ?>" class="media-poster" id="media-img">
        
        <button class="btn-action-list" id="btnAddList">+ AÑADIR A MI LISTA</button>
        <button class="btn-action-list" id="btnFav">❤ AÑADIR A FAVORITOS</button>
        
        <div class="info-box">
            <h3>INFORMATION</h3>
            <div class="info-unit"><b>Status:</b> <?php echo $status; ?></div>
            <?php foreach($extra_info as $label => $value): ?>
                <div class="info-unit"><b><?php echo $label; ?>:</b> <?php echo $value; ?></div>
            <?php endforeach; ?>
        </div>
    </aside>

    <main class="main-content">
        <div class="media-header-title"><h1><?php echo htmlspecialchars($title); ?></h1></div>

        <nav class="media-tabs">
            <a href="?id=<?php echo $id; ?>&type=<?php echo $type; ?>&view=details" class="<?php echo ($view=='details')?'active':''; ?>">Details</a>
            <a href="?id=<?php echo $id; ?>&type=<?php echo $type; ?>&view=characters" class="<?php echo ($view=='characters')?'active':''; ?>">Characters</a>
            <a href="?id=<?php echo $id; ?>&type=<?php echo $type; ?>&view=episodes" class="<?php echo ($view=='episodes')?'active':''; ?>">Episodes</a>
            <a href="?id=<?php echo $id; ?>&type=<?php echo $type; ?>&view=videos" class="<?php echo ($view=='videos')?'active':''; ?>">Videos</a>
            <a href="?id=<?php echo $id; ?>&type=<?php echo $type; ?>&view=stats" class="<?php echo ($view=='stats')?'active':''; ?>">Stats</a>
            <a href="?id=<?php echo $id; ?>&type=<?php echo $type; ?>&view=reviews" class="<?php echo ($view=='reviews')?'active':''; ?>">Reviews</a>
        </nav>

        <div class="content-body">
            <?php if($view == 'details'): ?>
                <div class="score-box" style="display: flex; align-items: center; gap: 20px;">
                    <div class="score-value">★ <?php echo $score; ?></div>
                    <div class="score-stats">
                        <div class="score-label">RANKING</div>
                        <div class="score-rank">#<?php echo rand(1, 2000); ?></div>
                    </div>

                    <div class="score-user-rating" style="margin-left: 20px; border-left: 1px solid #444; padding-left: 20px; display: flex; align-items: center; gap: 20px;">
                        
                        <div>
                            <div class="score-label" style="font-size: 10px; color: #aaa; margin-bottom: 5px;">TU NOTA</div>
                            <div id="star-rating-container" style="display: flex; gap: 2px; cursor: pointer; font-size: 18px;">
                                <?php for($i=1; $i<=10; $i++): 
                                    $color = ($i <= $mi_nota) ? '#ffdd1c' : '#444';
                                ?>
                                    <span class="star" data-value="<?php echo $i; ?>" style="color: <?php echo $color; ?>; transition: color 0.2s;">★</span>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div id="status-select-container" style="display: <?php echo ($en_lista || $mi_nota > 0) ? 'block' : 'none'; ?>;">
                            <div class="score-label" style="font-size: 10px; color: #aaa; margin-bottom: 5px;">ESTADO</div>
                            <select id="status-media-selector" style="background: #222; color: white; border: 1px solid #444; padding: 3px 5px; border-radius: 3px; font-size: 12px; cursor: pointer;">
                            <?php 
                                $status_actual = $fila['status'] ?? ''; 
                                $opciones = [
                                    'watching'  => 'Viendo',
                                    'completed' => 'Completado',
                                    'paused'    => 'En espera', // Cambiado a 'paused' por tu base de datos
                                    'dropped'   => 'Dropeado',
                                    'planned'   => 'Planteando verlo'
                                ];
                                foreach($opciones as $val => $texto) {
                                    $sel = ($status_actual == $val) ? 'selected' : '';
                                    echo "<option value='$val' $sel>$texto</option>";
                                }
                            ?>
                        </select>
                        </div>
                    </div>
                </div>
                <h2 class="section-title">Synopsis</h2>
                <p><?php echo nl2br($desc); ?></p>
                <?php if(!empty($trailer_url)): ?>
                    <h2 class="section-title">Trailer</h2>
                    <div class="video-container"><iframe src="<?php echo $trailer_url; ?>" frameborder="0" allowfullscreen></iframe></div>
                <?php endif; ?>

            <?php elseif($view == 'videos'): ?>
                <h2 class="section-title">Promotional Videos</h2>
                <div class="videos-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                    <?php foreach($videos_list as $vid): 
                        $v_url = $vid['trailer']['embed_url'] ?? (isset($vid['key']) ? "https://www.youtube.com/embed/".$vid['key'] : null);
                        if($v_url): ?>
                        <div class="video-item">
                            <iframe width="100%" height="250" src="<?php echo $v_url; ?>" frameborder="0" allowfullscreen></iframe>
                            <p style="font-size:12px; margin-top:5px;"><?php echo $vid['title'] ?? 'Trailer'; ?></p>
                        </div>
                    <?php endif; endforeach; ?>
                </div>
            <?php elseif($view == 'episodes'): ?>
            <h2 class="section-title">Episode List</h2>
            <table class="ep-table">
                <thead><tr><th>#</th><th>Título</th><th>Aired</th></tr></thead>
                <tbody>
                    <?php foreach($episodes_list as $ep): ?>
                    <tr><td><?php echo $ep['mal_id']; ?></td><td><?php echo $ep['title']; ?></td><td><?php echo $ep['aired']; ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php elseif($view == 'stats'): ?>
                <h2 class="section-title">Summary Stats</h2>
                <?php if(!empty($stats)): ?>
                    <div class="stats-list" style="background: #1a1a1a; padding: 20px; border-radius: 5px;">
                        <p><b>Watching:</b> <?php echo number_format($stats['watching']); ?></p>
                        <p><b>Completed:</b> <?php echo number_format($stats['completed']); ?></p>
                        <p><b>On Hold:</b> <?php echo number_format($stats['on_hold']); ?></p>
                        <p><b>Dropped:</b> <?php echo number_format($stats['dropped']); ?></p>
                        <p><b>Plan to Watch:</b> <?php echo number_format($stats['plan_to_watch']); ?></p>
                        <p style="border-top: 1px solid #333; margin-top: 10px; padding-top: 10px;"><b>Total Members:</b> <?php echo number_format($stats['total']); ?></p>
                    </div>
                <?php else: echo "No stats available for this media."; endif; ?>

            <?php elseif($view == 'reviews'): ?>
            <h2 class="section-title">User Reviews</h2>
            <?php foreach($reviews_list as $rev): ?>
                <div class="review-card" style="background:#1a1a1a; padding:15px; margin-bottom:10px; border-radius:5px;">
                    <b style="color:#ffdd1c;"><?php echo $rev['user']['username'] ?? $rev['author']; ?></b>
                    <p style="font-size:13px; color:#ccc;"><?php echo substr($rev['review'] ?? $rev['content'], 0, 500); ?>...</p>
                </div>
            <?php endforeach; ?>
            <?php elseif($view == 'characters'): ?>
                    <?php 
                    // --- 1. Averiguamos el ID del Usuario ---
                    // (Asegúrate de tener session_start() al principio de media.php si no lo tienes)
                    $id_usuario_actual = $_SESSION['id_usuario'] ?? 0; 
                    
                    // --- 2. Averiguamos el ID de la serie en TU base de datos ---
                    // Asumimos que $id es la variable que guarda el ID de la API (Jikan o TMDB)
                    // Cambia $pdo por $conexion si en este archivo usas MySQLi en lugar de PDO
                    $stmt_media = $pdo->prepare("SELECT id_media FROM media WHERE tmdb_id = ? OR mal_id = ?");
                    $stmt_media->execute([$id, $id]);
                    $media_row = $stmt_media->fetch(PDO::FETCH_ASSOC);
                    $id_media_actual = $media_row ? $media_row['id_media'] : 0;

                    // --- 3. Buscamos los favoritos con las variables correctas ---
                    $ids_favoritos = [];
                    if ($id_usuario_actual > 0 && $id_media_actual > 0) {
                        $stmt_check_favs = $pdo->prepare("SELECT personaje_id FROM personajes_usuario WHERE id_usuario = ? AND id_media = ?");
                        $stmt_check_favs->execute([$id_usuario_actual, $id_media_actual]);
                        $ids_favoritos = $stmt_check_favs->fetchAll(PDO::FETCH_COLUMN); 
                    }
                    ?>

                <h2 class="section-title">Characters & Staff</h2>
                <table class="characters-table">
                    <?php foreach(array_slice($characters, 0, 15) as $char): 
                        $c_id = $char['character']['mal_id'] ?? $char['id'] ?? 0;
                        $c_name = $char['character']['name'] ?? $char['name'];
                        $c_img = $char['character']['images']['jpg']['image_url'] ?? ($char['profile_path'] ? "https://image.tmdb.org/t/p/w185".$char['profile_path'] : "../../Recursos/no-image.png");
                        
                        // --- NUEVO: Comprobar si este personaje específico está en la lista de favoritos ---
                        $es_favorito = in_array($c_id, $ids_favoritos);
                    ?>
                    <tr class="char-row">
                        <td class="char-img-cell"><img src="<?php echo $c_img; ?>" width="50"></td>
                        <td class="char-info-cell"><a href="#"><?php echo $c_name; ?></a><p><?php echo $char['role'] ?? 'Cast'; ?></p></td>
                        <td style="text-align: right; padding-right: 15px;">
                            <span class="btn-fav-char" 
                                data-char-id="<?php echo $c_id; ?>" 
                                data-char-name="<?php echo htmlspecialchars($c_name); ?>"
                                data-char-img="<?php echo htmlspecialchars($c_img); ?>"
                                style="cursor:pointer; font-size: 18px; color: <?php echo $es_favorito ? '#e74c3c' : '#444'; ?>; transition: 0.3s;">
                                ❤
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnList = document.getElementById('btnAddList');
    const btnFav = document.getElementById('btnFav');
    
    const statusContainer = document.getElementById('status-select-container');
    const statusSelector = document.getElementById('status-media-selector');

    function enviarSolicitud(tipoAccion, boton) {
    const datos = new FormData();
    datos.append('id_api', '<?php echo $id; ?>');
    datos.append('type', '<?php echo $type; ?>');
    datos.append('titulo', <?php echo json_encode($title); ?>);
    datos.append('portada', '<?php echo $img; ?>');
    datos.append('action', tipoAccion);

    if (tipoAccion === 'add_list') {
    datos.append('nuevo_status', 'planned'); // Valor por defecto al añadir
}

    fetch('../funcionalidades/procesar_interaccion.php', {
        method: 'POST',
        body: datos
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            if (res.result === 'added') {
                boton.style.backgroundColor = '#2ecc71'; 
                boton.style.color = 'white';
                boton.innerText = (tipoAccion === 'favorite') ? '❤ EN FAVORITOS' : '✓ EN MI LISTA';
                
                // --- ESTO ES LO QUE FALTA ---
                if (tipoAccion === 'add_list' && statusContainer) {
                    statusContainer.style.display = 'block';
                }
                // -----------------------------
                
            } else {
                boton.style.backgroundColor = '';
                boton.style.color = '';
                boton.innerText = (tipoAccion === 'favorite') ? '❤ AÑADIR A FAVORITOS' : '+ AÑADIR A MI LISTA';
                
                // Ocultar si se quita de la lista y no hay nota puesta
                if (tipoAccion === 'add_list' && statusContainer && currentRating === 0) {
                    statusContainer.style.display = 'none';
                }
            }
        }
    })
    .catch(err => console.error("Error:", err));
}

    if(btnList) btnList.addEventListener('click', () => enviarSolicitud('add_list', btnList));
    if(btnFav) btnFav.addEventListener('click', () => enviarSolicitud('favorite', btnFav));

    const stars = document.querySelectorAll('#star-rating-container .star');
    // Forzamos que sea un número entero
    let currentRating = parseInt(<?php echo (int)$mi_nota; ?>) || 0; 

    function highlightStars(value) {
        // Convertimos el valor a número para evitar el error del "1" y "10"
        const numValue = parseInt(value);
        
        stars.forEach(s => {
            const starValue = parseInt(s.dataset.value);
            if (starValue <= numValue) {
                s.style.setProperty('color', '#ffdd1c', 'important');
            } else {
                s.style.setProperty('color', '#444', 'important');
            }
        });
    }

    // Ejecutar inmediatamente al cargar para limpiar la estrella 10
    highlightStars(currentRating);

    stars.forEach(star => {
        star.addEventListener('mouseover', function() {
            highlightStars(this.dataset.value);
        });

        star.addEventListener('mouseout', function() {
            highlightStars(currentRating);
        });

        star.addEventListener('click', function() {
            currentRating = parseInt(this.dataset.value);
            highlightStars(currentRating); // Refrescar visualmente al hacer clic
            enviarNota(currentRating);
        });
    });

    function enviarNota(nota) {
        const datos = new FormData();
        datos.append('id_api', '<?php echo $id; ?>');
        datos.append('type', '<?php echo $type; ?>');
        datos.append('titulo', <?php echo json_encode($title); ?>);
        datos.append('portada', '<?php echo $img; ?>');
        datos.append('action', 'rate'); 
        datos.append('puntuacion', nota);

        fetch('../funcionalidades/procesar_interaccion.php', {
            method: 'POST',
            body: datos
        })
        .then(res => res.json())
        .then(res => {
            if (res.status !== 'success') alert(res.message);
        })
        .catch(err => console.error("Error:", err));
    }

    if (statusSelector) {
        statusSelector.addEventListener('change', function() {
            const nuevoEstado = this.value;
            const datos = new FormData();
            datos.append('id_api', '<?php echo $id; ?>');
            datos.append('type', '<?php echo $type; ?>');
            datos.append('titulo', <?php echo json_encode($title); ?>);
            datos.append('portada', '<?php echo $img; ?>');
            datos.append('action', 'update_status');
            datos.append('nuevo_status', nuevoEstado);

            fetch('../funcionalidades/procesar_interaccion.php', {
                method: 'POST',
                body: datos
            })
            .then(res => res.json())
            .then(res => {
                if (res.status !== 'success') alert("Error al actualizar estado");
            })
            .catch(err => console.error("Error:", err));
        });
    }

    // Inicialización de estados al cargar la página
    if (<?php echo $en_lista ? 'true' : 'false'; ?>) {
        const bL = document.getElementById('btnAddList');
        if(bL) { bL.style.backgroundColor = '#2ecc71'; bL.style.color = 'white'; bL.innerText = '✓ EN MI LISTA'; }
    }
    
    if (<?php echo $es_fav ? 'true' : 'false'; ?>) {
        const bF = document.getElementById('btnFav');
        if(bF) { bF.style.backgroundColor = '#2ecc71'; bF.style.color = 'white'; bF.innerText = '❤ EN FAVORITOS'; }
    }

// Lógica para Personaje Favorito
    document.querySelectorAll('.btn-fav-char').forEach(btn => {
        btn.addEventListener('click', function() {
            const datos = new FormData();
            datos.append('id_api', '<?php echo $id; ?>');
            datos.append('type', '<?php echo $type; ?>');
            datos.append('titulo', <?php echo json_encode($title); ?>);
            datos.append('portada', '<?php echo $img; ?>');
            datos.append('action', 'fav_character');
            
            datos.append('char_id', this.dataset.charId);
            datos.append('char_name', this.dataset.charName);
            datos.append('char_img', this.dataset.charImg); 

            fetch('../funcionalidades/procesar_interaccion.php', {
                method: 'POST',
                body: datos
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    // Solo cambiamos el estado del botón que hemos clicado
                    if (res.result === 'added') {
                        this.style.color = '#e74c3c'; // Se pone rojo al añadir
                    } else if (res.result === 'removed') {
                        this.style.color = '#444'; // Se vuelve gris al quitar
                    }
                }
            })
            // ... dentro del fetch ...
            .then(res => {
                if (res.status === 'success') {
                    if (res.result === 'added') {
                        this.style.color = '#e74c3c'; // Se marca en rojo
                        console.log("Añadido a favoritos");
                    } else if (res.result === 'removed') {
                        this.style.color = '#444'; // Se vuelve gris
                        console.log("Eliminado de favoritos");
                    }
                }
            })
            .catch(err => console.error("Error:", err));
            
        });
    });
});
</script>

</body>
</html>