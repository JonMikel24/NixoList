<?php

class Amigo {
    private $id_amigo;
    private $id_usuario;
    private $id_amigo_usuario;
    private $status;
    private $created_at;

    // Constructor
    public function __construct($id_usuario = null, $id_amigo_usuario = null, $status = 'pendiente', $created_at = null, $id_amigo = null) {
        $this->id_amigo = $id_amigo;
        $this->id_usuario = $id_usuario;
        $this->id_amigo_usuario = $id_amigo_usuario;
        $this->status = $status;
        $this->created_at = $created_at;
    }

    // Getters
    public function getIdAmigo() {
        return $this->id_amigo;
    }

    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function getIdAmigoUsuario() {
        return $this->id_amigo_usuario;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    // Setters
    public function setIdAmigo($id_amigo) {
        $this->id_amigo = $id_amigo;
    }

    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    public function setIdAmigoUsuario($id_amigo_usuario) {
        $this->id_amigo_usuario = $id_amigo_usuario;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }
}

?>