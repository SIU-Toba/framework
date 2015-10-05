<?php

namespace SIUToba\rest\http;

class vista_raw extends vista_respuesta
{
    protected $content_type = null;

    public function set_content_type($type)
    {
        $this->content_type = $type;
    }

    protected function get_content_type()
    {
        if (! isset($this->content_type)) {
            throw new rest_error_interno("La vista_raw necesita definirle un set_content_type");
        }
        return $this->content_type;
    }

    public function get_cuerpo()
    {
        return $this->respuesta->get_data();
    }

}
