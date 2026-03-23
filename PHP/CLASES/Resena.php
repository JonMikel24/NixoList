<?php

class Resena {
    private $id_resena;
    private $id_usuario;
    private $id_media;
    private $texto_resena;
    private $likes;
    private $created_at;

    // Constructor
    public function __construct(
        $id_usuario = null,
        $id_media = null,
        $texto_resena = null,
        $likes = 0,
        $id_resena = null,
        $created_at = null
    ) {
        $this->id_resena = $id_resena;
        $this->id_usuario = $id_usuario;
        $this->id_media = $id_media;
        $this->texto_resena = $texto_resena;
        $this->likes = $likes;
        $this->created_at = $created_at;
    }

    // Getters
    public function getIdResena() {
        return $this->id_resena;
    }

    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function getIdMedia() {
        return $this->id_media;
    }

    public function getTextoResena() {
        return $this->texto_resena;
    }

    public function getLikes() {
        return $this->likes;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    // Setters
    public function setIdResena($id_resena) {
        $this->id_resena = $id_resena;
    }

    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    public function setIdMedia($id_media) {
        $this->id_media = $id_media;
    }

    public function setTextoResena($texto_resena) {
        $this->texto_resena = $texto_resena;
    }

    public function setLikes($likes) {
        $this->likes = $likes;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    // Métodos útiles
    public function incrementarLikes() {
        $this->likes++;
    }

    public function decrementarLikes() {
        if ($this->likes > 0) {
            $this->likes--;
        }
    }
}

?>