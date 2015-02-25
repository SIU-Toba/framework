<?php
require_once("modelo/modelo_persona.php");

use SIUToba\rest\lib\rest_hidratador;
use rest\rest;


/**
 * @description Operaciones sobre Deportes de las personas
 */
class recurso_deportes implements SIUToba\rest\lib\modelable
{

	public static function _get_modelos()
	{
		return $models = array(
			'Deporte' => array(
				'deporte' => array('type' => 'integer'),
				'dia' => array('type' => 'string', '_mapeo' => 'dia_semana'),
				'hora_inicio' => array('type' => 'string'),
				'hora_fin' => array('type' => 'string')
			)

		);
	}

     /**
     * Se consume en GET /personas/{id}/deportes
     * @summary Retorna todos los deportes que practica la persona.
	 * @param_query $nombre string 
     * @response_type [ {deporte: integer, dia_semana: integer, hora_inicio: string, hora_fin:string}, ]
     * @errors 404 No se pudo encontrar a la persona
     */
    function get_list($id_persona)
    {
	    //si estuviese en el padre, se llamaria como get_deportes_list
		$deportes = modelo_persona::get_deportes($id_persona);
	    $deportes_vista = rest_hidratador::hidratar(current($this->_get_modelos()), $deportes);
		rest::response()->get($deportes_vista);
    }

}