<?
require_once("ap.php");
define("apex_db_registros_separador","%");
/*
	Administrador de persistencia a DB
	Supone que la tabla de datos se va a mapear a algun tipo de estructura en una base de datos

	PENDIENTE

	- Como se implementa la carga de columnas externas??
	- Donde se hacen los controles pre-sincronizacion??
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
		$this->indice_columnas = $this->objeto_tabla->get_indice_columnas();
		$this->fuente = $this->objeto_tabla->get_fuente_datos();
	}

	function get_estado_datos_tabla()
	{
		$this->cambios = $this->objeto_tabla->get_cambios();
		$this->datos = $this->objeto_tabla->get_datos();
	}
	
	protected function log($txt)
	{
		toba::get_logger()->debug("AP: " . get_class($this). " DATOS: ". get_class($this->objeto_tabla). " -- " .$txt);
	}

	public function info()
	{
		return get_object_vars($this);
	}

	//-------------------------------------------------------------------------------
	//------  Configuracion  ----------------------------------------------------------------
	//-------------------------------------------------------------------------------

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

	public function activar_transaccion()		
	{
		$this->utilizar_transaccion = true;
	}

	public function desactivar_transaccion()		
	{
		$this->utilizar_transaccion = false;
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

	public function cargar_datos($where=null, $from=null)
	{
		asercion::es_array_o_null($where,"El WHERE debe ser un array");
		$this->log("Cargar de DB");
		$this->where = $where;
		$this->from = $from;
		/*
			Cargo los datos de la base
		*/
		$db = toba::get_fuente($this->fuente);
		$sql = $this->generar_sql_select();//echo $sql . "<br>";
		//-- Intento cargar el db_registros
		$rs = $db[apex_db_con]->Execute($sql);
		if(!is_object($rs)){
			toba::get_logger()->error("db_registros  " . get_class($this). " [{$this->identificador}] - Error cargando datos, no se genero un RECORDSET" .
									$sql . " - " . $db[apex_db_con]->ErrorMsg());
			throw new excepcion_toba("Error cargando datos en el db_registros. Verifique la definicion. $sql");
		}
		if($rs->EOF){
			if($carga_estricta){
				toba::get_logger()->error("db_registros  " . get_class($this). " [{$this->identificador}] - " .
								"No se recuperarron DATOS. Se solicito carga estricta");
			}
		}else{
			$datos =& $rs->getArray();
			//ei_arbol($datos);
			//Los campos NO SQL deberian estar metidos en el array
			if(isset($this->campos_externa)){
				foreach($this->campos_externa as $externa){
					for($a=0;$a<count($datos);$a++){
						$datos[$a][$externa] = "";
					}
				}
			}
		}
		$this->objeto_tabla->set_datos($datos);
	}

	public function cargar_datos_clave($id)
	/*
		La clave tiene que ser un array asociativo con el nombre de la columna
	*/
	{
		assercion::es_array($id,"La carga por clave debe realizarse a travez de un array");
		foreach($this->clave as $clave){
			if(!isset($id[$clave])){
				throw new exception_toba("La carga por clave tiene que ser a travez de un array 
									asociativo cuyas claves sean identicas a las CLAVES del OBJETO");
			}
			$where[] = " $clave = ";
		}
	}

	//-------------------------------------------------------------------------------
	//------  SINCRONIZACION  -------------------------------------------------------
	//-------------------------------------------------------------------------------

	public function sincronizar($control_tope_minimo=true)
	//Sincroniza las modificaciones del db_registros con la DB
	{
		$this->log("Inicio SINCRONIZACION");
		$this->get_estado_datos_tabla();
/*
		if($control_tope_minimo){
			if( $this->tope_min_registros != 0){
				if( ( $this->get_cantidad_registros() < $this->tope_min_registros) ){
					$this->log("No se cumplio con el tope minimo de registros necesarios" );
					throw new excepcion_toba("Los registros cargados no cumplen con el TOPE MINIMO necesario");
				}
			}
		}
*/
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
			if($this->utilizar_transaccion) abrir_transaccion();
			$this->evt__pre_sincronizacion();
			$modificaciones = 0;
			//-- DELETE --
			foreach($deletes as $registro){
				$this->evt__pre_delete($registro);
				$this->eliminar($registro);
				$this->evt__post_delete($registro);
				$modificaciones ++;
			}
			//-- INSERT --
			foreach($inserts as $registro){
				$this->evt__pre_insert($registro);
				$this->insertar($registro);
				$this->evt__post_insert($registro);
				$modificaciones ++;
			}
			//-- UPDATE --
			foreach($updates as $registro){
				$this->evt__pre_update($registro);
				$this->modificar($registro);
				$this->evt__post_update($registro);
				$modificaciones ++;
			}
			$this->evt__post_sincronizacion();
			if($this->utilizar_transaccion) cerrar_transaccion();

			//Devuelvo los DATOS sincronizados al objeto
			$this->objeto_tabla->resetear();
			$this->objeto_tabla->set_datos($this->datos);

			$this->log("Fin SINCRONIZACION: $modificaciones."); 
			return $modificaciones;
		}catch(excepcion_toba $e){
			if($this->utilizar_transaccion) abortar_transaccion();
			toba::get_logger()->debug($e);
			throw new excepcion_toba($e->getMessage());
		}
	}

	protected function insertar($id_registro){}	
	protected function modificar($id_registro){}
	protected function eliminar($id_registro){}

	function ejecutar_sql( $sql )
	{
		/* Aca no es necesario usar adodb */
		ejecutar_sql( $sql, $this->fuente);			
	}

	//-------------------------------------------------------------------------------
	//--  EVENTOS de SINCRONIZACION con la DB   -------------------------------------
	//-------------------------------------------------------------------------------
	/*
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
	//---------------  Carga de CAMPOS EXTERNOS   -----------------------------------
	//-------------------------------------------------------------------------------

	private function actualizar_campos_externos()
	//Actualiza los campos externos despues de cargar el db_registros
	{
		foreach(array_keys($this->cambios) as $registro)
		{
			$this->actualizar_campos_externos_registro($registro);
		}	
	}
	
	private function actualizar_campos_externos_registro($id_registro, $evento=null)
	/*
		Recuperacion de valores para las columnas externas.
		Para que esto funcione, la consultas realizadas tienen que devolver un solo registro,
			cuyas claves asociativas se correspondan con la columna que se quiere
	*/
	{
		//Itero planes de carga externa
		if(isset($this->proceso_carga_externa)){
			foreach(array_keys($this->proceso_carga_externa) as $carga)
			{
				//SI entre por un evento, tengo que controlar que la carga este
				//Activada para eventos, si no esta activada paso al siguiente
				if(isset($evento)){
					if(! $this->proceso_carga_externa[$carga]['sincro_continua'] ){	
						continue;
					}
				}
				//-[ 1 ]- Recupero valores correspondientes al registro
				$parametros = $this->proceso_carga_externa[$carga];
				if($parametros['tipo']=="sql")											//--- carga SQL!!
				{
					// - 1 - Obtengo el query
					$sql = $parametros['sql'];
					// - 2 - Reemplazo valores llave con los parametros correspondientes a la fila actual
					foreach( $parametros['col_parametro'] as $col_llave ){
						$valor_llave = $this->datos[$id_registro][$col_llave];
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
					foreach( $parametros['col_parametro'] as $col_llave ){
						$param_dao[] = $this->datos[$id_registro][$col_llave];
					}
					//ei_arbol($param_dao,"Parametros para el DAO");
					// - 2 - Recupero datos
					include_once($parametros['include']);
					$datos = call_user_func_array(array($parametros['clase'],$parametros['metodo']), $param_dao);
				}
				//ei_arbol($datos,"datos");
				//-[ 2 ]- Seteo los valores recuperados en las columnas correspondientes
				foreach( $parametros['col_resultado'] as $columna_externa ){
					$this->datos[$id_registro][$columna_externa] = $datos[0][$columna_externa];
				}
			}
		}
	}

	//-------------------------------------------------------------------------------
	//------ Servicios de generacion de SQL   ---------------------------------------
	//-------------------------------------------------------------------------------

	public function get_sql_inserts()
	{
		$this->get_estado_datos_tabla();
		$sql = array();
		foreach(array_keys($this->cambios) as $registro){
			$sql[] = $this->generar_sql_insert($registro);
		}
		return $sql;
	}

	//-------------------------------------------------------------------------------
	//--  Control de VERSIONES  -----------------------------------------------------
	//-------------------------------------------------------------------------------

	public function controlar_alteracion_db()
	//Controla que los datos
	{
		/*
			Esto hay que pensarlo bien
		*/
	}

	private function controlar_alteracion_db_array()
	//Soporte al manejo transaccional OPTIMISTA
	//Indica si los datos iniciales extraidos de la base difieren de
	//los datos existentes en el momento de realizar la transaccion
	{
		$ok = true;
		$datos_actuales = $this->cargar_db();
		//Hay datos?
		if(is_array($datos_actuales)){
			//La cantidad de filas es la misma?
			if(count($datos_actuales) == count($this->datos_orig)){
				for($a=0;$a<count($this->datos_orig);$a++){
					//Existe la fila?
					if(isset($datos_actuales[$a])){
						foreach(array_keys($this->datos_orig[$a]) as $columna){
							//El valor de las columnas coincide?
							if($this->datos_orig[$a][$columna] !== $datos_actuales[$a][$columna]){
								$ok = false;
								break 2;
							}
						}
					}else{
						$ok = false;
						break 1;
					}
				}
			}else{
				$ok = false;
			}
		}else{
			$ok = false;
		}
		return $ok;
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