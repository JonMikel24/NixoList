<?php
class ActividadFeed {

    private ?int $IdActividad = null;
    private int $IdUsuario;
    private ?int $IdMedia;
    private string $Type;
    private DateTime $CreatedAt;

    public function __construct(
        int $pIdUsuario,
        ?int $pIdMedia,
        string $pType,
        ?DateTime $pCreatedAt = null
    ){
        $this->IdUsuario = $pIdUsuario;
        $this->IdMedia = $pIdMedia;
        $this->Type = $pType;
        $this->CreatedAt = $pCreatedAt ?? new DateTime();
    }

    public function getIdActividad(){ return $this->IdActividad; }
    public function setIdActividad(int $id){ $this->IdActividad = $id; }

    public function getIdUsuario(){ return $this->IdUsuario; }
    public function setIdUsuario(int $id){ $this->IdUsuario = $id; }

    public function getIdMedia(){ return $this->IdMedia; }
    public function setIdMedia(?int $id){ $this->IdMedia = $id; }

    public function getType(){ return $this->Type; }
    public function setType(string $type){ $this->Type = $type; }

    public function getCreatedAt(){ return $this->CreatedAt; }
}