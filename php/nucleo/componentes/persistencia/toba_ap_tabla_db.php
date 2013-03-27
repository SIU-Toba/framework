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
 
abstract class toba_ap_tabla_db implements toba_ap_tabla
{
	const tipo_tabla_unica = 'st';
	const tipo_multitabla  = 'mt';

	/**
	 * @var toba_datos_tabla
	 */
	protected $objeto_tabla;					// DATOS_TABLA: Referencia al objeto asociado
	protected $_columnas;						// DATOS_TABLA: Estructura del objeto
	protected $datos;							// DATOS_TABLA: DATOS que conforman las filas
	protected $_cambios;						// DATOS_TABLA: Estado de los cambios
	protected $_tabla;							// DATOS_TABLA: Tabla
	protected $_alias;							// DATOS_TABLA: Alias
	protected $_clave;							// DATOS_TABLA: Clave
	protected $_fuente;							// DATOS_TABLA: Fuente de datos
	protected $_secuencias;
	protected $_columnas_predeterminadas_db;	// Manejo de datos generados por el motor (autonumericos, predeterninados, etc)
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
	protected $_hacer_trim_datos = true;		// Hace un trim de los datos en el insert/update
	protected $_lock_optimista = true;
	//-------------------------------
	protected $_insert_campos_default = array();
	protected $_usar_perfil_de_datos = false;
	
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
		$this->_schema = $this->objeto_tabla->get_schema();		
		
		//Determino las secuencias de la tabla
		foreach($this->_columnas as $columna){
			if( $columna['secuencia']!=""){
				$this->_secuencias[$columna['columna']] = $columna['secuencia'];
			}
		}
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
		toba::logger()->debug("AP_TABLA: [{$this->_tabla}]\n".$txt, 'toba');
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
	 * Activa el uso de perfil de datos en la carga del componente
	 */
	function activar_perfil_de_datos()
	{
		$this->_usar_perfil_de_datos = true;
	}
	
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
	 * @param string $sql Query de carga que devolvera un registro conteniendo las columnas 'externas'
	 * @param array $col_parametros Columnas que espera recibir el sql, en la sql necesitan esta el campo entre % (%nombre_campo%)
	 * @param array $col_resultado Columnas del registro resultante que se tomarán para rellenar la tabla
	 * @param boolean $sincro_continua En cada pedido de página ejecuta la sql para actualizar los valores de las columnas
	 * @param boolean $estricto Indica si es imperioso que la columna externa posea un estado o se
	 * permite que no posea valor.
	 */
	function activar_proceso_carga_externa_sql($sql, $col_parametros, $col_resultado, $sincro_continua=true, $estricto = true)
	{
		$proximo = count($this->_proceso_carga_externa);
		$this->_proceso_carga_externa[$proximo]["tipo"] = "sql";
		$this->_proceso_carga_externa[$proximo]["sql"] = $sql;
		$this->_proceso_carga_externa[$proximo]["col_parametro"] = $col_parametros;
		$this->_proceso_carga_externa[$proximo]["col_resultado"] = $col_resultado;
		$this->_proceso_carga_externa[$proximo]["sincro_continua"] = $sincro_continua;
		$this->_proceso_carga_externa[$proximo]["dato_estricto"] = $estricto;
	}

	/**
	 * Carga una columna separada del proceso común de carga
	 * Se brinda un DAO que carga una o más columnas denominadas como 'externas'
	 * Una columna externa no participa en la sincronización posterior, pero por necesidades casi siempre estéticas
	 * necesitan mantenerse junto al conjunto de datos.
	 *
	 * @param string $metodo Método que obtiene los datos.
	 * @param string $clase  Clase a la que pertenece el método.	Si es NULL usa el mismo AP
	 * @param string $include Archivo donde se encuentra la clase.	Si es NULL usa el mismo AP
	 * @param array $col_parametros Columnas que espera recibir el DAO.
	 * @param array $col_resultado Columnas del registro resultante que se tomarán para rellenar la tabla
	 * @param boolean $sincro_continua En cada pedido de página ejecuta el DAO para actualizar los valores de las columnas
	 * @param boolean $estricto Indica si es imperioso que la columna externa posea un estado o se
	 * permite que no posea valor.	 
	 */
	function activar_proceso_carga_externa_dao($metodo, $clase=null, $include=null, $col_parametros, $col_resultado, $sincro_continua=true, $estricto=true, $carga_masiva = 0, $metodo_masivo = '')
	{
		$proximo = count($this->_proceso_carga_externa);
		$this->_proceso_carga_externa[$proximo]["tipo"] = "dao";
		$this->_proceso_carga_externa[$proximo]["metodo"] = $metodo;
		$this->_proceso_carga_externa[$proximo]["clase"] = $clase;
		$this->_proceso_carga_externa[$proximo]["include"] = $include;
		$this->_proceso_carga_externa[$proximo]["col_parametro"] = $col_parametros;
		$this->_proceso_carga_externa[$proximo]["col_resultado"] = $col_resultado;
		$this->_proceso_carga_externa[$proximo]["sincro_continua"] = $sincro_continua;
		$this->_proceso_carga_externa[$proximo]["dato_estricto"] = $estricto;
		$this->_proceso_carga_externa[$proximo]["permite_carga_masiva"] = $carga_masiva;
		$this->_proceso_carga_externa[$proximo]["metodo_masivo"] = $metodo_masivo;
	}

	/**
	 * Carga una columna separada del proceso común de carga
	 * Se brinda un Datos Tabla  que carga una o más columnas denominadas como 'externas'
	 * Una columna externa no participa en la sincronización posterior, pero por necesidades casi siempre estéticas
	 * necesitan mantenerse junto al conjunto de datos.
	 *
	 * @param string $tabla Identificador del objeto_datos_tabla a utilizar.
	 * @param string $metodo Método que obtiene los datos.
	 * @param array $col_parametros Columnas que espera recibir el DAO.
	 * @param array $col_resultado Columnas del registro resultante que se tomarán para rellenar la tabla
	 * @param boolean $sincro_continua En cada pedido de página ejecuta el DAO para actualizar los valores de las columnas
	 * @param boolean $estricto Indica si es imperioso que la columna externa posea un estado o se
	 * permite que no posea valor.
	 */
	function activar_proceso_carga_externa_datos_tabla($tabla, $metodo, $col_parametros, $col_resultado, $sincro_continua=true, $estricto=true, $carga_masiva = 0, $metodo_masivo = '')
	{
		$proximo = count($this->_proceso_carga_externa);
		$this->_proceso_carga_externa[$proximo]["tipo"] = "d_t";
		$this->_proceso_carga_externa[$proximo]["tabla"] = $tabla;
		$this->_proceso_carga_externa[$proximo]["metodo"] = $metodo;
		$this->_proceso_carga_externa[$proximo]["col_parametro"] = $col_parametros;
		$this->_proceso_carga_externa[$proximo]["col_resultado"] = $col_resultado;
		$this->_proceso_carga_externa[$proximo]["sincro_continua"] = $sincro_continua;
		$this->_proceso_carga_externa[$proximo]["dato_estricto"] = $estricto;
		$this->_proceso_carga_externa[$proximo]["permite_carga_masiva"] = $carga_masiva;
		$this->_proceso_carga_externa[$proximo]["metodo_masivo"] = $metodo_masivo;
	}

	function activar_proceso_carga_externa_consulta_php($metodo, $id_consulta_php, $col_parametros, $col_resultado, $sincro_continua=true, $estricto=true, $carga_masiva=0, $metodo_masivo = '')
	{
		$proximo = count($this->_proceso_carga_externa);
		$this->_proceso_carga_externa[$proximo]["tipo"] = "ccp";
		$this->_proceso_carga_externa[$proximo]["metodo"] = $metodo;
		$this->_proceso_carga_externa[$proximo]["clase"] = $id_consulta_php;
		$this->_proceso_carga_externa[$proximo]["col_parametro"] = $col_parametros;
		$this->_proceso_carga_externa[$proximo]["col_resultado"] = $col_resultado;
		$this->_proceso_carga_externa[$proximo]["sincro_continua"] = $sincro_continua;
		$this->_proceso_carga_externa[$proximo]["dato_estricto"] = $estricto;
		$this->_proceso_carga_externa[$proximo]["permite_carga_masiva"] = $carga_masiva;
		$this->_proceso_carga_externa[$proximo]["metodo_masivo"] = $metodo_masivo;
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
		$this->_schema = $schema;
	}
	
	/**
	 * Activa/Desactiva el uso automático del trim sobre datos en el insert o update
	 * @param boolean $usar
	 */	
	function set_usar_trim($usar)
	{
		$this->_hacer_trim_datos = $usar;
	}

	function get_usar_trim()
	{
		return $this->_hacer_trim_datos;
	}
	
	/**
	 * Activa/Desactiva un mecanismo de chequeo de concurrencia en la edición
	 */
	function set_lock_optimista($usar=true)
	{
		$this->_lock_optimista = $usar;
	}

	function get_lock_optimista()
	{
		return $this->_lock_optimista;
	}

	/**
	 * recibe una columna y una tabla y devuelve verdadero si la columna
	 * pertenece a la tabla y falso en caso contrario
	 */
	function pertenece_a_tabla(&$col, $tabla)
	{
		// si no está seteado $col['tabla'] entonces no puede ser un ap
		// multitabla, por tanto todas las columnas perteneces a la tabla $this->_tabla
		// porque no hay otra ;)
		return !isset($col['tabla']) || $tabla == $col['tabla'];
	}

	protected function agregar_schema($elemento, $es_externa = false)
	{
		$resultado = (is_null($this->_schema)) ? $elemento : $this->_schema . '.' . $elemento;
		return $resultado;
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
			if ($this->objeto_tabla->posee_columnas_externas()) {
				//Aca hay que decidir que hacer si la tabla ya tiene datos o si es la carga inicial
				if (! $this->objeto_tabla->esta_cargada()) {		//Seria la carga inicial?
					$datos = $this->carga_inicial_campos_externos($datos);
				} else {
					for ($a=0;$a<count($datos);$a++) {
						$campos_externos = $this->completar_campos_externos_fila($datos[$a]);
						foreach ($campos_externos as $id => $valor) {
							$datos[$a][$id] = $valor;
						}
					}
				}
			}
			
			// Lleno la TABLA
			if ( $anexar_datos ) {
				$this->objeto_tabla->anexar_datos($datos, $usar_cursores);
			} else {
				$this->objeto_tabla->cargar_con_datos($datos);
			}
			//ei_arbol($datos);
			return true;
		}else{
			//No se carga nada!
			$this->log(" FILAS: 0" );
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
	function sincronizar($filas=array())
	{
		$this->log("Inicio SINCRONIZAR");
		try{
			if($this->_utilizar_transaccion) abrir_transaccion($this->_fuente);
			$this->evt__pre_sincronizacion();		
			$modificaciones = 0;
			$modificaciones += $this->sincronizar_eliminados($filas);
			$modificaciones += $this->sincronizar_insertados($filas);
			$modificaciones += $this->sincronizar_actualizados($filas);
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
	function sincronizar_eliminados($filas=array())
	{
		$this->get_estado_datos_tabla();
		$modificaciones = 0;
		if($filas) {
			$registros = $filas;
		}else{
			$registros = array_keys($this->_cambios);
		}
		foreach($registros as $registro){
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
	function sincronizar_insertados($filas=array())
	{
		$this->get_estado_datos_tabla();
		$modificaciones = 0;
		if($filas) {
			$registros = $filas;
		}else{
			$registros = array_keys($this->_cambios);
		}
		foreach($registros as $registro){
			if ($this->_cambios[$registro]['estado'] == "i") {
				$this->evt__pre_insert($registro);
				$this->insertar_registro_db($registro);
				$this->evt__post_insert($registro);
				$modificaciones ++;
			}
		}
		//Seteo en la TABLA los datos generados durante la sincronizacion
		$this->actualizar_columnas_predeterminadas_db($filas);
		return $modificaciones;
	}
	

	/**
	 * Sincroniza con la BD aquellos registros que suponen actualizaciones
	 * @return integer Cantidad de modificaciones a la base
	 */
	function sincronizar_actualizados($filas=array())
	{
		$this->get_estado_datos_tabla();
		$modificaciones = 0;
		if($filas) {
			$registros = $filas;
		}else{
			$registros = array_keys($this->_cambios);
		}
		
		foreach($registros as $registro) {
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
		$this->_insert_campos_default = array();
		$this->ejecutar_sql_insert($id_registro);
		
		//Actualizo las secuencias
		if(count($this->_secuencias)>0) {
			foreach($this->_secuencias as $columna => $secuencia) {
				if ($this->es_seq_tabla_ext($columna)) {
					continue;
				}
				
				$secuencia = $this->agregar_schema($secuencia);			
				$valor = recuperar_secuencia($secuencia, $this->_fuente);
				//El valor es necesario en el evt__post_insert!!
				$this->datos[$id_registro][$columna] = $valor;
				$this->registrar_recuperacion_valor_db( $id_registro, $columna, $valor );
			}
		}

		//Actualizo los valores que tomaron los DEFAULT enviados
		if (! empty($this->_insert_campos_default)) {
			$id = array();
			foreach ($this->_clave as $campo_clave) {
				$id[$campo_clave] = $this->datos[$id_registro][$campo_clave];
			}
			$where = $this->generar_clausula_where_lineal($id, false);
			$sql =	$this->get_sql_campos_default($where);
			$fila_base = toba::db($this->_fuente)->consultar_fila($sql);

			if ($fila_base === false) {
				throw new toba_error("Se esperaba encontrar un registro", $sql);
			}
			foreach ($this->_insert_campos_default as $campo) {
				$this->registrar_recuperacion_valor_db($id_registro, $campo, $fila_base[$campo]);
			}
		}
	}

	abstract protected function es_seq_tabla_ext($col);

	abstract protected function get_sql_campos_default($where);
	
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
		$this->ejecutar_sql($sql, $id_registro);
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
	protected function actualizar_columnas_predeterminadas_db($filas=array())
	{
		if(isset($this->_columnas_predeterminadas_db)){
			foreach( $this->_columnas_predeterminadas_db as $id_registro => $columnas ){
				if($filas && !in_array($id_registro,$filas)) {
					continue;	
				}
				foreach( $columnas as $columna => $valor ){
					$this->objeto_tabla->set_fila_columna_valor($id_registro, $columna, $valor);
				}
			}
			unset($this->_columnas_predeterminadas_db);						
		}
	}

	/**
	 * Ventana para incluír validaciones (disparar una excepcion) o disparar procesos previo a sincronizar con la base de datos
	 * La transacción con la bd ya fue iniciada (si es que esta definida)
	 * @ventana
	 */
	function evt__pre_sincronizacion(){}
	
	/**
	 * Ventana para incluír validaciones (disparar una excepcion) o disparar procesos antes de terminar de sincronizar con la base de datos
	 * La transacción con la bd aún no se terminó (si es que esta definida)
	 * @ventana
	 */	
	function evt__post_sincronizacion(){}


	/**
	 * Ventana para manejar la pérdida de sincronización con la tabla en la base de datos
	 * El escenario es que ejecuto un update/delete usando los valores de las columnas originales y no arrojo resultados, con lo que se asume que alguien más modifico el registro en el medio
	 * La transacción con la bd aún no se terminó (si es que esta definida)
	 * 
	 * @param integer $id_fila Id. de fila de la tabla en la cual se encontró el problema
	 * @param string $sql_origen Sentencia que se intento ejecutar
	 * @ventana
	 */
	function evt__perdida_sincronizacion($id_fila, $sql_origen)
	{
		$mensaje_usuario = "Error de concurrencia en la edición de los datos.<br><br>".
							"Mientras Ud. editaba esta información, la misma fue modificada por alguien más. ".
							"Para garantizar consistencia sólo podrá guardar cambios luego de reiniciar la edición.<br>";

		//--Hace una consulta SQL contra la tabla para averiguar puntualmente cuál fue el cambio que llevo a esta situación
		$columnas = array();
		foreach ($this->_columnas as $col) {
			if(!$col['externa'] && $col['tipo'] != 'B') {
				$columnas[] = $col['columna'];
			}
		}
		$id = array();
		foreach($this->_clave as $clave){
			$id[$clave] = $this->_cambios[$id_fila]['clave'][$clave];
		}
		$where = $this->generar_clausula_where_lineal($id, false);
		$sql =	"SELECT\n\t" . implode(", \n\t", $columnas);
		$sql .= "\nFROM\n\t " . $this->agregar_schema($this->_tabla);
		$sql .= "\nWHERE ".implode(' AND ', $where);
		$fila_base = toba::db($this->_fuente)->consultar_fila($sql);
		
		//-- Averigua que cambio
		if ($fila_base === false) {
			$diff = "La fila '$id_fila' no existe en la base, fue borrada";
		} else {
			$fila_original = $this->_cambios[$id_fila]['original'];
			$diff = "<ul>";
			foreach ($columnas as $col) {
				if (! isset($fila_base[$col])) {
					$fila_base[$col] = null;
				}
				if (! isset($fila_original[$col])) {
					$fila_original[$col] = null;
				}
				$modificado = (string) $fila_base[$col] !== (string) $fila_original[$col];
				if ($modificado) {
					$anterior = isset($fila_original[$col]) ? "'".$fila_original[$col]."'" : 'NULL';
					$actual = isset($fila_base[$col]) ? "'".$fila_base[$col]."'" : 'NULL';
					$diff .= "<li>$col: tenía el valor $anterior y ahora tiene $actual </li>";
				}
			}
			$diff .= '</ul>';
		}
		
		$mensaje_debug = '';
		$mensaje_debug .= "<p><b>Tabla:</b> {$this->_tabla}</p>";
		$mensaje_debug .= "<p><b>Diff de datos:</b> Cambios en fila $id_fila ".$diff."</p>";
		$mensaje_debug .= "<p><b>SQL:</b> $sql_origen</p>";
		throw new toba_error_usuario($mensaje_usuario, $mensaje_debug);
	}


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
	protected function ejecutar_sql($sql, $id_fila=null)
	{
		$sen = toba::db($this->_fuente)->sentencia_preparar($sql);
		$reg = toba::db($this->_fuente)->sentencia_ejecutar($sen);
		if ($this->_lock_optimista && isset($id_fila) && $reg == 0) {
			$this->evt__perdida_sincronizacion($id_fila, $sql);
		}
	}
	
	protected function ejecutar_con_binarios($sql, $binarios, $id_fila = null)
	{
		$sen = toba::db($this->_fuente)->sentencia_preparar($sql);
		toba::db($this->_fuente)->sentencia_agregar_binarios($sen, $binarios);
		$reg = toba::db($this->_fuente)->sentencia_ejecutar($sen);
		toba::db($this->_fuente)->sentencia_eliminar($sen);
		if ($this->_lock_optimista && isset($id_fila) && $reg == 0) {
			$this->evt__perdida_sincronizacion($id_fila, $sql);
		}
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
			if (isset($valor)) {
				if (is_bool($valor)) {
					$valor = ($valor) ? 'true' : 'false';
				}
				$valor = toba::db($this->_fuente)->quote($valor);
				$clausula[] = "$tabla_alias" . "$columna = $valor";
			} else {
				$clausula[] = "$tabla_alias" . "$columna IS NULL";
			}
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
					$columnas[] = $this->get_select_col($col['columna']);
				}
			}
		}
		//Si no se explicitan los from se asume que es la tabla local
		if (!isset($from)) {
			$from = array($this->get_from_default());
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
		if ($this->_usar_perfil_de_datos) {					//Si el datos_tabla maneja perfil de datos
			$sql = toba::perfil_de_datos()->filtrar($sql);				
		}

		//Se guardan los datos de la carga
		$this->_sql_carga = array('from' => $from, 'where' => $where);
		return $sql;
	}


	abstract protected function get_select_col($col);

	abstract protected function get_from_default();

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
			throw new toba_error_def("AP-TABLA Db: La tabla no ha sido cargada en este pedido de página");
		}
	}
	
	
	/**
	 * @param mixed $id_registro Clave interna del registro
	 * @ignore 
	 */
	protected function ejecutar_sql_insert($id_registro, $solo_retornar=false, $tabla = null, $cols_tabla = array(), $tabla_ext = false)
	{
		$a=0;
		$registro = $this->datos[$id_registro];
		$db = toba::db($this->_fuente);
		
		//Arreglos donde se guardara la informacion
		$binarios = array();		
		$valores_sql = array();
		$columnas_sql = array();
		$valores_sql_binarios = array();
		$columnas_sql_binarios = array();
		
		//Determinacion para el DT multitabla		
		$tabla = (is_null($tabla)) ? $this->_tabla : $tabla;
		$columnas = (empty($cols_tabla)) ? $this->_columnas : $cols_tabla;
	
		foreach($columnas as $columna) {
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
						$valores_sql_binarios[$a] = '?';
						$columnas_sql_binarios[$a] = $col;						
					} else {
						//No tocar nada
					}
				} elseif ( !isset($registro[$col]) || $registro[$col] === NULL ) {
					//-- Es un campo NULO
					$valores_sql[$a] = $db->get_semantica_valor_defecto();
					$columnas_sql[$a] = $col;
					$this->_insert_campos_default[] = $col;
				} else {
					if (is_bool($registro[$col])) {		//Si es un valor booleano lo transformo a entero
						if ($registro[$col] === true) {
							$registro[$col] = 1;
						} elseif ($registro[$col] === false) {
							$registro[$col] = 0;
						}
					}						
					
					if ($this->_hacer_trim_datos) {
						$valores_sql[$a] =  $db->quote(trim($registro[$col]));
					} else {
						$valores_sql[$a] =  $db->quote($registro[$col]);
					}
					$columnas_sql[$a] = $col;
				}
				$a++;
			}
		}
		// Para evitar un "bug" de PDO se colocan los campos de tipo binario al inicio de la sentencia INSERT.
		$valores_sql = array_merge($valores_sql_binarios, $valores_sql);
		$columnas_sql = array_merge($columnas_sql_binarios, $columnas_sql);
		$sql = "INSERT INTO " . $this->agregar_schema($tabla, $tabla_ext) .
					" ( " . implode(", ", $columnas_sql) . " ) ".
					"\n VALUES (" . implode(", ", $valores_sql) . ");";
		if ($solo_retornar) {
			return $sql;
		}
		$this->log("registro: $id_registro - " . $sql); 															
		if (empty($binarios)) {
			$this->ejecutar_sql($sql);			
		} else {
			$this->ejecutar_con_binarios($sql, $binarios);
		}
	}	

	abstract protected function get_flag_mod_clave();

	/**
	 * @param mixed $id_registro Clave interna del registro
	 * @ignore 
	 */	
	protected function ejecutar_sql_update($id_registro, $tabla = null, $where = null, $cols_tabla = array(), $tabla_ext = false)			
	{
		$binarios = array();
		$registro = $this->datos[$id_registro];
		$cambios_reales = $this->objeto_tabla->get_cambios_fila($id_registro, $registro);
		$tabla = (is_null($tabla)) ? $this->_tabla : $tabla;
		$columnas = (empty($cols_tabla)) ? $this->_columnas : $cols_tabla;
		
		//Genero las sentencias de la clausula SET para cada columna
		$set = array();
		$db = toba::db($this->_fuente);
		foreach($columnas as $columna) {
			$col = $columna['columna'];
			$es_binario = ($columna['tipo'] == 'B');
			$es_secuencia = ($columna['secuencia'] != "");
			$es_externa = ($columna['externa'] == 1);
			$es_clave = ($columna['pk'] == 1);
			
			//columna modificable: realmente se modifico, no es secuencia, no es externa, 
			$es_modificable = !$es_secuencia && !$es_externa  && isset($cambios_reales[$col])
							&& (!$es_clave || ($es_clave && $this->get_flag_mod_clave() ));  //	no es PK (excepto que se se declare explicitamente la alteracion de PKs)
							
			if( $es_modificable ) {
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
				} elseif (is_bool($registro[$col])){
					$valor = $registro[$col] ? 1 : 0;
					$set[] = "$col = '$valor'";
				}else{
					if ($this->_hacer_trim_datos) {					
						$set[] = "$col = " . $db->quote(trim($registro[$col]));
					} else {
						$set[] = "$col = " . $db->quote($registro[$col]);
					}
				}
			}
		}
		if(empty($set)){
			$this->log('No hay campos para hacer el UPDATE');
			return null;	
		}
		//Armo el SQL
		$where = (is_null($where)) ? $this->generar_sql_where_registro($id_registro) : $where;
		$sql = "UPDATE " . $this->agregar_schema($tabla, $tabla_ext) . "\nSET ".
				implode(",\n\t",$set) .
				"\nWHERE " . implode("\n\tAND ", $where ) .";";
		$this->log("registro: $id_registro\n " . $sql);
		if (empty($binarios)) {
			$this->ejecutar_sql($sql, $id_registro);
		} else {			
			$this->ejecutar_con_binarios($sql, $binarios, $id_registro);
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
			$sql = "UPDATE " . $this->agregar_schema($this->_tabla) .
					" SET " . $this->_baja_logica_columna . " = '". $this->_baja_logica_valor ."' " .
					" WHERE " . implode(" AND ",$this->generar_sql_where_registro($id_registro)) .";";
		}else{
			$sql = "DELETE FROM " . $this->agregar_schema($this->_tabla) .
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
		$id = array();
		if (! $this->_lock_optimista) {
			//Sin lock optimista arma el where sólo con las claves
			foreach($this->_clave as $clave){
				$id[$clave] = $this->_cambios[$id_registro]['clave'][$clave];
			}
		} else {
			//Con lock optimista arma el where con todos los campos originales, asegurando que no sean columnas externas
			foreach ($this->_columnas as $col) {
				if (!$this->pertenece_a_tabla($col, $this->_tabla)) continue;
				if(!$col['externa'] && $col['tipo'] != 'B') {
					if (isset($this->_cambios[$id_registro]['original'][$col['columna']])) {
						$id[$col['columna']] = $this->_cambios[$id_registro]['original'][$col['columna']];
					} else {
						$id[$col['columna']] = null;
					}
				}
			}
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
		$sql = "SELECT $columna FROM " . $this->agregar_schema($this->_tabla) .
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
	 * cuyas claves asociativas se contengan la columna que se quiere llenar
	 * @param array $fila Fila que toma de referencia la carga externa
	 * @param string $evento 
	 * @return array Se devuelven los valores recuperados de la DB.
	 * @ignore 
	 */
	function completar_campos_externos_fila($fila, $evento=null)
	{
		//Itero planes de carga externa
		$valores_recuperados = array();
		if (isset($this->_proceso_carga_externa)) {
			foreach(array_keys($this->_proceso_carga_externa) as $carga)
			{
				$parametros = $this->_proceso_carga_externa[$carga];
				//Si la columna no solicito sincro continua, paso a la siguiente.
				if(isset($evento)&& !($parametros["sincro_continua"])) continue;
				//Si algun valor requerido no esta seteado, no ejecutar la carga				
				if (! $this->verificar_existencia_valores($fila, $parametros, $evento)) {
					continue;
				}
				$valores_recuperados = array_merge($valores_recuperados, $this->completa_campos_externos_fila_con_proceso($fila, $parametros));
			}
		}
		return $valores_recuperados;
	}

	function carga_inicial_campos_externos($datos)
	{
			$this->log('Inicio carga de campos externos');
			if (isset($this->_proceso_carga_externa)) {
				foreach(array_keys($this->_proceso_carga_externa) as $carga) {
					$parametros = $this->_proceso_carga_externa[$carga];
					if (isset($parametros['permite_carga_masiva']) && $parametros['permite_carga_masiva'] == '1') {
						$claves = $this->get_valores_llaves($datos, $parametros);
						$recuperado = array();
						//Aca tengo que decidir el tipo de carga y llamar al correspondiente
						switch($parametros['tipo']){
							case 'ccp':
										$recuperado = $this->usar_clase_consulta_php($claves, $parametros, true);
										break;
							case 'dao':
										$recuperado = $this->usar_metodo_dao($claves, $parametros, true);
										break;
							case 'd_t':
										$recuperado = $this->usar_metodo_dt($claves, $parametros, true);
										break;
						}
						$datos = $this->adjuntar_campos_externos_masivo($datos, $recuperado, $parametros);
					} else {
						//Aca tengo que ciclar por los datos como hice antes
						for ($a=0;$a<count($datos);$a++) {							
							$campos_externos = $this->completa_campos_externos_fila_con_proceso($datos[$a], $parametros);
							if (is_array($campos_externos)) {
								foreach ($campos_externos as $id => $valor) {
									$datos[$a][$id] = $valor;
								}
							}
						}
					}				
				}
			}
			//ei_arbol($datos);
			return $datos;
	}

	protected function completa_campos_externos_fila_con_proceso($fila, $proceso)
	{
			$recuperado = array();
			if ($this->verificar_existencia_valores($fila, $proceso)) {	//Verifico que esten las claves para la carga
					switch($proceso['tipo']) {
						case 'sql':
									$recuperado = $this->usar_metodo_sql_fila($fila, $proceso);
									break;
						case 'dao':
									$param_dao = array();
									foreach ($proceso['col_parametro'] as $col_llave) {
										$param_dao[] = $fila[$col_llave];
									}
									$recuperado = $this->usar_metodo_dao($param_dao, $proceso);
									break;
						case 'd_t':
									$param_dao = array();
									foreach ($proceso['col_parametro'] as $col_llave) {
										$param_dao[] = $fila[$col_llave];
									}
									$recuperado = $this->usar_metodo_dt($param_dao, $proceso);
									break;
						case 'ccp':
									$param_dao = array();
									foreach ($proceso['col_parametro'] as $col_llave) {
										$param_dao[] = $fila[$col_llave];
									}
									$recuperado = $this->usar_clase_consulta_php($param_dao, $proceso);
									break;
					}
			}
			if (! empty($recuperado)) {
				$recuperado = $this->adjuntar_campos_externos($recuperado, $proceso);
			}
			return $recuperado;
	}

	protected function usar_metodo_sql_fila($fila, $parametros)
	{
		// - 1 - Obtengo el query
		$sql = $parametros['sql'];
		// - 2 - Reemplazo valores llave con los parametros correspondientes a la fila actual
		foreach($parametros['col_parametro'] as $col_llave) {
			$valor_llave = $fila[$col_llave];
			$sql = str_replace(apex_db_registros_separador.$col_llave.apex_db_registros_separador, $valor_llave, $sql);
		}
		// - 3 - Ejecuto SQL
		toba::logger()->debug($sql);
		$datos = toba::db($this->_fuente)->consultar($sql);
		$es_obligatoria = ($parametros['dato_estricto'] == '1');
		if (!$datos && $es_obligatoria) {
			$this->log(" no se recuperaron datos " . $sql, 'toba');
			throw new toba_error_def("AP_TABLA: [{$this->_tabla}]:\n ERROR en la carga de una columna externa.");
		}
		return $datos;
	}

	protected function usar_metodo_dao($param_dao, $parametros, $es_carga_inicial = false)
	{
		//Elijo el metodo de carga dependiendo de si es masiva o no.
		if ($es_carga_inicial && isset($parametros['permite_carga_masiva']) && $parametros['permite_carga_masiva'] == '1') {
			$nombre_metodo = $parametros['metodo_masivo'];
		} else {
			$nombre_metodo = $parametros['metodo'];
		}
		// - 2 - Recupero datos
		if (isset($parametros['clase']) && isset($parametros['include'])) {
			if (!class_exists($parametros['clase'])) {
				require_once($parametros['include']);
			}
			$datos = call_user_func_array(array($parametros['clase'],$nombre_metodo), $param_dao);
		} else {
			if (method_exists($this, $nombre_metodo)) {
				$datos = call_user_func_array(array($this,$nombre_metodo), $param_dao);
			}else {
				$this->log(' ERROR en la carga de una columna externa. El metodo: '. $nombre_metodo .' no esta definido');
				throw new toba_error_def('AP_TABLA_DB: ERROR en la carga de una columna externa. El metodo: '. $nombre_metodo .' no esta definido');
			}
		}
		return $datos;
	}

	protected function usar_metodo_dt($param_dt, $parametros, $es_carga_inicial = false)
	{
		//Elijo el metodo de carga dependiendo de si es masiva o no.
		if ($es_carga_inicial && isset($parametros['permite_carga_masiva']) && $parametros['permite_carga_masiva'] == '1') {
			$nombre_metodo = $parametros['metodo_masivo'];
		} else {
			$nombre_metodo = $parametros['metodo'];
		}

		$id = array('proyecto' =>  toba::proyecto()->get_id(), 'componente' => $parametros['tabla']);
		$dt = toba_constructor::get_runtime($id, 'toba_datos_tabla');
		 if (! method_exists($dt, $nombre_metodo)) {
			$clase = get_class($dt);
			$this->log("ERROR en la carga de una columna externa. No existe el método '$nombre_metodo' de la clase '$clase'");
			throw new toba_error_def("AP_TABLA_DB: ERROR en la carga de una columna externa. No existe el método '$nombre_metodo' de la clase '$clase'");
		}
		$datos = call_user_func_array(array($dt, $nombre_metodo), $param_dt);
		return $datos;
	}

	protected function usar_clase_consulta_php($param_clase, $parametros, $es_carga_inicial = false)
	{
		//Elijo el metodo de carga dependiendo de si es masiva o no.
		if ($es_carga_inicial && isset($parametros['permite_carga_masiva']) && $parametros['permite_carga_masiva'] == '1') {
			$nombre_metodo = $parametros['metodo_masivo'];
		} else {
			$nombre_metodo = $parametros['metodo'];
		}
		//Recupero el objeto asociado a la clase php
		$obj = toba::consulta_php($parametros['clase']);
		if (method_exists($obj, $nombre_metodo)) {
				$datos = call_user_func_array(array($obj,$nombre_metodo), $param_clase);
		}else {
			$this->log(' ERROR en la carga de una columna externa. El metodo: '. $nombre_metodo .' no esta definido en la clase de consulta '. $parametros['clase']);
			throw new toba_error_def('AP_TABLA_DB: ERROR en la carga de una columna externa. El metodo: '. $nombre_metodo .' no esta definido');
		}
		return $datos;
	}

	protected function get_valores_llaves($datos, $parametros)
	{
		$claves = array();
		//Controlo que los parametros del cargador me alcanzan para recuperar datos de la DB
		foreach( $parametros['col_parametro'] as $col_llave ) {			//Ciclo por las columnas clave
			$claves[$col_llave] = array();
			foreach(array_keys($datos) as $indice) {
				$claves[$col_llave][$indice] = $datos[$indice][$col_llave];
			}
		}
		return $claves;
	}

	protected function adjuntar_campos_externos_masivo($datos, $externos, $parametros)
	{
		$campos_externos = array();
		$es_obligatoria = ($parametros['dato_estricto'] == '1');
		$claves_carga = array_fill_keys(array_values($parametros['col_parametro']), 0);
		foreach($externos as $externo) {			
			$cmp_indice = array_intersect_key($externo, $claves_carga);
			$indice = implode('_', $cmp_indice);
			if (isset($indice) && ($indice != '')) {
					$campos_externos[$indice] = $externo;
			}
		}
		if (empty($campos_externos) && $es_obligatoria) {
			$this->log('El método de carga masiva no devuelve los campos clave, no se puede adjuntar los datos externos');
			throw new toba_error_def('AP_TABLA_DB: ERROR El método de carga no devuelve los campos clave, no se puede adjuntar los datos externos');
		}
		$claves = array_keys($datos);
		foreach($claves as $id) {
			$cmp_indice = array_intersect_key($datos[$id], $claves_carga);
			$indice = implode('_', $cmp_indice);
			if (isset($campos_externos[$indice])) {
				$datos[$id] = array_merge($campos_externos[$indice], $datos[$id]);
			} elseif ($es_obligatoria) {
				toba::logger()->error("AP_TABLA_DB [{$this->_tabla}]: \n No se recupero un valor para la columna externa durante la carga masiva", 'toba');
				toba::logger()->error($datos, 'toba');
				throw new toba_error_def('AP_TABLA_DB: ERROR No se recupero un valor para la columna externa durante la carga masiva.');
			}
		}
		return $datos;
	}

	protected function adjuntar_campos_externos($datos, $parametros)
	{
		$valores_recuperados = array();
		if (count($datos) > 0) {
			$es_obligatoria = ($parametros['dato_estricto'] == '1');
			foreach($parametros['col_resultado'] as $columna_externa) {
				if (is_array($columna_externa)) {
					$clave_buscada = $columna_externa['origen'];
					$clave_destino = $columna_externa['destino'];
				} else {
					$clave_buscada = $columna_externa;
					$clave_destino = $columna_externa;
				}
				$datos_recordset = current($datos);
				if (! array_key_exists($clave_buscada, $datos) && ! array_key_exists($clave_buscada, $datos_recordset) && $es_obligatoria) {
					toba::logger()->error("AP_TABLA_DB [{$this->_tabla}]: \n Se esperaba que que conjunto de valores devueltos posean la columna '$clave_buscada'", 'toba');
					toba::logger()->error($datos, 'toba');
					throw new toba_error_def('AP_TABLA_DB: ERROR en la carga de una columna externa.');
				}
				$valores_recuperados[$clave_destino] = (isset($datos[$clave_buscada])) ? $datos[$clave_buscada]: $datos_recordset[$clave_buscada];
			}//fe
		}
		return $valores_recuperados;
	}

	protected function verificar_existencia_valores($fila, $parametros, $evento = null)
	{
			//Controlo que los parametros del cargador me alcanzan para recuperar datos de la DB
			$estan_todos = true;
			foreach( $parametros['col_parametro'] as $col_llave ) {
				if (isset($evento) && isset($this->_secuencias[$col_llave])) {
					throw new toba_error_def("AP_TABLA: [{$this->_tabla}]:\n No puede actualizarse en linea un valor que dependende de una secuencia");
				}
				if (!isset($fila[$col_llave])) {
					$estan_todos = false;
				}
			}
			return $estan_todos;
	}
}
?>
