<?
require_once("ap.php");
require_once("tipo_datos.php");

if (!defined("apex_db_registros_separador")) {
	define("apex_db_registros_separador","%"); //Por si ya esta definida en db_registros
}

/**
 * Administrador de persistencia a una tabla de DB desde un objeto_datos_tabla
 * Supone que la tabla de datos se va a mapear a algun tipo de estructura en una base de datos
 * 
 * @todo Poder desactivar el control de sincronizacion (¿se necesita esto?)
 * @todo Como se implementa la carga de columnas externas??
 * @todo Donde se hacen los controles pre-sincronizacion (nulos db)??
 * @todo Hay que definir el manejo de claves (en base a objeto_datos_relacion)	
 * @todo Esta clase no deberia utilizar ADOdb!!!
 * @package Objetos
 * @subpackage Persistencia
 */
 
class ap_tabla_db implements ap_tabla
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

	/**
	 * @param objeto_datos_tabla $datos_tabla Tabla que persiste
	 */
	function __construct($datos_tabla)
	{
		$this->objeto_tabla = $datos_tabla;
		$this->tabla = $this->objeto_tabla->get_tabla();
		$this->alias = $this->objeto_tabla->get_alias();
		$this->clave = $this->objeto_tabla->get_clave();
		$this->columnas = $this->objeto_tabla->get_columnas();
		$this->fuente = $this->objeto_tabla->get_fuente();
		//Determino las secuencias de la tabla
		foreach($this->columnas as $columna){
			if( $columna['secuencia']!=""){
				$this->secuencias[$columna['columna']] = $columna['secuencia'];
			}
		}
		$this->inicializar();
	}
	
	/**
	 * Ventana para agregar configuraciones particulares antes de que el objeto sea construido en su totalidad
	 */
	protected function inicializar(){}

	protected function get_estado_datos_tabla()
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

	/**
	 * Se brinda una query que carga una o más columnas denominadas como 'externas'
	 * Una columna externa no participa en la sincronización posterior, pero por necesidades casi siempre estéticas
	 * necesitan mantenerse junto al conjunto de datos.
	 *
	 * @param string $sql Query de carga
	 * @param array $col_parametros Columnas que espera recibir el sql, en la sql necesitan esta el campo entre % (%nombre_campo%)
	 * @param array $col_resultado Columnas del recorset resultante que se tomarán para rellenar la tabla
	 * @param boolean $sincro_continua En cada pedido de página ejecuta la sql para actualizar los valores de las columnas
	 */
	public function activar_proceso_carga_externa_sql($sql, $col_parametros, $col_resultado, $sincro_continua=true)
	{
		$proximo = count($this->proceso_carga_externa);
		$this->proceso_carga_externa[$proximo]["tipo"] = "sql";
		$this->proceso_carga_externa[$proximo]["sql"] = $sql;
		$this->proceso_carga_externa[$proximo]["col_parametro"] = $col_parametros;
		$this->proceso_carga_externa[$proximo]["col_resultado"] = $col_resultado;
		$this->proceso_carga_externa[$proximo]["sincro_continua"] = $sincro_continua;
	}

	/**
	 * Se brinda un DAO que carga una o más columnas denominadas como 'externas'
	 * Una columna externa no participa en la sincronización posterior, pero por necesidades casi siempre estéticas
	 * necesitan mantenerse junto al conjunto de datos.
	 *
	 * @param string $metodo Método que obtiene los datos
	 * @param string $clase  Clase a la que pertenece el método
	 * @param string $include Archivo donde se encuentra la clase
	 * @param array $col_parametros Columnas que espera recibir el DAO
	 * @param array $col_resultado Columnas del recorset resultante que se tomarán para rellenar la tabla
	 * @param boolean $sincro_continua En cada pedido de página ejecuta el DAO para actualizar los valores de las columnas
	 */
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

	/**
	 * Activa el mecanismo de baja lógica
	 * En este mecanismo en lugar de hacer DELETES actualiza una columna
	 *
	 * @param string $columna Columna que determina la baja lógica
	 * @param mixed $valor Valor que toma la columna al dar de baja un registro
	 */
	public function activar_baja_logica($columna, $valor)
	{
		$this->baja_logica = true;
		$this->baja_logica_columna = $columna;
		$this->baja_logica_valor = $valor;	
	}

	/**
	 * Permite que las modificaciones puedan cambiar las claves del registro
	 */
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
	 * @see cargar_por_clave
	 * @deprecated Desde 0.8.4
	 */
	public function cargar($clave)
	{
		toba::get_logger()->obsoleto(__CLASS__, __FUNCTION__, 'Usar cargar_por_*');
		return $this->cargar_por_clave($clave);	
	}

	/**
	 * Carga el datos_tabla asociado restringiendo POR valores especificos de campos de la tabla
	 *
	 * @param array $clave Arreglo asociativo campo-valor
	 * @return boolean Falso si no se encontro ningun registro
	 */
	public function cargar_por_clave($clave)
	{
		asercion::es_array($clave, "AP [$this->tabla] ERROR: La clave debe ser un array");
		$where = $this->generar_clausula_where_lineal($clave);
		return $this->cargar_con_where_from($where);
	}

	/**
	 * Carga el datos_tabla asociado CON clausulas WHERE y FROM especificas
 	 * @param array $where Clasulas que seran concatenadas con un AND
	 * @param array $from Tablas extra que participan (la actual se incluye automaticamente)
	 *
	 * @return boolean Falso si no se encontro ningún registro
	 */
	public function cargar_con_where_from($where=null, $from=null)
	{
		asercion::es_array_o_null($where,"AP [$this->tabla] El WHERE debe ser un array");
		asercion::es_array_o_null($from,"AP [$this->tabla] El FROM debe ser un array");
		$this->log("Cargar de DB");
		$sql = $this->generar_sql_select($where, $from);
		return $this->cargar_con_sql($sql);
	}

	/**
	 * Carga el datos_tabla asociado CON una query SQL directa
	 *
	 * @return boolean Falso si no se encontro ningún registro
	 */
	public function cargar_con_sql($sql)
	{
		$this->log("SQL de carga - " . $sql); 
		try{
			$db = toba::get_db($this->fuente);			
			$datos = $db->consultar($sql);
		}catch(excepcion_toba $e){
			toba::get_logger()->error( get_class($this). ' - '.
									'Error cargando datos. ' .$e->getMessage() );
			throw new excepcion_toba('AP - OBJETO_DATOS_TABLA: Error cargando datos. Verifique la definicion.\n' . $e->getMessage() );
		}
		return $this->cargar_con_datos($datos);
	}
	
	/**
	 * Carga el datos_tabla asociado CON un conjunto de datos especifico
	 * @param array $datos Datos a cargar en {@link http://toba.siu.edu.ar/trac/wiki/API/RecordSet formato RecordSet}. No incluye las columnas externas.
	 *
	 * @return boolean Falso si no se encontro ningún registro
	 */	
	public function cargar_con_datos($datos)
	{
		if(count($datos)>0){
			//Si existen campos externos, los recupero.
			if($this->objeto_tabla->posee_columnas_externas()){
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
			$this->objeto_tabla->cargar_con_datos($datos);
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
	
	/**
	 * Sincroniza los cambios en los registros con la base de datos
	 * @return integer Cantidad de registros modificados
	 */
	public function sincronizar()
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
	
	/**
	 * Sincronización a nivel de inserts, updates y deletes con la base de datos
	 */
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

	/**
	 * @param mixed $id_registro Clave interna del registro
	 */
	protected function insertar_registro_db($id_registro)
	{
		$sql = $this->generar_sql_insert($id_registro);
		$this->log("registro: $id_registro - " . $sql); 
		$this->ejecutar_sql( $sql );
		//Actualizo las secuencias
		if(count($this->secuencias)>0){
			foreach($this->secuencias as $columna => $secuencia){
				$valor = recuperar_secuencia($secuencia, $this->fuente);
				//El valor es necesario en el evt__post_insert!!
				$this->datos[$id_registro][$columna] = $valor;
				$this->registrar_recuperacion_valor_db( $id_registro, $columna, $valor );
			}
		}
	}

	/**
	 * @param mixed $id_registro Clave interna del registro
	 */
	protected function modificar_registro_db($id_registro)
	{
		$sql = $this->generar_sql_update($id_registro);
		if(isset($sql)){
			$this->log("registro: $id_registro - " . $sql); 
			$this->ejecutar_sql( $sql, $this->fuente);
		}
	}
	
	/**
	 * @param mixed $id_registro Clave interna del registro
	 */

	protected function eliminar_registro_db($id_registro)
	{
		$sql = $this->generar_sql_delete($id_registro);
		$this->log("registro: $id_registro - " . $sql); 
		$this->ejecutar_sql( $sql, $this->fuente);
		return $sql;
	}

	/**
	 * Registra el valor generado por el motor de un columna
	 *
	 * @param string $id_registro Id. interno del registro
	 */
	protected function registrar_recuperacion_valor_db($id_registro, $columna, $valor)
	{
		$this->columnas_predeterminadas_db[$id_registro][$columna] = $valor;
	}
	
	/**
	 * Actualiza en los registros los valores generados por el motor durante la transacción
	 */
	protected function actualizar_columnas_predeterminadas_db()
	{
		if(isset($this->columnas_predeterminadas_db)){
			foreach( $this->columnas_predeterminadas_db as $id_registro => $columnas ){
				foreach( $columnas as $columna => $valor ){
					$this->objeto_tabla->set_fila_columna_valor($id_registro, $columna, $valor);
				}
			}
		}
	}

	/**
	 * Este es el lugar para incluír validaciones (disparar una excepcion) o disparar procesos previo a sincronizar con la base de datos
	 * La transacción con la bd ya fue iniciada (si es que esta definida)
	 */
	protected function evt__pre_sincronizacion(){}
	
	/**
	 * Este es el lugar para incluír validaciones (disparar una excepcion) o disparar procesos antes de terminar de sincronizar con la base de datos
	 * La transacción con la bd aún no se terminó (si es que esta definida)
	 */	
	protected function evt__post_sincronizacion(){}
	
	/**
	 * Esta es una ventana de extensión previo a la inserción de un registro durante una sincronización con la base
	 * @param array $registro Arreglo asociativo campo-valor del registro a insertar
	 */	
	protected function evt__pre_insert($registro){}
	
	/**
	 * Esta es una ventana de extensión posterior a la inserción de un registro durante una sincronización con la base
	 * @param array $registro Arreglo asociativo campo-valor del registro insertado
	 */	
	protected function evt__post_insert($registro){}
	
	/**
	 * Esta es una ventana de extensión previo a la actualización de un registro durante una sincronización con la base
	 * @param array $registro Arreglo asociativo campo-valor del registro a actualizar
	 */		
	protected function evt__pre_update($id){}

	/**
	 * Esta es una ventana de extensión posterior a la actualización de un registro durante una sincronización con la base
	 * @param array $registro Arreglo asociativo campo-valor del registro actualizado
	 */	
	protected function evt__post_update($id){}

	/**
	 * Esta es una ventana de extensión previa al borrado de un registro durante una sincronización con la base
	 * @param array $registro Arreglo asociativo campo-valor del registro a borrar
	 */
	protected function evt__pre_delete($id){}

	/**
	 * Esta es una ventana de extensión posterior al borrado de un registro durante una sincronización con la base
	 * @param array $registro Arreglo asociativo campo-valor del registro borrado
	 */
	protected function evt__post_delete($id){}

	//-------------------------------------------------------------------------------
	//------  ELIMINAR  -------------------------------------------------------
	//-------------------------------------------------------------------------------
	
	/**
	 * Elimina físicamente los registros de esta tabla
	 * En una base de datos, esto implica borrar cada uno de los registros
	 */
	public function eliminar()
	{
		$this->log("Inicio ELIMINAR");
		$this->actualizar_estado_db();
		$this->log("Fin ELIMINAR");
	}	

	//-------------------------------------------------------------------------------
	//------ Servicios SQL   --------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Alias de la función global ejecutar_sql
	 */
	function ejecutar_sql( $sql )
	{
		ejecutar_sql( $sql, $this->fuente);			
	}

	/**
	 * Genera la sentencia WHERE del estilo ( nombre_columna = valor ) respetando el tipo de datos
	 * @param array $clave Arreglo asociativo clave - valor de la clave a filtrar
	 * @param boolean $alias Útil para cuando se generan SELECTs complejos
	 * @return array Clausulas where
	 */
	function generar_clausula_where_lineal($clave,$alias=true)
	{
		if ($alias) {
			$tabla_alias = isset($this->alias) ? $this->alias . "." : "";
		} else {
			$tabla_alias = "";	
		}
		foreach($clave as $columna => $valor) {
			if( tipo_datos::numero( $this->columnas[$columna]['tipo'] ) ) {
				$clausula[] = "( $tabla_alias" . "$columna = $valor )";
			} else {
				$clausula[] = "( $tabla_alias" . "$columna = '$valor' )";
			}
		}
		return $clausula;
	}
	
	/**
	 * @param array $where Clasulas que seran concatenadas con un AND
	 * @param array $from Tablas extra que participan (la actual se incluye automaticamente)
	 * @return string Consulta armada
	 */
	protected function generar_sql_select($where=null, $from=null)
	{
		foreach($this->columnas as $col){
			if(!$col['externa']){
				$columnas[] = $this->tabla  . "." . $col['columna'];
			}
		}
		$sql =	" SELECT	" . implode(", \n",$columnas); 
		if(isset($this->alias)){	
			$sql .= "\n FROM "	. $this->tabla  . " " . $this->alias;
		}else{
			$sql .= "\n FROM "	. $this->tabla;
		}
		if(isset($from)){
			$sql .= ", " . implode(",",$from);
		}
		if(isset($where)){
			$sql .= "\n WHERE " .	implode("\n AND ",$where) .";";
		}
		return $sql;
	}	
	
	/**
	 * @param mixed $id_registro Clave interna del registro
	 */
	protected function generar_sql_insert($id_registro)
	{
		$a=0;
		$registro = $this->datos[$id_registro];
		foreach($this->columnas as $columna)
		{
			$col = $columna['columna'];
			$es_insertable = (trim($columna['secuencia']=="")) && ($columna['externa'] != 1);
			if( $es_insertable )
			{
				if( !isset($registro[$col]) || $registro[$col] === NULL ){
					$valores_sql[$a] = "NULL";
				}else{
					if(	tipo_datos::numero($columna['tipo']) ){
						$valores_sql[$a] = $registro[$col];
					}else{
						$valores_sql[$a] = "'" . addslashes(trim($registro[$col])) . "'";
					}
				}
				$columnas_sql[$a] = $col;
				$a++;
			}
		}
		$sql = "INSERT INTO " . $this->tabla .
				" ( " . implode(", ", $columnas_sql) . " ) ".
				" VALUES (" . implode(", ", $valores_sql) . ");";
		return $sql;
	}

	/**
	 * @param mixed $id_registro Clave interna del registro
	 */	
	function generar_sql_update($id_registro)
	// Modificacion de claves
	{
		$registro = $this->datos[$id_registro];
		//Genero las sentencias de la clausula SET para cada columna
		foreach($this->columnas as $columna){
			$col = $columna['columna'];
			//columna modificable: no es secuencia, no es extena, no es PK 
			//	(excepto que se se declare explicitamente la alteracion de PKs)
			$es_modificable = ($columna['secuencia']=="") && ($columna['externa'] != 1) 
							&& ( ($columna['pk'] != 1) || (($columna['pk'] == 1) && $this->flag_modificacion_clave ) );
			if( $es_modificable ){
				if( !isset($registro[$col]) || $registro[$col] === NULL ){
					$set[] = "$col = NULL";
				}else{
					if(	tipo_datos::numero($columna['tipo']) ){
						$set[] = "$col = " . $registro[$col];
					}else{
						$set[] = "$col = '" . addslashes(trim($registro[$col])) . "'";
					}
				}
			}
		}
		if(!is_array($set)){
			toba::get_logger()->info('AP - datos_tabla: No hay campos para hacer el UPDATE');
			return null;	
		}
		//Armo el SQL
		$sql = "UPDATE " . $this->tabla . " SET ".
				implode(", ",$set) .
				" WHERE " . implode(" AND ",$this->generar_sql_where_registro($id_registro) ) .";";
		return $sql;		
	}

	/**
	 * @param mixed $id_registro Clave interna del registro
	 */
	protected function generar_sql_delete($id_registro)
	{
		$registro = $this->datos[$id_registro];
		if($this->baja_logica){
			$sql = "UPDATE " . $this->tabla .
					" SET " . $this->baja_logica_columna . " = '". $this->baja_logica_valor ."' " .
					" WHERE " . implode(" AND ",$sql_where) .";";
		}else{
			$sql = "DELETE FROM " . $this->tabla .
					" WHERE " . implode(" AND ",$this->generar_sql_where_registro($id_registro) ) .";";
		}
		return $sql;
	}

	/**
	 * @param mixed $id_registro Clave interna del registro
	 */	
	function generar_sql_where_registro($id_registro)
	//Genera la sentencia WHERE correspondiente a la clave de un registro
	{
		foreach($this->clave as $clave){
			$id[$clave] = $this->cambios[$id_registro]['clave'][$clave];
		}
		return $this->generar_clausula_where_lineal($id,false);
	}
	
	/**
	 * Retorna los sql de insert de cada registro cargado en el datos_tabla, sin importar su estado actual
	 *
	 * @return array
	 */
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
	//---------------  Carga de CAMPOS EXTERNOS   -----------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Recuperacion de valores para las columnas externas.
	 * Para que esto funcione, la consultas realizadas tienen que devolver un solo registro,
	 * cuyas claves asociativas se correspondan con la columna que se quiere llenar
	 * @param array $fila Fila que toma dereferencia la carga externa
	 * @param string $evento 
	 * @return array Se devuelven los valores recuperados de la DB.
	 * 
	 * @todo Este mecanismo requiere OPTIMIZACION (Mas que nada para la carga inicial)* 
	 */
	public function completar_campos_externos_fila($fila, $evento=null)
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
						throw new excepcion_toba('AP_TABLA_DB: ERROR en la carga de una columna externa.');
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
					// - 3 - Ejecuto SQL
					$datos = consultar_fuente($sql, $this->fuente);
					if(!$datos){
						toba::get_logger()->debug('AP_TABLA_DB: no se recuperaron datos ' . $sql);
						throw new excepcion_toba('AP_TABLA_DB: ERROR en la carga de una columna externa.');
					}
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
						if(is_array($columna_externa)){
							//Hay una regla de mapeo entre el valor devuelto y la columna del DT
							if(!isset($datos[0][$columna_externa['origen']])){
								toba::get_logger()->debug("AP_TABLA_DB: Se esperaba que que conjunto de valores devueltos posean la columna '{$columna_externa['origen']}'");
								throw new excepcion_toba('AP_TABLA_DB: ERROR en la carga de una columna externa.');
							}
							$valores_recuperados[$columna_externa['destino']] = $datos[0][$columna_externa['origen']];
						}else{
							if(!isset($datos[0][$columna_externa])){
								toba::get_logger()->debug("AP_TABLA_DB: Se esperaba que que conjunto de valores devueltos posean la columna '$columna_externa'");
								throw new excepcion_toba('AP_TABLA_DB: ERROR en la carga de una columna externa.');
							}
							$valores_recuperados[$columna_externa] = $datos[0][$columna_externa];
						}
					}
				}
			}
		}
		return $valores_recuperados;
	}

	//-------------------------------------------------------------------------------
	//--  Control de VERSIONES  -----------------------------------------------------
	//-------------------------------------------------------------------------------

	protected function controlar_alteracion_db()
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