<?php
require_once("modelo/modelo_persona.php");

use rest\rest;


/** // https://github.com/wordnik/swagger-core/wiki/Datatypes
 * La anotación model permite utilizar el model "Persona" en la definicion de los metodos, sino hay que definir los tipos de datos complejos inline en cada metodo
 *
 * @description Operaciones sobre Personas de carne y hueso
 * 
 * @model {
 * "id": "Persona",
 * "required": ["id"],
 *      "properties": {
 *          "id": {"type": "integer"},
 *          "nombre": {"type": "string" },
*			"fecha_nac" : {"type": "string"}
 *      }
 * }
 */
class recurso_deportes
{

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