<?php
session_start();
require_once("../conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../Login/Index.php");
    exit;
}

$mi_id = $_SESSION['id_usuario'];

// 1. Solicitudes PENDIENTES (Corregido a 'username' y 'avatar')
$sql_req = "SELECT a.id_usuario, u.username, u.avatar 
            FROM amigos a 
            JOIN usuarios u ON a.id_usuario = u.id_usuario 
            WHERE a.id_amigo_usuario = ? AND a.status = 'pendiente'";
$stmt_req = $conexion->prepare($sql_req);
$stmt_req->bind_param("i", $mi_id);
$stmt_req->execute();
$solicitudes = $stmt_req->get_result();

// 2. Amigos ACEPTADOS (Corregido a 'username' y 'avatar')
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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Amigos - NixoList</title>
    <link rel="stylesheet" href="../../CSS/styles.css">
    <style>
        body { background-color: #0d0d0d; color: white; font-family: sans-serif; }
        .main-wrapper { max-width: 800px; margin: 40px auto; padding: 20px; }
        .search-box { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #333; background: #1a1a1a; color: white; margin-bottom: 20px; }
        .user-card { background: #1a1a1a; padding: 15px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; border-radius: 8px; border: 1px solid #222; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .user-img { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; background: #333; }
        .btn-add { background: #3498db; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; }
        .btn-accept { background: #2ecc71; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; margin-right: 5px; }
        .btn-reject { background: #e74c3c; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; }
        .pendiente { background: #444 !important; cursor: not-allowed; }
    </style>
</head>
<body>

<div class="main-wrapper">
    <h2>Buscar Usuarios</h2>
    <input type="text" id="search-user" class="search-box" placeholder="Escribe el username de un usuario...">
    <div id="search-results"></div>

    <?php if ($solicitudes->num_rows > 0): ?>
        <h2 style="color: #e67e22;">Solicitudes Pendientes</h2>
        <?php while($sol = $solicitudes->fetch_assoc()): ?>
            <div class="user-card" id="req-<?php echo $sol['id_usuario']; ?>">
                <div class="user-info">
                    <img src="<?php echo $sol['avatar'] ?: '../../Recursos/fotousuario.png'; ?>" class="user-img">
                    <span><?php echo htmlspecialchars($sol['username']); ?></span>
                </div>
                <div>
                    <button class="btn-accept" onclick="responderSolicitud(<?php echo $sol['id_usuario']; ?>, 'aceptar')">Aceptar</button>
                    <button class="btn-reject" onclick="responderSolicitud(<?php echo $sol['id_usuario']; ?>, 'rechazar')">Rechazar</button>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

    <h2>Mis Amigos</h2>
    <div class="amigos-lista">
        <?php if ($mis_amigos->num_rows > 0): ?>
            <?php while($amigo = $mis_amigos->fetch_assoc()): ?>
                <div class="user-card">
                    <div class="user-info">
                        <img src="<?php echo $amigo['avatar'] ?: '../../Recursos/fotousuario.png'; ?>" class="user-img">
                        <span><?php echo htmlspecialchars($amigo['username']); ?></span>
                    </div>
                    <button class="btn-add" onclick="window.location.href='perfil_usuario.php?id=<?php echo $amigo['id_usuario']; ?>'">Ver Perfil</button>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color:gray;">Aún no tienes amigos agregados.</p>
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