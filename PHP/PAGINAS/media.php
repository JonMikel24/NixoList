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
        if($type == 'anime') $reviews_list = callAPI("https://api.jikan.moe/v4/anime/".$id."/reviews")["data"] ?? [];
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
            <h1 class="logo-texto">NixoList</h1>
        </div>

        <div class="PerfilContenedor" onclick="window.location.href='../Perfil/Index.php'" style="cursor:pointer;">
            <?php
            if (isset($_SESSION['Usuario'])) {
                $Foto = (!empty($_SESSION['Foto'])) ? $_SESSION['Foto'] : '/Recursos/fotousuario.png';
                echo '
                <div class="perfil-horiz">
                    <div class="perfil-info">
                        <p class="perfil-nombre nombre-mio">' . htmlspecialchars($_SESSION['Usuario']) . ' <span class="flecha">▼</span></p>
                    </div>
                    <img src="' . htmlspecialchars($Foto) . '" class="profile-pic foto-mia">
                </div>';
            } else {
                echo '
                <div class="auth-buttons">
                    <a href="../Login/Index.php"><button class="login-btn">Iniciar Sesión</button></a>
                    <a href="../Login/registrarse.php"><button class="register-btn">Registrarse</button></a>
                </div>';
            }
            ?>
        </div>
    </div>
</header>

<nav class="navbar">
    <div class="nav-links">
        <a href="index.php" class="<?php echo ($pagina_actual == 'index.php') ? 'active' : ''; ?>">Inicio</a>
        <a href="anime.php" class="<?php echo ($type == 'anime') ? 'active' : ''; ?>">Anime</a>
        <a href="peliculas.php" class="<?php echo ($type == 'movie') ? 'active' : ''; ?>">Películas</a>
        <a href="series.php" class="<?php echo ($type == 'tv') ? 'active' : ''; ?>">Series</a>
    </div>

    <div class="search-container">
        <select class="search-select">
            <option value="all">All</option>
            <option value="anime">Anime</option>
            <option value="manga">Manga</option>
        </select>
        <input type="text" placeholder="Buscar Anime, Manga..." class="search-input">
        <button type="submit" class="search-button">🔍</button>
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
                <div class="score-box">
                    <div class="score-value">★ <?php echo $score; ?></div>
                    <div class="score-stats"><div class="score-label">RANKING</div><div class="score-rank">#<?php echo rand(1, 2000); ?></div></div>
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
                <h2 class="section-title">Characters & Staff</h2>
                <table class="characters-table">
                    <?php foreach(array_slice($characters, 0, 15) as $char): 
                        $c_name = $char['character']['name'] ?? $char['name'];
                        $c_img = $char['character']['images']['jpg']['image_url'] ?? ($char['profile_path'] ? "https://image.tmdb.org/t/p/w185".$char['profile_path'] : "../../Recursos/no-image.png");
                    ?>
                    <tr class="char-row">
                        <td class="char-img-cell"><img src="<?php echo $c_img; ?>" width="50"></td>
                        <td class="char-info-cell"><a href="#"><?php echo $c_name; ?></a><p><?php echo $char['role'] ?? 'Cast'; ?></p></td>
                        <td class="char-va-cell">Japanese</td>
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

    function enviarSolicitud(tipoAccion, boton) {
        const datos = new FormData();
        datos.append('id_api', '<?php echo $id; ?>');
        datos.append('type', '<?php echo $type; ?>');
        datos.append('titulo', <?php echo json_encode($title); ?>);
        datos.append('portada', '<?php echo $img; ?>');
        datos.append('action', tipoAccion);

        fetch('../funcionalidades/procesar_interaccion.php', {
            method: 'POST',
            body: datos
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                if (res.result === 'added') {
                    // Estilo cuando está activo
                    boton.style.backgroundColor = '#2ecc71'; 
                    boton.style.color = 'white';
                    boton.innerText = (tipoAccion === 'favorite') ? '❤ EN FAVORITOS' : '✓ EN MI LISTA';
                } else {
                    // Estilo original cuando se quita
                    boton.style.backgroundColor = '';
                    boton.style.color = '';
                    boton.innerText = (tipoAccion === 'favorite') ? '❤ AÑADIR A FAVORITOS' : '+ AÑADIR A MI LISTA';
                }
            } else {
                alert(res.message);
            }
        })
        .catch(err => console.error("Error:", err));
    }

    if(btnList) btnList.addEventListener('click', () => enviarSolicitud('add_list', btnList));
    if(btnFav) btnFav.addEventListener('click', () => enviarSolicitud('favorite', btnFav));
});
</script>

</body>
</html>