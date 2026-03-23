<?php

class EstadisticasMedia {
    private $id_media;
    private $puntuacion_media;
    private $total_puntuaciones;
    private $total_favoritos;

    // Constructor
    public function __construct($id_media = null, $puntuacion_media = 0.00, $total_puntuaciones = 0, $total_favoritos = 0) {
        $this->id_media = $id_media;
        $this->puntuacion_media = $puntuacion_media;
        $this->total_puntuaciones = $total_puntuaciones;
        $this->total_favoritos = $total_favoritos;
    }

    // Getters
    public function getIdMedia() {
        return $this->id_media;
    }

    public function getPuntuacionMedia() {
        return $this->puntuacion_media;
    }

    public function getTotalPuntuaciones() {
        return $this->total_puntuaciones;
    }

    public function getTotalFavoritos() {
        return $this->total_favoritos;
    }

    // Setters
    public function setIdMedia($id_media) {
        $this->id_media = $id_media;
    }

    public function setPuntuacionMedia($puntuacion_media) {
        $this->puntuacion_media = $puntuacion_media;
    }

    public function setTotalPuntuaciones($total_puntuaciones) {
        $this->total_puntuaciones = $total_puntuaciones;
    }

    public function setTotalFavoritos($total_favoritos) {
        $this->total_favoritos = $total_favoritos;
    }
}

?>