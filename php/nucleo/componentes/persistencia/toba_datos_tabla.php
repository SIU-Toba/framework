<?php
/**
 * Representa una estructura tabular tipo tabla o RecordSet en memoria
 *
 * - Utiliza un administrador de persistencia para obtener y sincronizar los datos con un medio de persistencia.
 * - Una vez en memoria existen primitivas para trabajar sobre estos datos.
 * - Los datos y sus modificaciones son mantenidos automáticamente en sesión entre los distintos pedidos de página.
 * - Una vez terminada la edición se hace la sincronización con el medio de persistencia marcando el final de la transacción de negocios.
 *
 * @package Componentes
 * @subpackage Persistencia
 * @todo Control de FK y PK
 */
class toba_datos_tabla extends toba_componente 
{
	protected $_info_estructura;
	protected $_info_columnas;
	protected $_info_externas;
	protected $_info_externas_col;
	protected $_persistidor;						// Mantiene el persistidor del OBJETO
	// Definicion asociada a la TABLA
	protected $_clave = array();							// Columnas que constituyen la clave de la tabla
	protected $_columnas;
	protected $_posee_columnas_ext = false;		// Indica si la tabla posee columnas externas (cargadas a travez de un mecanismo especial)
	//Constraints
	protected $_no_duplicado;					// Combinaciones de columnas que no pueden duplicarse
	// Definicion general
	protected $_tope_max_filas;					// Cantidad de maxima de datos permitida.
	protected $_tope_min_filas;					// Cantidad de minima de datos permitida.
	protected $_es_unico_registro=true;		//La tabla tiene com maximo un registro?
	// ESTADO
	protected $_cambios = array();				// Cambios realizados sobre los datos
	protected $_datos = array();					// Datos cargados en el db_filas
	protected $_proxima_fila = 0;				// Posicion del proximo registro en el array de datos
	protected $_cursor;							// Puntero a una fila específica
	protected $_cursor_original;					// Backup del cursor que se usa para deshacer un seteo
	protected $_cargada = false;
	protected $_from;
	protected $_where;
	//Valores de las columnas BLOB
	protected $_blobs = array();					//Arreglo [$fila][$columna]['fp' => fp, 'path' =>string, 'modificado' => boolean]
													// Si [$fila][$columna] 
															//es null quiere decir que no esta cargado en la transaccion
															//es array(...,'modificado' => true) hay que actualizarlo a la base
															//es array(...,'modificado' => false) se cargo en la transacion pero no se modifico
	// Relaciones con el exterior
	protected $_relaciones_con_padres = array();			// ARRAY con un objeto RELACION por cada PADRE de la tabla
	protected $_relaciones_con_hijos = array();			// ARRAY con un objeto RELACION por cada HIJO de la tabla

	/**
	 * @ignore 
	 */
	final function __construct($id)
	{
		$propiedades = array();
		$propiedades[] = "_cambios";
		$propiedades[] = "_datos";
		$propiedades[] = "_proxima_fila";
		$propiedades[] = "_cursor";
		$propiedades[] = "_cargada";
		$propiedades[] = "_blobs";
		$this->set_propiedades_sesion($propiedades);		
		parent::__construct($id);
		for($a=0; $a < count($this->_info_columnas); $a++){
			//Armo una propiedad "columnas" para acceder a la definicion mas facil
			$this->_columnas[ $this->_info_columnas[$a]['columna'] ] =& $this->_info_columnas[$a];
			if($this->_info_columnas[$a]['pk']==1){
				$this->_clave[] = $this->_info_columnas[$a]['columna'];
			}
			if($this->_info_columnas[$a]['externa']==1){
				$this->_posee_columnas_ext = true;
			}
		}
		$this->activar_cargas_externas();
		$this->activar_control_valores_unicos();
	}

	
	/**
	 * Ventana para agregar configuraciones particulares antes de que el objeto sea construido en su totalidad
	 * @ventana
	 */
	function ini(){}

	
	/**
	 * Destructor del componente
	 */	
	function destruir()
	{
		//-- Recorre los blobs modificados que tienen un resource y los convierte a nombre de archivos
		foreach(array_keys($this->_blobs) as $id_fila) {
			foreach(array_keys($this->_blobs[$id_fila]) as $id_campo) {
				//-- Hay blob?
				if (isset($this->_blobs[$id_fila][$id_campo])) {
					 //-- Si no se subio a un archivo
					if ($this->_blobs[$id_fila][$id_campo]['path'] == '') {
						$fp = $this->_blobs[$id_fila][$id_campo]['fp'];					
						if (is_resource($fp)) {
							//-- Lo convierte a archivo
							rewind($fp);
						  	$temp_nombre = toba::proyecto()->get_path_temp().'/'.md5(uniqid(time()));
						  	$fpd = fopen($temp_nombre, 'w');
						  	stream_copy_to_stream($fp, $fpd);
		  					fclose($fpd);
							//-- Guarda el path
							$this->_blobs[$id_fila][$id_campo]['path'] = $temp_nombre;
						}
					}
					//--Borra la referencia al fp, ya esta subido al Sist.Archv
					$this->_blobs[$id_fila][$id_campo]['fp'] = null;
				} else {
					unset($this->_blobs[$id_fila][$id_campo]);
				}
			}
		}
		parent::destruir();
	}

	/**
	 * @ignore 
	 */
	protected function activar_cargas_externas()
	{
		//--- Se recorren las cargas externas, el lugar ideal seria hacer esto en el ap, pero aca es mas simple y eficiente
		if ($this->_posee_columnas_ext) {
			foreach($this->_info_externas as $externa) {
				$parametros = array();
				$resultados = array();
				//-- Se identifican las columnas de esta carga
				foreach($this->_info_externas_col as $ext_col) {
					if ($ext_col['externa_id'] == $externa['externa_id']) {
						if ($ext_col['es_resultado'] == 1) {
							$resultados[] = $ext_col['columna'];
						} else {
							$parametros[] = $ext_col['columna'];
						}	
					}					
				}
				if ($externa['sql'] != '') {
					//---Caso SQL
					$this->persistidor()->activar_proceso_carga_externa_sql(
							$externa['sql'], $parametros, $resultados, $externa['sincro_continua'], $externa['dato_estricto']);
				} else {
					//---- Caso de carga mediante DAO se divide en 3 casos
					$this->definir_metodo_carga_dao($externa, $resultados, $parametros);
				}
			}
		}		
	}
	
	protected function definir_metodo_carga_dao($externa, $resultados, $parametros)
	{
		//-- La carga se realiza por una clase consulta php
		if (isset($externa['carga_consulta_php']) && !is_null($externa['carga_consulta_php'])) {
			$this->persistidor()->activar_proceso_carga_externa_consulta_php(
								$externa['metodo'], $externa['carga_consulta_php'], $parametros, $resultados, $externa['sincro_continua'],
								$externa['dato_estricto'], $externa['permite_carga_masiva'], $externa['metodo_masivo']);

		}elseif (isset($externa['carga_dt']) && ($externa['carga_dt'] != '')) {	//--Se carga mediante datos_tabla
			$this->persistidor()->activar_proceso_carga_externa_datos_tabla(
								$externa['carga_dt'], $externa['metodo'],$parametros,
								$resultados, $externa['sincro_continua'], $externa['dato_estricto'], $externa['permite_carga_masiva'], $externa['metodo_masivo']);
		} else {		//Se carga con llamada estatica a una clase especifica.
			$this->persistidor()->activar_proceso_carga_externa_dao(
								$externa['metodo'], $externa['clase'], $externa['include'],
								$parametros, $resultados, $externa['sincro_continua'], $externa['dato_estricto'], $externa['permite_carga_masiva'], $externa['metodo_masivo']);
		}		
	}

	/**
	 * @ignore 
	 */
	protected function activar_control_valores_unicos()
	{
		foreach( $this->_info_valores_unicos as $regla ) {
			if(isset($regla['columnas'])) {
				$columnas = explode(',',$regla['columnas']);
				$columnas = array_map('trim', $columnas);
				$this->set_no_duplicado( $columnas );
			}
		}
	}
	
	function set_definicion_columna($columna, $propiedad, $valor) 
	{
		$this->_columnas[$columna][$propiedad] = $valor;
	}
	
	/**
	 * Reserva un id interno y lo retorna
	 */
	function reservar_id_fila()
	{
		$actual = $this->_proxima_fila;
		$this->_proxima_fila++;
		return $actual;
	}

	/**
	 * Retorna el proximo id interno a ser utilizado
	 */
	function get_proximo_id()
	{
		return $this->_proxima_fila;	
	}

	/**
	 * Shorcut a toba::logger()->debug incluyendo infomación básica del componente
	 */
	protected function log($txt)
	{
		toba::logger()->debug("TABLA: [{$this->get_tabla()}]\n".$txt, 'toba');
	}
	//-------------------------------------------------------------------------------
	//--  Relacion con otros ELEMENTOS
	//-------------------------------------------------------------------------------

	/**
	 * Informa a la tabla que existe una tabla padre
	 * @param toba_relacion_entre_tablas $relacion
	 * @ignore 
	 */
	function agregar_relacion_con_padre($relacion, $id_padre)
	{
		$this->_relaciones_con_padres[$id_padre] = $relacion;
	}
	
	/**
	 * Retorna las relaciones con las tablas padre
	 * @return array de {@link toba_relacion_entre_tablas toba_relacion_entre_tablas}
	 * @ignore 
	 */
	function get_relaciones_con_padres()
	{
		return $this->_relaciones_con_padres;
	}

	/**
	 * Retorna la relación con una tabla padre
	 * @return {@link toba_relacion_entre_tablas toba_relacion_entre_tablas}
	 * @ignore 
	 */	
	function get_relacion_con_padre($id_tabla_padre)
	{
		return $this->_relaciones_con_padres[$id_tabla_padre];	
	}
	
	/**
	 * Informa a la tabla que existe una tabla hija de la actual
	 * @param toba_relacion_entre_tablas $relacion
	 * @ignore 
	 */	
	function agregar_relacion_con_hijo($relacion, $id_hijo)
	{
		$this->_relaciones_con_hijos[$id_hijo] = $relacion;
	}

	/*
		***  Notificaciones  ***
	*/

	private function notificar_contenedor($evento, $param1=null, $param2=null)
	{
		if(isset($this->controlador)){
			//$this->_contenedor->registrar_evento($this->_id, $evento, $param1, $param2);
		}
	}

	/**
	 * Aviso a las relacion padres que el componente HIJO se CARGO
	 * @ignore 
	 */
	function notificar_padres_carga($reg_hijos=null)
	{
		if(isset($this->_relaciones_con_padres)){
			foreach ($this->_relaciones_con_padres as $relacion) {
				$relacion->evt__carga_hijo($reg_hijos);
			}
		}
	}

	/**
	 * Aviso a las relaciones hijas que el componente PADRE sincrozo sus actualizaciones
	 * @ignore 
	 */
	function notificar_hijos_sincronizacion($filas=array())
	{
		if(isset($this->_relaciones_con_hijos)){
			foreach ($this->_relaciones_con_hijos as $relacion) {
				$relacion->evt__sincronizacion_padre($filas);
			}
		}
	}


	/**
	 * Retorna la {@link toba_datos_relacion relacion} que contiene a esta tabla, si existe
	 * @return toba_datos_relacion
	 */
	function get_relacion()
	{
		if (isset($this->controlador)) {
			return $this->controlador;		
		}
	}

	/**
	 * Retorna un objeto en el cual se puede realizar busquedas complejas de registros en memoria
	 * @return toba_datos_busqueda
	 */
	function nueva_busqueda()
	{
		return new toba_datos_busqueda($this->controlador, $this);		
	}

	//-------------------------------------------------------------------------------
	//-- Preguntas BASICAS
	//-------------------------------------------------------------------------------

	/**
	 *	Retorna las columnas que son claves en la tabla
	 */
	function get_clave()
	{
		if (empty($this->_clave)) {
			return null;
		}else{
			return $this->_clave;
		}
	}
	
	/**
	 * Retorna el valor de la clave para un fila dada
	 * @param mixed $id_fila Id. interno de la fila
	 * @return array Valores de las claves para esta fila, en formato RecordSet
	 */
	function get_clave_valor($id_fila)
	{
		$temp = array();
		foreach( $this->_clave as $columna ){
			$temp[$columna] = $this->get_fila_columna($id_fila, $columna);
		}
		return $temp;
	}

	/**
	 * Retorna la cantidad maxima de filas que puede contener la tabla (si existe tal restriccion)
	 * @return integer, 0 si no hay tope
	 */
	function get_tope_max_filas()
	{
		return $this->_tope_max_filas;	
	}


	/**
	 * Retorna la cantidad minima de fila que debe contener la tabla (si existe tal restriccion)
	 * @return integer, 0 si no hay tope
	 */	
	function get_tope_min_filas()
	{
		return $this->_tope_min_filas;	
	}

	/**
	 * Retorna la cantidad de filas que sufrieron cambios desde la carga, y por lo tanto se van a sincronizar
	 * @return integer 
	 */
	function get_cantidad_filas_a_sincronizar()
	{
		$cantidad = 0;
		foreach(array_keys($this->_cambios) as $fila){
			if( ($this->_cambios[$fila]['estado'] == "d") ||
				($this->_cambios[$fila]['estado'] == "i") ||
				($this->_cambios[$fila]['estado'] == "u") ){
				$cantidad++;
			}
		}
		return $cantidad;
	}

	/**
	 * Retorna lasfilas que sufrieron cambios desde la carga
	 * @param array $cambios Combinación de tipos de cambio a buscar: d, i o u  (por defecto los tres)
	 * @return array Ids. internos
	 */
	function get_id_filas_a_sincronizar( $cambios=array("d","i","u") )
	{
		$ids = array();
		foreach(array_keys($this->_cambios) as $fila){
			if( in_array($this->_cambios[$fila]['estado'], $cambios) ){
				$ids[] = $fila;
			}
		}
		return $ids;
	}

	/**
	 * Devuelve las fks que asocian a las tablas extendidas
	 * @return array
	 */
	function get_fks()
	{
		return $this->_info_fks;
	}

	//-------------------------------------------------------------------------------
	//-- Configuracion
	//-------------------------------------------------------------------------------

	/**
	 * Cambia la cantidad maxima de filas que puede contener la tabla
	 * @param integer $cantidad 0 si no hay tope
	 */	
	function set_tope_max_filas($cantidad)
	{
		if ($cantidad == '')
			$cantidad = 0;		
		if(is_numeric($cantidad) && $cantidad >= 0){
			$this->_tope_max_filas = $cantidad;	
			if ($cantidad != 1) {
				$this->set_es_unico_registro(false);	
			}
		}else{
			throw new toba_error_def("El valor especificado en el TOPE MAXIMO de registros es incorrecto");
		}
	}

	/**
	 * Cambia la cantidad mínima de filas que debe contener la tabla
	 * @param integer $cantidad 0 si no hay tope
	 */		
	function set_tope_min_filas($cantidad)
	{
		if ($cantidad == '')
			$cantidad = 0;
		if(is_numeric($cantidad) && $cantidad >= 0){
			$this->_tope_min_filas = $cantidad;
		}else{
			throw new toba_error_def("El valor especificado en el TOPE MINIMO de registros es incorrecto");
		}
	}

	/**
	 * Indica una combinacion de columnas cuyos valores no deben duplicarse (similar a un unique de sql)
	 */
	function set_no_duplicado( $columnas )
	{
		$this->_no_duplicado[] = $columnas;
	}

	/**
	 * Indica que la tabla maneja un único registro en memoria, habilitando la api get/set
	 * @param boolean $unico
	 */
	function set_es_unico_registro($unico)
	{
		$this->_es_unico_registro = $unico;	
	}
	
	//-------------------------------------------------------------------------------
	//-- MANEJO DEL CURSOR INTERNO---------------------------------------------------
	//-------------------------------------------------------------------------------
	
	/**
	 * Fija el cursor en una fila dada
	 * Cuando la tabla tiene un cursor muchas de sus operaciones empiezan a tratar a esta fila como la única 
	 * y sus tablas padres e hijas también. Por ejemplo al pedir las filas de la tabla hija solo retorna aquellas filas hijas del registro cursor de la tabla padre.
	 * @param mixed $id Id. interno de la fila
	 */
	function set_cursor($id)
	{
		$id = $this->normalizar_id($id);
		if( $this->existe_fila($id) ){
			$this->_cursor_original = isset($this->_cursor) ? $this->_cursor : null;
			$this->_cursor = $id;	
			$this->log("Nuevo cursor '{$this->_cursor}' en reemplazo del anterior '{$this->_cursor_original}'");
		}else{
			throw new toba_error_def($this->get_txt() . "La fila '$id' no es valida");
		}
	}	
	
	/**
	 * Deshace el ultimo seteo de cursor
	 */
	function restaurar_cursor()
	{
		$this->_cursor = $this->_cursor_original;
		$this->log("Se restaura el cursor '{$this->_cursor_original}'");		
	}

	
	/**
	 * Asegura que el cursor no se encuentre posicionado en ninguna fila específica
	 */
	function resetear_cursor()
	{
		unset($this->_cursor);
		$this->log("Se resetea el cursor");				
	}
	
	/**
	 * Retorna el Id. interno de la fila donde se encuentra actualmente el cursor de la tabla
	 * @return mixed
	 */
	function get_cursor()
	{
		if(isset($this->_cursor)){
			return $this->_cursor;
		}	
	}

	/**
	 * Hay una fila seleccionada por el cursor?
	 */
	function hay_cursor()
	{
		return isset($this->_cursor);
	}
	
	//-------------------------------------------------------------------------------
	//-- ACCESO a FILAS   -----------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Retorna el conjunto de filas que respeta las condiciones dadas
	 * Por defecto la búsqueda es afectada por la presencia de cursores en las tablas padres.
	 * @param array $condiciones Se utiliza este arreglo campo=>valor y se retornan los registros que cumplen (con condicion de igualdad) con estas restricciones. El valor no puede ser NULL porque siempre da falso
	 * @param boolean $usar_id_fila Hace que las claves del array resultante sean
	 * las claves internas del datos_tabla. Sino se usa una clave posicional y
	 * la clave viaja en la columna apex_datos_clave_fila
	 * @param boolean $usar_cursores Este conjunto de filas es afectado por la presencia de cursores en las tablas padres. 
	 * @return array Formato tipo RecordSet
	 */
	function get_filas($condiciones=null, $usar_id_fila=false, $usar_cursores=true)
	{
		$datos = array();
		$a = 0;
		foreach( $this->get_id_fila_condicion($condiciones, $usar_cursores) as $id_fila ) {
			if($usar_id_fila){
				$datos[$id_fila] = $this->_datos[$id_fila];
			}else{
				$datos[$a] = $this->_datos[$id_fila];
				//esta columna indica cual fue la clave del registro
				$datos[$a][apex_datos_clave_fila] = $id_fila;
			}
			$a++;
		}		
		return $datos;
	}
	
	/**
	 * Retorna los ids de todas las filas (sin eliminar) de esta tabla
	 * @param boolean $usar_cursores Este conjunto de filas es afectado por la presencia de cursores en las tablas padres
	 * @return array()
	 * @todo Se podría optimizar este método para no recaer en tantos recorridos
	 */
	function get_id_filas($usar_cursores=true)
	{
		$coincidencias = array();
		foreach(array_keys($this->_cambios) as $id_fila){
			if($this->_cambios[$id_fila]['estado']!="d"){
				$coincidencias[] = $id_fila;
			}
		}
		if ($usar_cursores) {
			//Si algún padre tiene un cursor posicionado, 
			//se restringe a solo las filas que son hijas de esos cursores
			foreach ($this->_relaciones_con_padres as $id => $rel_padre) {
				$coincidencias = $rel_padre->filtrar_filas_hijas($coincidencias);
			}
		}
		return $coincidencias;		
	}
	
	/**
	 * Retorna los ids de todas las filas (sin eliminar) de esta tabla
	 * @param boolean $usar_cursores Este conjunto de filas es afectado por la presencia de cursores en las tablas padres
	 * @return array()
	 * @todo Se podría optimizar este método para no recaer en tantos recorridos
	 */
	function get_id_filas_filtradas_por_cursor($incluir_eliminados=false)
	{
		if($this->hay_cursor()){
			return array( $this->get_cursor() );
		} else {
			$coincidencias = array();
			foreach(array_keys($this->_cambios) as $id_fila){
				if($incluir_eliminados || $this->_cambios[$id_fila]['estado']!="d"){
					$coincidencias[] = $id_fila;
				}
			}
			foreach ($this->_relaciones_con_padres as $id => $rel_padre) {
				$coincidencias = $rel_padre->filtrar_filas_hijas($coincidencias, $incluir_eliminados);
			}
			return $coincidencias;		
		}
	}


	/**
	 * Retorna los padres de un conjunto de registros especificos
	 */
	function get_id_padres($ids_propios, $tabla_padre)
	{
		$salida = array();
		foreach ($ids_propios as $id_propio) {
			$id_padre = $this->get_id_fila_padre($tabla_padre, $id_propio);
			if ($id_padre !== null) {
				$salida[] = $id_padre;	
			}
		}
		return array_unique($salida);
	}
	
	/**
	* Busca en una tabla padre el id de fila padre que corresponde a la fila hija especificada
	*/
	function get_id_fila_padre($tabla_padre, $id_fila)
	{
		$id_fila = $this->normalizar_id($id_fila);
		if(!isset($this->_relaciones_con_padres[$tabla_padre])) {
			throw new toba_error_def("La tabla padre '$tabla_padre' no existe");	
		}
		return $this->_relaciones_con_padres[$tabla_padre]->get_id_padre($id_fila);
	}
	
	
	/**
	 * Busca los registros en memoria que cumplen una condicion.
	 * Solo se chequea la condicion de igualdad. No se chequean tipos
	 * @param array $condiciones Asociativo de campo => valor. El valor no puede ser NULL porque siempre da falso
	 *  			Para condiciones más complejas (no solo igualdad) puede ser array($columna, $condicion, $valor), 
	 * 				por ejemplo array(array('id_persona','>=',10),...)
	 * @param boolean $usar_cursores Este conjunto de filas es afectado por la presencia de cursores en las tablas padres
	 * @return array Ids. internos de las filas, pueden no estar numerado correlativamente
	 */	
	function get_id_fila_condicion($condiciones=null, $usar_cursores=true)
	{	
		//En principio las coincidencias son todas las filas
		$coincidencias = $this->get_id_filas($usar_cursores);
		//Si hay condiciones, se filtran estas filas
		if(isset($condiciones)){
			if(!is_array($condiciones)){
				throw new toba_error("Las condiciones de filtrado deben ser un array asociativo");
			}
			//Controlo que todas los campos que se utilizan para el filtrado existan
			/*foreach( array_keys($condiciones) as $columna){

			}*/
			foreach($coincidencias as $pos => $id_fila){
				//Verifico las condiciones
				foreach( array_keys($condiciones) as $campo){
					if (is_array($condiciones[$campo])) {
						list($columna, $operador, $valor) = $condiciones[$campo];
					} else {
						$columna = $campo;
						$operador = '==';						
						$valor = $condiciones[$campo];
					}					
					if( !isset($this->_columnas[$columna]) ){
						throw new toba_error_def("El campo '$columna' no existe. No es posible filtrar por dicho campo");
					}
					if(!isset($this->_datos[$id_fila][$columna])) {
						// Es posible que una fila no posea una columa. Ej: una nueva fila no tiene la clave si esta es una secuencia.
						// Si el valor no existe, considero que la comparacion con esa fila da falso (* != NULL)
						unset($coincidencias[$pos]);
						break;
					} else {
						if (! comparar($this->_datos[$id_fila][$columna], $operador, $valor)) {
							//Se filtra la fila porque no cumple las condiciones
							unset($coincidencias[$pos]);
							break;
						}
					}
				}
			}
		}
		return array_values($coincidencias);
	}

	/**
	 * Retorna el contenido de una fila, a partir de su clave interna
	 * @param mixed $id Id. interno de la fila en memoria
	 * @return array columna => valor. En caso de no existir la fila retorna NULL
	 */
	function get_fila($id)
	{
		$id = $this->normalizar_id($id);
		if(isset($this->_datos[$id])){
			$temp = $this->_datos[$id];
			$temp[apex_datos_clave_fila] = $id;	//incorporo el ID del dbr
			return $temp;
		}else{
			return null;
			//throw new toba_error("Se solicito un registro incorrecto");
		}
	}

	/**
	 * Retorna el valor de una columna en una fila dada
	 * @param mixed $id Id. interno de la fila
	 * @param string $columna Nombre de la columna
	 * @return mixed En caso de no existir, retorna NULL
	 */
	function get_fila_columna($id, $columna)
	{
		$id = $this->normalizar_id($id);
		if(isset($this->_datos[$id][$columna])){
			return  $this->_datos[$id][$columna];
		}else{
			return null;
		}
	}
	
	/**
	 * Retorna los valores de una columna específica
	 * El conjunto de filas utilizado es afectado por la presencia de cursores en las tablas padres
	 * @param string $columna Nombre del campo o columna
	 * @return array Arreglo plano de valores
	 */
	function get_valores_columna($columna)
	{
		$temp = array();
		foreach($this->get_id_filas() as $fila){
			$temp[] = $this->_datos[$fila][$columna];
		}
		return $temp;
	}
	
	/**
	 * Retorna el valor de la columna de la fila actualmente seleccionada como cursor
	 * @param string $columna Id. de la columna que contiene el valor a retornar
	 * @return mixed NULL si no cursor o no hay filas
	 */	
	function get_columna($columna)
	{
		if ($this->get_cantidad_filas() == 0) {
			return null;
		} elseif ($this->hay_cursor()) {
			return $this->get_fila_columna($this->get_cursor(), $columna);
		} else {
			throw new toba_error_def("No hay posicionado un cursor en la tabla, no es posible determinar la fila actual");
		}		
	}
	
	/**
	 * Cantidad de filas que tiene la tabla en memoria
	 * El conjunto de filas utilizado es afectado por la presencia de cursores en las tablas padres
	 * @return integer
	 */
	function get_cantidad_filas($usar_cursores=true)
	{
		return count($this->get_id_filas($usar_cursores));
	}
	
	/**
	 * Existe una determina fila? (la fila puede estar marcada como para borrar)
	 * @param mixed $id Id. interno de la fila
	 * @return boolean
	 */
	function existe_fila($id)
	{
		$id = $this->normalizar_id($id);
		if(! isset($this->_datos[$id]) ){
			return false;			
		}
		if($this->_cambios[$id]['estado']=="d"){
			return false;
		}
		return true;
	}
	
	/**
	 * Busca los registros en memoria que cumplen una condicion.
	 * Solo se chequea la condicion de igualdad. No se chequean tipos
	 * @param array $condiciones Asociativo de campo => valor.
	 *  			Para condiciones más complejas (no solo igualdad) puede ser array($columna, $condicion, $valor), 
	 * 				por ejemplo array(array('id_persona','>=',10),...)
	 * @param boolean $usar_cursores Este conjunto de filas es afectado por la presencia de cursores en las tablas padres	
	 */
	function existe_fila_condicion($condiciones, $usar_cursores = true)
	{
		$ids = $this->get_id_fila_condicion($condiciones, $usar_cursores);
		return !empty($ids);
	}

	/**
	 * Valida un id interno y a la vez permite aceptarlo como parte de un arreglo en
	 * la columna apex_datos_clave_fila
	 * @ignore 
	 */
	protected function normalizar_id($id)
	{
		if(!is_array($id)){
			return $id;	
		}else{
			if(isset($id[apex_datos_clave_fila])){
				return $id[apex_datos_clave_fila];
			}
		}
		throw new toba_error_def($this->get_txt() . ' La clave tiene un formato incorrecto.');
	}

	//-------------------------------------------------------------------------------
	//-- ALTERACION de FILAS  ------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Crea una nueva fila en la tabla en memoria
	 *
	 * @param array $fila Asociativo campo=>valor a insertar
	 * @param mixed $ids_padres Asociativo padre =>id de las filas padres de esta nueva fila, 
	 * 						  en caso de que no se brinde, se utilizan los cursores actuales en estas tablas padres
	 * @param integer $id_nuevo Opcional. Id interno de la nueva fila, si no se especifica (recomendado)
	 * 								Se utiliza el proximo id interno.
	 * @return mixed Id. interno de la fila creada
	 */
	function nueva_fila($fila=array(), $ids_padres=null, $id_nuevo=null)
	{		
		$this->notificar_contenedor("ins", $fila);
		//Saco el campo que indica la posicion del registro
		if(isset($fila[apex_datos_clave_fila])) unset($fila[apex_datos_clave_fila]);
		if(isset($fila[apex_ei_analisis_fila])) unset($fila[apex_ei_analisis_fila]);
		
		$this->validar_fila($fila);
		//SI existen columnas externas, completo la fila con las mismas
		if($this->_posee_columnas_ext){
			$campos_externos = $this->persistidor()->completar_campos_externos_fila($fila,"ins");
			foreach($campos_externos as $id => $valor) {
				$fila[$id] = $valor;
			}
		}
		
		//---Se le asigna un id a la fila
		if (!isset($id_nuevo) || $id_nuevo < $this->_proxima_fila) {
			$id_nuevo = $this->_proxima_fila;
		}
		$this->_proxima_fila = $id_nuevo + 1;
		//Se notifica a las relaciones del alta
		foreach ($this->_relaciones_con_padres as $padre => $relacion) {
			$id_padre = null;
			if (isset($ids_padres[$padre])) {
				$id_padre = $ids_padres[$padre];
			}
			$relacion->asociar_fila_con_padre($id_nuevo, $id_padre);							
		}
		
		//Se agrega la fila
		$this->_datos[$id_nuevo] = $fila;
		$this->registrar_cambio($id_nuevo,"i");
		
		return $id_nuevo;
	}

	/**
	 * Modifica los valores de una fila de la tabla en memoria
	 * Solo se modifican los valores de las columnas enviadas y que realmente cambien el valor de la fila.
	 * @param mixed $id Id. interno de la fila a modificar
	 * @param array $fila Contenido de la fila, en formato columna=>valor, puede ser incompleto
	 * @param array $nuevos_padres Arreglo (id_tabla_padre => $id_fila_padre, ....), solo se cambian los padres que se pasan por parámetros
	 * 				El resto de los padres sigue con la asociación anterior
	 * @return mixed Id. interno de la fila modificada
	 */
	function modificar_fila($id, $fila, $nuevos_padres=null)
	{
		$id = $this->normalizar_id($id);
		if (!$this->existe_fila($id)){
			$mensaje = $this->get_txt() . " MODIFICAR. No existe un registro con el INDICE indicado ($id)";
			toba::logger()->error($mensaje);
			throw new toba_error_def($mensaje);
		}
		//Saco el campo que indica la posicion del registro
		if(isset($fila[apex_datos_clave_fila])) unset($fila[apex_datos_clave_fila]);
		if(isset($fila[apex_ei_analisis_fila])) unset($fila[apex_ei_analisis_fila]);      
		
		$this->validar_fila($fila, $id);
		$this->notificar_contenedor("pre_modificar", $fila, $id);
		
		//Actualizo los valores
		$alguno_modificado = false;
		foreach(array_keys($fila) as $clave){
			$modificar = $this->es_campo_modificado($clave, $id, $fila);
			if ($modificar) {
				$alguno_modificado = true;
				$this->_datos[$id][$clave] = $fila[$clave];
			}
		}
		//--- Esto evita propagar cambios que en realidad no sucedieron
		if ($alguno_modificado) {
			if($this->_cambios[$id]['estado']!="i"){
				$this->registrar_cambio($id,"u");
			}
			
			/*
				Como los campos externos pueden necesitar un campo que no entrego la
				interfaz, primero actualizo los valores y despues tomo la fila y la
				proceso con la actualizacion de campos externos
			*/
			//Si la tabla posee campos externos, le pido la nueva fila al persistidor
			if($this->_posee_columnas_ext){
				$campos_externos = $this->persistidor()->completar_campos_externos_fila($this->_datos[$id],"upd");
				foreach($campos_externos as $clave => $valor){
					$this->_datos[$id][$clave] = $valor;
				}
			}
		}
		$this->notificar_contenedor("post_modificar", $fila, $id);
		if (isset($nuevos_padres)) {
			$this->cambiar_padre_fila($id, $nuevos_padres);
		}
		return $id;
	}

	/**
	 * Cambia los padres de una fila
	 * @param mixed $id_fila 
	 * @param array $nuevos_padres Arreglo (id_tabla_padre => $id_fila_padre, ....), solo se cambian los padres que se pasan por parámetros
	 * 				El resto de los padres sigue con la asociación anterior
	 */
	function cambiar_padre_fila($id_fila, $nuevos_padres)
	{
		$id = $this->normalizar_id($id_fila);		
		if (!$this->existe_fila($id)){
			$mensaje = $this->get_txt() . " CAMBIAR PADRE. No existe un registro con el INDICE indicado ($id)";
			toba::logger()->error($mensaje);
			throw new toba_error_def($mensaje);
		}
		$cambio_padre = false;
		foreach ($nuevos_padres as $tabla_padre => $id_padre) {
			if (!isset($this->_relaciones_con_padres[$tabla_padre])) {
				$mensaje = $this->get_txt() . " CAMBIAR PADRE. No existe una relación padre $tabla_padre.";
				throw new toba_error_def($mensaje);
			}
			if ($this->_relaciones_con_padres[$tabla_padre]->set_padre($id_fila, $id_padre)) {
				$cambio_padre = true;	
			}
		}
		//-- Si algun padre efectivamente cambio, tengo que marcar al registro como actualizado
		if ($cambio_padre) {
			if($this->_cambios[$id_fila]['estado']!="i"){
				$this->registrar_cambio($id_fila,"u");
			}
		}
	}
	
	/**
	 * Elimina una fila de la tabla en memoria
	 * En caso de que la fila sea el cursor actual de la tabla, este ultimo se resetea
	 * @param mixed $id Id. interno de la fila a eliminar
	 * @return Id. interno de la fila eliminada
	 */
	function eliminar_fila($id)
	{
		$id = $this->normalizar_id($id);
		if (!$this->existe_fila($id)) {
			$mensaje = $this->get_txt() . " ELIMINAR. No existe un registro con el INDICE indicado ($id)";
			toba::logger()->error($mensaje);
			throw new toba_error_def($mensaje);
		}
		if ( $this->get_cursor() == $id ) { 
 			$this->resetear_cursor();        
		}
 		$this->notificar_contenedor("pre_eliminar", $id);
		//Se notifica la eliminación a las relaciones
		foreach ($this->_relaciones_con_hijos as $rel) {
			$rel->evt__eliminacion_fila_padre($id);
		}
		foreach ( $this->_relaciones_con_padres as $rel) {
			$rel->evt__eliminacion_fila_hijo($id);			
		}
		if($this->_cambios[$id]['estado']=="i"){
			unset($this->_cambios[$id]);
			unset($this->_datos[$id]);
		}else{
			$this->registrar_cambio($id,"d");
		}
		$this->notificar_contenedor("post_eliminar", $id);
		return $id;
	}

	/**
	 * Elimina todas las filas de la tabla en memoria
	 * @param boolean $con_cursores Tiene en cuenta los cursores del padre para afectar solo sus filas hijas, por defecto utiliza cursores. 
	 */
	function eliminar_filas($con_cursores = true)
	{
		foreach($this->get_id_filas($con_cursores) as $fila) {
			$this->eliminar_fila($fila);
		}
	}

	/**
	 * Cambia el valor de una columna de una fila especifica
	 *
	 * @param mixed $id Id. interno de la fila de la tabla en memoria
	 * @param string $columna Columna o campo de la fila
	 * @param mixed $valor Nuevo valor
	 */
	function set_fila_columna_valor($id, $columna, $valor)
	{
		$id = $this->normalizar_id($id);
		if( $this->existe_fila($id) ){
			if( isset($this->_columnas[$columna]) ){
				$this->modificar_fila($id, array($columna => $valor));
			}else{
				throw new toba_error_def("La columna '$columna' no es valida");
			}
		}else{
			throw new toba_error_def("La fila '$id' no es valida");
		}
	}

	/**
	 * Cambia el valor de una columna en todas las filas
	 * 
	 * @param string $columna Nombre de la columna a modificar
	 * @param mixed $valor Nuevo valor comun a toda la columna
	 * @param boolean $con_cursores Tiene en cuenta los cursores del padre para afectar sus filas hijas, por defecto no
	 */
	function set_columna_valor($columna, $valor, $con_cursores=false)
	{
		if(! isset($this->_columnas[$columna]) ) { 
			throw new toba_error_def("La columna '$columna' no es valida");
		}
		foreach($this->get_id_filas($con_cursores) as $fila) {
			$this->modificar_fila($fila, array($columna => $valor));
		}		
	}

	/**
	 * Procesa los cambios masivos de filas
	 * 
	 * El id de la fila se asume que la key del registro o la columna apex_datos_clave_fila
	 * Para procesar es necesario indicar el estado de cada fila utilizando una columna referenciada con la constante 'apex_ei_analisis_fila' los valores pueden ser:
	 *  - 'A': Alta
	 *  - 'B': Baja
	 *  - 'M': Modificacion
	 *
	 * @param array $filas Filas en formato RecordSet, cada registro debe contener un valor para la constante apex_ei_analisis_fila
	 * @param mixed $ids_padres Asociativo padre =>id de las filas padres de esta nueva fila, 
	 * 						  en caso de que no se brinde, se utilizan los cursores actuales en estas tablas padres
	 */
	function procesar_filas($filas, $ids_padres=null)
	{
		toba_asercion::es_array($filas,"toba_datos_tabla - El parametro no es un array.");
		//--- Controlo estructura
		foreach(array_keys($filas) as $id){
			if(!isset($filas[$id][apex_ei_analisis_fila])){
				throw new toba_error_def("Para procesar un conjunto de registros es necesario indicar el estado ".
									"de cada uno utilizando una columna referenciada con la constante 'apex_ei_analisis_fila'.
									Si los datos provienen de un ML, active la opción de analizar filas.");
			}
		}
		//--- Se asume que el id de la fila es la key del registro o la columna apex_datos_clave_fila. 
		foreach ($filas as $id => $fila) {
			$id_explicito = false;
			if (isset($fila[apex_datos_clave_fila])) {
				$id = $fila[apex_datos_clave_fila];
				$id_explicito = true;
			}	
			$accion = $fila[apex_ei_analisis_fila];
			unset($fila[apex_ei_analisis_fila]);
			switch($accion){
				case "A":
					//--- Si el ML notifico explicitamente el id, este es el id de la nueva fila, sino usa el mecanismo interno
					$nuevo_id = ($id_explicito) ? $id : null;
					$this->nueva_fila($fila, $ids_padres, $nuevo_id);
					break;	
				case "B":
					$this->eliminar_fila($id);
					break;	
				case "M":
					$this->modificar_fila($id, $fila);
					break;	
			}
		}
	}

	//-------------------------------------------------------------------------------
	//-- Simplificación sobre una sola línea
	//-------------------------------------------------------------------------------

	/**
	 * Cambia el contenido de la fila donde se encuentra el cursor interno
	 * Si la tabla se definio admitiendo a lo sumo un registro, este cursor se posiciona automáticamente en la carga, sino se debe explicitar con el método set_cursor
	 * En caso que no existan filas, se crea una nueva y se posiciona el cursor en ella
	 * Si la fila es null, se borra la fila actual
	 *
	 * @param array $fila Contenido total o parcial de la fila a crear o modificar (si es null borra la fila actual)
	 */
	function set($fila)
	{
		if($this->hay_cursor()){
			if (isset($fila)) {
				$this->modificar_fila($this->get_cursor(), $fila);
			} else {
				$this->eliminar_fila($this->get_cursor());
			}
		} else {
			if (isset($fila)) {
				$id = $this->nueva_fila($fila);
				$this->set_cursor($id);
			}
		}
	}
	
	/**
	 * Retorna el contenido de la fila donde se encuentra posicionado el cursor interno
	 * Si la tabla se definio admitiendo a lo sumo un registro, este cursor se posiciona automáticamente en la carga, sino se debe explicitar con el método set_cursor
	 * En caso de que no haya registros retorna NULL
	 */
	function get()
	{
		if ($this->get_cantidad_filas() == 0) {
			return null;
		} elseif ($this->hay_cursor()) {
			return $this->get_fila($this->get_cursor());
		} else {
			throw new toba_error_def("No hay posicionado un cursor en la tabla, no es posible determinar la fila actual");
		}
	}


	//---------------------------------------------------------------------------
	//-- Trabajo con campos BLOBs  ----------------------------------------------
	//---------------------------------------------------------------------------	
	
	/**
	 * Almacena un 'file pointer' en un campo binario o blob de la tabla. 
	 * @param string $columna Nombre de la columna binaria-blob
	 * @param resource $blob file pointer o null en caso de querer borrar el valor
	 * @param mixed $id_fila Id. interno de la fila que contiene la columna, en caso de ser vacio se utiliza el cursor
	 */
	function set_blob($columna, $blob, $id_fila=null)
	{
		if (isset($blob) && ! is_resource($blob)) {
			throw new toba_error_def("Las columnas binarias o BLOB esperan un 'resource', producto generalmente de un 'fopen' del archivo a subir a la base");	
		}
		if (! isset($id_fila)) {
			if ($this->hay_cursor()){
				$id_fila = $this->get_cursor();
			} else {
				throw new toba_error_def("No hay posicionado un cursor en la tabla, no es posible determinar la fila actual");	
			}
		}
		//Borra algïñun cache previo
		if (isset($this->_blobs[$id_fila][$columna]['path']) && $this->_blobs[$id_fila][$columna]['path'] != '') {
			$path = $this->_blobs[$id_fila][$columna]['path'];
			if (file_exists($path)) {
				unlink($path);
			}
		}
		
		$this->_blobs[$id_fila][$columna] = array('fp'=>$blob, 'path'=>'',  'modificado' => true);
		if(isset($this->_cambios[$id_fila]['estado']) && $this->_cambios[$id_fila]['estado']!="i"){
			$this->registrar_cambio($id_fila,"u");
		}
	}
	
	/**
	 * Retorna un 'file pointer' apuntando al campo binario o blob de la tabla.
	 *
	 * @param string $columna Nombre de la columna binaria-blob
	 * @param mixed $id_fila Id. interno de la fila que contiene la columna, en caso de ser vacio se utiliza el cursor
	 * @return resource
	 */
	function get_blob($columna, $id_fila=null)
	{
		if (! isset($id_fila)) {
			if ($this->get_cantidad_filas() == 0) {
				return null;
			} elseif ($this->hay_cursor()) {
				$id_fila = $this->get_cursor();
			} else {
				throw new toba_error_def("No hay posicionado un cursor en la tabla, no es posible determinar la fila actual");
			}
		}
		if (!isset($this->_blobs[$id_fila][$columna])) {
			//-- Si no tiene el dato y es una fila nueva, no hay nada 
			if ($this->_cambios[$id_fila]['estado'] == "i") {
				return null;
			}
			//-- Carga peresoza del file_pointer
			$fp = $this->persistidor()->consultar_columna_blob($id_fila, $columna);
			$this->_blobs[$id_fila][$columna] = array('fp' => $fp, 'path' => '', 'modificado' => false);
		}
		$fp = $this->_blobs[$id_fila][$columna]['fp'];
		$path = $this->_blobs[$id_fila][$columna]['path'];
		if (!is_resource($fp) && $path != '') {
			//Si no es un recurso el $fp, se carga desde el path previamente cargado
			$fp = fopen($path, 'rb');
			$this->_blobs[$id_fila][$columna]['fp'] = $fp;
		}
		return $fp;
	}
	
	/**
	 * Permite obtener el fp de un blob que se haya modificado en esta transaccion
	 * @ignore
	 */
	function _get_blob_transaccion($id_registro, $col)
	{
		//-- Si no esta seteado, no se cargo ni modifico
		if (!isset($this->_blobs[$id_registro][$col])) {
			return null;
		} elseif ($this->_blobs[$id_registro][$col]['modificado']) {
			//--Hay que actualizar el BLOB
			$fp = $this->_blobs[$id_registro][$col]['fp'];
			$path = $this->_blobs[$id_registro][$col]['path'];
			if (! is_resource($fp)) {
				if ($path != '') {
					$fp = fopen($path, 'rb');
					if (! is_resource($fp)) {
						throw new toba_error_def("No fue posible recuperar el campo '$col' de la fila '$id_registro' desde archivo temporal '$fp'");
					}
				} else {
					// Quiere decir que explicitamente se lo hizo nulo
					return false;
				}
			}
			return $fp;

		} else {
			return null;
		}		
	}
	
	//-------------------------------------------------------------------------------
	//-- VALIDACION en LINEA
	//-------------------------------------------------------------------------------

	/**
	 * Valida un registro durante el procesamiento
	 */
	private function validar_fila($fila, $id=null)
	{
		if(!is_array($fila)){
			throw new toba_error_def($this->get_txt() . ' La fila debe ser una array');	
		}
		$this->evt__validar_ingreso($fila, $id);
		$this->control_estructura_fila($fila);
	}

	/**
	 * Ventana de validacion que se invoca cuando se crea o modifica una fila en memoria. Lanzar una excepcion en caso de error
	 * @param array $fila Datos de la fila
	 * @param mixed $id Id. interno de la fila, si tiene (en el caso modificacion de la fila)
	 * 
	 * @ventana
	 */
	protected function evt__validar_ingreso($fila, $id=null){}

	//-------------------------------------------------------------------------------

	/**
	 * Controla que los campos del registro existan
	 * @ignore 
	 */
	protected function control_estructura_fila($fila)
	{
		foreach($fila as $campo => $valor){
			//SI el registro no esta en la lista de manipulables o en las secuencias...
			if( !(isset($this->_columnas[$campo]))  ){
				$mensaje = $this->get_txt() . get_class($this)." El registro tiene una estructura incorrecta: El campo '$campo' ". 
						" no forma parte de la DEFINICION.";
				toba::logger()->warning($mensaje);
			}
		}
	}
	//-------------------------------------------------------------------------------

	/**
	 * Controla que un registro no duplique los valores existentes
	 */
	private function control_valores_unicos_fila($fila, $id=null)
	//Controla que un registro no duplique los valores existentes
	{
		if (isset($this->_no_duplicado)) {
			foreach($this->_no_duplicado as $columnas) {				//Para cada constraint de columnas
				$this->validar_columnas_en_fila($columnas, $fila, $id);
			}
		}
	}

	/**
	 * Funcion que realiza la comparacion propiamente dicha de los datos
	 * para el control de valores unicos. Esta separacion hace que la relacion entre
	 * los distintos constraints sea un OR.. (antes era un AND)
	 * @ignore
	 */
	protected function  validar_columnas_en_fila($columnas, $fila, $id)
	{
		foreach(array_keys($this->_datos) as $id_fila) {					  //Recorro todos los datos cargados
			 if (! is_null($id) && ($id_fila == $id)) continue;						//Si es la misma fila no se procesa
			 if ($this->_cambios[$id_fila]['estado'] != 'd') {						 //Si la fila no esta marcada para borrado
				$combinacion_existente = true;
				foreach($columnas as $columna) {									//Comparo los valores de las columnas del constraint
					if (isset($fila[$columna])) {
						$combinacion_existente = $combinacion_existente && ($fila[$columna] == $this->_datos[$id_fila][$columna]);
					}else{
						return;										//No existe valor para la columna, el constraint no salta
					}
				}
				if ($combinacion_existente) {
					throw new toba_error_validacion($this->get_txt().": Error de valores repetidos en columna '$columna'");
				}
			}
		}		
	}

	//-------------------------------------------------------------------------------
	//-- VALIDACION global
	//-------------------------------------------------------------------------------

	/**
	 * Validacion de toda la tabla necesaria previa a la sincronización
	 */
	function validar($filas=array())
	{
		$this->control_tope_maximo_filas($this->get_cantidad_filas());		
		$ids = $this->get_id_filas_a_sincronizar( array("u","i") );
		if(isset($ids)){
			foreach($ids as $id){
				if(count($filas)>0) {
					if (!in_array($id,$filas)) continue;	
				}
				//$this->control_nulos($fila);
				$this->evt__validar_fila( $this->_datos[$id] );
				$this->control_valores_unicos_fila($this->_datos[$id], $id);
			}
		}
		$this->control_tope_minimo_filas();
	}
	
	/**
	 * Ventana para hacer validaciones particulares previo a la sincronización
	 * El proceso puede ser abortado con un toba_error, el mensaje se muestra al usuario
	 * @param array $fila Asociativo clave-valor de la fila a validar
	 * 
	 * @ventana
	 */
	function evt__validar_fila($fila){}

	/*
		Controles previos a la sincronizacion
		Esto va a aca o en el AP??
	*/
/*
	private function control_nulos($fila)
	//Controla que un registro posea los valores OBLIGATORIOS
	{
		$mensaje_usuario = "El elemento posee valores incompletos";
		$mensaje_programador = $this->get_txt() . " Es necesario especificar un valor para el campo: ";
		if(isset($this->_campos_no_nulo)){
			foreach($this->_campos_no_nulo as $campo){
				if(isset($fila[$campo])){
					if((trim($fila[$campo])=="")||(trim($fila[$campo])=='NULL')){
						toba::logger()->error($mensaje_programador . $campo);
						throw new toba_error($mensaje_usuario . " ('$campo' se encuentra vacio)");
					}
				}else{
						toba::logger()->error($mensaje_programador . $campo);
						throw new toba_error($mensaje_usuario . " ('$campo' se encuentra vacio)");
				}
			}
		}
	}
*/
	/**
	 * Valida que la cantidad de filas supere el mínimo establecido
	 */
	protected function control_tope_minimo_filas()
	{
		if ($this->_tope_min_filas != 0 && $this->get_cantidad_filas() < $this->_tope_min_filas) {
				throw new toba_error_validacion("La tabla <em>{$this->_id_en_controlador}</em> requiere ingresar al menos {$this->_tope_min_filas} registro/s (se encontraron
				sólo {$this->get_cantidad_filas()}).");
		}
	}

	/**
	 * Valida que la cantidad de filas a crear no supere el maximo establecido
	 */	
	protected function control_tope_maximo_filas($cantidad)
	{
		if (($this->_tope_max_filas != 0) && ($cantidad > $this->_tope_max_filas)) {
			throw new toba_error_validacion("No está permitido ingresar más de {$this->_tope_max_filas} registros
									en la tabla <em>{$this->_id_en_controlador}</em> (se encontraron $cantidad).");
		}
	}
	

	//-------------------------------------------------------------------------------
	//-- PERSISTENCIA  -------------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Retorna el admin. de persistencia que asiste a este objeto durante la sincronización
	 * @return toba_ap_tabla_db
	 */
	function persistidor()
	{
		if(!isset($this->_persistidor)){
			if($this->_info_estructura['ap']=='0'){
				$clase = $this->_info_estructura['ap_sub_clase'];
				$include = $this->_info_estructura['ap_sub_clase_archivo'];
				if( (trim($clase) == '' ) ){
					throw new toba_error_def( $this->get_txt() . "Error en la definicion, falta definir la subclase");
				}
			}else{
				$clase = 'toba_'.$this->_info_estructura['ap_clase'];
				$include = $this->_info_estructura['ap_clase_archivo'];
			}
			if( ! class_exists($clase) ) {
				$punto = toba::puntos_montaje()->get_por_id($this->_info_estructura['punto_montaje']);
				$path  = $punto->get_path_absoluto().'/'.$include;
				require_once($path);
			}
			$this->_persistidor = new $clase( $this );
			if($this->_info_estructura['ap_modificar_claves']){
				$this->_persistidor->activar_modificacion_clave();
			}
		}
		return $this->_persistidor;
	}
	
	/**
	 * @deprecated  Usar persistidor() a secas
	 */
	function get_persistidor()
	{
		return $this->persistidor();
	}
	


	/**
	 * Carga la tabla restringiendo POR valores especificos de campos
	 * Si los datos contienen una unica fila, esta se pone como cursor de la tabla
	 */
	function cargar($clave=array())
	{
		return $this->persistidor()->cargar_por_clave($clave);
	}
	
	/**
	 * La tabla esta cargada con datos?
	 * @return boolean
	 */
	function esta_cargada()
	{
		return $this->_cargada;
	}

	/**
	 * Carga la tabla en memoria con un nuevo set de datos (se borra todo estado anterior)
	 * Si los datos contienen una unica fila, esta se pone como cursor de la tabla
	 * @param array $datos en formato RecordSet
	 */
	function cargar_con_datos($datos)
	{
		$this->log("Carga de datos  FILAS: " . count($datos));
		$this->_datos = null;
		//Controlo que no se haya excedido el tope de registros		
		$this->control_tope_maximo_filas(count($datos));
		
		$this->_datos = $datos;		

		//Genero la estructura de control de cambios
		$this->generar_estructura_cambios();
		//Actualizo la posicion en que hay que incorporar al proximo registro
		$this->_proxima_fila = count($this->_datos);
		//Marco la tabla como cargada
		$this->_cargada = true;
		//Si es una unica fila se pone como cursor de la tabla
		if (count($datos) == 1 && $this->_es_unico_registro) {
			$this->_cursor = 0;
		}
		//Disparo la actulizacion de los mapeos con las tablas padres
		$this->notificar_padres_carga();
	}

	/**
	 * Agrega a la tabla en memoria un nuevo set de datos (conservando el estado anterior). 
	 * Se asume que el set de datos llega desde el mecanismo de persistencia.
	 * 
	 * @param array $datos en formato RecordSet
	 * @param boolean $usar_cursores Los datos cargados se marcan como hijos de los cursores actuales en las tablas padre, sino son hijos del padre que tenia en la base 
	 * @return array Ids. internos de los datos anexados
	 */
	function anexar_datos($datos, $usar_cursores=true)
	{
		$this->log("Anexado de datos  FILAS: " . count($datos) );
		//Controlo que no se haya excedido el tope de registros
		$this->control_tope_maximo_filas(count($this->get_id_filas(false)) + count($datos));

		//Agrego las filas
		$hijos = array();
		foreach( $datos as $fila ) {
			$this->_datos[$this->_proxima_fila] = $fila;
			if ($usar_cursores) {
				//Se notifica a las relaciones a los padres.
				foreach ($this->_relaciones_con_padres as $padre => $relacion) {
					$this->log("Anexado de datos: SET mapeo $padre");
					$relacion->asociar_fila_con_padre($this->_proxima_fila, null);
	            }
			}
			$hijos[] = $this->_proxima_fila;
			$this->_proxima_fila++;            
		}
		$this->regenerar_estructura_cambios($hijos);
		//Marco la tabla como cargada
		$this->_cargada = true;
		if (! $usar_cursores) {
			//Disparo la actulizacion de los mapeos con las tablas padres
			$this->log("Mapear cursores [". implode(',',$hijos) ."] a padres.");
			$this->notificar_padres_carga($hijos);
		}
		return $hijos;
	}
		
	/**
	 * Sincroniza la tabla en memoria con el medio físico a travéz del administrador de persistencia.
	 *
	 * @return integer Cantidad de registros modificados en el medio
	 */
	function sincronizar($usar_cursores=false)
	{
		if($usar_cursores) {
			$filas = $this->get_id_filas_filtradas_por_cursor(false);
			if($filas) {	// Si los cursores no filtran registros, no sincronizo nada
				$this->validar($filas);
				$modif = $this->persistidor()->sincronizar($filas);
				//Regenero la estructura que mantiene los cambios realizados
				$this->notificar_fin_sincronizacion($filas);				
			}
		} else {
			$this->validar();
			$modif = $this->persistidor()->sincronizar();
			//Regenero la estructura que mantiene los cambios realizados
			$this->notificar_fin_sincronizacion();
		}
		return $modif;
	}

	/**
	 * Sincroniza un conjunto de filas de la tabla en memoria con el medio físico a travéz del administrador de persistencia.
	 *
	 * @return integer Cantidad de registros modificados en el medio
	 */
	function sincronizar_filas($filas)
	{
		$this->validar($filas);
		$modif = $this->persistidor()->sincronizar($filas);
		$this->notificar_fin_sincronizacion($filas);
		return $modif;
	}


	/**
	 * Elimina todas las filas de la tabla en memoria y sincroniza con el medio de persistencia
	 */
	function eliminar_todo()
	{
		//Me elimino a mi
		$this->eliminar_filas(false);
		//Sincronizo con la base
		$this->persistidor()->sincronizar_eliminados();
		$this->resetear();
	}
	
	/**
	 * @deprecated Desde 0.8.4, usar eliminar_todo()
	 */
	function eliminar()
	{
		toba::logger()->obsoleto(__CLASS__, __METHOD__, "0.8.4", "Usar eliminar_todo");
		$this->eliminar_todo();	
	}

	/**
	 * Deja la tabla sin carga alguna, se pierden todos los cambios realizados desde la carga
	 */
	function resetear()
	{
		$this->log("RESET!!");
		$this->_datos = array();
		$this->_cargada = false;
		$this->_cambios = array();
		$this->_proxima_fila = 0;
		$this->_where = null;
		$this->_from = null;
		//-- Borra los temporales creados
		foreach (array_keys($this->_blobs) as $fila) {
			foreach (array_keys($this->_blobs[$fila]) as $campo) {
				if (isset($this->_blobs[$fila][$campo]['path']) && $this->_blobs[$fila][$campo]['path'] != '') {
					$path = $this->_blobs[$fila][$campo]['path'];
					if (file_exists($path)) {
						unlink($path);
					}
				}
			}
		}
		$this->_blobs = array();
		foreach ($this->_relaciones_con_hijos as $rel_hijo) {
			$rel_hijo->resetear();	
		}
		$this->resetear_cursor();
	}

	//-------------------------------------------------------------------------------
	//-- Comunicacion con el Administrador de Persistencia
	//-------------------------------------------------------------------------------

	/*--- Del AP a mi ---*/

	/**
	 * El AP avisa que terminó la sincronización
	 * @ignore 
	 */
	function notificar_fin_sincronizacion($filas=array())
	{
		$this->regenerar_estructura_cambios($filas);
	}

	/*--- De mi al AP ---*/

	/**
	 * @ignore 
	 */
	function get_conjunto_datos_interno()
	{
		return $this->_datos;
	}

	/**
	 * Retorna la estructura interna que mantiene registro de las modificaciones/altas/bajas producidas en memoria
	 * @ignore 
	 */
	function get_cambios()
	{
		return $this->_cambios;	
	}

	/**
	 * Retorna el nombre de las columnas de esta tabla
	 */
	function get_columnas()
	{
		return $this->_columnas;
	}
	
	/**
	 * Retorna el nombre de la {@link toba_fuente_datos fuente de datos} utilizado por este componente
	 * @return string
	 */
	function get_fuente()
	{
		return $this->_info["fuente"];
	}

	/**
	 * Nombre de la tabla que se representa en memoria
	 */
	function get_tabla()
	{
		return $this->_info_estructura['tabla'];
	}

	/**
	 * Devuelve el nombre de la tabla extendida
	 */
	function get_tabla_extendida()
	{
		return $this->_info_estructura['tabla_ext'];
	}

	/**
	 * Retorna el schema de BD sobre el que trabaja el datos_tabla
	 * @return string 
	 */
	function get_schema()
	{
		if (isset($this->_info_estructura['esquema'])) {
			return $this->_info_estructura['esquema'];
		}
		return null;
	}	
	
	function get_schema_ext()
	{
		if (isset($this->_info_estructura['esquema_ext'])) {
			return $this->_info_estructura['esquema_ext'];
		}
		return null;		
	}
	
	
	/**
	 * Retorna el alias utilizado para desambiguar la tabla en uniones tales como JOINs
	 * Se toma el primero seteado de: el alias definido, el rol en la relación o el nombre de la tabla
	 * @return string
	 */
	function get_alias()
	{
		if (isset($this->_info_estructura['alias'])) {
			return $this->_info_estructura['alias'];	
		} elseif (isset($this->_id_en_controlador)) {
			return $this->_id_en_controlador;
		} else {
			return $this->get_tabla();
		}
	}

	/**
	 * La tabla posee alguna columna marcada como de 'carga externa'
	 * Una columna externa no participa en la sincronización posterior, pero por necesidades casi siempre estáticas
	 * necesitan mantenerse junto al conjunto de datos.
	 * @return boolean
	 */
	function posee_columnas_externas()
	{
		return $this->_posee_columnas_ext;
	}

	//-------------------------------------------------------------------------------
	//-- Manejo de la estructura de cambios
	//-------------------------------------------------------------------------------

	/**
	 * @ignore 
	 */
	protected function generar_estructura_cambios($limpiar=true, $indice_datos=null)
	{
		//Genero la estructura de control
		if ($limpiar) {
			$this->_cambios = array();
		}
		if (! isset($indice_datos)) {
			$indice_datos = array_keys($this->_datos);
		}
		foreach($indice_datos as $dato){
			$this->_cambios[$dato]['estado']	= "db";
			$this->_cambios[$dato]['clave']		= $this->get_clave_valor($dato);
			$this->_cambios[$dato]['original']	= $this->_datos[$dato];
		}
	}
	
	/**
	 * @ignore 
	 */
	protected function regenerar_estructura_cambios($filas=array())
	{
		//BORRO los datos eliminados
		if(!$filas) {
			foreach(array_keys($this->_cambios) as $cambio){
				if($this->_cambios[$cambio]['estado']=='d'){
					unset($this->_datos[$cambio]);
				}
			}
			$this->generar_estructura_cambios();
		} else {
			$this->generar_estructura_cambios(false, $filas);
		}
	}

	/**
	* Determina que todas las filas de la tabla son nuevas
	* @param boolean $usar_cursores Si esta seteado, solo se marcan como nuevas las filas marcadas por el cursor
	*	
	*/
	function forzar_insercion($usar_cursores=false, $filas=null)
	{
		if($usar_cursores) {
			$filas = $this->get_id_filas_filtradas_por_cursor();
		} else {
			if (!isset($filas)) {
				$filas = array_keys($this->_cambios);
			}
		}
		foreach( $filas as $fila) {
			$this->log("FORZAR INSERT en FILA: $fila");
			$this->registrar_cambio($fila, "i");
		}
	}

	/**
	 * Fuerza una cambio directo a la estructura interna que mantiene registro de los cambios
	 * @param mixed $fila Id. interno de la fila
	 * @param string $estado
	 */
	protected function registrar_cambio($fila, $estado)
	{
		$this->_cambios[$fila]['estado'] = $estado;
	}
	
	/**
	 * Determina si los datos cargados en la tabla difieren de los datos existentes en la base al inicio de la transacción
	 * @return boolean
	 */	
	function hay_cambios()
	{
		if ($this->get_cantidad_filas_a_sincronizar() > 0) {
			$altas_bajas = $this->get_id_filas_a_sincronizar(array('d','i'));
			if (! empty($altas_bajas)) {
				return true;
			}
			foreach ($this->get_id_filas_a_sincronizar(array('u')) as $id_fila) {
				if ($this->hay_cambios_fila($id_fila)) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Retorna verdadero si algún valor de la tabla cambio desde el inicio de la transacción
	 * @return boolean
	 */
	function hay_cambios_fila($id_fila)
	{
		$cambios = $this->get_cambios_fila($id_fila);
		return ! empty($cambios);
	}

	/**
	 * Calcula las diferencias entre el valor original de la fila al momento de carga y el valor actual
	 * @return array Asociativo campo => array('anterior' => $anterior, 'actual' => $actual)
	 */
	function get_cambios_fila($id_fila, $datos_ap = array())
	{
		$diferencias = array();
		
		// Se chequea que exista el indice original, por si se invoca en un alta.
		if (isset($this->_cambios[$id_fila]['original'])) {
			$datos_viejos = $this->_cambios[$id_fila]['original'];
		} else {
			$datos_viejos = array();
		}
		
		if (empty($datos_ap)) {												//Si el administrador de persistencia no pide comparacion especifica
				$datos_nuevos = $this->_datos[$id_fila];	//utilizo los datos internos del datos_tabla.
		} else {
				$datos_nuevos = $datos_ap;
		}
		foreach ($this->_columnas as $campo => $col) {
			if (!$col['externa']) {
				if ($col['tipo'] != 'B') {
					if (! isset($datos_nuevos[$campo]) && isset($datos_viejos[$campo])) {
						$diferencias[$campo] = array('anterior' => $datos_viejos[$campo], 'actual' => null);
					} elseif (isset($datos_nuevos[$campo]) && !isset($datos_viejos[$campo])) {
						$diferencias[$campo] = array('anterior' => null, 'actual' => $datos_nuevos[$campo]);
					} elseif (! isset($datos_nuevos[$campo]) && ! isset($datos_viejos[$campo])) {
						//No hace nada
					} else {
						if (is_bool($datos_viejos[$campo])) {
							$datos_viejos[$campo] = $datos_viejos[$campo] ? 1 : 0;
						}
						if (is_bool($datos_nuevos[$campo])) {
							$datos_nuevos[$campo] = $datos_nuevos[$campo] ? 1 : 0;
						}
						//--- Comparacion por igualdad estricta con un cast a string
						if ($this->persistidor()->get_usar_trim()) {
							$modificar =  (trim((string) $datos_viejos[$campo]) !== trim((string) $datos_nuevos[$campo]));							
						} else {
							$modificar =  (((string) $datos_viejos[$campo]) !== ((string) $datos_nuevos[$campo]));
						}
						if ($modificar) {
							$diferencias[$campo] = array('anterior' => $datos_viejos[$campo], 'actual' => $datos_nuevos[$campo]);
						}
					}
				} else {
					//Es binario, se modifico? En ese caso no decimos las diferencias 
					if (isset($this->_blobs[$id_fila][$campo]) && $this->_blobs[$id_fila][$campo]['modificado']) {
						$diferencias[$campo] = array('anterior' => '?', 'actual' => '?');
					}
				}
			}
		}
		return $diferencias;
	}



	/**
	 * Verifica si hubo cambios en los valores de un campo especifico
	 * @param string $campo Nombre de la columna a comparar
	 * @param integer $id_viejos Identificador de la fila con los datos viejos
	 * @param array $datos_nuevos Arreglo con los datos nuevos
	 * @return boolean
	 */
	function es_campo_modificado($campo, $id_viejos, $datos_nuevos)
	{
		if (! isset($this->_columnas[$campo])) {
			return false;
		}
		if (isset($this->_datos[$id_viejos][$campo])) {
			$viejo = $this->_datos[$id_viejos][$campo];
			if (is_bool($viejo)) {
				$viejo = $viejo ? 1 : 0;
			}
			switch ($this->_columnas[$campo]['tipo']) {
				case 'N':
					//--- Comparacion por igualdad estricta con un cast a Float en caso de Tipo Numero
					$modificar = (float)$viejo !== (float)$datos_nuevos[$campo];
				break;
			  default:
					//--- Comparacion por igualdad estricta con un cast a string
					if ($this->persistidor()->get_usar_trim()) {
						$modificar = (trim((string) $viejo) !== trim((string) $datos_nuevos[$campo]));
					} else  {
						$modificar = (((string) $viejo) !== ((string) $datos_nuevos[$campo]));
					}
			}
		} else {
			//--- Si antes era null, se modifica si ahora no es null! (y si es una columna valida)
			$modificar = isset($this->_columnas[$campo]) && isset($datos_nuevos[$campo]);
		}
		return $modificar;
	}

	/**
	 * Agrega en un nodo xml los datos del registro seleccinado en la tabla por el cursor, como atributos del nodo
	 * @param SimpleXMLElement $xml El objeto nodo xml al que se le van a agregar los atributos
	 */
	function get_xml($xml)
	{
		// Recupera los datos del registro marcado por el cursor
		$datos = $this->get();
		
		// Para cada columna, la agrega como atributo del nodo
		foreach($datos as $clave => $valor){
			$xml->addAttribute($clave,utf8_encode($valor));
		}
	}

}
?>