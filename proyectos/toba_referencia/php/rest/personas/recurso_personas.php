<?php
require_once("modelo/modelo_persona.php");

use rest\rest;
use rest\lib\rest_filtro_sql;


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
class recurso_personas
{

    /**
	 * Se consume en GET /personas/{id}
     * @summary Retorna datos de una persona. 
 	 * @param_query $con_imagen integer Retornar además la imagen de la persona, por defecto 0
     * @response_type Persona
     * @errors 404 No se pudo encontrar a la persona
     */
    function get($id_persona)
    {
		//toba::logger()->debug("Usuario: " . rest::app()->usuario->get_usuario());
        $incluir_imagen = (bool) rest::request()->get('con_imagen', 0);
		$modelo = new modelo_persona($id_persona);
        $fila = $modelo->get_datos($incluir_imagen);
        rest::response()->get($fila);
    }

    /**
	 * Se consume en PUT /personas/{id}
     * @summary Modificar datos de la persona. 
     * @param_body $persona Persona  [required] los datos a editar de la persona
     * @notes Si envia la componente 'imagen' de la persona se actualiza unicamente la imagen (binario base64)
     * @errors 404 No se pudo encontrar a la persona
     * @errors 400 El pedido no cumple con las reglas de negocio - validacion erronea.
     */
	function put($id_persona)
	{
		$datos = rest::request()->get_body_json();
		$modelo = new modelo_persona($id_persona);
		$errores = $modelo->validar($datos);
		if (!empty($errores)) {
			return rest::response()->error_negocio($errores);
		} else {
			if (isset($datos['imagen'])) {
				$ok = $modelo->update_imagen($datos);
			} else {
				$ok = $modelo->update($datos);
			}
			if (!$ok) {
				rest::response()->not_found();
			} else {
				rest::response()->put();
			}
		}
	}


    /**
	 * Se consume en DELETE /personas/{id}
     * @summary Eliminar la persona.
     * @notes Cuidado, borra datos de deportes y juegos tambien
     * @errors 404 No se pudo encontrar a la persona
     */
    function delete($id_persona)
    {
		$modelo = new modelo_persona($id_persona);
		$ok = $modelo->delete();
        if(!$ok){
            rest::response()->not_found();
        }else {
            rest::response()->delete();
        }
    }


    /**
     * Se consume en POST /personas
     * @summary Crear una persona
     * @notes El parametro ID de la persona se ignora. </br>
     * @param_body $persona Persona  [required] los datos iniciales de la persona
     * @response_type {"id": "integer"}
     */
    function post_list()
    {
        $datos = rest::request()->get_body_json();
	    $errores = modelo_persona::validar($datos);
	    if($errores){
		    return rest::response()->error_negocio($errores);
	    }else{
		    $nuevo = modelo_persona::insert($datos);
		    $fila = array('id' => $nuevo);
		    rest::response()->post($fila);
	    }
    }


    /**
	 * Se consume en GET /personas
	 * 
	 * @param_query $nombre string Se define como 'condicion;valor' donde 'condicion' puede ser contiene|no_contiene|comienza_con|termina_con|es_igual_a|es_distinto_de
	 * @param_query $fecha_nac string Se define como 'condicion;valor' donde 'condicion' puede ser es_menor_que|es_menor_igual_que|es_igual_a|es_distinto_de|es_mayor_igual_que|es_mayor_que|entre
	 * 
	 * @param_query $limit integer Limitar a esta cantidad de registros
	 * @param_query $page integer Limitar desde esta pagina
 	 * @param_query $order string +/-campo,...
	 * @notes Retorna un header 'Total-Registros' con la cantidad total de registros a paginar
     * @response_type array {"$ref":"Persona"}
     */
    function get_list()
    {
	    $filtro = $this->get_filtro_get_list();
		$where = $filtro->get_sql_where();
		$limit = $filtro->get_sql_limit();
		$order_by = $filtro->get_sql_order_by();
        $personas = modelo_persona::get_personas($where, $order_by, $limit);
		$cantidad = modelo_persona::get_cant_personas($where);
		rest::response()->get_list($personas);
		rest::response()->add_headers(array('Cantidad-Registros' => $cantidad));		
    }

    /**
     * Esto es un alias. Si bien se aleja del REST puro, se puede utilizar para destacar
     * una operación o proveer un acceso simplificado a operaciones frecuentes.
     * Se consume en GET /personas/confoto.
     * @summary Retorna aquellas personas que tienen la foto cargada
     */
    function get_list__confoto()
    {
	    $filtro = $this->get_filtro_get_list();
	    $limit = $filtro->get_sql_limit();
        $order_by = $filtro->get_sql_order_by();
        $where = $filtro->get_sql_where() . " AND imagen <> ''";
        $personas = modelo_persona::get_personas($where, $order_by, $limit);
        $cantidad = modelo_persona::get_cant_personas($where);
        rest::response()->get($personas);
        rest::response()->add_headers(array('Cantidad-Registros' => $cantidad));
    }

    /**
     * Se consume en GET /personas/{id}/juegos
	 * @summary Retorna todos los juego que practica la persona
     * @response_type [ {juego: integer, dia_semana: integer, hora_inicio: string, hora_fin:string}, ]
     * @errors 404 No se pudo encontrar a la persona
     */
    function get_juegos_list($id_persona)
    {
		$juegos = modelo_persona::get_juegos($id_persona);
		rest::response()->get_list($juegos);
    }

	/**
	 * @return rest_filtro_sql
	 */
	protected function get_filtro_get_list()
	{
		$filtro = new rest_filtro_sql();
		$filtro->agregar_campo("nombre", "pers.nombre");
		$filtro->agregar_campo("fecha_nac", "pers.fecha_nac");
		$filtro->agregar_campo("id", "pers.id");
		return $filtro;
	}
}