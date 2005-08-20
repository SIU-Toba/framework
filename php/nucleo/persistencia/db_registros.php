<?
define("apex_db_registros_separador","%");

class db_registros
{
	// Definicion asociada a la TABLA
	protected $clave;							// Columnas que constituyen la clave de la tabla
	protected $campos;							// Campos del db_registros
	protected $campos_no_nulo;					// Campos que no admiten el valor NULL
	protected $campos_externa;
	// Definicion general
	protected $tope_registros;					// Cantidad de registros permitida. 0 = n registros
	protected $fuente;							// Fuente de datos utilizada
	protected $definicion;						// Definicion que indica la construccion del db_registros
	protected $where;							// Condicion utilizada para cargar datos - WHERE
	protected $from;							// Condicion utilizada para cargar datos - FROM
	// Estructuras CORE
	protected $control = array();				// Estructura de control
	protected $datos = array();					// Datos cargados en el db_registros
	protected $datos_orig = array();			// Datos tal cual salieron de la DB (Control de SINCRO)
	protected $proximo_registro = 0;			// Posicion del proximo registro en el array de datos
	protected $msg_error_sincro = "Error interno. Los datos no fueron guardados.";
	protected $controlador = null;				// referencia al db_tablas del cual forma parte, si se aplica
	// Servicios activados por metodos
	protected $control_sincro_db;				// Se activa el control de sincronizacion con la DB?
	protected $flag_modificacion_clave = false;	// Es posible modificar la clave en el UPDATE? Por defecto
	protected $proceso_carga_externa = null;	// Declaracion del proceso utilizado para cargar columnas externas
	protected $baja_logica = false;				// Baja logica. (delete = update de una columna a un valor)
	protected $baja_logica_columna;				// Columna de la baja logica
	protected $baja_logica_valor;				// Valor de la baja logica
	protected $utilizar_transaccion;			// La sincronizacion con la DB se ejecuta dentro de una transaccion
	protected $no_duplicado;					// Combinacines de columnas que no pueden duplicarse
	// Memoria autonoma
	protected $memoria_autonoma = false;		// Se persiste en la sesion por si mismo
	protected $identificador;					// Identificador del registro
	protected $posicion_finalizador;			// Posicion del objeto en el array de finalizacion

	function __construct($definicion_tabla, $fuente=null, $tope_min_registros=0, $tope_max_registros=0)
	{
		$this->definicion = $definicion_tabla;
		$this->fuente = $fuente;
		$this->set_tope_max_registros($tope_max_registros);
		$this->set_tope_min_registros($tope_min_registros);
		//Inicializar la estructura de campos
		$this->inicializar_definicion_campos();
		//ATENCION, hay que analizar si no es mas eficiente dejarlo en la sesion
		if($this->memoria_autonoma){
			$this->inicializar_memoria_autonoma();
		}
	}

	public function registrar_controlador($controlador)
	{
		/*
			ATENCION, el manejo de controladores debe ser consistente con
						el mecanismo de serializacion, ya que hay que evitar las referencias
						circulares porque van a destruir la memoria
			-->	Hacer una implementacion con __sleep y __wakeup
		*/
		//$this->controlador = $controlador;
	}

	protected function log($txt)
	{
		toba::get_logger()->debug("db_registros  '" . get_class($this). "' " . $txt);
	}
	

	//-------------------------------------------------------------------------------
	//-- Preguntas BASICAS
	//-------------------------------------------------------------------------------

	public function info($mostrar_datos=false)
	//Informacion del estado del db_registros
	{
		$estado['control']=$this->control;
		$estado['proximo_registro']=$this->proximo_registro;
		$estado['where']=$this->where;
		$estado['from']=$this->from;
		if($mostrar_datos) $estado['datos']=$this->datos;
		return $estado;
	}

	public function info_definicion()
	//Info sobre la definicion del db_registros
	{
		$estado['clave'] = isset($this->clave) ? $this->clave : null;				
		$estado['campos'] = $this->campos;
		$estado['campos_no_nulo'] = isset($this->campos_no_nulo) ? $this->campos_no_nulo: null;
		$estado['no_duplicado'] = isset($this->no_duplicado) ? $this->no_duplicado: null;
		return $estado;
	}

	public function get_definicion()
	{
		return $this->definicion;
	}

	public function get_tope_max_registros()
	{
		return $this->tope_max_registros;	
	}

	public function get_tope_min_registros()
	{
		return $this->tope_min_registros;	
	}

	public function get_cantidad_registros_a_sincronizar()
	{
		$cantidad = 0;
		foreach(array_keys($this->control) as $registro){
			if( ($this->control[$registro]['estado'] == "d") ||
				($this->control[$registro]['estado'] == "i") ||
				($this->control[$registro]['estado'] == "u") ){
				$cantidad++;
			}
		}
		return $cantidad;
	}

	public function get_id_registros_a_sincronizar()
	{
		$ids = null;
		foreach(array_keys($this->control) as $registro){
			if( ($this->control[$registro]['estado'] == "d") ||
				($this->control[$registro]['estado'] == "i") ||
				($this->control[$registro]['estado'] == "u") ){
				$ids[] = $registro;
			}
		}
		return $ids;
	}

	//-------------------------------------------------------------------------------
	//-- Especificacion de SERVICIOS
	//-------------------------------------------------------------------------------

	public function set_tope_max_registros($cantidad)
	{
		if(is_numeric($cantidad) && $cantidad >= 0){
			$this->tope_max_registros = $cantidad;	
		}else{
			throw new excepcion_toba("El valor especificado en el TOPE MAXIMO de registros es incorrecto");
		}
	}

	public function set_tope_min_registros($cantidad)
	{
		if(is_numeric($cantidad) && $cantidad >= 0){
			$this->tope_min_registros = $cantidad;
		}else{
			throw new excepcion_toba("El valor especificado en el TOPE MINIMO de registros es incorrecto");
		}
	}

	public function set_no_duplicado( $columnas )
	//Indica una combinacion de columnas que no debe duplicarse
	{
		$this->no_duplicado[] = $columnas;
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

	public function activar_memoria_autonoma($id)
	{
		$this->memoria_autonoma = true;
		$this->identificador = $id; 		//Tiene que ser UNICO en la sesion
	}
	
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-----------------------------  Manejo de DATOS  -------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	public function resetear()
	{
		$this->log("RESET!!");
		$this->datos = array();
		$this->datos_orig = array();
		$this->control = array();
		$this->proximo_registro = 0;
		$this->where = null;
		$this->from = null;
		if($this->memoria_autonoma){
			//Borro informacion de la sesion
			if($this->existe_instanciacion_previa()){
				toba::get_hilo()->eliminar_dato_global($this->identificador);
			}
		}
	}

	public function cargar_datos_clave($id)
	{
		/*
			Esta funcion deberia mapear un ID expresado como un array
			y transformarlo en un WHERE
		*/		
	}

	public function cargar_datos($where=null, $from=null)
	{
		if(isset($where)){
			if(!is_array($where)){
				throw new excepcion_toba("El WHERE debe ser un array");
			}	
		}
		$this->log("Cargar de DB");
		$this->where = $where;
		$this->from = $from;
		//Obtengo los datos de la DB
		$this->datos = $this->cargar_db();
		//Controlo que no se haya excedido el tope de registros
		if( $this->tope_max_registros != 0){
			if( $this->tope_max_registros < count( $this->datos ) ){
				//Hay mas datos que los que permite el tope, todo mal
				$this->datos = null;
				$this->log("Se sobrepaso el tope maximo de registros en carga: " . count( $this->datos ) . " registros" );
				throw new excepcion_toba("Los registros cargados superan el TOPE MAXIMO de registros");
			}
		}
		//ei_arbol($this->datos);
		//Se solicita control de SINCRONIA a la DB?
		if($this->control_sincro_db){
			$this->datos_orig = $this->datos;
		}
		$this->generar_estructura_control_post_carga();
		//Le saco los caracteres de escape a los valores traidos de la DB
		for($a=0;$a<count($this->datos);$a++){
			foreach(array_keys($this->datos[$a]) as $columna){
				$this->datos[$a][$columna] = stripslashes($this->datos[$a][$columna]);
			}	
		}
		//Actualizo la posicion en que hay que incorporar al proximo registro
		$this->proximo_registro = count($this->datos);	
		//Controlo que no se haya excedido el tope de registros
		if( $this->tope_max_registros != 0){
			if( ( $this->get_cantidad_registros() > $this->proximo_registro) ){
				$this->log("Se sobrepaso el tope maximo de registros mientras se agregaba un registro" );
				throw new excepcion_toba("Los registros cargados superan el TOPE MAXIMO de registros");
			}
		}
		//Lleno las columnas basadas en valores EXTERNOS
		$this->actualizar_campos_externos();
	}

	private function cargar_db($carga_estricta=false)
	//Cargo los db_registrosS con datos de la DB
	//Los datos son 
	{
		$db = toba::get_fuente($this->fuente);
		$sql = $this->generar_sql_select();//echo $sql . "<br>";
		//-- Intento cargar el db_registros
		$rs = $db[apex_db_con]->Execute($sql);
		if(!is_object($rs)){
			$error = $db[apex_db_con]->ErrorMsg();
			toba::get_logger()->error("db_registros  " . get_class($this). " - Error cargando datos: $error - $sql");
			throw new excepcion_toba("Error cargando datos en el db_registros. Verifique la definicion. $error");
		}
		if($rs->EOF){
			if($carga_estricta){
				toba::get_logger()->error("db_registros  " . get_class($this). " [{$this->identificador}] - " .
								"No se recuperarron DATOS. Se solicito carga estricta");
			}
			return null;
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
			return $datos; 
		}
	}

	private function controlar_conservacion_where($where)
	/*
		El uso de este metodo ya no tiene sentido
	*/
	{
		if(!isset($this->where)){
			if(isset($where)){
				$this->log("Control WHERE: No existe");
				return false;	
			}
		}else{
			for($a=0;$a<count($this->where);$a++){
				if(!isset($where[$a])){
					$this->log("Control WHERE: nuevo mas corto"); 
					return false;
				}else{
					if($where[$a] !== $this->where[$a]){
						$this->log("Control WHERE: nuevo distinto"); 
						return false;	
					}
				}
			}
		}
		$this->log("Control WHERE: OK!");
		return true;
	}

	//-------------------------------------------------------------------------------
	//-- Mantenimiento de la estructura de control ----------------------------------
	//-------------------------------------------------------------------------------

	protected function generar_estructura_control_post_carga()
	{
		//Genero la estructura de control
		$this->control = array();
		for($a=0;$a<count($this->datos);$a++){
			$this->control[$a]['estado']="db";
			$this->control[$a]['clave']= $this->get_clave_valor($a);
		}
	}
	
	protected function actualizar_estructura_control($registro, $estado)
	{
		$this->control[$registro]['estado'] = $estado;
	}

	protected function sincronizar_estructura_control()
	{
		foreach(array_keys($this->control) as $registro){
			switch($this->control[$registro]['estado']){
				case "d":	//DELETE
					unset($this->control[$registro]);
					unset($this->datos[$registro]);
					break;
				case "i":	//INSERT
					$this->control[$registro]['estado'] = "db";
					break;
				case "u":	//UPDATE
					$this->control[$registro]['estado'] = "db";
					break;
			}
		}
	}

	public function get_estructura_control()
	{
		return $this->control;	
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-------  Primitivas de ACCESO a REGISTROS   -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	public function get_registros($condiciones=null, $usar_id_registro=false)
	//Las condiciones permiten filtrar la lista de registros que se devuelves
	//Usar ID registro hace que las claves del array devuelto sean las claves internas del dbr
	{
		$datos = null;
		$a = 0;
		foreach( $this->get_id_registro_condicion($condiciones) as $id_registro )
		{
			if($usar_id_registro){
				$datos[$id_registro] = $this->datos[$id_registro];
			}else{
				$datos[$a] = $this->datos[$id_registro];
				//esta columna indica cual fue la clave del registro
				$datos[$a][apex_db_registros_clave] = $id_registro;
			}
			$a++;
		}
		return $datos;
	}
	//-------------------------------------------------------------------------------
	
	public function get_id_registro_condicion($condiciones=null)
	/*
		Devuelve los registros que cumplen una condicion.
		Solo se chequea la condicion de igualdad.
		El parametro es un array asociativo de campo => valor.
		ATENCION, NO se utiliza chequeo de tipos
	*/
	{	
		$coincidencias = array();
		if(!isset($condiciones)){
			foreach(array_keys($this->control) as $id_registro){
				if($this->control[$id_registro]['estado']!="d"){
					$coincidencias[] = $id_registro;
				}
			}
		}else{
			//Controlo que todas los campos que se utilizan para el filtrado existan
			foreach( array_keys($condiciones) as $campo){
				if(!in_array($campo, $this->campos)){
					throw new excepcion_toba("El campo '$campo' no existe. No es posible filtrar por dicho campo");
				}
			}
			//Busco coincidencias
			foreach(array_keys($this->control) as $id_registro){
				if($this->control[$id_registro]['estado']!="d"){	// Excluir los eliminados
					//Verifico las condiciones
					$ok = true;
					foreach( array_keys($condiciones) as $campo){
						if( $condiciones[$campo] != $this->datos[$id_registro][$campo] ){
							$ok = false;
							break;	
						}
					}
					if( $ok ) $coincidencias[] = $id_registro;
				}
			}
		}
		return $coincidencias;
	}
	//-------------------------------------------------------------------------------

	public function get_registro($id)
	{
		if(isset($this->datos[$id])){
			$temp = $this->datos[$id];
			$temp[apex_db_registros_clave] = $id;	//incorporo el ID del dbr
			return $temp;
		}else{
			return null;
			//throw new excepcion_toba("Se solicito un registro incorrecto");
		}
	}
	//-------------------------------------------------------------------------------

	public function get_registro_valor($id, $columna)
	{
		if(isset($this->datos[$id][$columna])){
			return  $this->datos[$id][$columna];
		}else{
			return null;
		}
	}
	//-------------------------------------------------------------------------------

	public function get_valores_columna($columna)
	//Retorna una columna de valores
	{
		$temp = null;
		foreach(array_keys($this->control) as $registro){
			if($this->control[$registro]['estado']!="d"){
				$temp[] = $this->datos[$registro][$columna];
			}
		}
		return $temp;
	}
	//-------------------------------------------------------------------------------
	
	public function get_cantidad_registros()
	{
		$a = 0;
		foreach(array_keys($this->control) as $id_registro){
			if($this->control[$id_registro]['estado']!="d")	$a++;
		}
		return $a;
	}
	
	public function existe_registro($id)
	{
		if(! isset($this->datos[$id]) ){
			return false;			
		}
		if($this->control[$id]['estado']=="d"){
			return false;
		}
		return true;
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-------  Primitivas de MODIFICACION de REGISTROS   ----------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	public function agregar_registro($registro, $id=null)
	/*
	*	Si el $id es nulo, se autogenera
	*/
	{
		
		if ($id === null) {
			$id = $this->proximo_registro;
			$this->proximo_registro++;
		}
		if( $this->tope_max_registros != 0){
			if( !($this->get_cantidad_registros() < $this->tope_max_registros) ){
				throw new excepcion_toba("No es posible agregar registros (TOPE MAX.)");
			}
		}
		$this->notificar_controlador("ins", $registro);
		//Saco el campo que indica la posicion del registro
		if(isset($registro[apex_db_registros_clave])) unset($registro[apex_db_registros_clave]);
		$this->validar_registro($registro);

		$this->datos[$id] = $registro;
		$this->actualizar_estructura_control($id,"i");
		//Actualizo los valores externos
		$this->actualizar_campos_externos_registro( $id, "agregar");
		return $id++;
		
	}
	//-------------------------------------------------------------------------------

	public function modificar_registro($registro, $id)
	{
		if(!$this->existe_registro($id)){
			$mensaje = "db_registros: MODIFICAR. No existe un registro con el INDICE indicado ($id)";
			toba::get_logger()->error($mensaje);
			throw new excepcion_toba($mensaje);
		}
		$this->notificar_controlador("upd", $registro, $id);
		//Saco el campo que indica la posicion del registro
		if(isset($registro[apex_db_registros_clave])) unset($registro[apex_db_registros_clave]);
		$this->validar_registro($registro, $id);
		//Actualizo los valores
		foreach(array_keys($registro) as $clave){
			$this->datos[$id][$clave] = $registro[$clave];
		}
		if($this->control[$id]['estado']!="i"){
			$this->actualizar_estructura_control($id,"u");
		}
		//Actualizo los valores externos
		$this->actualizar_campos_externos_registro($id,"modificar");
	}
	//-------------------------------------------------------------------------------

	public function eliminar_registro($id=null)
	{
		if(!$this->existe_registro($id)){
			$mensaje = "db_registros: MODIFICAR. No existe un registro con el INDICE indicado ($id)";
			toba::get_logger()->error($mensaje);
			throw new excepcion_toba($mensaje);
		}
		$this->notificar_controlador("del", $id);
		if($this->control[$id]['estado']=="i"){
			unset($this->control[$id]);
			unset($this->datos[$id]);
		}else{
			$this->actualizar_estructura_control($id,"d");
		}
	}
	//-------------------------------------------------------------------------------

	public function eliminar_registros()
	//Elimina todos los registros
	{
		foreach(array_keys($this->control) as $registro)
		{
			if($this->control[$registro]['estado']=="i"){
				unset($this->control[$registro]);
				unset($this->datos[$registro]);
			}else{
				if($this->existe_registro($registro)){
					$this->actualizar_estructura_control($registro,"d");
				}
			}
		}
	}
	//-------------------------------------------------------------------------------

	public function set_registro_valor($id, $columna, $valor)
	{
		if( in_array($columna, $this->campos) ){
			$this->datos[$id][$columna] = $valor;
			if($this->control[$id]['estado']!="i" && $this->control[$id]['estado']!="d"){
				$this->actualizar_estructura_control($id,"u");
			}		
		}else{
			throw new excepcion_toba("La columna '$columna' no es valida");
		}
	}
	//-------------------------------------------------------------------------------

	public function set_valor_columna($columna, $valor)
	//Setea todas las columnas con un valor
	{
		foreach(array_keys($this->control) as $registro){
			if($this->control[$registro]['estado']!="d"){
				$this->datos[$registro][$columna] = $valor;
				if($this->control[$registro]['estado']!="i"){
					$this->actualizar_estructura_control($registro,"u");
				}		
			}
		}
	}
	//-------------------------------------------------------------------------------
	//Simplificacion para los db_registross que manejan un solo registro. solo manejan el registro "0"
		
	public function set($registro)
	{
		if($this->get_cantidad_registros() === 0){
			$this->agregar_registro($registro);
		}else{
			$this->modificar_registro($registro, 0);
		}
	}
	
	public function get()
	{
		return $this->get_registro(0);
	}
	//-------------------------------------------------------------------------------

	public function procesar_registros($registros)
	{
		asercion::es_array($registros,"db_registros - El parametro no es un array.");
		//Controlo estructura
		foreach(array_keys($registros) as $id){
			if(!isset($registros[$id][apex_ei_analisis_fila])){
				throw new excepcion_toba("Para procesar un conjunto de registros es necesario indicar el estado ".
									"de cada uno utilizando una columna referenciada con la constante 'apex_ei_analisis_fila'");
			}
		}
		//Proceso las modificaciones sobre el db_registros
		foreach(array_keys($registros) as $id){
			$accion = $registros[$id][apex_ei_analisis_fila];
			unset($registros[$id][apex_ei_analisis_fila]);
			switch($accion){
				case "A":
					$this->agregar_registro($registros[$id]);
					break;	
				case "B":
					$this->eliminar_registro($id);
					break;	
				case "M":
					$this->modificar_registro($registros[$id], $id);
					break;	
			}
		}
	}

	//-------------------------------------------------------------------------------
	//------  EVENTOS disparados durante la ejecucion normal la ejecucion  ----------
	//-------------------------------------------------------------------------------
	/*
		Este es el lugar para meter validaciones, 
		si algo sale mal se deberia disparar una excepcion	
	*/

	private function notificar_controlador($evento, $param1=null, $param2=null)
	{
		if(isset($this->controlador)){
			$this->controlador->registrar_evento($this->id, $evento, $param1, $param2);
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------  VALIDACION de REGISTROS   ------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	private function validar_registro($registro, $id=null)
	//Valida un registro durante el procesamiento
	{
		$this->control_estructura_registro($registro);
		$this->control_nulos($registro);
		$this->control_valores_unicos_registro($registro, $id);
	}
	//-------------------------------------------------------------------------------

	private function control_estructura_registro($registro)
	//Controla que los campos del registro existan
	{
		foreach($registro as $campo => $valor){
			//SI el registro no esta en la lista de manipulables o en las secuencias...
			if( !(in_array($campo, $this->campos))  ){
					$this->log("El registro tiene una estructura incorrecta: El campo '$campo' ". 
							" se encuentra definido y no existe en el registro.");
					//toba::get_logger()->debug( debug_backtrace() );
					throw new excepcion_toba("El elemento posee una estructura incorrecta");
			}
		}
	}
	//-------------------------------------------------------------------------------

	private function control_valores_unicos_registro($registro, $id=null)
	//Controla que un registro no duplique los valores existentes
	{
		if(isset($this->no_duplicado))	
		{	//La iteracion de afuera es por cada constraint, 
			//si hay muchos es ineficiente, pero en teoria hay pocos (en general 1)
			foreach($this->no_duplicado as $columnas){
				foreach(array_keys($this->control) as $id_registro)	{
					//a) La operacion es una modificacion y estoy comparando con el registro contra su original
					if( isset($id) && ($id_registro == $id)) continue; //Sigo con el proximo
					//b) Comparo contra otro registro, que no este eliminado
					if($this->control[$id_registro]['estado']!="d"){
						$combinacion_existente = true;
						foreach($columnas as $columna)
						{
							if(!isset($registro[$columna])){
								//Si las columnas del constraint no estan completas, fuera
								return;
							}else{
								if($registro[$columna] != $this->datos[$id_registro][$columna]){
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
	
	private function control_nulos($registro)
	//Controla que un registro posea los valores OBLIGATORIOS
	{
		$mensaje_usuario = "El elemento posee valores incompletos";
		$mensaje_programador = "db_registros " . get_class($this). " [{$this->identificador}] - ".
					" Es necesario especificar un valor para el campo: ";
		if(isset($this->campos_no_nulo)){
			foreach($this->campos_no_nulo as $campo){
				if(isset($registro[$campo])){
					if((trim($registro[$campo])=="")||(trim($registro[$campo])=='NULL')){
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

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------  Carga de CAMPOS EXTERNOS   -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	private function actualizar_campos_externos()
	//Actualiza los campos externos despues de cargar el db_registros
	{
		foreach(array_keys($this->control) as $registro)
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
	//-------------------------------------------------------------------------------
	//---------------  SINCRONIZACION con la DB   -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	public function sincronizar($control_tope_minimo=true)
	//Sincroniza las modificaciones del db_registros con la DB
	{
		$this->log("Inicio SINCRONIZACION"); 
		if($control_tope_minimo){
			if( $this->tope_min_registros != 0){
				if( ( $this->get_cantidad_registros() < $this->tope_min_registros) ){
					$this->log("No se cumplio con el tope minimo de registros necesarios" );
					throw new excepcion_toba("Los registros cargados no cumplen con el TOPE MINIMO necesario");
				}
			}
		}
		$this->controlar_alteracion_db();
		// No puedo ejecutar los cambios en cualguier orden
		// Necesito ejecutar primero los deletes, por si el usuario borra algo y despues inserta algo igual
		$inserts = array(); $deletes = array();	$updates = array();
		foreach(array_keys($this->control) as $registro){
			switch($this->control[$registro]['estado']){
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
			//Actualizo la estructura interna que mantiene el estado de los registros
			$this->sincronizar_estructura_control();
			$this->log("Fin SINCRONIZACION: $modificaciones."); 
			return $modificaciones;
		}catch(excepcion_toba $e){
			if($this->utilizar_transaccion) abortar_transaccion();
			toba::get_logger()->debug($e);
			throw new excepcion_toba($e->getMessage());
		}
	}

	protected function insertar($id_registro)
	{
	}
	
	protected function modificar($id_registro)
	{
	}

	protected function eliminar($id_registro)
	{
	}

	//-------------------------------------------------------------------------------
	//------  EVENTOS de SINCRONIZACION  --------------------------------------------
	//-------------------------------------------------------------------------------
	/*
		Este es el lugar para meter validaciones, 
		si algo sale mal se deberia disparar una excepcion	
	*/

	protected function evt__pre_sincronizacion()
	{
	}
	
	protected function evt__post_sincronizacion()
	{
	}

	protected function evt__pre_insert($id)
	{
	}
	
	protected function evt__post_insert($id)
	{
	}
	
	protected function evt__pre_update($id)
	{
	}
	
	protected function evt__post_update($id)
	{
	}

	protected function evt__pre_delete($id)
	{
	}
	
	protected function evt__post_delete($id)
	{
	}

	//-------------------------------------------------------------------------------

	public function get_sql_inserts()
	{
		$sql = array();
		foreach(array_keys($this->control) as $registro){
			$sql[] = $this->generar_sql_insert($registro);
		}
		return $sql;
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//------------  Control de SINCRONISMO  -----------------------------------------
	//-------------------------------------------------------------------------------
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
	//-------------------------------------------------------------------------------
	//-- Memoria AUTONOMA -- Candidato a desaparecer
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	/*
		El db_registros se encarga solo de que su estado se mantenga en la sesion
	*/
	
	private function inicializar_memoria_autonoma()
	{
		$this->log("Se esta utilizando la memoria autonoma.");
		//Registro la finalizacion del objeto
		$this->posicion_finalizador = registrar_finalizacion( $this );
		//-- Si el db_registros fue creado en el request previo, lo recargo
		if( $this->existe_instanciacion_previa() ){
			//Si vengo del menu, no lo recargo.
			if( toba::get_hilo()->verificar_acceso_menu() ){
				$this->log("Acceso desde el MENU: no se recargan los datos");
			}else{
				$this->cargar_datos_sesion();
			}
		}
	}
	//-------------------------------------------------------------------------------

	private	function finalizar()
	//Finaliza la ejecucion del db_registros
	{
		$this->guardar_datos_sesion();
	}
	//-------------------------------------------------------------------------------

	private function desregistrar_finalizacion()
	//Desregistrar el destructor, por si se necesita eliminar un objeto registrado
	{
		desregistar_finalizacion($this->posicion_finalizador);
	}
	//-------------------------------------------------------------------------------

	private function cargar_datos_sesion()
	//Cargo el db_registros desde la sesion
	{
		$this->log("Cargar de SESION");
		$datos = toba::get_hilo()->recuperar_dato_global($this->identificador);
		//Traera un problema el pasaje por referencia
		$this->datos = $datos['datos'];
		$this->datos_orig = $datos['datos_orig'];
		$this->control = $datos['control'];
		$this->proximo_registro = $datos['proximo_registro'];
		$this->where = $datos['where'];
		$this->from = $datos['from'];
	}
	//-------------------------------------------------------------------------------

	private function guardar_datos_sesion()
	//Guardo datos en la sesion
	{
		$datos['where'] = $this->where;
		$datos['from'] = $this->from;
		$datos['datos'] = $this->datos;
		$datos['datos_orig'] = $this->datos_orig;
		$datos['control'] = $this->control;
		$datos['proximo_registro'] = $this->proximo_registro;
		toba::get_hilo()->persistir_dato_global($this->identificador, $datos, true);
	}
	//-------------------------------------------------------------------------------
	
	private function existe_instanciacion_previa()
	{
		return toba::get_hilo()->existe_dato_global($this->identificador);
	}
	//-------------------------------------------------------------------------------
}
?>