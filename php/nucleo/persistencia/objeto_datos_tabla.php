<?
require_once("nucleo/browser/clases/objeto.php");
define("apex_datos_clave_fila","dt_clave");
/*
	Tipos de dato (apex_tipo_datos)

	E    Entero                         
	N    Numero                         
	C    Caracter                       
	F    Fecha                          
	T    Timestamp                      
	L    Logico                         
	X    Caracter largo    
	B    Binario                        
*/
class objeto_datos_tabla extends objeto
{
	// Definicion asociada a la TABLA
	protected $clave;							// Columnas que constituyen la clave de la tabla
	protected $indice_columnas;
	//Constraints
	protected $no_duplicado;					// Combinacines de columnas que no pueden duplicarse
	// Definicion general
	protected $tope_max_filas;					// Cantidad de maxima de datos permitida.
	protected $tope_min_filas;					// Cantidad de minima de datos permitida.
	protected $fuente;							// Fuente de datos utilizada
	// Estructuras Centrales
	protected $cambios = array();				// Cambios realizados sobre los datos
	protected $datos = array();					// Datos cargados en el db_filas
	protected $datos_originales = array();		// Datos tal cual salieron de la DB (Control de SINCRO)
	protected $proxima_fila = 0;				// Posicion del proximo registro en el array de datos
	// Controlador
	protected $controlador = null;				// referencia al db_tablas del cual forma parte, si se aplica

	function __construct($id)
	{
		parent::objeto($id);
		//Armo un indice de las definiciones de las columnas y la lista de claves
		for($a=0; $a<count($this->info_columnas);$a++){
			$this->indice_columnas[ $this->info_columnas[$a]['columna'] ] = $a;
			if($this->info_columnas[$a]['pk']==1){
				$this->clave[] = $this->info_columnas[$a]['columna'];
			}
		}
		//Topes de registros
		if(trim($this->info_estructura['max_registros']!="")){
			$this->set_tope_max_filas( $this->info_estructura['max_registros'] );
		}
		if(trim($this->info_estructura['min_registros']!="")){
			$this->set_tope_max_filas( $this->info_estructura['min_registros'] );
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

	protected function log($txt)
	/*
		El objeto deberia tener directamente algo asi
	*/
	{
		toba::get_logger()->debug("db_filas  '" . get_class($this). "' " . $txt);
	}
	
	public function resetear()
	{
		$this->log("RESET!!");
		$this->datos = array();
		$this->datos_originales = array();
		$this->cambios = array();
		$this->proxima_fila = 0;
		$this->where = null;
		$this->from = null;
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

	//-------------------------------------------------------------------------------
	//-- Preguntas BASICAS
	//-------------------------------------------------------------------------------

	public function get_clave()
	{
		return $this->clave;
	}
	
	public function get_clave_valor($id_fila)
	{
		foreach( $this->clave as $columna ){
			$temp[$columna] = $this->get_fila_columna($id_fila, $columna);
		}	
		return $temp;
	}

	public function get_tope_max_filas()
	{
		return $this->tope_max_filas;	
	}

	public function get_tope_min_filas()
	{
		return $this->tope_min_filas;	
	}

	public function get_cantidad_filas_a_sincronizar()
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

	public function get_id_filas_a_sincronizar()
	{
		$ids = null;
		foreach(array_keys($this->cambios) as $fila){
			if( ($this->cambios[$fila]['estado'] == "d") ||
				($this->cambios[$fila]['estado'] == "i") ||
				($this->cambios[$fila]['estado'] == "u") ){
				$ids[] = $fila;
			}
		}
		return $ids;
	}

	function get_fuente_datos()
	{
		return $this->info['fuente_datos'];
	}

	//-------------------------------------------------------------------------------
	//-- Configuracion
	//-------------------------------------------------------------------------------

	public function set_tope_max_filas($cantidad)
	{
		if(is_numeric($cantidad) && $cantidad >= 0){
			$this->tope_max_filas = $cantidad;	
		}else{
			throw new excepcion_toba("El valor especificado en el TOPE MAXIMO de registros es incorrecto");
		}
	}

	public function set_tope_min_filas($cantidad)
	{
		if(is_numeric($cantidad) && $cantidad >= 0){
			$this->tope_min_filas = $cantidad;
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
	//-- ACCESO a FILAS   -----------------------------------------------------------
	//-------------------------------------------------------------------------------

	public function get_filas($condiciones=null, $usar_id_fila=false)
	//Las condiciones permiten filtrar la lista de registros que se devuelves
	//Usar ID registro hace que las claves del array devuelto sean las claves internas del dbr
	{
		$datos = null;
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
	
	public function get_id_fila_condicion($condiciones=null)
	/*
		Devuelve los registros que cumplen una condicion.
		Solo se chequea la condicion de igualdad.
		El parametro es un array asociativo de campo => valor.
		ATENCION, NO se utiliza chequeo de tipos
	*/
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
			foreach( array_keys($condiciones) as $campo){
				if(!in_array($campo, $this->campos)){
					throw new excepcion_toba("El campo '$campo' no existe. No es posible filtrar por dicho campo");
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

	public function get_fila($id)
	{
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

	public function get_fila_columna($id, $columna)
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
		foreach(array_keys($this->cambios) as $fila){
			if($this->cambios[$fila]['estado']!="d"){
				$temp[] = $this->datos[$fila][$columna];
			}
		}
		return $temp;
	}
	//-------------------------------------------------------------------------------
	
	public function get_cantidad_filas()
	{
		$a = 0;
		foreach(array_keys($this->cambios) as $id_fila){
			if($this->cambios[$id_fila]['estado']!="d")	$a++;
		}
		return $a;
	}
	
	public function existe_fila($id)
	{
		if(! isset($this->datos[$id]) ){
			return false;			
		}
		if($this->cambios[$id]['estado']=="d"){
			return false;
		}
		return true;
	}

	//-------------------------------------------------------------------------------
	//-- ALTERACION de FILAS  ------------------------------------------------------
	//-------------------------------------------------------------------------------

	public function nueva_fila($fila)
	{
		if( $this->tope_max_filas != 0){
			if( !($this->get_cantidad_filas() < $this->tope_max_filas) ){
				throw new excepcion_toba("No es posible agregar FILAS (TOPE MAX.)");
			}
		}
		$this->notificar_controlador("ins", $fila);
		//Saco el campo que indica la posicion del registro
		if(isset($fila[apex_datos_clave_fila])) unset($fila[apex_datos_clave_fila]);
		$this->validar_fila($fila);
		$this->datos[$this->proxima_fila] = $fila;
		$this->registrar_cambio($this->proxima_fila,"i");
		/*
			¿Como relaciono esto con el AP?
			//Actualizo los valores externos
			$this->actualizar_campos_externos_fila( $this->proxima_fila, "agregar");
		*/
		return $this->proxima_fila++;
	}
	//-------------------------------------------------------------------------------

	public function modificar_fila($id, $fila)
	{
		if(!$this->existe_fila($id)){
			$mensaje = "db_filas: MODIFICAR. No existe un registro con el INDICE indicado ($id)";
			toba::get_logger()->error($mensaje);
			throw new excepcion_toba($mensaje);
		}
		$this->notificar_controlador("upd", $fila, $id);
		//Saco el campo que indica la posicion del registro
		if(isset($fila[apex_datos_clave_fila])) unset($fila[apex_datos_clave_fila]);
		$this->validar_fila($fila, $id);
		//Actualizo los valores
		foreach(array_keys($fila) as $clave){
			$this->datos[$id][$clave] = $fila[$clave];
		}
		if($this->cambios[$id]['estado']!="i"){
			$this->registrar_cambio($id,"u");
		}
		/*
			¿Como relaciono esto con el AP?
			//Actualizo los valores externos
			$this->actualizar_campos_externos_fila($id,"modificar");
		*/
	}
	//-------------------------------------------------------------------------------

	public function eliminar_fila($id)
	{
		if(!$this->existe_fila($id)){
			$mensaje = "db_filas: MODIFICAR. No existe un registro con el INDICE indicado ($id)";
			toba::get_logger()->error($mensaje);
			throw new excepcion_toba($mensaje);
		}
		$this->notificar_controlador("del", $id);
		if($this->cambios[$id]['estado']=="i"){
			unset($this->cambios[$id]);
			unset($this->datos[$id]);
		}else{
			$this->registrar_cambio($id,"d");
		}
	}
	//-------------------------------------------------------------------------------

	public function eliminar_filas()
	//Elimina todos los registros
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

	public function set_fila_columna_valor($id, $columna, $valor)
	{
		if( in_array($columna, $this->campos) ){
			$this->datos[$id][$columna] = $valor;
			if($this->cambios[$id]['estado']!="i" && $this->cambios[$id]['estado']!="d"){
				$this->registrar_cambio($id,"u");
			}		
		}else{
			throw new excepcion_toba("La columna '$columna' no es valida");
		}
	}
	//-------------------------------------------------------------------------------

	public function set_columna_valor($columna, $valor)
	//Setea todas las columnas con un valor
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

	public function procesar_filas($filas)
	//Procesamiento de un conjunto de filas
	{
		asercion::es_array($filas,"db_filas - El parametro no es un array.");
		//Controlo estructura
		foreach(array_keys($filas) as $id){
			if(!isset($filas[$id][apex_ei_analisis_fila])){
				throw new excepcion_toba("Para procesar un conjunto de registros es necesario indicar el estado ".
									"de cada uno utilizando una columna referenciada con la constante 'apex_ei_analisis_fila'");
			}
		}
		//Proceso las modificaciones sobre el db_filas
		foreach(array_keys($filas) as $id){
			$accion = $filas[$id][apex_ei_analisis_fila];
			unset($filas[$id][apex_ei_analisis_fila]);
			switch($accion){
				case "A":
					$this->agregar_fila($filas[$id]);
					break;	
				case "B":
					$this->eliminar_fila($id);
					break;	
				case "M":
					$this->modificar_fila($filas[$id], $id);
					break;	
			}
		}
	}

	//-------------------------------------------------------------------------------
	// Simplificacion para los casos en que se utiliza una sola fila
		
	public function set($fila)
	{
		if($this->get_cantidad_filas() === 0){
			$this->agregar_fila($fila);
		}else{
			$this->modificar_fila($fila, 0);
		}
	}
	
	public function get()
	{
		return $this->get_fila(0);
	}

	//-------------------------------------------------------------------------------
	//--  Relacion con el CONTROLADOR
	//-------------------------------------------------------------------------------

	public function registrar_controlador($controlador)
	{
		/*
			ATENCION, el manejo de controladores debe ser consistente con
						el mecanismo de serializacion, ya que hay que evitar las referencias
						circulares porque van a destruir la memoria
			-->	Hacer una implementacion con __sleep y __wakeup
		*/
		//$this->cambiosador = $controlador;
	}

	private function notificar_controlador($evento, $param1=null, $param2=null)
	{
		if(isset($this->cambiosador)){
			$this->cambiosador->registrar_evento($this->id, $evento, $param1, $param2);
		}
	}

	//-------------------------------------------------------------------------------
	//-- VALIDACION de FILAS   ----------------------------------------
	//-------------------------------------------------------------------------------

	private function validar_fila($fila, $id=null)
	//Valida un registro durante el procesamiento
	{
		$this->control_estructura_fila($fila);
		$this->control_valores_unicos_fila($fila, $id);
	}
	//-------------------------------------------------------------------------------

	public function control_estructura_fila($fila)
	//Controla que los campos del registro existan
	{
		foreach($fila as $campo => $valor){
			//SI el registro no esta en la lista de manipulables o en las secuencias...
			if( !(isset($this->indice_columnas[$campo]))  ){
					$this->log("El registro tiene una estructura incorrecta: El campo '$campo' ". 
							" se encuentra definido y no existe en el registro.");
					//toba::get_logger()->debug( debug_backtrace() );
					throw new excepcion_toba("El elemento posee una estructura incorrecta");
			}
		}
	}
	//-------------------------------------------------------------------------------

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

	/*
		Controles previos a la sincronizacion
		Esto va a aca o en el AP??
	*/

	private function control_nulos($fila)
	//Controla que un registro posea los valores OBLIGATORIOS
	{
		$mensaje_usuario = "El elemento posee valores incompletos";
		$mensaje_programador = "db_filas " . get_class($this). " [{$this->identificador}] - ".
					" Es necesario especificar un valor para el campo: ";
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

	function control_tope_minimo_filas()
	{
		$control_tope_minimo=true;
		$this->log("Inicio SINCRONIZACION"); 
		if($control_tope_minimo){
			if( $this->tope_min_filas != 0){
				if( ( $this->get_cantidad_filas() < $this->tope_min_filas) ){
					$this->log("No se cumplio con el tope minimo de registros necesarios" );
					throw new excepcion_toba("Los registros cargados no cumplen con el TOPE MINIMO necesario");
				}
			}
		}
	}

	//-------------------------------------------------------------------------------
	//-- PERSISTENCIA  -------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function get_persistidor()
	//Devuelve el persistidor por defecto
	{
		require_once("ap_tabla_db_s.php");
		return new ap_tabla_db_s( $this );
	}

	/*
		Acceso al AP interno
		--------------------
		
			Tiene sentido encapsular al persistidor?
			(¿O es mejor que el cliente lo solicite y trabaje directamente sobre el?)
	*/

	function cargar_datos($where=null, $from=null)
	{
		$ap = $this->get_persistidor();
		//ei_arbol($ap->info());
		$ap->cargar_datos($where, $from);
	}

	function sincronizar()
	{
		$ap = $this->get_persistidor();
		return $ap->sincronizar();
	}

	//-------------------------------------------------------------------------------
	//-- API para el persitidor
	//-------------------------------------------------------------------------------

	public function set_datos($datos)
	//El AP entrega un conjunto de datos al objeto_datos_tabla
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
		$this->generar_estructura_cambios();
		//Le saco los caracteres de escape a los valores traidos de la DB
		for($a=0;$a<count($this->datos);$a++){
			foreach(array_keys($this->datos[$a]) as $columna){
				$this->datos[$a][$columna] = stripslashes($this->datos[$a][$columna]);
			}	
		}
		//Actualizo la posicion en que hay que incorporar al proximo registro
		$this->proxima_fila = count($this->datos);	
	}

	public function get_datos()
	{
		return $this->datos;
	}

	public function get_cambios()
	{
		return $this->cambios;	
	}

	public function get_datos_originales()
	{
		return $this->datos_originales;
	}

	public function get_columnas()
	{
		return $this->info_columnas;
	}
	
	public function get_indice_columnas()
	{
		return $this->indice_columnas;
	}

	public function get_fuente()
	{
		return $this->info["fuente"];
	}

	public function get_tabla()
	{
		return $this->info_estructura['tabla'];
	}

	public function get_alias()
	{
		return $this->info_estructura['alias'];
	}

	//-------------------------------------------------------------------------------
	//-- Mantenimiento de la estructura de control ----------------------------------
	//-------------------------------------------------------------------------------

	protected function generar_estructura_cambios()
	{
		//Genero la estructura de control
		$this->cambios = array();
		for($a=0;$a<count($this->datos);$a++){
			$this->cambios[$a]['estado']="db";
			$this->cambios[$a]['clave']= $this->get_clave_valor($a);
		}
	}
	
	protected function registrar_cambio($fila, $estado)
	{
		$this->cambios[$fila]['estado'] = $estado;
	}
	//-------------------------------------------------------------------------------
}
?>