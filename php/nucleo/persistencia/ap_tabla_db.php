<?
require_once("ap.php");
require_once("tipo_datos.php");

if (!defined("apex_db_registros_separador")) {
	define("apex_db_registros_separador","%"); //Por si ya esta definida en db_registros
}
/*
	Administrador de persistencia a DB
	Supone que la tabla de datos se va a mapear a algun tipo de estructura en una base de datos

	PENDIENTE

	- Como se implementa la carga de columnas externas??
	- Donde se hacen los controles pre-sincronizacion (nulos db)??
	- Hay que definir el manejo de claves (en base a objeto_datos_relacion)	
	- Esta clase no deberia utilizar ADOdb!!!
*/
class ap_tabla_db extends ap
{
	protected $objeto_tabla;					// DATOS_TABLA: Referencia al objeto asociado
	protected $columnas;						// DATOS_TABLA: Estructura del objeto
	protected $datos;							// DATOS_TABLA: DATOS que conforman las filas
	protected $cambios;							// DATOS_TABLA: Estado de los cambios
	protected $tabla;							// DATOS_TABLA: Tabla
	protected $alias;							// DATOS_TABLA: Alias
	protected $clave;							// DATOS_TABLA: Clave
	protected $fuente;							// DATOS_TABLA: Fuente de datos
	protected $secuencias;
	protected $columnas_predeterminadas_db;		// Manejo de datos generados por el motor (autonumericos, predeterninados, etc)
	protected $posee_columnas_ext;				// Columnas que se cargan de una manera especial (no estan en la tabla)
	//-------------------------------
	protected $baja_logica = false;				// Baja logica. (delete = update de una columna a un valor)
	protected $baja_logica_columna;				// Columna de la baja logica
	protected $baja_logica_valor;				// Valor de la baja logica
	protected $flag_modificacion_clave = false;	// Es posible modificar la clave en el UPDATE? Por defecto
	protected $proceso_carga_externa = null;	// Declaracion del proceso utilizado para cargar columnas externas
	//-------------------------------
	protected $control_sincro_db;				// Se activa el control de sincronizacion con la DB?
	protected $utilizar_transaccion;			// La sincronizacion con la DB se ejecuta dentro de una transaccion
	protected $msg_error_sincro = "Error interno. Los datos no fueron guardados.";
	//-------------------------------
	protected $where;							// Condicion utilizada para cargar datos - WHERE
	protected $from;							// Condicion utilizada para cargar datos - FROM

	function __construct($datos_tabla)
	{
		$this->objeto_tabla = $datos_tabla;
		$this->tabla = $this->objeto_tabla->get_tabla();
		$this->alias = $this->objeto_tabla->get_alias();
		$this->clave = $this->objeto_tabla->get_clave();
		$this->columnas = $this->objeto_tabla->get_columnas();
		$this->fuente = $this->objeto_tabla->get_fuente();
		$this->posee_columnas_ext = $this->objeto_tabla->posee_columnas_externas();
		//Determino las secuencias de la tabla
		foreach($this->columnas as $columna){
			if( $columna['secuencia']!=""){
				$this->secuencias[$columna['columna']] = $columna['secuencia'];
			}
		}
		$this->inicializar();
	}
	
	protected function inicializar(){}

	function get_estado_datos_tabla()
	{
		$this->cambios = $this->objeto_tabla->get_cambios();
		$this->datos = $this->objeto_tabla->get_conjunto_datos_interno();
	}
	
	protected function log($txt)
	{
		toba::get_logger()->debug("AP: " . get_class($this). "- TABLA: $this->tabla - OBJETO: ". get_class($this->objeto_tabla). " -- " .$txt);
	}

	public function info()
	{
		return get_object_vars($this);
	}

	//-------------------------------------------------------------------------------
	//------  Configuracion  --------------------------------------------------------
	//-------------------------------------------------------------------------------

	public function activar_transaccion()		
	{
		$this->utilizar_transaccion = true;
	}

	public function desactivar_transaccion()		
	{
		$this->utilizar_transaccion = false;
	}

	public function activar_proceso_carga_externa_sql($sql, $col_parametros, $col_resultado, $sincro_continua=true)
	{
		$proximo = count($this->proceso_carga_externa);
		$this->proceso_carga_externa[$proximo]["tipo"] = "sql";
		$this->proceso_carga_externa[$proximo]["sql"] = $sql;
		$this->proceso_carga_externa[$proximo]["col_parametro"] = $col_parametros;
		$this->proceso_carga_externa[$proximo]["col_resultado"] = $col_resultado;
		$this->proceso_carga_externa[$proximo]["sincro_continua"] = $sincro_continua;
	}
	
	public function activar_proceso_carga_externa_dao($metodo, $clase, $include, $col_parametros, $col_resultado, $sincro_continua=true)
	{
		$proximo = count($this->proceso_carga_externa);
		$this->proceso_carga_externa[$proximo]["tipo"] = "dao";
		$this->proceso_carga_externa[$proximo]["metodo"] = $metodo;
		$this->proceso_carga_externa[$proximo]["clase"] = $clase;
		$this->proceso_carga_externa[$proximo]["include"] = $include;
		$this->proceso_carga_externa[$proximo]["col_parametro"] = $col_parametros;
		$this->proceso_carga_externa[$proximo]["col_resultado"] = $col_resultado;
		$this->proceso_carga_externa[$proximo]["sincro_continua"] = $sincro_continua;
	}

	public function activar_baja_logica($columna, $valor)
	{
		$this->baja_logica = true;
		$this->baja_logica_columna = $columna;
		$this->baja_logica_valor = $valor;	
	}

	public function activar_modificacion_clave()
	{
		$this->flag_modificacion_clave = true;
	}

	public function activar_control_sincro()
	{
		$this->control_sincro_db = true;
	}

	public function desactivar_control_sincro()
	{
		$this->control_sincro_db = false;
	}

	//-------------------------------------------------------------------------------
	//------  CARGA  ----------------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
		Carga la tabla tomando como parametro el valor de algunas columnas
	*/
	public function cargar($clave)
	{
		asercion::es_array($clave, "AP [$this->tabla] ERROR: La clave debe ser un array");
		$where = $this->generar_clausula_where_lineal($clave);
		return $this->cargar_db($where);
	}

	/**
		Carga datos de la base a partir de clausulas WHERE y FROM
	*/
	public function cargar_db($where=null, $from=null)
	{
		asercion::es_array_o_null($where,"AP [$this->tabla] El WHERE debe ser un array");
		asercion::es_array_o_null($from,"AP [$this->tabla] El FROM debe ser un array");
		$this->log("Cargar de DB");
		$this->where = $where;
		$this->from = $from;
		$db = toba::get_db($this->fuente);
		$sql = $this->generar_sql_select();//echo $sql . "<br>";
		$this->log("SQL de carga - " . $sql); 
		try{
			$datos = $db->consultar($sql);
		}catch(excepcion_toba $e){
			toba::get_logger()->error( get_class($this). ' - '.
									'Error cargando datos. ' .$e->getMessage() );
			throw new excepcion_toba('AP - OBJETO_DATOS_TABLA: Error cargando datos. Verifique la definicion.\n' . $e->getMessage() );
		}
		if(count($datos)>0){
			//Si existen campos externos, los recupero.
			if($this->posee_columnas_ext){
				for($a=0;$a<count($datos);$a++){
					$campos_externos = $this->completar_campos_externos_fila($datos[$a]);
					foreach($campos_externos as $id => $valor){
						$datos[$a][$id] = $valor;
					}
				}				
			}
			//Le saco los caracteres de escape a los valores traidos de la DB
			for($a=0;$a<count($datos);$a++){
				foreach(array_keys($datos[$a]) as $columna){
					if(isset($datos[$a][$columna])){
						$datos[$a][$columna] = stripslashes($datos[$a][$columna]);				
					}
				}	
			}
			// Lleno la TABLA
			$this->objeto_tabla->set_datos($datos);
			//ei_arbol($datos);
			return true;
		}else{
			//No se carga nada!
			return false;
		}
	}

	//-------------------------------------------------------------------------------
	//------  SINCRONIZACION  -------------------------------------------------------
	//-------------------------------------------------------------------------------
	
	private function actualizar_estado_db()
	{
		$this->get_estado_datos_tabla();
		//$this->controlar_alteracion_db();
		// No puedo ejecutar los cambios en cualguier orden
		// Necesito ejecutar primero los deletes, por si el usuario borra algo y despues inserta algo igual
		$inserts = array(); $deletes = array();	$updates = array();
		foreach(array_keys($this->cambios) as $registro){
			switch($this->cambios[$registro]['estado']){
				case "d":
					$deletes[] = $registro;
					break;
				case "i":
					$inserts[] = $registro;
					break;
				case "u":
					$updates[] = $registro;
					break;
			}
		}
		try{
			if($this->utilizar_transaccion) abrir_transaccion($this->fuente);
			$this->evt__pre_sincronizacion();
			$modificaciones = 0;
			//-- DELETE --
			foreach($deletes as $registro){
				$this->evt__pre_delete($registro);
				$this->eliminar_registro_db($registro);
				$this->evt__post_delete($registro);
				$modificaciones ++;
			}
			//-- INSERT --
			foreach($inserts as $registro){
				$this->evt__pre_insert($registro);
				$this->insertar_registro_db($registro);
				$this->evt__post_insert($registro);
				$modificaciones ++;
			}
			//-- UPDATE --
			foreach($updates as $registro){
				$this->evt__pre_update($registro);
				$this->modificar_registro_db($registro);
				$this->evt__post_update($registro);
				$modificaciones ++;
			}
			$this->evt__post_sincronizacion();
			if($this->utilizar_transaccion) cerrar_transaccion($this->fuente);
			return $modificaciones;
		}catch(excepcion_toba $e){
			if($this->utilizar_transaccion) abortar_transaccion($this->fuente);
			toba::get_logger()->debug($e);
			throw new excepcion_toba($e->getMessage());
		}

	}

	public function sincronizar($control_tope_minimo=true)
	//Sincroniza las modificaciones del db_registros con la DB
	{
		$this->log("Inicio SINCRONIZAR");
		$modificaciones = $this->actualizar_estado_db();
		//Seteo en la TABLA los datos generados durante la sincronizacion
		$this->actualizar_columnas_predeterminadas_db();
		//Regenero la estructura que mantiene los cambios realizados
		$this->objeto_tabla->notificar_fin_sincronizacion();
		$this->log("Fin SINCRONIZAR: $modificaciones."); 
		return $modificaciones;
	}

	protected function insertar_registro_db($id_registro){}	
	protected function modificar_registro_db($id_registro){}
	protected function eliminar_registro_db($id_registro){}

	/*
		Esquema de recuperacion de valores de COLUMNAS generados por el motor
			Es para los casos como secuencias, valores predeterminados, etc.
	*/

	function registrar_recuperacion_valor_db($id_registro, $columna, $valor)
	{
		$this->columnas_predeterminadas_db[$id_registro][$columna] = $valor;
	}
	
	function actualizar_columnas_predeterminadas_db()
	{
		if(isset($this->columnas_predeterminadas_db)){
			foreach( $this->columnas_predeterminadas_db as $id_registro => $columnas ){
				foreach( $columnas as $columna => $valor ){
					$this->objeto_tabla->set_fila_columna_valor($id_registro, $columna, $valor);
				}
			}
		}
	}

	/*
		EVENTOS de SINCRONIZACION con la DB
			Este es el lugar para meter validaciones (disparar una excepcion) o disparar procesos.
	*/

	protected function evt__pre_sincronizacion(){}
	protected function evt__post_sincronizacion(){}
	protected function evt__pre_insert($id){}
	protected function evt__post_insert($id){}
	protected function evt__pre_update($id){}
	protected function evt__post_update($id){}
	protected function evt__pre_delete($id){}
	protected function evt__post_delete($id){}

	//-------------------------------------------------------------------------------
	//------  ELIMINAR  -------------------------------------------------------
	//-------------------------------------------------------------------------------
	
	public function eliminar()
	{
		$this->log("Inicio ELIMINAR");
		//Elimino a mis hijos
		$this->objeto_tabla->notificar_hijos_eliminacion();
		//Me elimino a mi
		$this->actualizar_estado_db();
		$this->log("Inicio ELIMINAR");
	}	

	//-------------------------------------------------------------------------------
	//------ Servicios SQL   --------------------------------------------------------
	//-------------------------------------------------------------------------------

	function ejecutar_sql( $sql )
	{
		ejecutar_sql( $sql, $this->fuente);			
	}

	public function get_sql_inserts()
	{
		$this->get_estado_datos_tabla();
		$sql = array();
		foreach(array_keys($this->cambios) as $registro){
			$sql[] = $this->generar_sql_insert($registro);
		}
		return $sql;
	}

	function generar_clausula_where_lineal($clave,$alias=true)
	//Genera la sentencia WHERE del estilo ( nombre_columna = valor ) respetando el tipo de datos
	//El alias es para cuando se generan SELECTs complejos
	{
		if($alias){
			$tabla_alias = isset($this->alias) ? $this->alias . "." : "";
		}else{
			$tabla_alias = "";	
		}
		foreach($clave as $columna => $valor)
		{
			if( tipo_datos::numero( $this->columnas[$columna]['tipo'] ) ){
				$clausula[] = "( $tabla_alias" . "$columna = $valor )";
			}else{
				$clausula[] = "( $tabla_alias" . "$columna = '$valor' )";
			}
		}
		return $clausula;
	}

	//-------------------------------------------------------------------------------
	//---------------  Carga de CAMPOS EXTERNOS   -----------------------------------
	//-------------------------------------------------------------------------------

	public function completar_campos_externos_fila($fila, $evento=null)
	/*
		ATENCION: Este mecanismo requiere OPTIMIZACION (Mas que nada para la carga inicial)
		Recuperacion de valores para las columnas externas.
		Se pasa una fila como parametro y se devuelven los valores recuperados de la DB.
		Para que esto funcione, la consultas realizadas tienen que devolver un solo registro,
			cuyas claves asociativas se correspondan con la columna que se quiere llenar
	*/
	{
		//Itero planes de carga externa
		$valores_recuperados = array();
		if(isset($this->proceso_carga_externa)){
			foreach(array_keys($this->proceso_carga_externa) as $carga)
			{
				$parametros = $this->proceso_carga_externa[$carga];
				//Si la columna no solicito sincro continua, paso a la siguiente.
				if(isset($evento)&& !($parametros["sincro_continua"])) continue;
				//Controlo que los parametros del cargador me alcanzan para recuperar datos de la DB
				foreach( $parametros['col_parametro'] as $col_llave ){
					if(isset($evento) && isset($this->secuencias[$col_llave])){
						throw new excepcion_toba('AP_TABLA_DB: No puede actualizarse en linea un valor que dependende de una secuencia');
					}
					if(!isset($fila[$col_llave])){
						toba::get_logger()->debug("AP_TABLA_DB: Falta el parametro '$col_llave'");
						toba::get_logger()->debug($fila);
						throw new excepcion_toba('AP_TABLA_DB: ERROR en la carga de una columna externa');
					}
				}
				//-[ 1 ]- Recupero valores correspondientes al registro
				if($parametros['tipo']=="sql")											//--- carga SQL!!
				{
					// - 1 - Obtengo el query
					$sql = $parametros['sql'];
					// - 2 - Reemplazo valores llave con los parametros correspondientes a la fila actual
					foreach( $parametros['col_parametro'] as $col_llave ){
						$valor_llave = $fila[$col_llave];
						$sql = ereg_replace( apex_db_registros_separador . $col_llave . apex_db_registros_separador, $valor_llave, $sql);
					}
					//echo "<pre>SQL: "  . $sql . "<br>";
					// - 3 - Ejecuto SQL
					$datos = consultar_fuente($sql, $this->fuente);//ei_arbol($datos);
					//ei_arbol($this->datos);
				}
				elseif($parametros['tipo']=="dao")										//--- carga DAO!!
				{
					// - 1 - Armo los parametros para el DAO
					$param_dao = array();
					foreach( $parametros['col_parametro'] as $col_llave ){
						$param_dao[] = $fila[$col_llave];
					}
					//ei_arbol($param_dao,"Parametros para el DAO");
					// - 2 - Recupero datos
					include_once($parametros['include']);
					$datos = call_user_func_array(array($parametros['clase'],$parametros['metodo']), $param_dao);
				}
				//ei_arbol($datos,"datos");
				//-[ 2 ]- Seteo los valores recuperados en las columnas correspondientes
				if(count($datos)>0){
					foreach( $parametros['col_resultado'] as $columna_externa ){
						$valores_recuperados[$columna_externa] = $datos[0][$columna_externa];
					}
				}
			}
		}
		return $valores_recuperados;
	}

	//-------------------------------------------------------------------------------
	//--  Control de VERSIONES  -----------------------------------------------------
	//-------------------------------------------------------------------------------

	public function controlar_alteracion_db()
	//Controla que los datos
	{
	}

	private function controlar_alteracion_db_array()
	//Soporte al manejo transaccional OPTIMISTA
	//Indica si los datos iniciales extraidos de la base difieren de
	//los datos existentes en el momento de realizar la transaccion
	{
	}
	//-------------------------------------------------------------------------------

	private function controlar_alteracion_db_timestamp()
	//Esto tiene que basarse en una forma generica de trabajar sobre tablas
	//(Una columna que posea el timestamp, y triggers que los actualicen)
	{
	}
	//-------------------------------------------------------------------------------
}
?>