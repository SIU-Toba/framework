<?php
define("apex_db_registros_separador","%");

/**
 * Administrador de persistencia a una tabla de DB desde un {@link toba_datos_tabla datos_tabla}
 * Supone que la tabla de datos se va a mapear a algun tipo de estructura en una base de datos
 * 
 * @todo Poder desactivar el control de sincronizacion (¿se necesita esto?)
 * @todo Como se implementa la carga de columnas externas??
 * @todo Donde se hacen los controles pre-sincronizacion (nulos db)??
 * @todo Hay que definir el manejo de claves (en base a toba_datos_relacion)	
 * @package Componentes
 * @subpackage Persistencia
 */
 
class toba_ap_tabla_db implements toba_ap_tabla
{
	protected $objeto_tabla;					// DATOS_TABLA: Referencia al objeto asociado
	protected $_columnas;						// DATOS_TABLA: Estructura del objeto
	protected $datos;							// DATOS_TABLA: DATOS que conforman las filas
	protected $_cambios;							// DATOS_TABLA: Estado de los cambios
	protected $_tabla;							// DATOS_TABLA: Tabla
	protected $_alias;							// DATOS_TABLA: Alias
	protected $_clave;							// DATOS_TABLA: Clave
	protected $_fuente;							// DATOS_TABLA: Fuente de datos
	protected $_secuencias;
	protected $_columnas_predeterminadas_db;		// Manejo de datos generados por el motor (autonumericos, predeterninados, etc)
	protected $_sql_carga;						// Partes de la SQL utilizado en la carga de la tabla
	protected $_schema;
	//-------------------------------
	protected $_baja_logica = false;				// Baja logica. (delete = update de una columna a un valor)
	protected $_baja_logica_columna;				// Columna de la baja logica
	protected $_baja_logica_valor;				// Valor de la baja logica
	protected $_flag_modificacion_clave = false;	// Es posible modificar la clave en el UPDATE? Por defecto
	protected $_proceso_carga_externa = null;	// Declaracion del proceso utilizado para cargar columnas externas
	//-------------------------------
	protected $_control_sincro_db;				// Se activa el control de sincronizacion con la DB?
	protected $_utilizar_transaccion=true;		// La sincronizacion con la DB se ejecuta dentro de una transaccion
	protected $_msg_error_sincro = "Error interno. Los datos no fueron guardados.";
	//-------------------------------

	
	/**
	 * @param toba_datos_tabla $datos_tabla Tabla que persiste
	 */
	function __construct($datos_tabla)
	{
		$this->objeto_tabla = $datos_tabla;
		$this->_tabla = $this->objeto_tabla->get_tabla();
		$this->_alias = $this->objeto_tabla->get_alias();
		$this->_clave = $this->objeto_tabla->get_clave();
		$this->_columnas = $this->objeto_tabla->get_columnas();
		$this->_fuente = $this->objeto_tabla->get_fuente();
		//Determino las secuencias de la tabla
		foreach($this->_columnas as $columna){
			if( $columna['secuencia']!=""){
				$this->_secuencias[$columna['columna']] = $columna['secuencia'];
			}
		}
		$this->inicializar();
		$this->ini();
	}
	
	/**
	 * Ventana para agregar configuraciones particulares antes de que el objeto sea construido en su totalidad
	 * @deprecated 
	 * @see ini
	 * @ventana
	 */
	protected function inicializar(){}

	/**
	 * Ventana para agregar configuraciones particulares despues de la construccion
	 * @ventana
	 */
	protected function ini(){}


	/**
	 * @ignore 
	 */
	protected function get_estado_datos_tabla()
	{
		$this->_cambios = $this->objeto_tabla->get_cambios();
		$this->datos = $this->objeto_tabla->get_conjunto_datos_interno();
	}
	
	/**
	 * Shorcut a toba::logger()->debug incluyendo infomación básica del componente
	 */
	protected function log($txt)
	{
		toba::logger()->debug("AP: " . get_class($this). "- TABLA: $this->_tabla - OBJETO: ". get_class($this->objeto_tabla). " -- " ."\n".$txt, 'toba');
	}

	/**
	 * Método de debug que retorna las propiedades internas
	 * @return array
	 */	
	function info()
	{
		return get_object_vars($this);
	}

	//-------------------------------------------------------------------------------
	//------  Configuracion  --------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Utilizar una transaccion de BD cuando sincroniza la tabla
	 */
	function activar_transaccion()		
	{
		$this->_utilizar_transaccion = true;
	}

	/**
	 * No utilizar una transaccion de BD cuando sincroniza la tabla
	 * Generalmente por que la transaccion la abre/cierra algun proceso de nivel superior
	 */	
	function desactivar_transaccion()		
	{
		$this->_utilizar_transaccion = false;
	}

	/**
	 * Carga una columna separada del proceso común de carga
	 * Se brinda una query que carga una o más columnas denominadas como 'externas'
	 * Una columna externa no participa en la sincronización posterior, pero por necesidades casi siempre estéticas
	 * necesitan mantenerse junto al conjunto de datos.
	 *
	 * @param string $sql Query de carga
	 * @param array $col_parametros Columnas que espera recibir el sql, en la sql necesitan esta el campo entre % (%nombre_campo%)
	 * @param array $col_resultado Columnas del recorset resultante que se tomarán para rellenar la tabla
	 * @param boolean $sincro_continua En cada pedido de página ejecuta la sql para actualizar los valores de las columnas
	 */
	function activar_proceso_carga_externa_sql($sql, $col_parametros, $col_resultado, $sincro_continua=true)
	{
		$proximo = count($this->_proceso_carga_externa);
		$this->_proceso_carga_externa[$proximo]["tipo"] = "sql";
		$this->_proceso_carga_externa[$proximo]["sql"] = $sql;
		$this->_proceso_carga_externa[$proximo]["col_parametro"] = $col_parametros;
		$this->_proceso_carga_externa[$proximo]["col_resultado"] = $col_resultado;
		$this->_proceso_carga_externa[$proximo]["sincro_continua"] = $sincro_continua;
	}

	/**
	 * Carga una columna separada del proceso común de carga
	 * Se brinda un DAO que carga una o más columnas denominadas como 'externas'
	 * Una columna externa no participa en la sincronización posterior, pero por necesidades casi siempre estéticas
	 * necesitan mantenerse junto al conjunto de datos.
	 *
	 * @param string $metodo Método que obtiene los datos
	 * @param string $clase  Clase a la que pertenece el método.	Si es NULL usa el mismo AP
	 * @param string $include Archivo donde se encuentra la clase.	Si es NULL usa el mismo AP
	 * @param array $col_parametros Columnas que espera recibir el DAO
	 * @param array $col_resultado Columnas del recorset resultante que se tomarán para rellenar la tabla
	 * @param boolean $sincro_continua En cada pedido de página ejecuta el DAO para actualizar los valores de las columnas
	 */
	function activar_proceso_carga_externa_dao($metodo, $clase=null, $include=null, $col_parametros, $col_resultado, $sincro_continua=true)
	{
		$proximo = count($this->_proceso_carga_externa);
		$this->_proceso_carga_externa[$proximo]["tipo"] = "dao";
		$this->_proceso_carga_externa[$proximo]["metodo"] = $metodo;
		$this->_proceso_carga_externa[$proximo]["clase"] = $clase;
		$this->_proceso_carga_externa[$proximo]["include"] = $include;
		$this->_proceso_carga_externa[$proximo]["col_parametro"] = $col_parametros;
		$this->_proceso_carga_externa[$proximo]["col_resultado"] = $col_resultado;
		$this->_proceso_carga_externa[$proximo]["sincro_continua"] = $sincro_continua;
	}

	/**
	 * Activa el mecanismo de baja lógica
	 * En este mecanismo en lugar de hacer DELETES actualiza una columna
	 *
	 * @param string $columna Columna que determina la baja lógica
	 * @param mixed $valor Valor que toma la columna al dar de baja un registro
	 */
	function activar_baja_logica($columna, $valor)
	{
		$this->_baja_logica = true;
		$this->_baja_logica_columna = $columna;
		$this->_baja_logica_valor = $valor;	
	}

	/**
	 * Permite que las modificaciones puedan cambiar las claves del registro
	 */
	function activar_modificacion_clave()
	{
		$this->_flag_modificacion_clave = true;
	}
	
	function set_schema($schema) 
	{
		$this->_tabla = $schema.'.'.$this->objeto_tabla->get_tabla();
	}

	//-------------------------------------------------------------------------------
	//------  CARGA  ----------------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Carga el datos_tabla asociado restringiendo POR valores especificos de campos de la tabla
	 *
	 * @param array $clave Arreglo asociativo campo-valor
	 * @param boolean $anexar_datos Si es false borra todos los datos actuales de la tabla, sino los mantiene y adjunto los nuevos
	 * @param boolean $usar_cursores En caso de anexar datos, fuerza a que los padres de la fila sean los cursores actuales de las tablas padre
	 * @return boolean Falso si no se encontro ningun registro
	 */
	function cargar_por_clave($clave, $anexar_datos=false, $usar_cursores=false)
	{
		toba_asercion::es_array($clave, "Error cargando la tabla <b>$this->_tabla</b>, se esperaba un arreglo asociativo por ejemplo ".
													"<pre>\$tabla->cargar(array('campo'=> 'valor'))</pre>", true);
		$where = $this->generar_clausula_where($clave);
		return $this->cargar_con_where_from_especifico($where, null, $anexar_datos, $usar_cursores);
	}


	/**
	 * Carga el datos_tabla asociaciado a partir de una clausula where personalizada
	 * @param string $clausula Cláusula where que será anexada con un AND a las cláusulas básicas de la tabla
	 * @param boolean $anexar_datos Si es false borra todos los datos actuales de la tabla, sino los mantiene y adjunto los nuevos
	 * @param boolean $usar_cursores En caso de anexar datos, fuerza a que los padres de la fila sean los cursores actuales de las tablas padre
	 * @return boolean Falso si no se encontro ningun registro
	 */
	function cargar_con_where($clausula, $anexar_datos=false, $usar_cursores=false)
	{
		$where_basico = $this->generar_clausula_where();
		if (trim($clausula) != '') {
			$where_basico[] = $clausula;
		}
		return $this->cargar_con_where_from_especifico($where_basico, null, $anexar_datos, $usar_cursores);
	}

	/**
	 * Carga el datos_tabla asociado CON clausulas WHERE y FROM especificas, el entorno no incide en ellas
 	 * @param array $where Clasulas que seran concatenadas con un AND
	 * @param array $from Tablas extra que participan (la actual se incluye automaticamente)
	 * @param boolean $anexar_datos Si es false borra todos los datos actuales de la tabla, sino los mantiene y adjunto los nuevos
	 * @param boolean $usar_cursores En caso de anexar datos, fuerza a que los padres de la fila sean los cursores actuales de las tablas padre
	 * @return boolean Falso si no se encontro ningún registro
	 */
	function cargar_con_where_from_especifico($where=null, $from=null, $anexar_datos=false, $usar_cursores=false)
	{
		toba_asercion::es_array_o_null($where,"AP [{$this->_tabla}] El WHERE debe ser un array");
		toba_asercion::es_array_o_null($from,"AP [{$this->_tabla}] El FROM debe ser un array");
		$sql = $this->generar_sql_select($where, $from);
		return $this->cargar_con_sql($sql, $anexar_datos, $usar_cursores);
	}

	/**
	 * Carga el datos_tabla asociado CON una query SQL directa
	 * @param boolean $anexar_datos Si es false borra todos los datos actuales de la tabla, sino los mantiene y adjunto los nuevos
	 * @param boolean $usar_cursores En caso de anexar datos, fuerza a que los padres de la fila sean los cursores actuales de las tablas padre
	 * @return boolean Falso si no se encontro ningún registro
	 */
	function cargar_con_sql($sql, $anexar_datos=false, $usar_cursores=false)
	{
		$this->log("SQL de carga: \n" . $sql."\n"); 
		try{
			$db = toba::db($this->_fuente);
			$datos = $db->consultar($sql);
		}catch(toba_error_db $e){
			$mensaje = $e->get_mensaje_motor();
			$mensaje = "Error cargando la tabla <b>$this->_tabla</b>, a continuación el mensaje de la base:<br>".$mensaje;
			$e->set_mensaje_motor($mensaje);
			toba::logger()->error( get_class($this). ' - '.
									'Error cargando datos. ' .$e->getMessage() );
			throw $e;
		}
		return $this->cargar_con_datos($datos, $anexar_datos, $usar_cursores);
	}
	
	/**
	 * Carga el datos_tabla asociado CON un conjunto de datos especifico
	 * @param array $datos Datos a cargar en formato RecordSet. No incluye las columnas externas.
	 * @param boolean $anexar_datos Si es false borra todos los datos actuales de la tabla, sino los mantiene y adjunto los nuevos
	 * @param boolean $usar_cursores En caso de anexar datos, fuerza a que los padres de la fila sean los cursores actuales de las tablas padre
	 * @return boolean Falso si no se encontro ningún registro
	 */	
	function cargar_con_datos($datos, $anexar_datos=false, $usar_cursores=false)
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
						$datos[$a][$columna] = $datos[$a][$columna];
					}
				}	
			}
			// Lleno la TABLA
			if( $anexar_datos ) {
				$this->objeto_tabla->anexar_datos($datos, $usar_cursores);
			} else {
				$this->objeto_tabla->cargar_con_datos($datos);
			}
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
	 * Sincroniza los cambios en los registros de esta tabla con la base de datos
	 * Sólo se utiliza cuando la tabla no está involucrada en algun datos_relacion, sino 
	 * la sincronización es guiada por ese objeto
	 * @return integer Cantidad de registros modificados
	 * @throws toba_error En case de error en la sincronizacion, se aborta la transaccion (si se esta utilizando)
	 */
	function sincronizar()
	{
		$this->log("Inicio SINCRONIZAR");
		try{
			if($this->_utilizar_transaccion) abrir_transaccion($this->_fuente);
			$this->evt__pre_sincronizacion();		
			$modificaciones = 0;
			$modificaciones += $this->sincronizar_eliminados();
			$modificaciones += $this->sincronizar_insertados();
			$modificaciones += $this->sincronizar_actualizados();
			//Regenero la estructura que mantiene los cambios realizados
			$this->objeto_tabla->notificar_fin_sincronizacion();
			$this->evt__post_sincronizacion();
			if($this->_utilizar_transaccion) cerrar_transaccion($this->_fuente);
			$this->log("Fin SINCRONIZAR: $modificaciones."); 
			return $modificaciones;
		} catch(toba_error $e) {
			if($this->_utilizar_transaccion) { 
				toba::logger()->info("Abortando transacción en {$this->_fuente}", 'toba');				
				abortar_transaccion($this->_fuente);
			}
			toba::logger()->debug("Relanzando excepción. ".$e, 'toba');
			throw $e;
		}		
	}		
	
	
	/**
	 * Sincroniza con la BD los registros borrados en esta tabla
	 * @return integer Cantidad de modificaciones a la base
	 */
	function sincronizar_eliminados()
	{
		$this->get_estado_datos_tabla();
		$modificaciones = 0;
		foreach(array_keys($this->_cambios) as $registro){
			if ($this->_cambios[$registro]['estado'] == 'd') {
				$this->evt__pre_delete($registro);
				$this->eliminar_registro_db($registro);
				$this->evt__post_delete($registro);
				$modificaciones ++;
			}
		}
		return $modificaciones;
	}
	
	
	/**
	 * Sincroniza con la BD aquellos registros que suponen altas
	 * @return integer Cantidad de modificaciones a la base
	 */
	function sincronizar_insertados()
	{
		$this->get_estado_datos_tabla();
		$modificaciones = 0;
		foreach(array_keys($this->_cambios) as $registro){
			if ($this->_cambios[$registro]['estado'] == "i") {
				$this->evt__pre_insert($registro);
				$this->insertar_registro_db($registro);
				$this->evt__post_insert($registro);
				$modificaciones ++;
			}
		}
		//Seteo en la TABLA los datos generados durante la sincronizacion
		$this->actualizar_columnas_predeterminadas_db();
		return $modificaciones;
	}
	

	/**
	 * Sincroniza con la BD aquellos registros que suponen actualizaciones
	 * @return integer Cantidad de modificaciones a la base
	 */
	function sincronizar_actualizados()
	{
		$this->get_estado_datos_tabla();
		$modificaciones = 0;
		foreach(array_keys($this->_cambios) as $registro){
			if ($this->_cambios[$registro]['estado'] == 'u') {
				$this->evt__pre_update($registro);
				$this->modificar_registro_db($registro);
				$this->evt__post_update($registro);
				$modificaciones ++;
			}
		}
		return $modificaciones;
	}	

	//-------------------------------------------------------------------------------
	//------  COMANDOS DE SINCRO------------------------------------------------------
	//-------------------------------------------------------------------------------
	
	/**
	 * Inserta un registro en la base y recupera su secuencia si la tiene
	 * @param mixed $id_registro Clave interna del registro
	 * @ignore 
	 */
	protected function insertar_registro_db($id_registro)
	{
		$this->ejecutar_sql_insert($id_registro);
		//Actualizo las secuencias
		if(count($this->_secuencias)>0){
			foreach($this->_secuencias as $columna => $secuencia){
				$valor = recuperar_secuencia($secuencia, $this->_fuente);
				//El valor es necesario en el evt__post_insert!!
				$this->datos[$id_registro][$columna] = $valor;
				$this->registrar_recuperacion_valor_db( $id_registro, $columna, $valor );
			}
		}
	}

	/**
	 * Ejecuta un update de un registro en la base
	 * @param mixed $id_registro Clave interna del registro
	 * @ignore 
	 */
	protected function modificar_registro_db($id_registro)
	{
		$this->ejecutar_sql_update($id_registro);
	}
	
	/**
	 * Ejecuta un delete de un registro en la base
	 * @param mixed $id_registro Clave interna del registro
	 * @ignore 
	 */
	protected function eliminar_registro_db($id_registro)
	{
		$sql = $this->generar_sql_delete($id_registro);
		$this->log("registro: $id_registro - " . $sql); 
		$this->ejecutar_sql( $sql );
		return $sql;
	}

	/**
	 * Registra el valor generado por el motor de un columna
	 * @param string $id_registro Id. interno del registro
	 * @ignore  
	 */
	protected function registrar_recuperacion_valor_db($id_registro, $columna, $valor)
	{
		$this->_columnas_predeterminadas_db[$id_registro][$columna] = $valor;
	}
	
	/**
	 * Actualiza en los registros los valores generados por el motor durante la transacción
	 * @ignore 
	 */
	protected function actualizar_columnas_predeterminadas_db()
	{
		if(isset($this->_columnas_predeterminadas_db)){
			foreach( $this->_columnas_predeterminadas_db as $id_registro => $columnas ){
				foreach( $columnas as $columna => $valor ){
					$this->objeto_tabla->set_fila_columna_valor($id_registro, $columna, $valor);
				}
			}
		}
	}

	/**
	 * Ventana para incluír validaciones (disparar una excepcion) o disparar procesos previo a sincronizar con la base de datos
	 * La transacción con la bd ya fue iniciada (si es que esta definida)
	 * @ventana
	 */
	protected function evt__pre_sincronizacion(){}
	
	/**
	 * Ventana para incluír validaciones (disparar una excepcion) o disparar procesos antes de terminar de sincronizar con la base de datos
	 * La transacción con la bd aún no se terminó (si es que esta definida)
	 * @ventana
	 */	
	protected function evt__post_sincronizacion(){}
	
	/**
	 * Ventana de extensión previo a la inserción de un registro durante una sincronización con la base
	 * @param mixed $id_registro Clave interna del registro
	 * @ventana
	 */	
	protected function evt__pre_insert($id_registro){}
	
	/**
	 * Ventana de extensión posterior a la inserción de un registro durante una sincronización con la base
	 * @param mixed $id_registro Clave interna del registro
	 * @ventana
	 */	
	protected function evt__post_insert($id_registro){}
	
	/**
	 * Ventana de extensión previo a la actualización de un registro durante una sincronización con la base
	 * @param mixed $id_registro Clave interna del registro
	 * @ventana
	 */		
	protected function evt__pre_update($id_registro){}

	/**
	 * Ventana de extensión posterior a la actualización de un registro durante una sincronización con la base
	 * @param mixed $id_registro Clave interna del registro
	 * @ventana
	 */	
	protected function evt__post_update($id_registro){}

	/**
	 * Ventana de extensión previa al borrado de un registro durante una sincronización con la base
	 * @param mixed $id_registro Clave interna del registro
	 * @ventana
	 */
	protected function evt__pre_delete($id_registro){}

	/**
	 * Ventana de extensión posterior al borrado de un registro durante una sincronización con la base
	 * @param mixed $id_registro Clave interna del registro
	 * @ventana
	 */
	protected function evt__post_delete($id_registro){}

	//-------------------------------------------------------------------------------
	//------ Servicios SQL   --------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Shortcut de {@link toba_db::ejecutar() toba::db()->ejecutar}
	 */
	protected function ejecutar_sql( $sql )
	{
		toba::db($this->_fuente)->ejecutar($sql);
	}

	
	/**
	 * Genera la sentencia WHERE del estilo ( nombre_columna = valor ) respetando el tipo de datos
	 * @param array $clave Arreglo asociativo clave - valor de la clave a filtrar
	 * @param boolean $alias Útil para cuando se generan SELECTs complejos
	 * @return array Clausulas where
	 * 
	 * @ignore 
	 */
	protected function generar_clausula_where_lineal($clave,$alias=true)
	{
		if ($alias) {
			$tabla_alias = isset($this->_alias) ? $this->_alias . "." : "";
		} else {
			$tabla_alias = "";	
		}
		$clausula = array();
		foreach($clave as $columna => $valor) {
			$valor = toba::db($this->_fuente)->quote($valor);
			$clausula[] = "( $tabla_alias" . "$columna = $valor )";
		}
		return $clausula;
	}	
	
	/**
	 * Genera la sentencia WHERE del estilo ( nombre_columna = valor ) respetando el tipo de datos
	 * y las asociaciones con los padres
	 * @param array $clave Arreglo asociativo clave - valor de la clave a filtrar
	 * @return array Clausulas where
	 * 
	 * @ignore 
	 */
	protected function generar_clausula_where($clave=array())
	{
		$clausula = $this->generar_clausula_where_lineal($clave, true);
		//Si la tabla tiene relaciones con padres
		//Se hace un subselect con los campos relacionados
		foreach ( $this->objeto_tabla->get_relaciones_con_padres() as $rel_padre) {
			$nuevo = $rel_padre->generar_clausula_subselect($this->_alias);
			if (isset($nuevo)) {
				$clausula[] = $nuevo;
			}
		}
		return $clausula;
	}

	/**
	 * @param array $where Clasulas que seran concatenadas con un AND
	 * @param array $from Tablas extra que participan (la actual se incluye automaticamente)
	 * @return string Consulta armada
	 * @ignore 
	 */
	protected function generar_sql_select($where=array(), $from=null, $columnas=null)
	{
		//Si no se explicitan las columnas, se asume que son todas
		if (!isset($columnas)) {
			$columnas = array();
			foreach ($this->_columnas as $col) {
				if(!$col['externa'] && $col['tipo'] != 'B') {
					$columnas[] = $this->_alias  . "." . $col['columna'];
				}
			}
		}
		//Si no se explicitan los from se asume que es la tabla local
		if (!isset($from)) {
			$from = array($this->_tabla . ' as '. $this->_alias);
		}

		$sql =	"SELECT\n\t" . implode(", \n\t", $columnas); 
		$sql .= "\nFROM\n\t" . implode(", ", $from);
		if(! empty($where)) {
			$sql .= "\nWHERE";
			foreach ($where as $clausula) {
				$sql .= "\n\t$clausula AND";
			}
			$sql = substr($sql, 0, -4); 	//Se saca el ultimo AND
		}
		//Se guardan los datos de la carga
		$this->_sql_carga = array('from' => $from, 'where' => $where);
		return $sql;
	}	
	
	/**
	 * Retorna la sentencia sql utilizada previamente para la carga de esta tabla, pero seleccionando solo algunos campos
	 * @param array $campos Columnas que se traen de la carga
	 * 
	 * @ignore 
	 */
	function get_sql_de_carga($campos)
	{
		if (isset($this->_sql_carga)) {
			return $this->generar_sql_select($this->_sql_carga['where'], $this->_sql_carga['from'], $campos);
		} else {
			throw new toba_error("AP-TABLA Db: La tabla no ha sido cargada en este pedido de página");
		}
	}
	
	
	/**
	 * @param mixed $id_registro Clave interna del registro
	 * @ignore 
	 */
	protected function ejecutar_sql_insert($id_registro, $solo_retornar=false)
	{
		$a=0;
		$registro = $this->datos[$id_registro];
		$binarios = array();
		$db = toba::db($this->_fuente);
		foreach($this->_columnas as $columna)
		{
			$col = $columna['columna'];
			$es_insertable = (trim($columna['secuencia']=="")) && ($columna['externa'] != 1);
			$es_binario = ($columna['tipo'] == 'B');
			if( $es_insertable) {
				if ($es_binario) {
					$blob = $this->objeto_tabla->_get_blob_transaccion($id_registro, $col);
					//-- Si no esta seteado es un blob nulo
					if ($blob === false) {
						$valores_sql[$a] = "NULL";
						$columnas_sql[$a] = $col;						
					} elseif (is_resource($blob)) {
						$binarios[] = $blob;
						$valores_sql[$a] = '?';
						$columnas_sql[$a] = $col;						
					} else {
						//No tocar nada
					}
				} elseif ( !isset($registro[$col]) || $registro[$col] === NULL ) {
					//-- Es un campo NULO
					$valores_sql[$a] = "NULL";
					$columnas_sql[$a] = $col;
				}else{
					if(	toba_tipo_datos::numero($columna['tipo']) ){
						//-- Los booleanos muchas veces se representan como enteros en la base
						if ($registro[$col] === true) {
							$registro[$col] = 1;
						} elseif ($registro[$col] === false) {
							$registro[$col] = 0;
						}
					}
					$valores_sql[$a] =  $db->quote(trim($registro[$col]));
					$columnas_sql[$a] = $col;
				}
				$a++;
			}
		}
		$sql = "INSERT INTO " . $this->_tabla .
					" ( " . implode(", ", $columnas_sql) . " ) ".
					"\n VALUES (" . implode(", ", $valores_sql) . ");";
		if ($solo_retornar) {
			return $sql;
		}
		$this->log("registro: $id_registro - " . $sql); 															
		if (empty($binarios)) {
			$this->ejecutar_sql($sql);			
		} else {
			$pdo = toba::db($this->_fuente)->get_pdo();
			$stmt = $pdo->prepare($sql);
			$i = 1;
			foreach (array_keys($binarios) as $clave) {
				$stmt->bindParam($i, $binarios[$clave], PDO::PARAM_LOB);
				$i++;
			}
			$stmt->execute();
		}
	}	
	
	/**
	 * @param mixed $id_registro Clave interna del registro
	 * @ignore 
	 */	
	protected function ejecutar_sql_update($id_registro)
	{
		$binarios = array();
		$registro = $this->datos[$id_registro];
		//Genero las sentencias de la clausula SET para cada columna
		$set = array();
		$db = toba::db($this->_fuente);
		foreach($this->_columnas as $columna){
			$col = $columna['columna'];
			$es_binario = ($columna['tipo'] == 'B');
			//columna modificable: no es secuencia, no es extena, no es PK 
			//	(excepto que se se declare explicitamente la alteracion de PKs)
			$es_modificable = ($columna['secuencia']=="") && ($columna['externa'] != 1) 
							&& ( ($columna['pk'] != 1) || (($columna['pk'] == 1) && $this->_flag_modificacion_clave ) );
			if( $es_modificable ){
				if ($es_binario) {
					$blob = $this->objeto_tabla->_get_blob_transaccion($id_registro, $col);
					if ($blob === false) {
						//-- Si no esta seteado es un blob nulo						
						$set[] = "$col = NULL";
					} elseif (is_resource($blob)) {
						$binarios[] = $blob;
						$set[] = "$col = ?";							
					} else {
						//No tocar nada
					}
				} elseif ( !isset($registro[$col]) || $registro[$col] === NULL ){
					$set[] = "$col = NULL";
				}else{
					$set[] = "$col = " . $db->quote(trim($registro[$col]));
				}
			}
		}
		if(empty($set)){
			$this->log('No hay campos para hacer el UPDATE');
			return null;	
		}
		//Armo el SQL
		$sql = "UPDATE " . $this->_tabla . " SET ".
				implode(", ",$set) .
				" WHERE " . implode(" AND ",$this->generar_sql_where_registro($id_registro) ) .";";
		$this->log("registro: $id_registro - " . $sql);		
		if (empty($binarios)) {
			$this->ejecutar_sql($sql);			
		} else {
			$pdo = toba::db($this->_fuente)->get_pdo();
			$stmt = $pdo->prepare($sql);
			$i = 1;
			foreach ($binarios as $binario) {
				$stmt->bindParam($i, $binario, PDO::PARAM_LOB);
				$i++;
			}
			$stmt->execute();
		}		
	}	
	
	/**
	 * @param mixed $id_registro Clave interna del registro
	 * @ignore 
	 */
	protected function generar_sql_delete($id_registro)
	{
		$registro = $this->datos[$id_registro];
		if($this->_baja_logica){
			$sql = "UPDATE " . $this->_tabla .
					" SET " . $this->_baja_logica_columna . " = '". $this->_baja_logica_valor ."' " .
					" WHERE " . implode(" AND ",$this->generar_sql_where_registro($id_registro)) .";";
		}else{
			$sql = "DELETE FROM " . $this->_tabla .
					" WHERE " . implode(" AND ",$this->generar_sql_where_registro($id_registro) ) .";";
		}
		return $sql;
	}

	/**
	 * Genera la sentencia WHERE correspondiente a la clave de un registro
	 * @param mixed $id_registro Clave interna del registro
	 * @ignore 
	 */	
	function generar_sql_where_registro($id_registro)
	{
		foreach($this->_clave as $clave){
			$id[$clave] = $this->_cambios[$id_registro]['clave'][$clave];
		}
		return $this->generar_clausula_where_lineal($id,false);
	}
	
	/**
	 * Retorna los sql de insert de cada registro cargado en el datos_tabla, sin importar su estado actual
	 * @return array
	 */
	function get_sql_inserts()
	{
		$this->get_estado_datos_tabla();
		$sql = array();
		foreach(array_keys($this->_cambios) as $registro){
			$sql[] = $this->ejecutar_sql_insert($registro, true);
		}
		return $sql;
	}	
	
	//---------------------------------------------------------------------------
	//---------------  Carga de CAMPOS BLOB   -----------------------------------
	//---------------------------------------------------------------------------

	/**
	 * @ignore 
	 */
	function consultar_columna_blob($id_registro, $columna)
	{
		$this->get_estado_datos_tabla();
		$sql = "SELECT $columna FROM " . $this->_tabla .
					" WHERE " . implode(" AND ",$this->generar_sql_where_registro($id_registro) ) .";";
			
		$this->log("Carga BLOB de columna '$columna' de fila '$id_registro':\n ". $sql);
		$datos = toba::db($this->_fuente)->consultar_fila($sql);
		if (! empty($datos)) {
			return $datos[$columna];
		}
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
	 * @todo Este mecanismo requiere OPTIMIZACION (Mas que nada para la carga inicial)
	 * @ignore 
	 */
	function completar_campos_externos_fila($fila, $evento=null)
	{
		//Itero planes de carga externa
		$valores_recuperados = array();
		if(isset($this->_proceso_carga_externa)){
			foreach(array_keys($this->_proceso_carga_externa) as $carga)
			{
				$parametros = $this->_proceso_carga_externa[$carga];
				//Si la columna no solicito sincro continua, paso a la siguiente.
				if(isset($evento)&& !($parametros["sincro_continua"])) continue;
				//Controlo que los parametros del cargador me alcanzan para recuperar datos de la DB
				$estan_todos = true;
				foreach( $parametros['col_parametro'] as $col_llave ){
					if(isset($evento) && isset($this->_secuencias[$col_llave])){
						throw new toba_error('AP_TABLA_DB: No puede actualizarse en linea un valor que dependende de una secuencia');
					}
					if(!isset($fila[$col_llave])){
						$estan_todos = false;
					}
				}
				//Si algun valor requerido no esta seteado, no ejecutar la carga				
				if (! $estan_todos) {
					continue;
				}
				//-[ 1 ]- Recupero valores correspondientes al registro
				if($parametros['tipo']=="sql")											//--- carga SQL!!
				{
					// - 1 - Obtengo el query
					$sql = $parametros['sql'];
					// - 2 - Reemplazo valores llave con los parametros correspondientes a la fila actual
					foreach( $parametros['col_parametro'] as $col_llave ){
						$valor_llave = $fila[$col_llave];
						$sql = str_replace(apex_db_registros_separador.$col_llave.apex_db_registros_separador, $valor_llave, $sql);
					}
					// - 3 - Ejecuto SQL
					$datos = toba::db($this->_fuente)->consultar($sql);
					if(!$datos){
						toba::logger()->error('AP_TABLA_DB: no se recuperaron datos ' . $sql, 'toba');
						throw new toba_error('AP_TABLA_DB: ERROR en la carga de una columna externa.');
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
					if (isset($parametros['clase']) && isset($parametros['include'])) {
						if(!class_exists($parametros['clase'])) {
							require_once($parametros['include']);
						}
						$datos = call_user_func_array(array($parametros['clase'],$parametros['metodo']), $param_dao);
					} else {
						if( method_exists($this, $parametros['metodo'])) {
							$datos = call_user_func_array(array($this,$parametros['metodo']), $param_dao);				
						} else {
							throw new toba_error('AP_TABLA_DB: ERROR en la carga de una columna externa. El metodo: '. $parametros['metodo'] .' no esta definido');
						}
					}
				}
				//ei_arbol($datos,"datos");
				//-[ 2 ]- Seteo los valores recuperados en las columnas correspondientes
				if(count($datos)>0){
					foreach( $parametros['col_resultado'] as $columna_externa ){
						if(is_array($columna_externa)){
							//Hay una regla de mapeo entre el valor devuelto y la columna del DT
							if(!array_key_exists($columna_externa['origen'], $datos[0])){
								toba::logger()->error("AP_TABLA_DB: Se esperaba que que conjunto de valores devueltos posean la columna '{$columna_externa['origen']}'", 'toba');
								throw new toba_error('AP_TABLA_DB: ERROR en la carga de una columna externa.');
							}
							$valores_recuperados[$columna_externa['destino']] = $datos[0][$columna_externa['origen']];
						}else{
							if(!array_key_exists($columna_externa, $datos[0])){
								toba::logger()->error("AP_TABLA_DB: Se esperaba que que conjunto de valores devueltos posean la columna '$columna_externa'", 'toba');
								toba::logger()->error($datos, 'toba');
								throw new toba_error('AP_TABLA_DB: ERROR en la carga de una columna externa.');
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

	/**
	 * @ignore 
	 */
	private function controlar_alteracion_db()
	{
	}

	/**
	 * @ignore 
	 */
	private function controlar_alteracion_db_array()
	//Soporte al manejo transaccional OPTIMISTA
	//Indica si los datos iniciales extraidos de la base difieren de
	//los datos existentes en el momento de realizar la transaccion
	{
	}
	//-------------------------------------------------------------------------------

	/**
	 * @ignore 
	 */
	private function controlar_alteracion_db_timestamp()
	//Esto tiene que basarse en una forma generica de trabajar sobre tablas
	//(Una columna que posea el timestamp, y triggers que los actualicen)
	{
	}
	//-------------------------------------------------------------------------------
}
?>
