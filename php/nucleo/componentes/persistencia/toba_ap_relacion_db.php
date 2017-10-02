<?php
/**
 * Administrador de persistencia de un relación a una DB relacion. Puede cargar y sincronizar un grupo de tablas
 * @package Componentes\Persistencia
 */	
class toba_ap_relacion_db implements toba_ap_relacion
{
	protected $_objeto_relacion; 				//toba_datos_relacion que persiste
	protected $_utilizar_transaccion;			//Determina si la sincronizacion con la DB se ejecuta dentro de una transaccion
	protected $_retrazar_constraints=false;		//Intenta retrasar el chequeo de claves foraneas hasta el fin de la transacción
	
	/**
	 * @param toba_datos_relacion $objeto_relacion Relación que persiste
	 */
	final function __construct($objeto_relacion)
	{
		$this->objeto_relacion = $objeto_relacion;
		$this->activar_transaccion();
		$this->ini();
	}
	

	/**
	 * Ventana para agregar configuraciones particulares despues de la construccion
	 * @ventana
	 */
	protected function ini(){}

	/**
	 * Método de debug que retorna las propiedades internas
	 * @return array
	 */
	function info()
	{
		return get_object_vars($this);
	}

	//-------------------------------------------------------------------------------
	//------  Configuracion  ----------------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Comando que fuerza una transacción a la hora de la sincronización
	 */
	function activar_transaccion()		
	{
		$this->_utilizar_transaccion = true;
	}

	/**
	 * Fuerza a no utilizar una transacción a la hora de la sincronización.
	 * Generalmente por que la transaccion la abre/cierra algun proceso de nivel superior
	 */
	function desactivar_transaccion($recursivo=false)		
	{
		$this->_utilizar_transaccion = false;
		if($recursivo) {
			foreach($this->objeto_relacion->get_tablas() as $tabla) {
				$tabla->persistidor()->desactivar_transaccion();
			}
		}
	}
	
	/**
	 * Retraza el chequeo de constraints hasta el final de la transacción
	 */
	function retrasar_constraints()
	{
		$this->_retrazar_constraints = true;	
	}
	
	/**
	 * Activa/Desactiva el uso automático del trim sobre datos en el insert o update
	 * @param boolean $usar
	 */
	function set_usar_trim($usar)
	{
		$tablas = $this->objeto_relacion->orden_sincronizacion();
		foreach ($tablas as $tabla) {
			$tabla->persistidor()->set_usar_trim($usar);
		}
	}

	/**
	 * Activa/Desactiva un mecanismo de chequeo de concurrencia en la edición
	 */
	function set_lock_optimista($usar=true)
	{
		foreach ($this->objeto_relacion->get_tablas() as $tabla) {
			$tabla->persistidor()->set_lock_optimista($usar);
		}
	}

	/**
	 * Activa el uso de perfil de datos para toda la relacion 
	 */
	function activar_perfil_de_datos()
	{
		foreach ($this->objeto_relacion->get_tablas() as $tabla) {
			$tabla->persistidor()->activar_perfil_de_datos();
		}
	}
		
	//-------------------------------------------------------------------------------
	//------  CARGA  ----------------------------------------------------------------
	//-------------------------------------------------------------------------------
	
	/**
	 * Se cargan las tablas de la relación restringiendo por las claves de las tablas raiz
	 * @param array $clave Asociativo campo=>valor correspondientes a campos de la(s) tabla(s) raiz
	 * @return boolean Verdadero si al menos se carga una tabla
	 */
	function cargar_por_clave($clave)
	{
		toba_asercion::es_array($clave, "Error cargando la relación, se esperaba un arreglo asociativo por ejemplo ".
													"<pre>\$relacion->cargar(array('campo'=> 'valor'))</pre>", true);
		
		$this->objeto_relacion->resetear();		
		$tablas_raiz = $this->objeto_relacion->get_tablas_raiz();
		toba_asercion::es_array($tablas_raiz, "Error cargando la relación, se esperaba un arreglo con las tablas raiz de la relacion.", true);		
		$tablas = $this->objeto_relacion->orden_carga();		
		$ok = false;
		foreach ($tablas as $id_tabla => $tabla) {
			if (in_array($id_tabla, $tablas_raiz)) {
				//Si es una tabla raiz se le restringue por los campos pasados
				$res = $tabla->persistidor()->cargar_por_clave($clave);
			} else {
				//Sino se hace una carga común (en base a las cargas de los padres)
				$res = $tabla->persistidor()->cargar_por_clave(array());
			}
			$ok = $ok || $res;
		}
		$this->objeto_relacion->set_cargado($ok);
		return $ok;
	}
	
	/**
	 * Carga las tablas de la relación especificando wheres particulares para las distintas tablas
	 * @param array $wheres Arreglo id_tabla => condicion
	 * @return boolean Verdadero si al menos se carga una tabla
	 */
	function cargar_con_wheres($wheres, $resetear=true, $anexar_datos=false, $usar_cursores=false)
	{
		if ($resetear) {
			$this->objeto_relacion->resetear();
		}
		$tablas = $this->objeto_relacion->orden_carga();		
		$ok = false;
		foreach ($tablas as $id_tabla => $tabla) {
			if (isset($wheres[$id_tabla])) {
				$res = $tabla->persistidor()->cargar_con_where($wheres[$id_tabla], $anexar_datos, $usar_cursores);
			} else {
				$res = $tabla->persistidor()->cargar_por_clave(array(), $anexar_datos, $usar_cursores);
			}
			$ok = $ok || $res;
		}
		$this->objeto_relacion->set_cargado($ok);
		return $ok;
	}

	/**
	 * La relacion tiene datos cargados?
	 * @return boolean
	 */
	function esta_cargada()
	{
		return $this->objeto_relacion->esta_cargada();
	}

	//-------------------------------------------------------------------------------
	//------  SINCRONIZACION  -------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Sincroniza los cambios con la base de datos
	 * En caso de error se aborta la transacción (si tiene) y se lanza una excepción
	 */
	function sincronizar($usar_cursores=false, $filas_tablas=null)
	{
		$fuente = $this->objeto_relacion->get_fuente();
		try{
			if($this->_utilizar_transaccion) {
				abrir_transaccion($fuente);
				if ($this->_retrazar_constraints) {
					toba::db($fuente)->retrasar_constraints();
				}
			}
			$this->evt__pre_sincronizacion();
			$this->proceso_sincronizacion($usar_cursores, $filas_tablas);
			$this->evt__post_sincronizacion();
			if($this->_utilizar_transaccion) cerrar_transaccion($fuente);			
		}catch(toba_error $e){
			if($this->_utilizar_transaccion) abortar_transaccion($fuente);
			toba::logger()->debug($e, 'toba');
			throw $e;
		}					
	}

	/**
	 * Sincroniza las tabla de la raiz, de ahi en mas sigue el proceso a travez de las relaciones
	 * @ignore 
	 */
	protected function proceso_sincronizacion($usar_cursores=false, $filas_tablas=null)
	{
		if(isset($filas_tablas)){
			if (!is_array($filas_tablas)) {
				throw new toba_error("La sincronizacion por filas requiere que se indiquen las filas a sincronizar por tabla en un array.");
			}
			if ($usar_cursores) {
				throw new toba_error("La sincronizacion por filas no puede utilizarse junto a la sincronizacion por cursores.");
			}
			$sincro_por_filas = true;
			toba::logger()->info("AP_RELACION: Sincronizacion por FILAS", 'toba');			
		} else {
			$sincro_por_filas = false;
		}
		if($usar_cursores) {
			toba::logger()->info("AP_RELACION: Sincronizacion con CURSORES", 'toba');
		}
		
		$tablas = $this->objeto_relacion->orden_sincronizacion();

		//-- [0] Se llaman a la ventan pre sincronización general
		foreach ($tablas as $tabla) {
			$tabla->persistidor()->evt__pre_sincronizacion();
		}		

		//-- [1] Se sincroniza las operaciones de eliminación, en orden inverso
		foreach (array_reverse($tablas) as $tabla) {
			if($usar_cursores){
				$filas = $tabla->get_id_filas_filtradas_por_cursor(true);
				if($filas) {
					$tabla->persistidor()->sincronizar_eliminados($filas);
				}
			} elseif ($sincro_por_filas) { 
				if(isset($filas_tablas[$tabla])) {
					if (!is_array($filas_tablas[$tabla])) {
						throw new toba_error("Sincronizacion por filas: error en la definicion de las filas de la tabla: $tabla. El parametro debe ser un array");
					}
					$tabla->persistidor()->sincronizar_eliminados($filas_tablas[$tabla]);
				}
			} else {
				$tabla->persistidor()->sincronizar_eliminados();
			}
		}		
		
		//-- [2] Se sincroniza las operaciones de actualizacion (insert)
		foreach ($tablas as $tabla) {
			if($usar_cursores){
				$filas = $tabla->get_id_filas_filtradas_por_cursor();
				if($filas) {
					toba::logger()->info("AP_RELACION: Sincronizar INSERTS con CURSOR [[[[".$tabla->get_tabla()."]]]] - FILAS(".implode(',',$filas).")", 'toba');
					$tabla->persistidor()->sincronizar_insertados($filas);
					$tabla->notificar_hijos_sincronizacion($filas);
				}
			} elseif ($sincro_por_filas) { 
				if(isset($filas_tablas[$tabla])) {
					if (!is_array($filas_tablas[$tabla])) {
						throw new toba_error("Sincronizacion por filas: error en la definicion de las filas de la tabla: $tabla. El parametro debe ser un array");
					}
					$tabla->persistidor()->sincronizar_insertados($filas_tablas[$tabla]);
					$tabla->notificar_hijos_sincronizacion($filas_tablas[$tabla]);
				}			
			} else {
				$tabla->persistidor()->sincronizar_insertados();
				$tabla->notificar_hijos_sincronizacion();
			}
		}
		
		//-- [3] Se sincroniza las operaciones de actualizacion (update)
		foreach ($tablas as $tabla) {
			if($usar_cursores){
				$filas = $tabla->get_id_filas_filtradas_por_cursor();
				if($filas) {
					$tabla->persistidor()->sincronizar_actualizados($filas);
					$tabla->notificar_hijos_sincronizacion($filas);
				}
			} elseif ($sincro_por_filas) { 
				if(isset($filas_tablas[$tabla])) {
					if (!is_array($filas_tablas[$tabla])) {
						throw new toba_error("Sincronizacion por filas: error en la definicion de las filas de la tabla: $tabla. El parametro debe ser un array");
					}
					$tabla->persistidor()->sincronizar_actualizados($filas_tablas[$tabla]);
					$tabla->notificar_hijos_sincronizacion($filas_tablas[$tabla]);
				}		
			} else {
				$tabla->persistidor()->sincronizar_actualizados();
				$tabla->notificar_hijos_sincronizacion();
			}
		}		

		//-- [4] Se llaman a la ventan post sincronización general
		foreach ($tablas as $tabla) {
			$tabla->persistidor()->evt__post_sincronizacion();
		}		
	}
	
	/**
	 * Ventana para incluír validaciones (disparar una excepcion) o disparar procesos previo a sincronizar
	 * La transacción con la bd ya fue iniciada (si es que hay)
	 * @ventana
	 */
	protected function evt__pre_sincronizacion(){}
	
	/**
	 * Ventana para incluír validaciones (disparar una excepcion) o disparar procesos posteriores a la sincronización
	 * La transacción con la bd aún no fue terminada (si es que hay)
	 * @ventana
	 */	
	protected function evt__post_sincronizacion(){}

	//-------------------------------------------------------------------------------
	//------  ELIMINACION  -------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Elimina cada elemento de las tabla de la relación y luego sincroniza con la base
	 * Todo el proceso se ejecuta dentro de una transacción, si se definio así
	 */
	function eliminar_todo()
	{
		$fuente = $this->objeto_relacion->get_fuente();		
		try {
			if ($this->_utilizar_transaccion) {
				abrir_transaccion($fuente);
				if ($this->_retrazar_constraints) {
					toba::db($fuente)->retrasar_constraints();
				}
			}
			$this->evt__pre_eliminacion();
			$this->eliminar_plan();
			$this->evt__post_eliminacion();
			if ($this->_utilizar_transaccion) {
				cerrar_transaccion($fuente);
			}
		} catch(toba_error $e) {
			if($this->_utilizar_transaccion) {
				abortar_transaccion($fuente);
			}
			throw $e;
		}
	}

	/**
	 * @ignore 
	 */
	protected function eliminar_plan()
	{
		$tablas = $this->objeto_relacion->orden_sincronizacion();		
		//-- Se elimina las tablas, en orden inverso
		foreach (array_reverse($tablas) as $tabla) {
			$tabla->eliminar_todo();
		}	
	}

	/**
	 * Ventana para incluír validaciones (disparar una excepcion) o disparar procesos previo a la eliminación
	 * La transacción con la bd ya fue iniciada (si es que hay)
	 * @ventana
	 */
	protected function evt__pre_eliminacion(){}
	
	/**
	 * Ventana para incluír validaciones (disparar una excepcion) o disparar procesos posteriores a la eliminación
	 * La transacción con la bd ya fue iniciada (si es que hay)
	 * @ventana
	 */	
	protected function evt__post_eliminacion(){}

}
?>
