<?php

class Notificacion {
    private $id_notificacion;
    private $id_usuario;
    private $id_emisor;
    private $type;
    private $id_referencia;
    private $leido;
    private $created_at;

    // Constructor
    public function __construct(
        $id_usuario = null,
        $id_emisor = null,
        $type = null,
        $id_referencia = null,
        $leido = 0,
        $id_notificacion = null,
        $created_at = null
    ) {
        $this->id_notificacion = $id_notificacion;
        $this->id_usuario = $id_usuario;
        $this->id_emisor = $id_emisor;
        $this->type = $type;
        $this->id_referencia = $id_referencia;
        $this->leido = $leido;
        $this->created_at = $created_at;
    }

    // Getters
    public function getIdNotificacion() {
        return $this->id_notificacion;
    }

    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function getIdEmisor() {
        return $this->id_emisor;
    }

    public function getType() {
        return $this->type;
    }

    public function getIdReferencia() {
        return $this->id_referencia;
    }

    public function isLeido() {
        return $this->leido;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    // Setters
    public function setIdNotificacion($id_notificacion) {
        $this->id_notificacion = $id_notificacion;
    }

    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    public function setIdEmisor($id_emisor) {
        $this->id_emisor = $id_emisor;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function setIdReferencia($id_referencia) {
        $this->id_referencia = $id_referencia;
    }

    public function setLeido($leido) {
        $this->leido = $leido;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }


    public function marcarComoLeido() {
        $this->leido = 1;
    }
    public function esPeticionAmistad() {
        return $this->type === 'peticion_amistad';
    }
}

?>