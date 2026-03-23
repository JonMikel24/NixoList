<?php

class EstadisticasUsuario {
    private $id_usuario;
    private $animes_completados;
    private $peliculas_completadas;
    private $tv_completadas;
    private $libros_completados;
    private $puntuacion_media;

    // Constructor
    public function __construct(
        $id_usuario = null,
        $animes_completados = 0,
        $peliculas_completadas = 0,
        $tv_completadas = 0,
        $libros_completados = 0,
        $puntuacion_media = 0.00
    ) {
        $this->id_usuario = $id_usuario;
        $this->animes_completados = $animes_completados;
        $this->peliculas_completadas = $peliculas_completadas;
        $this->tv_completadas = $tv_completadas;
        $this->libros_completados = $libros_completados;
        $this->puntuacion_media = $puntuacion_media;
    }

    // Getters
    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function getAnimesCompletados() {
        return $this->animes_completados;
    }

    public function getPeliculasCompletadas() {
        return $this->peliculas_completadas;
    }

    public function getTvCompletadas() {
        return $this->tv_completadas;
    }

    public function getLibrosCompletados() {
        return $this->libros_completados;
    }

    public function getPuntuacionMedia() {
        return $this->puntuacion_media;
    }

    // Setters
    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    public function setAnimesCompletados($animes_completados) {
        $this->animes_completados = $animes_completados;
    }

    public function setPeliculasCompletadas($peliculas_completadas) {
        $this->peliculas_completadas = $peliculas_completadas;
    }

    public function setTvCompletadas($tv_completadas) {
        $this->tv_completadas = $tv_completadas;
    }

    public function setLibrosCompletados($libros_completados) {
        $this->libros_completados = $libros_completados;
    }

    public function setPuntuacionMedia($puntuacion_media) {
        $this->puntuacion_media = $puntuacion_media;
    }
}

?>