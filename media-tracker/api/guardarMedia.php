<?php

require_once "../config/database.php";

$title = $_POST['title'];
$type = $_POST['type'];
$tmdb_id = $_POST['tmdb_id'] ?? null;
$mal_id = $_POST['mal_id'] ?? null;
$poster = $_POST['poster'] ?? null;
$description = $_POST['description'] ?? null;

$sql = "SELECT id FROM media
        WHERE tmdb_id=? OR mal_id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii",$tmdb_id,$mal_id);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){

    $insert = $conn->prepare("
        INSERT INTO media
        (titulo,type,tmdb_id,mal_id,portada,descripcion)
        VALUES (?,?,?,?,?,?)
    ");

    $insert->bind_param(
        "ssiiss",
        $titulo,
        $type,
        $tmdb_id,
        $mal_id,
        $portada,
        $descripcion
    );

    $insert->execute();

    echo json_encode(["inserted"=>true]);

}else{

    echo json_encode(["exists"=>true]);

}