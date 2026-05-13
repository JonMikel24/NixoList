<?php
session_start();
require_once(__DIR__ . "/../conexion.php");
if (!isset($conexion) && isset($conn)) {
    $conexion = $conn;
}
if (!isset($conexion)) {
    echo json_encode(['status' => 'error', 'message' => 'No hay conexión a la base de datos']);
    exit;
}

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

$mi_id = $_SESSION['id_usuario'];

if (isset($_GET['q'])) {
    $q = "%" . $_GET['q'] . "%";

    $sql = "SELECT id_usuario, username, avatar FROM usuarios WHERE username LIKE ? AND id_usuario != ? LIMIT 5";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("si", $q, $mi_id);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($u = $res->fetch_assoc()) {
        $id_u = $u['id_usuario'];
        $check = $conexion->prepare("SELECT status FROM amigos WHERE (id_usuario = ? AND id_amigo_usuario = ?) OR (id_usuario = ? AND id_amigo_usuario = ?)");
        $check->bind_param("iiii", $mi_id, $id_u, $id_u, $mi_id);
        $check->execute();
        $rel = $check->get_result()->fetch_assoc();
        
        $foto = !empty($u['avatar']) ? $u['avatar'] : '../../Recursos/fotousuario.png';

        echo '<div class="search-result-item">
                <div class="search-result-info">
                    <img src="'.$foto.'" alt="Avatar">
                    <span>'.htmlspecialchars($u['username']).'</span>
                </div>';
                
        if (!$rel) {
            // Si no hay relación, mostramos el botón amarillo de Añadir
            echo '<button class="btn-search-action btn-search-add" onclick="gestionarAmigo('.$id_u.', this)">Añadir</button>';
        } else {
            // Si ya hay solicitud pendiente (o ya son amigos), mostramos el botón gris deshabilitado
            echo '<button class="btn-search-action btn-search-pending" disabled>'.ucfirst($rel['status']).'</button>';
        }
        
        echo '</div>';
    }
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_amigo = $_POST['id_amigo'];
    $accion = $_POST['accion'];

    if ($accion === 'enviar_solicitud') {
        $ins = $conexion->prepare("INSERT INTO amigos (id_usuario, id_amigo_usuario, status) VALUES (?, ?, 'pendiente')");
        $ins->bind_param("ii", $mi_id, $id_amigo);
        $ins->execute();
        echo json_encode(['status' => 'success']);
    } 
    elseif ($accion === 'aceptar') {
        $upd = $conexion->prepare("UPDATE amigos SET status = 'aceptado' WHERE id_usuario = ? AND id_amigo_usuario = ?");
        $upd->bind_param("ii", $id_amigo, $mi_id);
        $upd->execute();
        echo json_encode(['status' => 'success']);
    } 
    elseif ($accion === 'rechazar') {
        $del = $conexion->prepare("DELETE FROM amigos WHERE id_usuario = ? AND id_amigo_usuario = ?");
        $del->bind_param("ii", $id_amigo, $mi_id);
        $del->execute();
        echo json_encode(['status' => 'success']);
    }
    exit;
}