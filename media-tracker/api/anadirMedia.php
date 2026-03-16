<?php

require_once "../config/conexion.php";

$title = $_POST['title'];
$type = $_POST['type'];
$tmdb_id = $_POST['tmdb_id'];

$sql = "INSERT INTO media (titulo,type,tmdb_id)
VALUES (?,?,?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi",$title,$type,$tmdb_id);
$stmt->execute();

echo json_encode(["success"=>true]);