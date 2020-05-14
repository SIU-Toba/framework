<?php

use SIUToba\rest\lib\rest_hidratador;
use SIUToba\rest\lib\rest_validador;
use SIUToba\rest\rest;
use SIUToba\rest\lib\rest_filtro_sql;


/**
 * @description Operaciones sobre Personas
 */
class recurso_personas implements SIUToba\rest\lib\modelable //esta interface es documentativa, puede no estar
{

	static function _get_modelos(){
		/**
		 * Hay diferencias entre una persona para mostrar o para crear. Por ej, el id.
		 * Ver el codigo fuente de rest_validador para ver las distintas reglas y opciones que llevan
		 */
		$persona_editar = array(
					'nombre' => array(	'type'     => 'string', 
										'_validar' => array(rest_validador::OBLIGATORIO,
															rest_validador::TIPO_LONGITUD => array('min' => 1, 'max' => 30))),
					'fecha_nacimiento' => array('_mapeo' => 'fecha_nac',
												'type' => 'date',
												'_validar' => array(rest_validador::TIPO_DATE => array('format' => 'Y-m-d'))),
					'imagen' => array(	'type' => 'byte')
				);
		
		$persona = array_merge(
							array('id' => array('type' => 'integer',
												'_validar' => array(rest_validador::TIPO_INT))),
							$persona_editar);
		return $models = array(
			'Persona' => $persona,
			'PersonaEditar' => $persona_editar

		);
	}
	
	protected function get_spec_persona($con_imagen = true, $tipo= 'Persona'){
		/** Notar que hay que modificar la spec si se va a incluir la foto o no, ya que de otro modo
		 * lanzaría un error cuando falta el campo. */
		$m = $this->_get_modelos();
		if(!$con_imagen){
			unset ($m[$tipo]['imagen']);
		}
		return $m[$tipo];
	}

	/**
	 * Se consume en GET /personas/{id}
	 * @summary Retorna datos de una persona. 
	 * @param_path $id_persona integer
	 * @param_query $con_imagen integer Retornar además la imagen de la persona, por defecto 0
	 * @responses 200 {"$ref": "Persona"} Persona
	 * @responses 400 No existe la persona
	 */
	function get($id_persona)
	{
		//toba::logger()->debug("Usuario: " . rest::app()->usuario->get_usuario());
		/**Obtengo los datos del modelo*/
		$incluir_imagen = (bool) rest::request()->get('con_imagen', 0);
		$modelo = new modelo_persona($id_persona);
		$fila = $modelo->get_datos($incluir_imagen);

		if ($fila) {
			/**Transformción al formato de la vista de la API -
			 * Si faltan campos se generarán 'undefined_index'. Si sobran, no se incluyen.
			 * La fila contiene exactamente los campos de la especificación */
			$fila = rest_hidratador::hidratar_fila($this->get_spec_persona($incluir_imagen), $fila);
		}

		/**Se escribe la respuesta*/
		rest::response()->get($fila);
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
	 * @responses 200 array {"$ref":"Persona"}
	 */
	function get_list()
	{
		/** Se recopilan parametros del usuario con ayuda de un helper - rest_filtro que genera sql*/
		$filtro = $this->get_filtro_get_list();
		$where = $filtro->get_sql_where();
		$limit = $filtro->get_sql_limit();
		$order_by = $filtro->get_sql_order_by();

		/** Se recuperan datos desde el modelo */
		$personas = modelo_persona::get_personas($where, $order_by, $limit);


		/**Transformción al formato de la vista de la API
		 * Como buen ciudadano, se agrega un header para facilitar el paginado al cliente*/
		$personas = rest_hidratador::hidratar($this->get_spec_persona(false), $personas);
		$cantidad = modelo_persona::get_cant_personas($where);
		rest::response()->add_headers(array('Cantidad-Registros' => $cantidad));

		/**Se escribe la respuesta */
		rest::response()->get_list($personas);

	}

	/**
	 * Esto es un alias. Si bien se aleja del REST puro, se puede utilizar para destacar
	 * una operación o proveer un acceso simplificado a operaciones frecuentes.
	 * Se consume en GET /personas/confoto.
	 * @summary Retorna aquellas personas que tienen la foto cargada
	 * @responses 200 array {"$ref": "Persona"} Persona
	 */
	function get_list__confoto()
	{
		$filtro = $this->get_filtro_get_list();
		$limit = $filtro->get_sql_limit();
		$order_by = $filtro->get_sql_order_by();
		$where = $filtro->get_sql_where() . " AND imagen <> ''";
		$personas = modelo_persona::get_personas($where, $order_by, $limit);
		$cantidad = modelo_persona::get_cant_personas($where);

		$personas = rest_hidratador::hidratar($this->get_spec_persona(true), $personas);

		rest::response()->get($personas);
		rest::response()->add_headers(array('Cantidad-Registros' => $cantidad));
	}

	/**
	 * Se consume en POST /personas
	 * @summary Crear una persona
	 * @notes La fecha es en formato 'Y-m-d'</br>
	 * @param_body $persona  PersonaEditar [required] los datos iniciales de la persona
	 * @responses 201 {"id" : "integer"} identificador de la persona agregada
	 * @responses 500 Error en los datos de ingresados para la persona
	 */
	function post_list()
	{
		/** Valido y traduzco los datos al formato de mi modelo*/
		$datos_modelo = $this->procesar_input_edicion();

		/**La validacion del input no reemplaza a las validaciones del modelo (reglas de negocio) */
		//$errores = modelo_persona::validar($datos_modelo);

		/**Aplicación de cambios al modelo*/
		$nuevo = modelo_persona::insert($datos_modelo);

		/** Se retorna el id recientemente creado, de acuerdo a las convenciones de la API*/
		$fila = array('id' => $nuevo);
		rest::response()->post($fila);
	}


    /**
	 * Se consume en PUT /personas/{id}
     * @summary Modificar datos de la persona.
     * @param_body $persona PersonaEditar  [required] los datos a editar de la persona
     * @notes Si envia la componente 'imagen' de la persona se actualiza unicamente la imagen (binario base64). La fecha es en formato 'Y-m-d'
     * @responses 404 No se pudo encontrar a la persona
     * @responses 400 El pedido no cumple con las reglas de negocio - validacion erronea.
     */
	function put($id_persona)
	{
		/** Valido y traduzco los datos al formato de mi modelo*/
		$datos_modelo = $this->procesar_input_edicion(true);
		
		$modelo = new modelo_persona($id_persona);
		//$errores = $modelo->validar($datos);
		
		if (isset($datos_modelo['imagen'])) { //por separado ya que es un caso especial
			$ok = $modelo->update_imagen($datos_modelo);
		} else {
			$ok = $modelo->update($datos_modelo);
		}
		if (!$ok) {
			rest::response()->not_found();
		} else {
			rest::response()->put();
		}
	}

	/**
	 * Se consume en DELETE /personas/{id}
	 * @summary Eliminar la persona.
	 * @notes Cuidado, borra datos de deportes y juegos tambien
	 * @responses 404 No se pudo encontrar a la persona
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
	 * Se consume en GET /personas/{id}/juegos
	 * @summary Retorna todos los juego que practica la persona
	 * @response_type [ {juego: integer, dia_semana: integer, hora_inicio: string, hora_fin:string}, ]
	 * @responses 404 No se pudo encontrar a la persona
	 */
	function get_juegos_list($id_persona)
	{
		//se omite hidratador por simplicidad.
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
		$filtro->agregar_campo("fecha_nacimiento", "pers.fecha_nac");
		$filtro->agregar_campo("id", "pers.id");

		$filtro->agregar_campo_ordenable("nombre", "pers.nombre");
		$filtro->agregar_campo_ordenable("fecha_nacimiento", "pers.fecha_nac");
		return $filtro;
	}


	/**
	 * $relajar_ocultos boolean no checkea campos obligatorios cuando no se especifican
	 */
	protected function procesar_input_edicion($relajar_ocultos = false)
	{
		/**Validacion del input del usuario, de acuerdo a la especificacion de la API
		 * La PersonaEditar tiene solo los campos editables, ej: el id no se puede setear
		 */
		$datos = rest::request()->get_body_json();
		$spec_persona = $this->get_spec_persona(true, 'PersonaEditar');
		rest_validador::validar($datos, $spec_persona, $relajar_ocultos);

		/**Transformo el input del usuario a formato del modelo, deshaciendo la hidratacion.
		 * Por ejemplo, cambia el nombre de fecha_nacimiento (vista) a fecha_nac (modelo)
		 * Se pueden requerir otros pasos, en casos mas complejos */
		$datos = rest_hidratador::deshidratar_fila($datos, $spec_persona);
		return $datos;
	}
}
