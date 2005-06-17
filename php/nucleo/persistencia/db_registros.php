<?
define("apex_buffer_separador","%");

class db_registros
{
/*
	PROBLEMAS NO RESUELTOS
	----------------------
	
		1) Usar el tope de registros para forzar la interface

		2) Reveer el nombre de las interfaces
			( Alguna relacionadas a eventos reportados a controladores )

		3) Ver la forma de persistir el buffer en la memoria
			( la actual podria denominarse "autonoma", pero deberia haber una que
				funcione con el metodo "mantener_estado_sesion" para relacionarse de una
				manera mas fluida con el esquema de CIs

		4) La funcion cosmetica tambien tiene que trabajar con DAOS

		5) validaciones
			- El control de valores duplicados no es multicolumna, cuando las claves son compuestas no sirve
			- El control de nulos deberia desactivarse si la columna es una secuencia

		6) Control de perdida de sincronizacion (asistencia a un modelo optimista de transaccion)			

		7) Manejo de datos por referencia para disminuir la cantidad de memoria utilizada??

		8) Es necesario implementar UPDATES que solo incluyan columnas afectadas??

 		9) Hay una alternativa para las claves internas de cada registro?
*/
	protected $log;						//Referencia al LOGGER
	protected $solicitud;				//Referencia a la solicitud
	protected $definicion;				//Definicion que indica la construccion del BUFFER
	protected $fuente;					//Fuente de datos utilizada
	protected $identificador;			//Identificador del registro
	protected $campos;					//Campos del BUFFER
	protected $campos_secuencia;		
	protected $campos_manipulables;		
	protected $where;					//Condicion utilizada para cargar datos - WHERE
	protected $from;					//Condicion utilizada para cargar datos - FROM
	protected $control = array();		//Estructura de control
	protected $datos = array();			//Datos cargados en el BUFFER
	protected $datos_orig = array();	//Datos tal cual salieron de la DB (Control de SINCRO)
	protected $proximo_registro = 0;	//Posicion del proximo registro en el array de datos
	protected $control_sincro_db;		//Se activa el control de sincronizacion con la DB?
	protected $posicion_finalizador;	//Posicion del objeto en el array de finalizacion
	protected $msg_error_sincro = 		"Error interno. Los datos no fueron guardados.";
	protected $baja_logica = false;		// Baja logica. (delete = update de una columna a un valor)
	protected $baja_logica_columna;		// Columna de la baja logica
	protected $baja_logica_valor;		// Valor de la baja logica
	protected $controlador = null;		// referencia al db_tablas del cual forma parte, si se aplica
	protected $utilizar_transaccion;	// La sincronizacion con la DB se ejecuta dentro de una transaccion
	protected $memoria_autonoma;		// Se persiste en la sesion por si mismo
	protected $tope_registros;			// Cantidad de registros permitida. 0 = n registros

	function __construct($id, $definicion, $fuente, $tope_registros=0, $utilizar_transaccion=false, $memoria_autonoma=true)
	{
		$this->solicitud = toba::get_solicitud();
		$this->log = toba::get_logger();		
		$this->identificador = $id; //ID unico, para buscarse en la sesion
		$this->definicion = $definicion;
		$this->fuente = $fuente;
		$this->tope_registros = $tope_registros;
		$this->utilizar_transaccion = $utilizar_transaccion;
		$this->memoria_autonoma = $memoria_autonoma;
		//la interaccion con la interface?
		if(isset($this->definicion["control_sincro"])){
			if($this->definicion["control_sincro"]=="1"){	
				$this->control_sincro_db = true;
			}else{
				$this->control_sincro_db = false;
			}
		}else{
			$this->control_sincro_db = false;
		}
		//Registro la finalizacion del objeto
		$this->posicion_finalizador = registrar_finalizacion( $this );
		//Inicializar la estructura de campos
		//ATENCION, hay que analizar si no es mas eficiente dejarlo en la sesion
		$this->inicializar_definicion_campos();
		//-- Si el BUFFER fue creado en el request previo, lo recargo
		if( $this->existe_instanciacion_previa() ){
			//Si vengo del menu, no lo recargo.
			if( $this->solicitud->hilo->verificar_acceso_menu() ){
				$this->log("Acceso desde el MENU: no se recargan los datos");
			}else{
				$this->cargar_datos_sesion();
			}
		}
	}
	//-------------------------------------------------------------------------------

	function obtener_definicion()
	{
		return $this->definicion;
	}
	//-------------------------------------------------------------------------------

	function get_clave()
	{
		return $this->definicion['clave'];
	}
	
	function get_clave_valor($id_registro)
	{
		foreach( $this->definicion['clave'] as $clave ){
			$temp[$clave] = $this->obtener_registro_valor($id_registro, $clave);
		}	
		return $temp;
	}
	
	function finalizar()
	//Finaliza la ejecucion del buffer
	{
		$this->guardar_datos_sesion();
	}
	//-------------------------------------------------------------------------------

	function desregistrar_finalizacion()
	//Desregistrar el destructor, por si se necesita eliminar un objeto registrado
	{
		desregistar_finalizacion($this->posicion_finalizador);
	}
	//-------------------------------------------------------------------------------

	public function info($mostrar_datos=false)
	//Informacion del buffer
	{
		$estado['control']=$this->control;
		$estado['proximo_registro']=$this->proximo_registro;
		$estado['where']=$this->where;
		$estado['from']=$this->from;
		if($mostrar_datos) $estado['datos']=$this->datos;
		return $estado;
	}

	public function registrar_controlador($controlador)
	{
		$this->controlador = $controlador;
	}

	public function set_baja_logica($columna, $valor)
	{
		$this->baja_logica = true;
		$this->baja_logica_columna = $columna;
		$this->baja_logica_valor = $valor;	
	}

	public function get_tope_registros()
	{
		return $this->tope_registros;	
	}

	protected function log($txt)
	{
		$this->log->debug("db_registros  '" . get_class($this). "' - [{$this->identificador}] - " . $txt);
	}
	
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------  Manejo GENERAL de DATOS  ---------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function cargar_datos_clave($id)
	{
		/*
			Esta funcion deberia mapear un ID expresado como un array
			y transformarlo en un WHERE
		*/		
	}

	function cargar_datos($where=null, $from=null)
	//Cargar datos en el BUFFER (DB o SESION). 
	{
		if(isset($where)){
			if(!is_array($where)){
				throw new excepcion_toba("El WHERE debe ser un array");
			}	
		}
		if( $this->existe_instanciacion_previa() ){
			//Es posible que el usuario haya cambiado de WHERE
			if( !($this->controlar_conservacion_where($where)) ){
				$this->cargar_datos_db($where, $from);
			}
		}else{
			$this->cargar_datos_db($where, $from);
		}
	}
	//-------------------------------------------------------------------------------
	
	function controlar_conservacion_where($where)
	//Si el consumidor cambia el WHERE, hay que traer datos de
	//la base nuevamente. Mejorar controles
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

	function cargar_datos_db($where=null, $from=null)
	//Cargo los BUFFERS con datos de la DB
	//ATENCION: Los datos solo se cargan si se le pasa como parametro un WHERE
	{
		$this->log("Cargar de DB");
		$this->where = $where;
		$this->from = $from;
		//Obtengo los datos de la DB
		if(isset($where)){
			//ei_arbol($where,"WHERE con datos");
			$this->datos = $this->cargar_db();
		}else{
			$this->datos = array();
		}
		//ei_arbol($this->datos);
		//Se solicita control de SINCRONIA a la DB?
		if(isset($this->definicion["control_sincro"])){
			if($this->definicion["control_sincro"]=="1"){	
				$this->datos_orig = $this->datos;
			}
		}
		$this->generar_estructura_control();
		//Le saco los caracteres de escape a los valores traidos de la DB
		for($a=0;$a<count($this->datos);$a++){
			foreach(array_keys($this->datos[$a]) as $columna){
				$this->datos[$a][$columna] = stripslashes($this->datos[$a][$columna]);
			}	
		}
		//Actualizo la posicion en que hay que incorporar al proximo registro
		$this->proximo_registro = count($this->datos);	
		//Lleno las columnas basadas en valores EXTERNOS
		$this->actualizar_campos_externos();
	}
	//-------------------------------------------------------------------------------
	//-- Uso de la estructura de control
	//-------------------------------------------------------------------------------

	function generar_estructura_control()
	{
		//Genero la estructura de control
		$this->control = array();
		for($a=0;$a<count($this->datos);$a++){
			$this->control[$a]['estado']="db";
			//Creo la columna que referencia a la posicion del registro en el BUFFER
			$this->datos[$a][apex_buffer_clave]=$a;
		}
	}
	
	function actualizar_estructura_control($registro, $estado)
	{
		$this->control[$registro] = $estado;
	}

	function sincronizar_estructura_control()
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
	//-------------------------------------------------------------------------------

	function cargar_db($carga_estricta=false)
	//Cargo los BUFFERS con datos de la DB
	//Los datos son 
	{
		$db = toba::get_fuente($this->fuente);
		$sql = $this->generar_sql_select();//echo $sql . "<br>";
		//-- Intento cargar el BUFFER
		$rs = $db[apex_db_con]->Execute($sql);
		if(!is_object($rs)){
			$this->log->error("BUFFER  " . get_class($this). " [{$this->identificador}] - Error cargando datos, no se genero un RECORDSET" .
									$sql . " - " . $db[apex_db_con]->ErrorMsg());
			throw new excepcion_toba("Error cargando datos en el buffer. Verifique la definicion. $sql");
		}
		if($rs->EOF){
			if($carga_estricta){
				$this->log->error("BUFFER  " . get_class($this). " [{$this->identificador}] - " .
								"No se recuperarron DATOS. Se solicito carga estricta");
			}
			return null;
		}else{
			$datos =& $rs->getArray();
			//ei_arbol($datos);
			//Los campos NO SQL deberian estar metidos en el array
			if(isset($this->definicion['externa'])){
				foreach($this->definicion['externa'] as $externa){
					for($a=0;$a<count($datos);$a++){
						$datos[$a][$externa] = "";
					}
				}
			}
			return $datos; 
		}
	}
	//-------------------------------------------------------------------------------

	function cargar_datos_sesion()
	//Cargo el BUFFER desde la sesion
	{
		$this->log("Cargar de SESION");
		$datos = $this->solicitud->hilo->recuperar_dato_global($this->identificador);
		//Traera un problema el pasaje por referencia
		$this->datos = $datos['datos'];
		$this->datos_orig = $datos['datos_orig'];
		$this->control = $datos['control'];
		$this->proximo_registro = $datos['proximo_registro'];
		$this->where = $datos['where'];
		$this->from = $datos['from'];
	}
	//-------------------------------------------------------------------------------

	function guardar_datos_sesion()
	//Guardo datos en la sesion
	{
		$datos['where'] = $this->where;
		$datos['from'] = $this->from;
		$datos['datos'] = $this->datos;
		$datos['datos_orig'] = $this->datos_orig;
		$datos['control'] = $this->control;
		$datos['proximo_registro'] = $this->proximo_registro;
		$this->solicitud->hilo->persistir_dato_global($this->identificador, $datos, true);
	}
	//-------------------------------------------------------------------------------
	
	function existe_instanciacion_previa()
	{
		return $this->solicitud->hilo->existe_dato_global($this->identificador);
	}
	//-------------------------------------------------------------------------------

	function resetear()
	{
		$this->log("RESET!!");
		if($this->existe_instanciacion_previa()){
			$this->solicitud->hilo->eliminar_dato_global($this->identificador);
		}
		$this->datos = array();
		$this->datos_orig = array();
		$this->control = array();
		$this->proximo_registro = 0;
		$this->where = null;
		$this->from = null;
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-------  Primitivas de ACCESO a REGISTROS   -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	public function obtener_registros($condiciones=null)
	{
		$datos = null;
		foreach(array_keys($this->control) as $registro){
			//Si no esta eliminado, lo devuelvo
			if($this->control[$registro]['estado']!="d"){
				$datos[] = $this->datos[$registro];
			}
		}
		return $datos;
	}
	//-------------------------------------------------------------------------------
	
	public function obtener_registro($id)
	{
		if(isset($this->datos[$id])){
			return  $this->datos[$id];
		}else{
			return null;
		}
	}
	//-------------------------------------------------------------------------------

	public function obtener_registro_valor($id, $columna)
	{
		if(isset($this->datos[$id][$columna])){
			return  $this->datos[$id][$columna];
		}else{
			return null;
		}
	}
	//-------------------------------------------------------------------------------

	public function cantidad_registros()
	{
		return count($this->datos);
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-------  Primitivas de MODIFICACION de REGISTROS   ----------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function agregar_registro($registro)
	{
		$this->evt__agregar_registro($registro);
		$this->notificar_controlador("ins", $registro);
		//Saco el campo que indica la posicion del registro
		if(isset($registro[apex_buffer_clave])) unset($registro[apex_buffer_clave]);
		$this->validar_registro($registro);
		$registro[apex_buffer_clave]=$this->proximo_registro;
		$this->datos[$this->proximo_registro] = $registro;
		$this->actualizar_estructura_control($this->proximo_registro,"i");
		//Actualizo los valores externos
		$this->actualizar_campos_externos_registro( $this->proximo_registro, "agregar");
		$this->proximo_registro++;
	}
	//-------------------------------------------------------------------------------

	function modificar_registro($registro, $id)
	{
		if(!isset($this->datos[$id])){
			$mensaje = "BUFFER: MODIFICAR. No existe un registro con el INDICE indicado ($id)";
			$this->log->error($mensaje);
			throw new excepcion_toba($mensaje);
		}
		$this->evt__modificar_registro($registro, $id);
		$this->notificar_controlador("upd", $registro, $id);
		//Saco el campo que indica la posicion del registro
		if(isset($registro[apex_buffer_clave])) unset($registro[apex_buffer_clave]);
		$this->validar_registro($registro, $id);
		if($this->control[$id]['estado']=="i"){
			$this->datos[$id] = $registro;
			$this->datos[$id][apex_buffer_clave] = $id; 
		}else{
			$this->actualizar_estructura_control($id,"u");
			foreach(array_keys($registro) as $clave){
				$this->datos[$id][$clave] = $registro[$clave];
			}
			$this->datos[$id][apex_buffer_clave] = $id; 
		}
		//Actualizo los valores externos
		$this->actualizar_campos_externos_registro($id,"modificar");
	}
	//-------------------------------------------------------------------------------

	function eliminar_registro($id=null)
	{
		if(!isset($this->datos[$id])){
			$mensaje = "BUFFER: MODIFICAR. No existe un registro con el INDICE indicado ($id)";
			$this->log->error($mensaje);
			throw new excepcion_toba($mensaje);
		}
		$this->evt__eliminar_registro($id);
		$this->notificar_controlador("del", $id);
		if($this->control[$id]['estado']=="i"){
			unset($this->control[$id]);
			unset($this->datos[$id]);
		}else{
			$this->actualizar_estructura_control($id,"d");
		}
	}
	//-------------------------------------------------------------------------------

	function eliminar_registros()
	//Elimina todos los registros
	{
		$this->evt__eliminar_registros();
		foreach(array_keys($this->control) as $registro)
		{
			if($this->control[$registro]['estado']=="i"){
				unset($this->control[$registro]);
				unset($this->datos[$registro]);
			}else{
				$this->actualizar_estructura_control($registro,"d");
			}
		}
	}
	//-------------------------------------------------------------------------------

	function establecer_registro_valor($id, $columna, $valor)
	{
		if(isset($this->datos[$id][$columna])){
			$this->datos[$id][$columna] = $valor;
		}
	}
	//-------------------------------------------------------------------------------

	function establecer_valor_columna($columna, $valor)
	//Setea todas las columnas con un valor
	{
		foreach(array_keys($this->control) as $registro){
			if($this->control[$registro]['estado']!="d"){
				$this->datos[$registro][$columna] = $valor;
			}
		}
	}

	//-------------------------------------------------------------------------------
	//Simplificacion para los buffers que manejan un solo registro. solo manejan el registro "0"
		
	function set($registro)
	{
		if($this->cantidad_registros() === 0){
			$this->agregar_registro($registro);
		}else{
			$this->modificar_registro($registro, 0);
		}
	}
	
	function get()
	{
		return $this->obtener_registro(0);
	}

	//-------------------------------------------------------------------------------
	//------  EVENTOS disparados durante la ejecucion normal la ejecucion  ----------
	//-------------------------------------------------------------------------------
	/*
		Este es el lugar para meter validaciones, 
		si algo sale mal se deberia disparar una excepcion	
	*/

	function notificar_controlador($evento, $param1=null, $param2=null)
	{
		if(isset($this->controlador)){
			$this->controlador->registrar_evento($this->id, $evento, $param1, $param2);
		}
	}

	function evt__agregar_registro($registro)
	{
	}
	
	function evt__modificar_registro($registro, $id)
	{
	}
	
	function evt__eliminar_registro($id)
	{	
	}

	function evt__eliminar_registros()
	{	
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------  VALIDACION de REGISTROS   ------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function validar_registro($registro, $id=null)
	//Valida el registro
	{
		$this->control_estructura_registro($registro);
		$this->control_nulos($registro);
		$this->control_valores_unicos($registro, $id);
	}
	//-------------------------------------------------------------------------------

	function control_estructura_registro($registro)
	//Controla que los campos del registro existan
	{
		foreach($registro as $campo => $valor){
			//SI el registro no esta en la lista de manipulables o 
			//en las secuencias...
			if( !((in_array($campo, $this->campos_manipulables)) ||
				(in_array($campo,$this->campos_secuencia)) ) ){
					$this->log("El registro tiene una estructura incorrecta: El campo '$campo' ". 
							" se encuentra definido y no existe en el registro.");
					//$this->log->debug( debug_backtrace() );
					throw new excepcion_toba("El elemento posee una estructura incorrecta");
			}
		}
	}
	//-------------------------------------------------------------------------------

	function control_valores_unicos($registro, $id=null)
	//Controla que no se dupliquen valores unicos del BUFFER
	{
		foreach($this->campos_no_duplicados as $campo)
		{
			//Busco los valores existentes en la columna
			$valores_columna = $this->obtener_columna_valores($campo);
			//Si esto es llamado desde un MODIFICAR, 
			//tengo que sacar de la lista al propio registro
			if(isset($id)){
				unset($valores_columna[$id]);
			}
			if(is_array($valores_columna)){
				//Controlo que el nuevo valor no exista
				if(in_array($registro[$campo], $valores_columna)){
					$this->log("El valor '".$registro[$campo] ."' crea un duplicado " .
									" en el campo '" . $campo . "', definido como no_duplicado");
					//$this->log->debug( debug_backtrace() );
					throw new excepcion_toba("El elemento ya se encuentra definido");
				}
			}
		}
	}
	//-------------------------------------------------------------------------------
	
	function control_nulos($registro)
	//Controla que los valores obligatorios existan
	{
		$mensaje_usuario = "El elemento posee valores incompletos";
		$mensaje_programador = "BUFFER " . get_class($this). " [{$this->identificador}] - ".
					" Es necesario especificar un valor para el campo: ";
		foreach($this->campos_no_nulo as $campo){
			if(isset($registro[$campo])){
				if((trim($registro[$campo]==""))||(trim($registro[$campo]=="NULL"))){
					$this->log->error($mensaje_programador . $campo);
					throw new excepcion_toba($mensaje_usuario);
				}
			}else{
					$this->log->error($mensaje_programador . $campo);
					throw new excepcion_toba($mensaje_usuario);
			}
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------  Columnas cosmeticas   ----------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function actualizar_campos_externos()
	//Actualiza los campos externos despues de cargar el buffer
	{
		foreach(array_keys($this->control) as $registro)
		{
			$this->actualizar_campos_externos_registro($registro);
		}	
	}
	
	function actualizar_campos_externos_registro($id_registro, $evento=null)
	{
		//Itero planes de carga externa
		if(isset($this->definicion['carga_externa'])){
			foreach(array_keys($this->definicion['carga_externa']) as $carga)
			{
				//SI entre por un evento, tengo que controlar que la carga este
				//Activada para eventos, si no esta activada paso al siguiente
				if(isset($evento)){
					if(! $this->definicion['carga_externa'][$carga]['eventos_iu'] ){	
						continue;
					}
				}
				// - 1 - Obtengo el query
				$sql = $this->definicion['carga_externa'][$carga]['sql'];
				// - 2 - Reemplazo valores llave
				foreach($this->definicion['carga_externa'][$carga]['llave'] as $col_llave ){
					$valor_llave = $this->datos[$id_registro][$col_llave];
					$sql = ereg_replace( apex_buffer_separador . $col_llave . apex_buffer_separador, $valor_llave, $sql);
				}
				//echo "<pre>SQL: "  . $sql . "<br>";
				// - 3 - Ejecuto SQL
				$datos = consultar_fuente($sql, $this->fuente);//ei_arbol($datos);
				// - 4 - Seteo los valores recuperados en el registro
				foreach($this->definicion['carga_externa'][$carga]['col'] as $columna_externa ){
					$this->datos[$id_registro][$columna_externa] = $datos[0][$columna_externa];
				}
				//ei_arbol($this->datos);
			}
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------  SINCRONIZACION con la DB   -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function sincronizar()
	//Sincroniza las modificaciones del BUFFER con la DB
	{
		$this->log("Inicio SINCRONIZACION!"); 
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
			//-- DELETE --
			foreach($deletes as $registro){
				$this->evt__pre_delete($registro);
				$this->eliminar($registro);
				$this->evt__post_delete($registro);
			}
			//-- INSERT --
			foreach($inserts as $registro){
				$this->evt__pre_insert($registro);
				$this->insertar($registro);
				$this->evt__post_insert($registro);
			}
			//-- UPDATE --
			foreach($updates as $registro){
				$this->evt__pre_update($registro);
				$this->modificar($registro);
				$this->evt__post_update($registro);
			}
			$this->evt__post_sincronizacion();
			if($this->utilizar_transaccion) cerrar_transaccion();
			//Actualizo la estructura interna que mantiene el estado de los registros
			$this->sincronizar_estructura_control();
		}catch(excepcion_toba $e){
			if($this->utilizar_transaccion) abortar_transaccion();
			$this->log->debug($e);
			throw new excepcion_toba($e->getMessage());
		}
	}

	function insertar($id_registro)
	{
	}
	
	function modificar($id_registro)
	{
	}

	function eliminar($id_registro)
	{
	}

	//-------------------------------------------------------------------------------
	//------  EVENTOS de SINCRONIZACION  --------------------------------------------
	//-------------------------------------------------------------------------------
	/*
		Este es el lugar para meter validaciones, 
		si algo sale mal se deberia disparar una excepcion	
	*/

	function evt__pre_sincronizacion()
	{
	}
	
	function evt__post_sincronizacion()
	{
	}

	function evt__pre_insert($id)
	{
	}
	
	function evt__post_insert($id)
	{
	}
	
	function evt__pre_update($id)
	{
	}
	
	function evt__post_update($id)
	{
	}

	function evt__pre_delete($id)
	{
	}
	
	function evt__post_delete($id)
	{
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//------------  Control de SINCRONISMO  -----------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function controlar_alteracion_db()
	//Controla que los datos
	{
		/*
			Esto hay que pensarlo bien
		*/
	}

	function controlar_alteracion_db_array()
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

	function controlar_alteracion_db_timestamp()
	//Esto tiene que basarse en una forma generica de trabajar sobre tablas
	//(Una columna que posea el timestamp, y triggers que los actualicen)
	{
	}
	//-------------------------------------------------------------------------------
}
?>