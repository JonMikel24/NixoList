<?php

class Media {
    private $id_media;
    private $titulo;
    private $type;
    private $descripcion;
    private $fecha_publicacion;
    private $portada;
    private $tmdb_id;
    private $mal_id;
    private $episodios_totales;
    private $temporadas_totales;
    private $created_at;
    private $last_updated_api;

    // Constructor
    public function __construct(
        $titulo = null,
        $type = null,
        $descripcion = null,
        $fecha_publicacion = null,
        $portada = null,
        $tmdb_id = null,
        $mal_id = null,
        $episodios_totales = null,
        $temporadas_totales = null,
        $last_updated_api = null,
        $id_media = null,
        $created_at = null
    ) {
        $this->id_media = $id_media;
        $this->titulo = $titulo;
        $this->type = $type;
        $this->descripcion = $descripcion;
        $this->fecha_publicacion = $fecha_publicacion;
        $this->portada = $portada;
        $this->tmdb_id = $tmdb_id;
        $this->mal_id = $mal_id;
        $this->episodios_totales = $episodios_totales;
        $this->temporadas_totales = $temporadas_totales;
        $this->created_at = $created_at;
        $this->last_updated_api = $last_updated_api;
    }

    // Getters
    public function getIdMedia() {
        return $this->id_media;
    }

    public function getTitulo() {
        return $this->titulo;
    }

    public function getType() {
        return $this->type;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function getFechaPublicacion() {
        return $this->fecha_publicacion;
    }

    public function getPortada() {
        return $this->portada;
    }

    public function getTmdbId() {
        return $this->tmdb_id;
    }

    public function getMalId() {
        return $this->mal_id;
    }

    public function getEpisodiosTotales() {
        return $this->episodios_totales;
    }

    public function getTemporadasTotales() {
        return $this->temporadas_totales;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getLastUpdatedApi() {
        return $this->last_updated_api;
    }

    // Setters
    public function setIdMedia($id_media) {
        $this->id_media = $id_media;
    }

    public function setTitulo($titulo) {
        $this->titulo = $titulo;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function setFechaPublicacion($fecha_publicacion) {
        $this->fecha_publicacion = $fecha_publicacion;
    }

    public function setPortada($portada) {
        $this->portada = $portada;
    }

    public function setTmdbId($tmdb_id) {
        $this->tmdb_id = $tmdb_id;
    }

    public function setMalId($mal_id) {
        $this->mal_id = $mal_id;
    }

    public function setEpisodiosTotales($episodios_totales) {
        $this->episodios_totales = $episodios_totales;
    }

    public function setTemporadasTotales($temporadas_totales) {
        $this->temporadas_totales = $temporadas_totales;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    public function setLastUpdatedApi($last_updated_api) {
        $this->last_updated_api = $last_updated_api;
    }
}

?>