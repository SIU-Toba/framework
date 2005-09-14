<?
require_once("nucleo/browser/clases/objeto.php");
require_once("tipo_datos.php");
define("apex_datos_clave_fila","x_dbr_clave");
/*
	FALTA:
		 - Control del FK y PK
*/
class objeto_datos_tabla extends objeto
{
	protected $persistidor;						// Mantiene el persistidor del OBJETO
	// Definicion asociada a la TABLA
	protected $clave;							// Columnas que constituyen la clave de la tabla
	protected $columnas;
	protected $posee_columnas_ext = false;		// Indica si la tabla posee columnas externas (cargadas a travez de un mecanismo especial)
	//Constraints
	protected $no_duplicado;					// Combinacines de columnas que no pueden duplicarse
	// Definicion general
	protected $tope_max_filas;					// Cantidad de maxima de datos permitida.
	protected $tope_min_filas;					// Cantidad de minima de datos permitida.
	protected $fuente;							// Fuente de datos utilizada
	// ESTADO
	protected $cambios = array();				// Cambios realizados sobre los datos
	protected $datos = array();					// Datos cargados en el db_filas
	protected $datos_originales = array();		// Datos tal cual salieron de la DB (Control de SINCRO)
	protected $proxima_fila = 0;				// Posicion del proximo registro en el array de datos
	protected $clave_actual;
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
		$propiedades[] = "clave_actual";
		return $propiedades;
	}

	public function elemento_toba()
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

	//-------------------------------------------------------------------------------
	//--  Relacion con otros ELEMENTOS
	//-------------------------------------------------------------------------------

	public function registrar_contenedor($contenedor)
	{
		$this->contenedor = $contenedor;
	}

	function agregar_relacion_con_padre($relacion)
	{
		$this->relaciones_con_padres[] = $relacion;
	}
	
	function agregar_relacion_con_hijo($relacion)
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

	function notificar_hijos_carga()
	//Aviso a la RELACION que el componente PADRE se CARGO
	{
		if(isset($this->relaciones_con_hijos)){
			for($a=0;$a<count($this->relaciones_con_hijos);$a++){
				$this->relaciones_con_hijos[$a]->evt__carga_padre();
			}
		}
	}

	function notificar_hijos_sincronizacion()
	//Aviso a la RELACION que el componente PADRE se SINCRONIZO
	{
		if(isset($this->relaciones_con_hijos)){
			for($a=0;$a<count($this->relaciones_con_hijos);$a++){
				$this->relaciones_con_hijos[$a]->evt__sincronizacion_padre();
			}
		}
	}

	function notificar_hijos_eliminacion()
	//Aviso a la RELACION que el componente PADRE se esta por eliminar
	{
		if(isset($this->relaciones_con_hijos)){
			for($a=0;$a<count($this->relaciones_con_hijos);$a++){
				$this->relaciones_con_hijos[$a]->evt__eliminacion_padre();
			}
		}
	}

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

	public function get_id_filas_a_sincronizar( $cambios=array("d","i","u") )
	{
		$ids = null;
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
		$this->notificar_contenedor("ins", $fila);
		//Saco el campo que indica la posicion del registro
		if(isset($fila[apex_datos_clave_fila])) unset($fila[apex_datos_clave_fila]);
		$this->validar_fila($fila);
		//SI existen columnas externas, completo la fila con las mismas
		if($this->posee_columnas_ext){
			$fila = $this->get_persistidor()->completar_campos_externos_fila($fila,"ins");
		}
		$this->datos[$this->proxima_fila] = $fila;
		$this->registrar_cambio($this->proxima_fila,"i");
		return $this->proxima_fila++;
	}
	//-------------------------------------------------------------------------------

	public function modificar_fila($id, $fila)
	{
		if(!$this->existe_fila($id)){
			$mensaje = $this->get_txt() . " MODIFICAR. No existe un registro con el INDICE indicado ($id)";
			toba::get_logger()->error($mensaje);
			throw new excepcion_toba($mensaje);
		}
		//Saco el campo que indica la posicion del registro
		if(isset($fila[apex_datos_clave_fila])) unset($fila[apex_datos_clave_fila]);
		$this->validar_fila($fila, $id);
		if($this->posee_columnas_ext){
			$this->get_persistidor()->completar_campos_externos_fila($fila,"upd");
		}
		$this->notificar_contenedor("pre_modificar", $fila, $id);
		//Actualizo los valores
		foreach(array_keys($fila) as $clave){
			$this->datos[$id][$clave] = $fila[$clave];
		}
		if($this->cambios[$id]['estado']!="i"){
			$this->registrar_cambio($id,"u");
		}
		$this->notificar_contenedor("post_modificar", $fila, $id);
	}
	//-------------------------------------------------------------------------------

	public function eliminar_fila($id)
	{
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
		
	public function set($fila)
	{
		if($this->get_cantidad_filas() === 0){
			$this->nueva_fila($fila);
		}else{
			$this->modificar_fila(0, $fila);
		}
	}
	
	public function get()
	{
		return $this->get_fila(0);
	}

	//-------------------------------------------------------------------------------
	//-- VALIDACION en LINEA
	//-------------------------------------------------------------------------------

	private function validar_fila($fila, $id=null)
	//Valida un registro durante el procesamiento
	{
		$this->evt__validar_ingreso($fila, $id);
		$this->control_estructura_fila($fila);
		$this->control_valores_unicos_fila($fila, $id);
	}

	protected function evt__validar_ingreso($fila, $id=null){}

	//-------------------------------------------------------------------------------

	public function control_estructura_fila($fila)
	//Controla que los campos del registro existan
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

	function validar()
	{
		$ids = $this->get_id_filas_a_sincronizar( array("u","i") );
		if(isset($ids)){
			foreach($ids as $id){
				//$this->control_nulos($fila);
				$this->evt__validar_fila( $this->datos[$id] );
			}
		}
	}
	
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
	public function control_tope_minimo_filas()
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

	public function get_persistidor()
	//Devuelve el persistidor PREDEFINIDO
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
		}
		return $this->persistidor;
	}

	public function cargar($id)
	{
		//$this->resetear();
		$ap = $this->get_persistidor();
		$ap->cargar($id);
		$this->clave_actual = $id;
	}

	public function sincronizar()
	{
		//Control de topes
		if( $this->tope_min_filas != 0){
			if( ( $this->get_cantidad_filas() < $this->tope_min_filas) ){
				$this->log("No se cumplio con el tope minimo de registros necesarios" );
				throw new excepcion_toba("Los registros cargados no cumplen con el TOPE MINIMO necesario");
			}
		}
		$ap = $this->get_persistidor();
		$modif = $ap->sincronizar();
		return $modif;
	}

	public function eliminar()
	{
		$this->eliminar_filas();
		$ap = $this->get_persistidor();
		$ap->eliminar();
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

	//-------------------------------------------------------------------------------
	//-- Comunicacion con el Administrador de Persistencia
	//-------------------------------------------------------------------------------

	/*--- Del AP a mi ---*/

	public function set_datos($datos)
	//El AP entrega un conjunto de datos cargados al objeto_datos_tabla
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

	public function notificar_fin_sincronizacion()
	//El AP avisa que termino la sincronizacion
	{
		$this->generar_estructura_cambios();
		$this->notificar_hijos_sincronizacion();
	}

	/*--- De mi al AP ---*/

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
		return $this->columnas;
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

	public function posee_columnas_externas()
	{
		return $this->posee_columnas_ext;
	}

	//-------------------------------------------------------------------------------
	//-- Cosas internas
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

	protected function log($txt)
	/*
		El objeto deberia tener directamente algo asi
	*/
	{
		toba::get_logger()->debug($this->get_txt() . get_class($this). "' " . $txt);
	}

	//-------------------------------------------------------------------------------
}
?>