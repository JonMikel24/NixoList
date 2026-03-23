<?php

class MediaUsuario {
    private $id_usuario_media;
    private $id_usuario;
    private $id_media;
    private $status;
    private $puntuacion;
    private $es_favorito;
    private $progreso;
    private $episodios_vistos;
    private $progreso_temporadas;
    private $fecha_comienzo;
    private $fecha_completado;
    private $created_at;
    private $updated_at;

    // Constructor
    public function __construct(
        $id_usuario = null,
        $id_media = null,
        $status = 'planned',
        $puntuacion = null,
        $es_favorito = 0,
        $progreso = 0,
        $episodios_vistos = 0,
        $progreso_temporadas = 0,
        $fecha_comienzo = null,
        $fecha_completado = null,
        $id_usuario_media = null,
        $created_at = null,
        $updated_at = null
    ) {
        $this->id_usuario_media = $id_usuario_media;
        $this->id_usuario = $id_usuario;
        $this->id_media = $id_media;
        $this->status = $status;
        $this->puntuacion = $puntuacion;
        $this->es_favorito = $es_favorito;
        $this->progreso = $progreso;
        $this->episodios_vistos = $episodios_vistos;
        $this->progreso_temporadas = $progreso_temporadas;
        $this->fecha_comienzo = $fecha_comienzo;
        $this->fecha_completado = $fecha_completado;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    // Getters
    public function getIdUsuarioMedia() {
        return $this->id_usuario_media;
    }

    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function getIdMedia() {
        return $this->id_media;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getPuntuacion() {
        return $this->puntuacion;
    }

    public function getEsFavorito() {
        return $this->es_favorito;
    }

    public function getProgreso() {
        return $this->progreso;
    }

    public function getEpisodiosVistos() {
        return $this->episodios_vistos;
    }

    public function getProgresoTemporadas() {
        return $this->progreso_temporadas;
    }

    public function getFechaComienzo() {
        return $this->fecha_comienzo;
    }

    public function getFechaCompletado() {
        return $this->fecha_completado;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }

    // Setters
    public function setIdUsuarioMedia($id_usuario_media) {
        $this->id_usuario_media = $id_usuario_media;
    }

    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    public function setIdMedia($id_media) {
        $this->id_media = $id_media;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setPuntuacion($puntuacion) {
        $this->puntuacion = $puntuacion;
    }

    public function setEsFavorito($es_favorito) {
        $this->es_favorito = $es_favorito;
    }

    public function setProgreso($progreso) {
        $this->progreso = $progreso;
    }

    public function setEpisodiosVistos($episodios_vistos) {
        $this->episodios_vistos = $episodios_vistos;
    }

    public function setProgresoTemporadas($progreso_temporadas) {
        $this->progreso_temporadas = $progreso_temporadas;
    }

    public function setFechaComienzo($fecha_comienzo) {
        $this->fecha_comienzo = $fecha_comienzo;
    }

    public function setFechaCompletado($fecha_completado) {
        $this->fecha_completado = $fecha_completado;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    public function setUpdatedAt($updated_at) {
        $this->updated_at = $updated_at;
    }
    public function isCompleted() {
    return $this->status === 'completed';
    
    if ($this->episodios_vistos == $total) {
    $this->status = 'completed';
}
}
}


?>