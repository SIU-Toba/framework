<?php
require_once("modelo/modelo_persona.php");

use rest\rest;


/**
 * @description Operaciones sobre Deportes de las personas
 */
class recurso_deportes implements \rest\lib\modelable
{

	public static function _get_modelos()
	{
		return $models = array(
			'Deporte' => array(
				'deporte' => array('type' => 'integer'),
				'dia_semana' => array('type' => 'string'),
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
		rest::response()->get($deportes);
    }

}