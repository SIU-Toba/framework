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
	protected $nombre_rol;						//Nombre que tiene esta tabla en la relación
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
	protected $cursor;							// Puntero a una fila específica
	protected $cargada = false;
	// Relaciones con el exterior
	protected $contenedor = null;				// Referencia al datos_relacion del cual forma parte, si aplica.
	protected $relaciones_con_padres = array();			// ARRAY con un objeto RELACION por cada PADRE de la tabla
	protected $relaciones_con_hijos = array();			// ARRAY con un objeto RELACION por cada HIJO de la tabla

	
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
		$propiedades[] = "cursor";
		$propiedades[] = "cargada";
		return $propiedades;
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
		$this->relaciones_con_padres[$id_padre] = $relacion;
	}
	
	/**
	 * Retorna las relaciones con las tablas padre
	 * @return relacion_entre_tablas
	 */
	function get_relaciones_con_padres()
	{
		return $this->relaciones_con_padres;
	}
	
	function agregar_relacion_con_hijo($relacion, $id_hijo)
	{
		$this->relaciones_con_hijos[$id_hijo] = $relacion;
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
	 * Aviso a las relaciones padres que el componente HIJO se CARGO
	 */
	function notificar_padres_carga()
	{
		if(isset($this->relaciones_con_padres)){
			foreach ($this->relaciones_con_padres as $relacion) {
				$relacion->evt__carga_hijo();
			}
		}
	}

	/**
	 * Aviso a las relaciones hijas que el componente PADRE sincrozo sus actualizaciones
	 */
	function notificar_hijos_sincronizacion()
	{
		if(isset($this->relaciones_con_hijos)){
			foreach ($this->relaciones_con_hijos as $relacion) {
				$relacion->evt__sincronizacion_padre();
			}
		}
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
	 * Determina el nombre del rol que cumple esta tabla en una relación
	 */
	function set_nombre_rol($nombre)
	{
		$this->nombre_rol = $nombre;	
	}
	
	/**
	 * Indica una combinacion de columnas que no debe duplicarse
	 */
	function set_no_duplicado( $columnas )
	{
		$this->no_duplicado[] = $columnas;
	}

	//-------------------------------------------------------------------------------
	//-- MANEJO DEL CURSOR INTERNO---------------------------------------------------
	//-------------------------------------------------------------------------------
	
	/**
	 * Fija el cursor en una fila dada
	 * @param mixed $id Id. interno de la fila
	 */
	function set_cursor($id)
	{
		$id = $this->normalizar_id($id);
		if( $this->existe_fila($id) ){
			$this->cursor = $id;	
		}else{
			throw new excepcion_toba($this->get_txt() . "La fila '$id' no es valida");
		}
	}	
	
	/**
	 * Asegura que el cursor no se encuentre posicionado en ninguna fila específica
	 */
	function resetear_cursor()
	{
		unset($this->cursor);
	}
	
	/**
	 * @return mixed Id. interno de la fila donde se encuentra actualmente el cursor de la tabla
	 */
	function get_cursor()
	{
		return $this->cursor;	
	}

	/**
	 * Hay una fila seleccionada por el cursor?
	 */
	function hay_cursor()
	{
		return isset($this->cursor);
	}
	
	//-------------------------------------------------------------------------------
	//-- ACCESO a FILAS   -----------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Retorna el conjunto de filas que respeta las condiciones dadas
	 * Este conjunto de filas es afectado por la presencia de cursores en las tablas padres
	 * @param array Las condiciones permiten filtrar la lista de registros que se devuelve
	 * @param boolean Hace que las claves del array devuelto sean las claves internas del dbr
	 * @param boolean $usar_cursores Este conjunto de filas es afectado por la presencia de cursores en las tablas padres
	 * @return array Formato tipo RecordSet
	 */
	function get_filas($condiciones=null, $usar_id_fila=false, $usar_cursores=true)
	{
		$datos = array();
		$a = 0;
		foreach( $this->get_id_fila_condicion($condiciones, $usar_cursores) as $id_fila )
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
	
	/**
	 * Retorna los ids de todas las filas (sin eliminar) de esta tabla
	 * @param boolean $usar_cursores Este conjunto de filas es afectado por la presencia de cursores en las tablas padres
	 * @return array()
	 * @todo Se podría optimizar este método para no recaer en tantos recorridos
	 */
	function get_id_filas($usar_cursores=true)
	{
		$coincidencias = array();
		foreach(array_keys($this->cambios) as $id_fila){
			if($this->cambios[$id_fila]['estado']!="d"){
				$coincidencias[] = $id_fila;
			}
		}
		if ($usar_cursores) {
			//Si algún padre tiene un cursor posicionado, 
			//se restringe a solo las filas que son hijas de esos cursores
			foreach ($this->relaciones_con_padres as $id => $rel_padre) {
				if ($rel_padre->hay_cursor_en_padre()) {
					$coincidencias = array_intersect($coincidencias, $rel_padre->get_id_filas_hijas());
				}
			}
		}
		return $coincidencias;		
	}
	
	/**
	 * Busca los registros en memoria que cumplen una condicion.
	 * Solo se chequea la condicion de igualdad. No se chequean tipos
	 * @param array $condiciones Asociativo de campo => valor.
	 * @param boolean $usar_cursores Este conjunto de filas es afectado por la presencia de cursores en las tablas padres* 
	 * @return array Ids. internos de las filas, pueden no estar numerado correlativamente
	 */	
	function get_id_fila_condicion($condiciones=null, $usar_cursores=true)
	{	
		//En principio las coincidencias son todas las filas
		$coincidencias = $this->get_id_filas($usar_cursores);
		//Si hay condiciones, se filtran estas filas
		if(isset($condiciones)){
			//Controlo que todas los campos que se utilizan para el filtrado existan
			foreach( array_keys($condiciones) as $columna){
				if( !isset($this->columnas[$columna]) ){
					throw new excepcion_toba("El campo '$columna' no existe. No es posible filtrar por dicho campo");
				}
			}
			foreach($coincidencias as $pos => $id_fila){
				//Verifico las condiciones
				foreach( array_keys($condiciones) as $campo){
					if( $condiciones[$campo] != $this->datos[$id_fila][$campo] ){
						//Se filtra la fila porque no cumple las condiciones
						unset($coincidencias[$pos]);
						break;	
					}
				}
			}
		}
		return $coincidencias;
	}

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
	
	/**
	 * Retorna los valores de una columna específica
	 * El conjunto de filas utilizado es afectado por la presencia de cursores en las tablas padres
	 * @param string $columna Nombre del campo o columna
	 * @return array
	 */
	function get_valores_columna($columna)
	//Retorna una columna de valores
	{
		$temp = array();
		foreach($this->get_id_filas() as $fila){
			$temp[] = $this->datos[$fila][$columna];
		}
		return $temp;
	}
	
	/**
	 * Cantidad de filas que tiene la tabla en memoria
	 * El conjunto de filas utilizado es afectado por la presencia de cursores en las tablas padres
	 */
	function get_cantidad_filas()
	{
		return count($this->get_id_filas());
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
	 * @param mixed $ids_padres Asociativo padre =>id de las filas padres de esta nueva, 
	 * 						  en caso de que no se brinde, se utilizan las posiciones actuales de estas tablas padres
	 * @return mixed Id. interno de la fila creada
	 */
	function nueva_fila($fila=array(), $ids_padres=null)
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
			foreach($campos_externos as $id => $valor) {
				$fila[$id] = $valor;
			}
		}
		
		//Se le asigna un id a la fila
		$id_nuevo = $this->proxima_fila;	
		
		//Se notifica a las relaciones del alta
		foreach ($this->relaciones_con_padres as $padre => $relacion) {
			$id_padre = null;
			if (isset($ids_padres[$padre])) {
				$id_padre = $ids_padres[$padre];
			}
			$relacion->asociar_fila_con_padre($id_nuevo, $id_padre);
		}
		
		//Se agrega la fila
		$this->datos[$id_nuevo] = $fila;
		$this->registrar_cambio($id_nuevo,"i");
		$this->proxima_fila++;
		
		return $id_nuevo;
	}

	/**
	 * Modifica una fila de la tabla en memoria
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
		
		//Se actualizan los cambios en la relación
		foreach ($this->relaciones_con_padres as $rel_padre) {
			$rel_padre->evt__modificacion_fila_hijo($id, $this->datos[$id], $fila);
		}
		
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

	/**
	 * Cambia la asociación de una fila con sus padres
	 *
	 * @param mixed $id_fila 
	 * @param array $nuevos_padres Arreglo (id_tabla_padre => $id_fila_padre, ....), solo se cambian los padres que se pasan por parámetros
	 * 				El resto de los padres sigue con la asociación anterior
	 */
	function cambiar_padre_fila($id_fila, $nuevos_padres)
	{
		$id = $this->normalizar_id($id_fila);		
		if (!$this->existe_fila($id)){
			$mensaje = $this->get_txt() . " CAMBIAR PADRE. No existe un registro con el INDICE indicado ($id)";
			toba::get_logger()->error($mensaje);
			throw new excepcion_toba($mensaje);
		}
		foreach ($nuevos_padres as $tabla_padre => $id_padre) {
			if (!isset($this->relaciones_con_padres[$tabla_padre])) {
				$mensaje = $this->get_txt() . " CAMBIAR PADRE. No existe una relación padre $tabla_padre.";
				throw new excepcion_toba($mensaje);
			}
			$this->relaciones_con_padres[$tabla_padre]->cambiar_padre($id_fila, $id_padre);
		}
	}
	
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
			$mensaje = $this->get_txt() . " ELIMINAR. No existe un registro con el INDICE indicado ($id)";
			toba::get_logger()->error($mensaje);
			throw new excepcion_toba($mensaje);
		}
		$this->notificar_contenedor("pre_eliminar", $id);
		//Se notifica la eliminación a las relaciones
		foreach ($this->relaciones_con_hijos as $rel) {
			$rel->evt__eliminacion_fila_padre($id);
		}
		foreach ( $this->relaciones_con_padres as $rel) {
			$rel->evt__eliminacion_fila_hijo($id);			
		}
		if($this->cambios[$id]['estado']=="i"){
			unset($this->cambios[$id]);
			unset($this->datos[$id]);
		}else{
			$this->registrar_cambio($id,"d");
		}
		$this->notificar_contenedor("post_eliminar", $id);
		return $id;
	}

	/**
	 * Elimina todas las filas de la tabla en memoria, sin tener en cuenta los cursores de los padres
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
	//-- Simplificación sobre una sola línea
	//-------------------------------------------------------------------------------

	/**
	 * Cambia el contenido de la fila donde se encuentra el cursor interno
	 * En caso que no existan filas, se crea una nueva y se posiciona el cursor en ella
	 *
	 * @param array $fila Contenido total o parcial de la fila
	 */
	function set($fila)
	{
		if($this->hay_cursor()){
			$this->modificar_fila($this->get_cursor(), $fila);
		} else {
			$id = $this->nueva_fila($fila);
			$this->set_cursor($id);
		}
	}
	
	/**
	 * Retorna el contenido de la fila donde se encuentra posicionado el cursor interno
	 * En caso de que no haya registros retorna NULL
	 */
	function get()
	{
		if ($this->get_cantidad_filas() == 0) {
			return null;
		} elseif ($this->hay_cursor()) {
			return $this->get_fila($this->get_cursor());
		} else {
			throw new excepcion_toba("No hay posicionado un cursor en la tabla, no es posible determinar la fila actual");
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
				$mensaje = $this->get_txt() . get_class($this)." El registro tiene una estructura incorrecta: El campo '$campo' ". 
						" no forma parte de la DEFINICION.";
				toba::get_logger()->warning($mensaje);
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
	
	function esta_cargada()
	{
		return $this->cargada;
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
		//Marco la tabla como cargada
		$this->cargada = true;
		//Si es una unica fila se pone como cursor de la tabla
		if (count($datos) == 1) {
			$this->cursor = 0;
		}
		//Disparo la actulizacion de los mapeos con las tablas padres
		$this->notificar_padres_carga();
	}
	
	/**
	 * Sincroniza la tabla en memoria con el medio físico a travéz del administrador de persistencia.
	 *
	 * @return integer Cantidad de registros modificados en el medio
	 */
	function sincronizar()
	{
		$this->validar();
		$modif = $this->get_persistidor()->sincronizar();
		return $modif;
	}

	/**
	 * Elimina todas las filas de la tabla en memoria y sincroniza con el medio de persistencia
	 */
	function eliminar_todo()
	{
		//Me elimino a mi
		$this->eliminar_filas();
		//Sincronizo con la base
		$this->get_persistidor()->sincronizar_eliminados();
		$this->resetear();
	}
	
	/**
	 * @deprecated Desde 0.8.4, usar eliminar_todo()
	 */
	function eliminar()
	{
		toba::get_logger()->obsoleto(__CLASS__, __METHOD__, "0.8.4", "Usar eliminar_todo");
		$this->eliminar_todo();	
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
		foreach ($this->relaciones_con_hijos as $rel_hijo) {
			$rel_hijo->resetear();	
		}
		$this->resetear_cursor();
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

	/**
	 * Se toma el primero seteado de: el alias definido, el rol en la relación o el nombre de la tabla
	 */
	function get_alias()
	{
		if (isset($this->info_estructura['alias'])) {
			return $this->info_estructura['alias'];	
		} elseif (isset($this->nombre_rol)) {
			return $this->nombre_rol;
		} else {
			return $this->get_tabla();
		}
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
