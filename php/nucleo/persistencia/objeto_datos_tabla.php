<?
require_once("nucleo/browser/clases/objeto.php");

class objeto_datos_tabla extends objeto
{
	// Definicion asociada a la TABLA
	protected $clave;							// Columnas que constituyen la clave de la tabla
	protected $campos;							// Campos del db_registros
	//Constraints
	protected $no_duplicado;					// Combinacines de columnas que no pueden duplicarse
	// Definicion general
	protected $tope_max_registros;				// Cantidad de maxima de datos permitida.
	protected $tope_min_registros;				// Cantidad de minima de datos permitida.
	protected $fuente;							// Fuente de datos utilizada
	// Estructuras Centrales
	protected $control = array();				// Estructura de control
	protected $datos = array();					// Datos cargados en el db_registros
	protected $datos_originales = array();		// Datos tal cual salieron de la DB (Control de SINCRO)
	protected $proximo_dato = 0;				// Posicion del proximo registro en el array de datos
	protected $controlador = null;				// referencia al db_tablas del cual forma parte, si se aplica
	// Servicios activados por metodos

	function __construct($id)
	{
		parent::objeto($id);		
		if(trim($this->info_estructura['max_registros']!="")){
			$this->set_tope_max_registros( $this->info_estructura['max_registros'] );
		}
		if(trim($this->info_estructura['min_registros']!="")){
			$this->set_tope_max_registros( $this->info_estructura['min_registros'] );
		}
	}

	function obtener_definicion_db()
	{
		$sql = parent::obtener_definicion_db();
		//------------- Info base de la estructura ----------------
		$sql["info_estructura"]["sql"] = "SELECT			tabla,
														alias,
														min_registros,
														max_registros
					 FROM		apex_objeto_db_registros
					 WHERE	proyecto='".$this->id[0]."'	
					 AND		objeto='".$this->id[1]."';";
		$sql["info_estructura"]["estricto"]="1";
		$sql["info_estructura"]["tipo"]="1";
		//------------ Columnas ----------------
		$sql["info_columnas"]["sql"] = "SELECT	proyecto,
						objeto 			,	
						col_id			,	
						columna			,	
						tipo			,	
						pk				,	
						secuencia		,
						largo			,	
						no_nulo			,	
						no_nulo_db	
					 FROM		apex_objeto_db_registros_col 
					 WHERE		proyecto = '".$this->id[0]."'
					 AND		objeto = '".$this->id[1]."';";
		$sql["info_columnas"]["tipo"]="x";
		$sql["info_columnas"]["estricto"]="1";		
		return $sql;
	}
	
	//-------------------------------------------------------------------------------
	//------  API de persistencia  --------------------------------------------------
	//-------------------------------------------------------------------------------

	function get_persistidor_por_defecto()
	{
		require_once("ap_tabla_db_s.php");
		$ap =  new ap_tabla_db_s();
		$ap->set_datos_tabla($this);
		return $ap;
	}
	/*
		Tiene sentido que exista una configuracion que use el persistidor por composicion
		(para no tener que perdir el AP y ejecutar metodos en el)
	*/

	//-------------------------------------------------------------------------------
	//------  Servicios al PERSISTIDOR  ------------------------------------------
	//-------------------------------------------------------------------------------

	function get_datos()
	{
		
	}

	function get_cambios()
	{
		
	}

	function get_columnas()
	{
		
	}
	
	function get_fuente()
	{
		$this->info["fuente"];
	}

	function get_tabla()
	{
		$this->info_estructura['tabla'];
		
	}

	//----------------------------------------------------------------
	//---------  Cumplir la interface que reclama el CI -------------
	//----------------------------------------------------------------
	function agregar_controlador(){}
	function inicializar($parametros)
	{
		$this->id_en_padre = $parametros['id'];		
	}
	//function cargar_datos(){}
	function get_lista_eventos(){
		 return array();
	}
	function disparar_eventos(){}
	//----------------------------------------------------------------
	//----------------------------------------------------------------

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

	public function get_clave()
	{
		return $this->clave;
	}
	
	public function get_clave_valor($id_registro)
	{
		foreach( $this->clave as $clave ){
			$temp[$clave] = $this->get_registro_valor($id_registro, $clave);
		}	
		return $temp;
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
	//-- Configuracion
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
	}

	function set_datos($datos)
	{
		$this->log("Carga de datos");
		$this->datos = $datos;
		//Controlo que no se haya excedido el tope de registros
		if( $this->tope_max_registros != 0){
			if( $this->tope_max_registros < count( $this->datos ) ){
				//Hay mas datos que los que permite el tope, todo mal
				$this->datos = null;
				$this->log("Se sobrepaso el tope maximo de registros en carga: " . count( $this->datos ) . " registros" );
				throw new excepcion_toba("Los registros cargados superan el TOPE MAXIMO de registros");
			}
		}
		
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

	public function agregar_registro($registro)
	{
		if( $this->tope_max_registros != 0){
			if( !($this->get_cantidad_registros() < $this->tope_max_registros) ){
				throw new excepcion_toba("No es posible agregar registros (TOPE MAX.)");
			}
		}
		$this->notificar_controlador("ins", $registro);
		//Saco el campo que indica la posicion del registro
		if(isset($registro[apex_db_registros_clave])) unset($registro[apex_db_registros_clave]);
		$this->validar_registro($registro);
		//$registro[apex_db_registros_clave]=$this->proximo_registro;
		$this->datos[$this->proximo_registro] = $registro;
		$this->actualizar_estructura_control($this->proximo_registro,"i");
		//Actualizo los valores externos
		$this->actualizar_campos_externos_registro( $this->proximo_registro, "agregar");
		return $this->proximo_registro++;
		
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
	//---------------  VALIDACION de DATOS   ----------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	private function validar_registro($registro, $id=null)
	//Valida un registro durante el procesamiento
	{
		$this->control_estructura_registro($registro);
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
	//-----  Controles previos a la sincronizacion  ---------------------------------
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

	function control_tope_minimo_registros()
	{
		$control_tope_minimo=true;
		$this->log("Inicio SINCRONIZACION"); 
		if($control_tope_minimo){
			if( $this->tope_min_registros != 0){
				if( ( $this->get_cantidad_registros() < $this->tope_min_registros) ){
					$this->log("No se cumplio con el tope minimo de registros necesarios" );
					throw new excepcion_toba("Los registros cargados no cumplen con el TOPE MINIMO necesario");
				}
			}
		}
	}
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
}
?>