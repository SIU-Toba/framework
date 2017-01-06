<?php

use SIUToba\rest\rest;
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

        $this->generar_decoder();
	}

    private function generar_decoder()
    {
		$servidor_ini = toba_modelo_rest::get_ini_server($this->modelo_proyecto);

        $decoder = $servidor_ini->get('jwt', 'decoder', null, true);
        $algoritmo = $servidor_ini->get('jwt', 'algoritmo', null, true);
        $key = $servidor_ini->get('jwt', 'key', null, true);

        if ($decoder == 'simetrico') {
            $decoder = new SimetricDecoder($algoritmo, $key);
        } elseif ($decoder == 'asimetrico') {
            $decoder = new AsimetricDecoder($algoritmo, $key);
        } else {
            throw new Exception('Se debe configurar un decoder (simetrico|asimetrico) para jwt.');
        }

        $this->set_decoder($decoder);
    }

    public function get_usuario_jwt($data)
    {
		$servidor_ini = toba_modelo_rest::get_ini_server($this->modelo_proyecto);

        // recupera el campo que indica el id del usuario en el token
        $uid = $servidor_ini->get('jwt', 'usuario_id', null, true);

        return $data->$uid;
    }

}
?>
