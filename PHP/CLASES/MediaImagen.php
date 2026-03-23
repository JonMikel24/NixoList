<?php

class MediaImagen {
    private $id_media_imagen;
    private $id_media;
    private $image_url;
    private $type;

    // Constructor
    public function __construct(
        $id_media = null,
        $image_url = null,
        $type = null,
        $id_media_imagen = null
    ) {
        $this->id_media_imagen = $id_media_imagen;
        $this->id_media = $id_media;
        $this->image_url = $image_url;
        $this->type = $type;
    }

    // Getters
    public function getIdMediaImagen() {
        return $this->id_media_imagen;
    }

    public function getIdMedia() {
        return $this->id_media;
    }

    public function getImageUrl() {
        return $this->image_url;
    }

    public function getType() {
        return $this->type;
    }

    // Setters
    public function setIdMediaImagen($id_media_imagen) {
        $this->id_media_imagen = $id_media_imagen;
    }

    public function setIdMedia($id_media) {
        $this->id_media = $id_media;
    }

    public function setImageUrl($image_url) {
        $this->image_url = $image_url;
    }

    public function setType($type) {
        $this->type = $type;
    }
}

?>