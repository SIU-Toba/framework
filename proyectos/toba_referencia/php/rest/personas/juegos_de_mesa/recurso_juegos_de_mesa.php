<?php
require_once("modelo/modelo_persona.php");

use SIUToba\rest\lib\rest_hidratador;
use SIUToba\rest\rest;


/**
 * @description Operaciones sobre Deportes de las personas
 */
class recurso_juegos_de_mesa implements SIUToba\rest\lib\modelable
{

	public static function _get_modelos()
	{
		return $models = array(
			'Juego' => array(
				'juego'	=> array(	'type' => 'integer'),
				'dia'		=> array(	'type' => 'string', 
										'_mapeo' => 'dia_semana'),
				'hora_inicio'	=>	array('type' => 'string'),
				'hora_fin'	=>	array('type' => 'string')
			)

		);
	}

     /**
     * Se consume en GET /personas/{id}/juego_de_mesa
     * @summary Retorna todos los juegos de mesa que juega la persona.
	 * @responses 200 array {"$ref":"Juego"}
     * @responses 404 No se pudo encontrar a la persona
     */
    function get_list($id_persona)
    {
		$juegos_mesa = modelo_persona::get_juegos($id_persona, 1);
		if ($juegos_mesa && !empty($juegos_mesa)) {
			$juegos_mesa_vista = rest_hidratador::hidratar(current($this->_get_modelos()), $juegos_mesa);
			rest::response()->get($juegos_mesa_vista);
		} else {
			rest::response()->not_found();
		}
		
    }

}