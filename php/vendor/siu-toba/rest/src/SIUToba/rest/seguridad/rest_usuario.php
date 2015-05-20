<?php

namespace SIUToba\rest\seguridad;

class rest_usuario
{
    protected $usuario;

    protected $perfiles;

    public function set_perfiles($perfiles)
    {
        $this->perfiles = $perfiles;
    }

    public function set_usuario($usuario)
    {
        $this->usuario = $usuario;
    }

    public function get_perfiles()
    {
        return $this->perfiles;
    }

    public function get_usuario()
    {
        return $this->usuario;
    }

    public function tiene_perfil($perfil)
    {
        return in_array($perfil, $this->perfiles);
    }
}
