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
        $extra_info = ["Tipo" => $data["type"], "Emitido" => $data["aired"]["string"], "Estudio" => $data["studios"][0]["name"] ?? "N/A", "Duración" => $data["duration"]];
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
        else $characters = callAPI("https://api.themoviedb.org/3/".$type."/".$id."/credits?api_key=".$tmdb_key)["cast"] ?? [];
        break;
    case 'episodes':
        if($type == 'anime') $episodes_list = callAPI("https://api.jikan.moe/v4/anime/".$id."/episodes")["data"] ?? [];
        break;
    case 'videos':
        if($type == 'anime') {
            // Jikan devuelve trailers en un formato distinto
            $res = callAPI("https://api.jikan.moe/v4/anime/".$id."/videos");
            $videos_list = $res["data"]["promo"] ?? []; // Promos (trailers)
        } else {
            $videos_list = callAPI("https://api.themoviedb.org/3/".$type."/".$id."/videos?api_key=".$tmdb_key)["results"] ?? [];
        }
        break;
    case 'stats':
        if($type == 'anime') $stats = callAPI("https://api.jikan.moe/v4/anime/".$id."/statistics")["data"] ?? [];
        break;
    case 'reviews':
    $reviews_list = [];
    if (isset($conexion)) {
        $stmt_rev = $conexion->prepare("
            SELECT r.*, 
                   r.texto_resena AS contenido, 
                   u.username AS Usuario, 
                   u.avatar AS Foto 
            FROM resenas r 
            JOIN usuarios u ON r.id_usuario = u.id_usuario 
            JOIN media m ON r.id_media = m.id_media 
            WHERE m.tmdb_id = ? OR m.mal_id = ?
            ORDER BY r.created_at DESC");
        $stmt_rev->bind_param("ss", $id, $id);
        $stmt_rev->execute();
        $reviews_list = $stmt_rev->get_result()->fetch_all(MYSQLI_ASSOC);
    }
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
                <h2 class="section-title">Reseñas de la Comunidad</h2>

                <?php if(isset($_SESSION['id_usuario'])): ?>
                    <div class="write-review-container" style="background: #1a1a1a; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #333;">
                        <h3 style="color: #ffdd1c; margin-top: 0;">Escribe tu reseña</h3>
                        <textarea id="user-review-text" placeholder="¿Qué te ha parecido? (Evita spoilers si es posible)" 
                            style="width: 100%; height: 100px; background: #000; color: white; border: 1px solid #444; padding: 10px; border-radius: 5px; resize: none;"></textarea>
                        <button id="btnSendReview" class="btn-action-list" style="margin-top: 10px; width: auto; padding: 10px 20px;">Publicar Reseña</button>
                    </div>
                <?php else: ?>
                    <p style="color: #aaa; background: #1a1a1a; padding: 15px; border-radius: 5px;">Debes <a href="../Login/Index.php" style="color: #ffdd1c;">iniciar sesión</a> para escribir una reseña.</p>
                <?php endif; ?>

                <div id="reviews-container">
                    <?php if(!empty($reviews_list)): ?>
                        <?php foreach($reviews_list as $rev): ?>
                            <div class="review-card" style="background:#1a1a1a; padding:20px; margin-bottom:15px; border-radius:8px; border-left: 4px solid #ffdd1c;">
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                    <img src="<?php echo !empty($rev['Foto']) ? $rev['Foto'] : '../../Recursos/fotousuario.png'; ?>" 
                                        style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                    <div>
                                        <b style="color:#ffdd1c; display: block;"><?php echo htmlspecialchars($rev['Usuario']); ?></b>
                                        <small style="color: #666;"><?php echo date("d M, Y", strtotime($rev['created_at'])); ?></small>
                                    </div>
                                </div>
                                <p style="font-size:14px; color:#eee; line-height: 1.5; white-space: pre-wrap;"><?php echo htmlspecialchars($rev['contenido']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #666; margin-top: 20px;">Aún no hay reseñas. ¡Sé el primero en escribir una!</p>
                    <?php endif; ?>
                </div>

            <?php elseif($view == 'characters'): ?>
                <h2 class="section-title">Characters & Staff</h2>
                <?php 
                    // Lógica para obtener favoritos usando $conexion (MySQLi)
                    $ids_favoritos = [];
                    if (isset($_SESSION['id_usuario'])) {
                        $stmt_favs = $conexion->prepare("
                            SELECT personaje_id FROM personajes_usuario pu
                            JOIN media m ON pu.id_media = m.id_media
                            WHERE pu.id_usuario = ? AND (m.tmdb_id = ? OR m.mal_id = ?)
                        ");
                        $stmt_favs->bind_param("iss", $_SESSION['id_usuario'], $id, $id);
                        $stmt_favs->execute();
                        $result_favs = $stmt_favs->get_result();
                        while($row_f = $result_favs->fetch_assoc()){
                            $ids_favoritos[] = $row_f['personaje_id'];
                        }
                    }
                ?>
                <table class="characters-table">
                    <?php foreach(array_slice($characters, 0, 15) as $char): 
                        $c_id = $char['character']['mal_id'] ?? $char['id'] ?? 0;
                        $c_name = $char['character']['name'] ?? $char['name'];
                        $c_img = $char['character']['images']['jpg']['image_url'] ?? ($char['profile_path'] ? "https://image.tmdb.org/t/p/w185".$char['profile_path'] : "../../Recursos/no-image.png");
                        $es_favorito = in_array($c_id, $ids_favoritos);
                    ?>
                    <tr class="char-row">
                        <td class="char-img-cell"><img src="<?php echo $c_img; ?>" width="50" style="border-radius:4px;"></td>
                        <td class="char-info-cell">
                            <a href="javascript:void(0)" onclick="verDetallesPersonaje('<?php echo $c_id; ?>', '<?php echo $type; ?>')" style="color:#ffdd1c; text-decoration:none; font-weight:bold;">
                                <?php echo htmlspecialchars($c_name); ?>
                            </a>
                            <p style="margin:0; font-size:12px; color:#aaa;"><?php echo $char['role'] ?? 'Cast'; ?></p>
                        </td>
                        <td style="text-align: right; padding-right: 15px;">
                            <span class="btn-fav-char" 
                                data-char-id="<?php echo $c_id; ?>" 
                                data-char-name="<?php echo htmlspecialchars($c_name); ?>"
                                data-char-img="<?php echo htmlspecialchars($c_img); ?>"
                                style="cursor:pointer; font-size: 18px; color: <?php echo $es_favorito ? '#e74c3c' : '#444'; ?>;">
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
<div id="characterModal" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <span class="close-modal" onclick="cerrarModal()">&times;</span>
        <div id="modal-body-content">
            <p style="text-align:center;">Cargando información...</p>
        </div>
    </div>
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
function verDetallesPersonaje(charId, type) {
    const modal = document.getElementById('characterModal');
    const content = document.getElementById('modal-body-content');
    
    modal.style.display = 'flex';
    content.innerHTML = '<p style="text-align:center;">Buscando en la base de datos de NixoList...</p>';

    let url = '';
    if (type === 'anime') {
        url = `https://api.jikan.moe/v4/characters/${charId}/full`;
    } else {
        // TMDB requiere el API Key que ya tienes en PHP
        url = `https://api.themoviedb.org/3/person/${charId}?api_key=0537b412710df9a2b7790cada44e494e&language=es-ES`;
    }

    fetch(url)
    .then(res => res.json())
    .then(res => {
        const data = res.data || res; // Jikan usa .data, TMDB devuelve directo
        
        let nombre = data.name;
        let imagen = type === 'anime' ? data.images.jpg.image_url : (data.profile_path ? `https://image.tmdb.org/t/p/w500${data.profile_path}` : '../../Recursos/no-image.png');
        let biografia = type === 'anime' ? (data.about || "No hay biografía disponible.") : (data.biography || "No hay biografía disponible.");
        let nicknames = data.nicknames ? `<p><b>Apodos:</b> ${data.nicknames.join(', ')}</p>` : '';

        content.innerHTML = `
            <div class="modal-grid">
                <img src="${imagen}" class="modal-img">
                <div>
                    <h2 style="margin-top:0; color:#ffdd1c;">${nombre}</h2>
                    ${nicknames}
                    <div class="modal-desc">${biografia.replace(/\n/g, '<br>')}</div>
                </div>
            </div>
        `;
    })
    .catch(err => {
        content.innerHTML = '<p>Error al cargar la información del personaje.</p>';
    });
}

function cerrarModal() {
    document.getElementById('characterModal').style.display = 'none';
}

// Cerrar modal si se hace clic fuera de la caja negra
window.onclick = function(event) {
    const modal = document.getElementById('characterModal');
    if (event.target == modal) { cerrarModal(); }
}

// // --- Lógica para enviar Review (Unificada) ---
const btnSendReview = document.getElementById('btnSendReview');
if (btnSendReview) {
    btnSendReview.addEventListener('click', function() {
        const contenido = document.getElementById('user-review-text').value;

        if (contenido.trim().length < 10) {
            alert("La reseña es muy corta. Escribe al menos 10 caracteres.");
            return;
        }

        const datos = new FormData();
        datos.append('id_api', '<?php echo $id; ?>');
        datos.append('type', '<?php echo $type; ?>');
        datos.append('titulo', <?php echo json_encode($title); ?>);
        datos.append('portada', '<?php echo $img; ?>');
        datos.append('action', 'add_review'); 
        datos.append('review', contenido);

        fetch('../funcionalidades/procesar_interaccion.php', {
            method: 'POST',
            body: datos
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                alert("¡Reseña publicada con éxito!");
                location.reload(); 
            } else {
                alert("Error: " + res.message);
            }
        })
        .catch(err => {
            console.error("Error:", err);
            alert("Hubo un problema al conectar con el servidor.");
        });
    });
}
</script>

</body>
</html>