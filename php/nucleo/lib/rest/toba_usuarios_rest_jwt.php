<?php

use SIUToba\rest\seguridad\autenticacion\validador_jwt;
use SIU\JWT\Decoder\SimetricDecoder;
use SIU\JWT\Decoder\AsimetricDecoder;

class toba_usuarios_rest_jwt extends validador_jwt
{
	protected $modelo_proyecto;

    protected $validador_jwt;

	function __construct(\toba_modelo_proyecto $proyecto)
	{
        parent::__construct();

		$this->modelo_proyecto = $proyecto;

        $this->cargar_ini_jwt();

        $this->generar_decoder();
	}

    private function cargar_ini_jwt()
    {
        //--- Levanto la CONFIGURACION de jwt.ini
        $ini = toba_modelo_rest::get_ini_server($this->modelo_proyecto);

        $this->decoder = $ini->get('jwt', 'tipo', null, true);
        $this->algoritmo = $ini->get('jwt', 'algoritmo', null, true);
        $this->usuario_id = $ini->get('jwt', 'usuario_id', null, true);
        $this->key_decoder = $ini->get('jwt', 'key_decoder', null, true);
    }

    private function generar_decoder()
    {
        if ($this->decoder == 'simetrico') {
            $decoder = new SimetricDecoder($this->algoritmo, $this->key_decoder);
        } elseif ($this->decoder == 'asimetrico') {
            $decoder = new AsimetricDecoder($this->algoritmo, $this->key_decoder);
        } else {
            throw new toba_error('Se debe configurar un decoder (simetrico|asimetrico) para jwt.');
        }

        $this->set_decoder($decoder);
    }

    public function get_usuario_jwt($data)
    {
        $uid = $this->usuario_id;

        if (!isset($data->$uid)){
            throw new toba_error("El identificador de usuario '$uid' no existe en los datos del token JWT.");
        }

        return $data->$uid;
    }
}
?>
