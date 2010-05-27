<?php
/**
 * Este componente permite unificar la carga y entrega de datos y servicios a una jerarquia completa de componentes de interface (especialmente a los cis)
 * 
 * Separar la carga y utilización de los datos (inicio y fin de una transaccion de negocios)
 * permite:
 *  -Lograr una maxima independencia entre la logica de pantalla y de la de negocio, 
 *  -Tener un lugar centralizado para brindar servicios comunes a una jerarquia de componentes
 * Estas flexibilidad se consigue a expensas de una mayor burocracia y complejidad en el manejo de datos.
 * @package Componentes
 * @subpackage Negocio
 */
class toba_cn extends toba_componente
{
	protected $_transaccion_abierta;			//Indica si la transaccion se encuentra en proceso

	final function __construct($id)
	{
		parent::__construct($id);
		$this->_transaccion_abierta = false;
		// Cargo las dependencias
		foreach( $this->_lista_dependencias as $dep){
			$this->cargar_dependencia($dep);
			$this->_dependencias[$dep]->set_controlador($this, $dep);
			$this->dep($dep)->inicializar();
		}		
	}
	

	/**
	 * Ventana de extensión que se ejecuta al iniciar el componente en todos los pedidos en los que participa.
	 * @ventana
	 */
	function ini()
	{
	}	

	/**
	 * Evento que se dispara cuando se limpia la memoria
	 * @deprecated Desde 1.4.0 usar limpiar_memoria
	 * @see limpiar_memoria($no_borrar)
	 */
	function evt__limpieza_memoria($no_borrar=null)
	{
		$this->_log->obsoleto(__CLASS__, __METHOD__, "1.4.0", "Usar limpiar_memoria");
		$this->limpiar_memoria($no_borrar);
	}

	/**
	 * Borra la memoria de este Cn y lo reinicializa
	 * @param array $no_borrar Excepciones, propiedades que no se van a poner en null
	 */
	function limpiar_memoria($no_borrar=null)
	{
		$this->_log->debug( $this->get_txt() . "[callback][ limpiar_memoria ]", 'toba');
		$this->eliminar_estado_sesion($no_borrar);
		$this->ini();
	}

	//-------------------------------------------------------------------------------
	//------------------  PROCESAMIENTO  --------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Limpia la memoria propia
	 */
	function cancelar()
	{
		$this->_log->debug( $this->get_txt() . "[ cancelar ]", 'toba');
		$this->evt__limpieza_memoria();
	}

	/**
	 * El procesamiento se dispara cuando la entrega de datos ci->cn ha finalizado
	 * Se inicia una transaccion de base de datos y dentro de ella se llama a :
	 *  - {@link evt__validar_datos() evt__validar_datos} 
	 *  - {@link evt__procesar_especifico() evt__procesar_especifico}
	 *  Una vez terminada la transacción se invoca a la limpieza de memoria
	 * 
	 * @todo Ver la posibilidad de usar ignore_user_abort() para evitar problemas con medios no transaccionales
	 */
	function procesar($parametros=null, $transaccionar=true, $limpiar_memoria=true)
	{
		$resultado = null;
		$this->_log->debug( $this->get_txt() . "[ toba_cn: procesar ]", 'toba');
		try {
			//ignore_user_abort();				//------> ?????
			if($transaccionar) $this->iniciar_transaccion();
			$this->evt__validar_datos();
			$resultado = $this->evt__procesar_especifico($parametros);
			if($transaccionar) $this->finalizar_transaccion();
			if($limpiar_memoria) $this->limpiar_memoria();
			return $resultado;
		}
		catch(toba_error $e){
			if($transaccionar) $this->abortar_transaccion();
			$this->_log->debug($e, 'toba');	
			throw $e;
		}
	}

	/**
	 * Ventana de validacion que se ejecuta al inicio del procesamiento final
	 * En caso de querer abortar el procesamiento lanzar una excepcion que herede de toba_error
	 * @ventana
	 */
	function evt__validar_datos()
	{}

	/**
	 * Ventana para incluir el procesamiento de negocio
	 * En caso de querer abortar el procesamiento lanzar una excepcion que herede de toba_error
	 * @ventana
	 */
	function evt__procesar_especifico()
	{}

	/**
	 *	Procesamiento para esquemas de CNs anidados
	 * 
	 * @todo Ver la posibilidad de usar ignore_user_abort() para evitar problemas con medios no transaccionales
	 */
	function procesar_anidado($parametros=null, $no_borrar=null)
	{
		$resultado = null;
		$this->_log->debug( $this->get_txt() . "[ toba_cn: procesar_anidado ]", 'toba');
		$this->evt__validar_datos();
		$resultado = $this->evt__procesar_especifico($parametros);
		$this->evt__limpieza_memoria($no_borrar);
		return $resultado;
	}

	//-------------------------------------------------------------------------------
	//------------------  Manejo de TRANSACCIONES  ----------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * @see toba_db::abrir_transaccion()
	 */
	function iniciar_transaccion()
	{
		$this->_transaccion_abierta = true;
		return toba::db($this->_info['fuente'])->abrir_transaccion();
	}
	
	/**
	 * @see toba_db::cerrar_transaccion()
	 */	
	function finalizar_transaccion()
	{
		$this->_transaccion_abierta = false;
		return toba::db($this->_info['fuente'])->cerrar_transaccion();
	}

	/**
	 * @see toba_db::abortar_transaccion()
	 */		
	function abortar_transaccion()
	{
		$this->_transaccion_abierta = false;
		return toba::db($this->_info['fuente'])->abortar_transaccion();
	}

	/**
	 * @see toba_db::ejecutar()
	 */			
	function ejecutar_sql($sentencias_sql)
	{
		if($this->_transaccion_abierta){
			if(!is_array($sentencias_sql)){
				$sentencias_sql = array($sentencias_sql);
			}
			foreach($sentencias_sql as $sql){
				toba::db($this->_info['fuente'])->ejecutar($sql);
			}
		}else{
			throw new toba_error_def('La transaccion no se encuentra abierta');
		}
	}
}
?>