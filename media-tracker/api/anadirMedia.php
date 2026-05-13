<?php

require_once "../config/conexion.php";

$title = $_POST['title'];
$type = $_POST['type'];
$tmdb_id = $_POST['tmdb_id'];

$conn = new mysqli('localhost', 'root', '', 'media_tracker');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO media (titulo,type,tmdb_id)
VALUES (?,?,?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi",$title,$type,$tmdb_id);
$stmt->execute();

echo json_encode(["success"=>true]);