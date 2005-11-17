<?
require_once("nucleo/browser/clases/objeto.php");
require_once("tipo_datos.php");

/**
 * Representa una estructura tipo tabla o RecordSet en memoria
 *
 * Utiliza un administrador de persistencia para obtener y sincronizar los datos con un medio de persistencia.
 * Una vez en memoria existen primitivas para trabajar sobre estos datos.
 * Los datos y sus modificaciones son mantenidos automáticamente en sesión entre los distintos pedidos de página.
 * Una vez terminada la edición se hace la sincronización con el medio de persistencia marcando el final de la 
 * transacción de negocios.
 *
 * @package Objetos
 * @subpackage Persistencia
 * @todo Control de FK y PK
 */
class objeto_datos_tabla extends objeto
{
	protected $persistidor;						// Mantiene el persistidor del OBJETO
	// Definicion asociada a la TABLA
	protected $clave;							// Columnas que constituyen la clave de la tabla
	protected $columnas;
	protected $posee_columnas_ext = false;		// Indica si la tabla posee columnas externas (cargadas a travez de un mecanismo especial)
	//Constraints
	protected $no_duplicado;					// Combinaciones de columnas que no pueden duplicarse
	// Definicion general
	protected $tope_max_filas;					// Cantidad de maxima de datos permitida.
	protected $tope_min_filas;					// Cantidad de minima de datos permitida.
	// ESTADO
	protected $cambios = array();				// Cambios realizados sobre los datos
	protected $datos = array();					// Datos cargados en el db_filas
	protected $datos_originales = array();		// Datos tal cual salieron de la DB (Control de SINCRO)
	protected $proxima_fila = 0;				// Posicion del proximo registro en el array de datos
	protected $fila_actual = 0;					// Fila marcada como 'actual'
	// Relaciones con el exterior
	protected $contenedor = null;				// Referencia al datos_relacion del cual forma parte, si aplica.
	protected $relaciones_con_padres;			// ARRAY con un objeto RELACION por cada PADRE de la tabla
	protected $relaciones_con_hijos;			// ARRAY con un objeto RELACION por cada HIJO de la tabla
			
	function __construct($id)
	{
		parent::objeto($id);
		for($a=0; $a<count($this->info_columnas);$a++){
			//Armo una propiedad "columnas" para acceder a la definicion mas facil
			$this->columnas[ $this->info_columnas[$a]['columna'] ] =& $this->info_columnas[$a];
			if($this->info_columnas[$a]['pk']==1){
				$this->clave[] = $this->info_columnas[$a]['columna'];
			}
			if($this->info_columnas[$a]['externa']==1){
				$this->posee_columnas_ext = true;
			}
		}
		$this->recuperar_estado_sesion();		
	}

	function destruir()
	{
		$this->guardar_estado_sesion();		
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "cambios";
		$propiedades[] = "datos";
		$propiedades[] = "proxima_fila";
		$propiedades[] = "fila_actual";
		return $propiedades;
	}

	function elemento_toba()
	{
		require_once('api/elemento_objeto_datos_tabla.php');
		return new elemento_objeto_datos_tabla();
	}

	function obtener_definicion_db()
	{
		$sql = parent::obtener_definicion_db();
		//------------- Info base de la estructura ----------------
		$sql["info_estructura"]["sql"] = "SELECT	dt.tabla          	as tabla,
													dt.alias          	as alias,
													dt.min_registros  	as min_registros,
													dt.max_registros  	as max_registros,
													dt.ap				as ap			,	
													dt.ap_clase			as ap_sub_clase	,	
													dt.ap_archivo	    as ap_sub_clase_archivo,
													dt.modificar_claves as ap_modificar_claves,
													ap.clase			as ap_clase,
													ap.archivo			as ap_clase_archivo
					 FROM		apex_objeto_db_registros as dt
				 				LEFT OUTER JOIN apex_admin_persistencia ap ON dt.ap = ap.ap
					 WHERE		objeto_proyecto='".$this->id[0]."'	
					 AND		objeto='".$this->id[1]."';";
		$sql["info_estructura"]["estricto"]="1";
		$sql["info_estructura"]["tipo"]="1";
		//------------ Columnas ----------------
		$sql["info_columnas"]["sql"] = "SELECT	objeto_proyecto,
						objeto 			,	
						col_id			,	
						columna			,	
						tipo			,	
						pk				,	
						secuencia		,
						largo			,	
						no_nulo			,	
						no_nulo_db		,
						externa
					 FROM		apex_objeto_db_registros_col 
					 WHERE		objeto_proyecto = '".$this->id[0]."'
					 AND		objeto = '".$this->id[1]."';";
		$sql["info_columnas"]["tipo"]="x";
		$sql["info_columnas"]["estricto"]="1";		
		return $sql;
	}

	/**
	 * @todo El objeto deberia tener directamente algo asi
	 */
	protected function log($txt)
	{
		toba::get_logger()->debug($this->get_txt() . get_class($this). "' " . $txt);
	}

	//-------------------------------------------------------------------------------
	//--  Relacion con otros ELEMENTOS
	//-------------------------------------------------------------------------------

	function registrar_contenedor($contenedor)
	{
		$this->contenedor = $contenedor;
	}

	function agregar_relacion_con_padre($relacion, $id_padre)
	{
		$this->relaciones_con_padres[] = $relacion;
	}
	
	function agregar_relacion_con_hijo($relacion, $id_hijo)
	{
		$this->relaciones_con_hijos[] = $relacion;
	}

	/*
		***  Notificaciones  ***
	*/

	private function notificar_contenedor($evento, $param1=null, $param2=null)
	{
		if(isset($this->contenedor)){
			$this->contenedor->registrar_evento($this->id, $evento, $param1, $param2);
		}
	}

	/**
	 * Aviso a las relaciones hijas que el componente PADRE se CARGO
	 */
	function notificar_hijos_carga()
	{
		if(isset($this->relaciones_con_hijos)){
			for($a=0;$a<count($this->relaciones_con_hijos);$a++){
				$this->relaciones_con_hijos[$a]->evt__carga_padre();
			}
		}
	}

	/**
	 * Aviso a las relaciones hijas que el componente PADRE se SINCRONIZO
	 */
	function notificar_hijos_sincronizacion()
	{
		if(isset($this->relaciones_con_hijos)){
			for($a=0;$a<count($this->relaciones_con_hijos);$a++){
				$this->relaciones_con_hijos[$a]->evt__sincronizacion_padre();
			}
		}
	}

	/**
	 * Aviso a las relaciones hijas que el componente PADRE se esta por eliminar
	 */
	function notificar_hijos_eliminacion()
	{
		if(isset($this->relaciones_con_hijos)){
			for($a=0;$a<count($this->relaciones_con_hijos);$a++){
				$this->relaciones_con_hijos[$a]->evt__eliminacion_padre();
			}
		}
	}

	/*
		***  Manejo de relaciones  ***
	*/

	function set_padre($id_fila, $id_fila_padre, $id_padre=null)
	{
		
	}

	//-------------------------------------------------------------------------------
	//-- Preguntas BASICAS
	//-------------------------------------------------------------------------------

	/**
	 *	Columnas que son son claves en la tabla
	 */
	function get_clave()
	{
		return $this->clave;
	}
	
	/**
	 * @param mixed $id_fila Id. interno de la fila
	 * @return array Valores de las claves para esta fila, en formato RecordSet
	 */
	function get_clave_valor($id_fila)
	{
		foreach( $this->clave as $columna ){
			$temp[$columna] = $this->get_fila_columna($id_fila, $columna);
		}	
		return $temp;
	}

	function get_tope_max_filas()
	{
		return $this->tope_max_filas;	
	}

	function get_tope_min_filas()
	{
		return $this->tope_min_filas;	
	}

	/**
	 * @return integer Cantidad de filas que sufrieron cambios desde la carga
	 */
	function get_cantidad_filas_a_sincronizar()
	{
		$cantidad = 0;
		foreach(array_keys($this->cambios) as $fila){
			if( ($this->cambios[$fila]['estado'] == "d") ||
				($this->cambios[$fila]['estado'] == "i") ||
				($this->cambios[$fila]['estado'] == "u") ){
				$cantidad++;
			}
		}
		return $cantidad;
	}

	/**
	 * @return array Ids. internos de las filas que sufrieron cambios desde la carga
	 */
	function get_id_filas_a_sincronizar( $cambios=array("d","i","u") )
	{
		$ids = array();
		foreach(array_keys($this->cambios) as $fila){
			if( in_array($this->cambios[$fila]['estado'], $cambios) ){
				$ids[] = $fila;
			}
		}
		return $ids;
	}

	//-------------------------------------------------------------------------------
	//-- Configuracion
	//-------------------------------------------------------------------------------

	function set_tope_max_filas($cantidad)
	{
		if ($cantidad == '')
			$cantidad = 0;		
		if(is_numeric($cantidad) && $cantidad >= 0){
			$this->tope_max_filas = $cantidad;	
		}else{
			throw new excepcion_toba("El valor especificado en el TOPE MAXIMO de registros es incorrecto");
		}
	}

	function set_tope_min_filas($cantidad)
	{
		if ($cantidad == '')
			$cantidad = 0;
		if(is_numeric($cantidad) && $cantidad >= 0){
			$this->tope_min_filas = $cantidad;
		}else{
			throw new excepcion_toba("El valor especificado en el TOPE MINIMO de registros es incorrecto");
		}
	}

	/**
	 * Indica una combinacion de columnas que no debe duplicarse
	 */
	function set_no_duplicado( $columnas )
	{
		$this->no_duplicado[] = $columnas;
	}

	//-------------------------------------------------------------------------------
	//-- ACCESO a FILAS   -----------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	*	@param array Las condiciones permiten filtrar la lista de registros que se devuelves
	*	@param boolean Hace que las claves del array devuelto sean las claves internas del dbr
	*	@param array Formato tipo RecordSet
	*/
	function get_filas($condiciones=null, $usar_id_fila=false)
	{
		$datos = array();
		$a = 0;
		foreach( $this->get_id_fila_condicion($condiciones) as $id_fila )
		{
			if($usar_id_fila){
				$datos[$id_fila] = $this->datos[$id_fila];
			}else{
				$datos[$a] = $this->datos[$id_fila];
				//esta columna indica cual fue la clave del registro
				$datos[$a][apex_datos_clave_fila] = $id_fila;
			}
			$a++;
		}
		return $datos;
	}
	//-------------------------------------------------------------------------------

	/**
	 * Busca los registros en memoria que cumplen una condicion.
	 * Solo se chequea la condicion de igualdad.
	 * No se chequean tipos
	 * @param array $condiciones Asociativo de campo => valor.
	 * @return array Ids. internos de las filas
	 */	
	function get_id_fila_condicion($condiciones=null)
	{	
		$coincidencias = array();
		if(!isset($condiciones)){
			foreach(array_keys($this->cambios) as $id_fila){
				if($this->cambios[$id_fila]['estado']!="d"){
					$coincidencias[] = $id_fila;
				}
			}
		}else{
			//Controlo que todas los campos que se utilizan para el filtrado existan
			foreach( array_keys($condiciones) as $columna){
				if( !isset($this->columnas[$columna]) ){
					throw new excepcion_toba("El campo '$columna' no existe. No es posible filtrar por dicho campo");
				}
			}
			//Busco coincidencias
			foreach(array_keys($this->cambios) as $id_fila){
				if($this->cambios[$id_fila]['estado']!="d"){	// Excluir los eliminados
					//Verifico las condiciones
					$ok = true;
					foreach( array_keys($condiciones) as $campo){
						if( $condiciones[$campo] != $this->datos[$id_fila][$campo] ){
							$ok = false;
							break;	
						}
					}
					if( $ok ) $coincidencias[] = $id_fila;
				}
			}
		}
		return $coincidencias;
	}
	//-------------------------------------------------------------------------------

	/**
	 * @param mixed $id Id. interno de la fila en memoria
	 */
	function get_fila($id)
	{
		$id = $this->normalizar_id($id);
		if(isset($this->datos[$id])){
			$temp = $this->datos[$id];
			$temp[apex_datos_clave_fila] = $id;	//incorporo el ID del dbr
			return $temp;
		}else{
			return null;
			//throw new excepcion_toba("Se solicito un registro incorrecto");
		}
	}
	//-------------------------------------------------------------------------------

	/**
	 * Retorna el valor de una columna en una fila dada
	 * @param mixed $id Id. interno de la fila
	 */
	function get_fila_columna($id, $columna)
	{
		$id = $this->normalizar_id($id);
		if(isset($this->datos[$id][$columna])){
			return  $this->datos[$id][$columna];
		}else{
			return null;
		}
	}
	//-------------------------------------------------------------------------------

	/**
	 * Retorna los valores de una columna específica
	 * @param string $columna Nombre del campo o columna
	 * @return array
	 */
	function get_valores_columna($columna)
	//Retorna una columna de valores
	{
		$temp = array();
		foreach(array_keys($this->cambios) as $fila){
			if($this->cambios[$fila]['estado']!="d"){
				$temp[] = $this->datos[$fila][$columna];
			}
		}
		return $temp;
	}
	//-------------------------------------------------------------------------------
	
	/**
	 * Cantidad de filas que tiene la tabla en memoria
	 */
	function get_cantidad_filas()
	{
		$a = 0;
		foreach(array_keys($this->cambios) as $id_fila){
			if($this->cambios[$id_fila]['estado']!="d")	$a++;
		}
		return $a;
	}
	
	/**
	 * @param mixed $id Id. interno de la fila
	 */
	function existe_fila($id)
	{
		$id = $this->normalizar_id($id);
		if(! isset($this->datos[$id]) ){
			return false;			
		}
		if($this->cambios[$id]['estado']=="d"){
			return false;
		}
		return true;
	}

	/**
	 * Valida un id interno y a la vez permite aceptarlo como parte de un arreglo en
	 * la columna apex_datos_clave_fila
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
		throw new excepcion_toba($this->get_txt() . ' La clave tiene un formato incorrecto.');
	}

	//-------------------------------------------------------------------------------
	//-- ALTERACION de FILAS  ------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Crea una nueva fila en memoria
	 *
	 * @param array $fila Asociativo campo->valor a insertar
	 * @return mixed Id. interno de la fila creada
	 * @todo Aceptar el parametro padre y machearlo en la relación
	 */
	function nueva_fila($fila, $padre=null)
	{
		if( $this->tope_max_filas != 0){
			if( !($this->get_cantidad_filas() < $this->tope_max_filas) ){
				$info = 'filas: ' . $this->get_cantidad_filas() . ' tope: ' . $this->tope_max_filas;
				throw new excepcion_toba("No es posible agregar FILAS (TOPE MAX.) $info");
			}
		}
		$this->notificar_contenedor("ins", $fila);
		//Saco el campo que indica la posicion del registro
		if(isset($fila[apex_datos_clave_fila])) unset($fila[apex_datos_clave_fila]);
		$this->validar_fila($fila);
		//SI existen columnas externas, completo la fila con las mismas
		if($this->posee_columnas_ext){
			$campos_externos = $this->get_persistidor()->completar_campos_externos_fila($fila,"ins");
			foreach($campos_externos as $id => $valor){
				$fila[$id] = $valor;
			}
		}
		$this->datos[$this->proxima_fila] = $fila;
		$this->registrar_cambio($this->proxima_fila,"i");
		$id = $this->proxima_fila++;
		//Si hay un padre, aviso a la relacion que tiene que crear un macheo
		if(isset($padre)){
				
		}
		return $id;
	}
	//-------------------------------------------------------------------------------

	/**
	 * Modifica una fila del tabla en memoria
	 *
	 * @param mixed $id Id. interno de la fila a modificar
	 * @param array $fila Contenido de la fila, puede ser incompleto
	 * @return mixed Id. interno de la fila modificada
	 */
	function modificar_fila($id, $fila)
	{
		$id = $this->normalizar_id($id);
		if(!$this->existe_fila($id)){
			$mensaje = $this->get_txt() . " MODIFICAR. No existe un registro con el INDICE indicado ($id)";
			toba::get_logger()->error($mensaje);
			throw new excepcion_toba($mensaje);
		}
		//Saco el campo que indica la posicion del registro
		if(isset($fila[apex_datos_clave_fila])) unset($fila[apex_datos_clave_fila]);
		$this->validar_fila($fila, $id);
		$this->notificar_contenedor("pre_modificar", $fila, $id);
		/*
			Como los campos externos pueden necesitar una campo que no entrego la
			interface, primero actualizo los valores y despues tomo la fila y la
			proceso con la actualizacion de campos externos
		*/
		//Actualizo los valores
		foreach(array_keys($fila) as $clave){
			$this->datos[$id][$clave] = $fila[$clave];
		}
		if($this->cambios[$id]['estado']!="i"){
			$this->registrar_cambio($id,"u");
		}
		//Si la tabla posee campos externos, le pido la nueva fila al persistidor
		if($this->posee_columnas_ext){
			$campos_externos = $this->get_persistidor()->completar_campos_externos_fila($this->datos[$id],"upd");
			foreach($campos_externos as $clave => $valor){
				$this->datos[$id][$clave] = $valor;
			}
		}
		$this->notificar_contenedor("post_modificar", $fila, $id);
		return $id;
	}
	//-------------------------------------------------------------------------------

	/**
	 * Elimina una fila de la tabla en memoria
	 *
	 * @param mixed $id Id. interno de la fila a eliminar
	 * @return Id. interno de la fila eliminada
	 */
	function eliminar_fila($id)
	{
		$id = $this->normalizar_id($id);
		if(!$this->existe_fila($id)){
			$mensaje = $this->get_txt() . " MODIFICAR. No existe un registro con el INDICE indicado ($id)";
			toba::get_logger()->error($mensaje);
			throw new excepcion_toba($mensaje);
		}
		$this->notificar_contenedor("pre_eliminar", $id);
		if($this->cambios[$id]['estado']=="i"){
			unset($this->cambios[$id]);
			unset($this->datos[$id]);
		}else{
			$this->registrar_cambio($id,"d");
		}
		$this->notificar_contenedor("post_eliminar", $id);
		return $id;
	}
	//-------------------------------------------------------------------------------

	/**
	 * Elimina todas las filas de la tabla en memoria
	 */
	function eliminar_filas()
	{
		foreach(array_keys($this->cambios) as $fila)
		{
			if($this->cambios[$fila]['estado']=="i"){
				unset($this->cambios[$fila]);
				unset($this->datos[$fila]);
			}else{
				if($this->existe_fila($fila)){
					$this->registrar_cambio($fila,"d");
				}
			}
		}
	}
	//-------------------------------------------------------------------------------

	/**
	 * Cambia el valor de una columna de una fila especifica
	 *
	 * @param mixed $id Id. interno de la fila de la tabla en memoria
	 * @param string $columna Columna o campo de la fila
	 */
	function set_fila_columna_valor($id, $columna, $valor)
	{
		$id = $this->normalizar_id($id);
		if( $this->existe_fila($id) ){
			if( isset($this->columnas[$columna]) ){
				$this->datos[$id][$columna] = $valor;
				if($this->cambios[$id]['estado']!="i" && $this->cambios[$id]['estado']!="d"){
					$this->registrar_cambio($id,"u");
				}		
			}else{
				throw new excepcion_toba("La columna '$columna' no es valida");
			}
		}else{
			throw new excepcion_toba("La fila '$id' no es valida");
		}
	}
	//-------------------------------------------------------------------------------

	/**
	 * Cambia el valor de una columna en todas las filas
	 */
	function set_columna_valor($columna, $valor)
	{
		foreach(array_keys($this->cambios) as $fila){
			if($this->cambios[$fila]['estado']!="d"){
				$this->datos[$fila][$columna] = $valor;
				if($this->cambios[$fila]['estado']!="i"){
					$this->registrar_cambio($fila,"u");
				}		
			}
		}
	}
	//-------------------------------------------------------------------------------

	/**
	 * Procesa los cambios masivos de filas
	 * 
	 * Para procesar es necesario indicar el estado
	 * de cada fila utilizando una columna referenciada con la constante 'apex_ei_analisis_fila'
	 *
	 * @param array $filas Filas en formato RecordSet
	 */
	function procesar_filas($filas)
	{
		asercion::es_array($filas,"objeto_datos_tabla - El parametro no es un array.");
		//Controlo estructura
		foreach(array_keys($filas) as $id){
			if(!isset($filas[$id][apex_ei_analisis_fila])){
				throw new excepcion_toba("Para procesar un conjunto de registros es necesario indicar el estado ".
									"de cada uno utilizando una columna referenciada con la constante 'apex_ei_analisis_fila'");
			}
		}
		foreach(array_keys($filas) as $id){
			$accion = $filas[$id][apex_ei_analisis_fila];
			unset($filas[$id][apex_ei_analisis_fila]);
			switch($accion){
				case "A":
					$this->nueva_fila($filas[$id]);
					break;	
				case "B":
					$this->eliminar_fila($id);
					break;	
				case "M":
					$this->modificar_fila($id, $filas[$id]);
					break;	
			}
		}
	}

	//-------------------------------------------------------------------------------
	// Simplificacion para los casos en que se utiliza una sola fila

	/**
	 * Fija un cursor interno en una fila
	 *
	 * @param mixed $id Id. interno de la fila
	 */
	function set_fila_actual($id)
	{
		$id = $this->normalizar_id($id);
		if( $this->existe_fila($id) ){
			$this->fila_actual = $id;	
		}else{
			throw new excepcion_toba($this->get_txt() . "La fila '$id' no es valida");
		}
	}
	
	/**
	 * @return mixed Id. interno de la fila donde se encuentra actualmente el cursor interno
	 */
	function get_fila_actual()
	{
		return $this->fila_actual;	
	}
		
	/**
	 * Cambia el contenido de la fila donde se encuentra el cursor interno
	 * En caso que no existan filas, se crea una nueva
	 *
	 * @param array $fila Contenido total o parcial de la fila
	 */
	function set($fila)
	{
		if($this->get_cantidad_filas() === 0){
			$this->nueva_fila($fila);
		}else{
			$this->modificar_fila($this->fila_actual, $fila);
		}
	}
	
	/**
	 * Retorna el contenido de la fila donde se encuentra posicionado el cursor interno
	 */
	function get()
	{
		return $this->get_fila($this->fila_actual);
	}

	//-------------------------------------------------------------------------------
	//-- VALIDACION en LINEA
	//-------------------------------------------------------------------------------

	/**
	 * Valida un registro durante el procesamiento
	 */
	private function validar_fila($fila, $id=null)
	{
		$this->evt__validar_ingreso($fila, $id);
		$this->control_estructura_fila($fila);
		$this->control_valores_unicos_fila($fila, $id);
	}

	protected function evt__validar_ingreso($fila, $id=null){}

	//-------------------------------------------------------------------------------

	/**
	 * 	Controla que los campos del registro existan
	 */
	protected function control_estructura_fila($fila)

	{
		foreach($fila as $campo => $valor){
			//SI el registro no esta en la lista de manipulables o en las secuencias...
			if( !(isset($this->columnas[$campo]))  ){
				$this->log("El registro tiene una estructura incorrecta: El campo '$campo' ". 
						" no forma parte de la DEFINICION.");
				//toba::get_logger()->debug( debug_backtrace() );
				throw new excepcion_toba("ERROR: La FILA ingresada posee una estructura incorrecta");
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
		if(isset($this->no_duplicado))	
		{	//La iteracion de afuera es por cada constraint, 
			//si hay muchos es ineficiente, pero en teoria hay pocos (en general 1)
			foreach($this->no_duplicado as $columnas){
				foreach(array_keys($this->cambios) as $id_fila)	{
					//a) La operacion es una modificacion y estoy comparando con el registro contra su original
					if( isset($id) && ($id_fila == $id)) continue; //Sigo con el proximo
					//b) Comparo contra otro registro, que no este eliminado
					if($this->cambios[$id_fila]['estado']!="d"){
						$combinacion_existente = true;
						foreach($columnas as $columna)
						{
							if(!isset($fila[$columna])){
								//Si las columnas del constraint no estan completas, fuera
								return;
							}else{
								if($fila[$columna] != $this->datos[$id_fila][$columna]){
									$combinacion_existente = false;
								}
							}
						}
						if($combinacion_existente){
							throw new excepcion_toba("Error de valores repetidos");
						}
					}
				}				
			}
		}
	}

	//-------------------------------------------------------------------------------
	//-- VALIDACION global
	//-------------------------------------------------------------------------------

	/**
	 * Validacion de toda la tabla que se produce previo a la sincronización
	 */
	function validar()
	{
		$ids = $this->get_id_filas_a_sincronizar( array("u","i") );
		if(isset($ids)){
			foreach($ids as $id){
				//$this->control_nulos($fila);
				$this->evt__validar_fila( $this->datos[$id] );
			}
		}
		$this->control_tope_minimo_filas();
	}
	
	/**
	 * Ventana para hacer validaciones particulares previo a la sincronización
	 * El proceso puede ser abortado con un excepcion_toba, el mensaje se muestra al usuario
	 * @param array $fila Asociativo clave-valor de la fila a validar
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
		if(isset($this->campos_no_nulo)){
			foreach($this->campos_no_nulo as $campo){
				if(isset($fila[$campo])){
					if((trim($fila[$campo])=="")||(trim($fila[$campo])=='NULL')){
						toba::get_logger()->error($mensaje_programador . $campo);
						throw new excepcion_toba($mensaje_usuario . " ('$campo' se encuentra vacio)");
					}
				}else{
						toba::get_logger()->error($mensaje_programador . $campo);
						throw new excepcion_toba($mensaje_usuario . " ('$campo' se encuentra vacio)");
				}
			}
		}
	}
*/
	protected function control_tope_minimo_filas()
	{
		$control_tope_minimo=true;
		if($control_tope_minimo){
			if( $this->tope_min_filas != 0){
				if( ( $this->get_cantidad_filas() < $this->tope_min_filas) ){
					$this->log("No se cumplio con el tope minimo de registros necesarios" );
					throw new excepcion_toba("datos_tabla '".$this->info['nombre']."': Los registros cargados no cumplen con el TOPE MINIMO necesario ({$this->tope_min_filas})");
				}
			}
		}
	}

	//-------------------------------------------------------------------------------
	//-- PERSISTENCIA  -------------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Retorna el admin. de persistencia que asiste a este objeto
	 * @return ap_tabla
	 */
	function get_persistidor()
	{
		if(!isset($this->persistidor)){
			if($this->info_estructura['ap']=='0'){
				$include = $this->info_estructura['ap_sub_clase_archivo'];
				$clase = $this->info_estructura['ap_sub_clase'];
				if( (trim($clase) == "") || (trim($include) == "") ){
					throw new excepcion_toba( $this->get_txt() . "Error en la definicion");
				}
			}else{
				$include = $this->info_estructura['ap_clase_archivo'];
				$clase = $this->info_estructura['ap_clase'];
			}
			require_once( $include );
			$this->persistidor = new $clase( $this );
			if($this->info_estructura['ap_modificar_claves']){
				$this->persistidor->activar_modificacion_clave();
			}
		}
		return $this->persistidor;
	}

	/**
	 * Carga la tabla restringiendo POR valores especificos de campos
	 */
	function cargar($clave)
	{
		return $this->get_persistidor()->cargar_por_clave($clave);
	}

	/**
	 * Carga la tabla en memoria con un nuevo set de datos (se borra todo estado anterior)
	 * @param array $datos en formato RecordSet
	 */
	function cargar_con_datos($datos)
	{
		$this->log("Carga de datos");
		$this->datos = $datos;
		//Controlo que no se haya excedido el tope de registros
		if( $this->tope_max_filas != 0){
			if( $this->tope_max_filas < count( $this->datos ) ){
				//Hay mas datos que los que permite el tope, todo mal
				$this->datos = null;
				$this->log("Se sobrepaso el tope maximo de registros en carga: " . count( $this->datos ) . " registros" );
				throw new excepcion_toba("Los registros cargados superan el TOPE MAXIMO de registros");
			}
		}
		if(false){	// Hay que pensar este esquema...
			$this->datos_originales = $this->datos;
		}
		//Genero la estructura de control de cambios
		$this->generar_estructura_cambios();
		//Actualizo la posicion en que hay que incorporar al proximo registro
		$this->proxima_fila = count($this->datos);
		//Disparo la actulizacion con las tablas hijas
		$this->notificar_hijos_carga();
	}
	
	/**
	 * Sincroniza la tabla en memoria con el medio físico a travéz del administrador de persistencia.
	 *
	 * @return integer Cantidad de registros modificados en el medio
	 */
	function sincronizar()
	{
		//Control de topes
		if( $this->tope_min_filas != 0){
			if( ( $this->get_cantidad_filas() < $this->tope_min_filas) ){
				$this->log("No se cumplio con el tope minimo de registros necesarios" );
				throw new excepcion_toba("Los registros cargados no cumplen con el TOPE MINIMO necesario");
			}
		}
		$modif = $this->get_persistidor()->sincronizar();
		return $modif;
	}

	/**
	 * Elimina todas las filas de la tabla en memoria y sincroniza con el medio de persistencia
	 */
	function eliminar()
	{
		//Elimino a mis hijos
		$this->notificar_hijos_eliminacion();
		//Me elimino a mi
		$this->eliminar_filas();
		$this->get_persistidor()->eliminar();
	}

	/**
	 * Deja la tabla sin carga alguna, se pierden todos los cambios realizados
	 */
	function resetear()
	{
		$this->log("RESET!!");
		$this->datos = array();
		$this->datos_originales = array();
		$this->cambios = array();
		$this->proxima_fila = 0;
		$this->where = null;
		$this->from = null;
	}

	//-------------------------------------------------------------------------------
	//-- Comunicacion con el Administrador de Persistencia
	//-------------------------------------------------------------------------------

	/*--- Del AP a mi ---*/

	/**
	 * @deprecated desde 0.8.4, usar cargar_con_datos
	 */
	function set_datos($datos)
	{
		toba::get_logger()->obsoleto(__CLASS__, __FUNCTION__, 'Usar cargar_con_datos');
		return $this->cargar_con_datos($datos);		
	}

	/**
	 * El AP avisa que terminóla sincronización
	 */
	function notificar_fin_sincronizacion()
	{
		$this->regenerar_estructura_cambios();
		$this->notificar_hijos_sincronizacion();
	}

	/*--- De mi al AP ---*/

	function get_conjunto_datos_interno()
	{
		return $this->datos;
	}

	function get_cambios()
	{
		return $this->cambios;	
	}

	function get_datos_originales()
	{
		return $this->datos_originales;
	}

	function get_columnas()
	{
		return $this->columnas;
	}
	
	function get_fuente()
	{
		return $this->info["fuente"];
	}

	/**
	 * Nombre de la tabla que se representa en memoria
	 */
	function get_tabla()
	{
		return $this->info_estructura['tabla'];
	}

	function get_alias()
	{
		return $this->info_estructura['alias'];
	}

	function posee_columnas_externas()
	{
		return $this->posee_columnas_ext;
	}

	//-------------------------------------------------------------------------------
	//-- Manejo de la estructura de cambios
	//-------------------------------------------------------------------------------

	protected function generar_estructura_cambios()
	{
		//Genero la estructura de control
		$this->cambios = array();
		foreach(array_keys($this->datos) as $dato){
			$this->cambios[$dato]['estado']="db";
			$this->cambios[$dato]['clave']= $this->get_clave_valor($dato);
		}
	}
	
	protected function regenerar_estructura_cambios()
	{
		//BORRO los datos eliminados
		foreach(array_keys($this->cambios) as $cambio){
			if($this->cambios[$cambio]['estado']=='d'){
				unset($this->datos[$cambio]);
			}
		}
		$this->generar_estructura_cambios();
	}

	/**
	*	Determina que todas las filas de la tabla son nuevas
	*/
	function forzar_insercion()
	{
		foreach(array_keys($this->cambios) as $fila) {
			$this->registrar_cambio($fila, "i");
		}
	}
	
	protected function registrar_cambio($fila, $estado)
	{
		$this->cambios[$fila]['estado'] = $estado;
	}
	//-------------------------------------------------------------------------------
}
?>